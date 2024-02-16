package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"os"
	"strconv"
	"time"
)

type Vendor struct {
	ID               int    `json:"id"`
	Name             string `json:"name"`
	TgID             int    `json:"tg_id"`
	Role             int    `json:"role"`
	CityName         string `json:"city_name"`
	Deleted          int    `json:"deleted"`
	PriceConfirmed   int    `json:"price_confirmed"`
	TimePriceConfirm int64  `json:"time_price_confirm"`
}

type SendMessageResponseT struct {
	Ok     bool `json:"ok"`
	Result struct {
		MessageID  int `json:"message_id"`
		SenderChat struct {
			ID       int64  `json:"id"`
			Title    string `json:"title"`
			Username string `json:"username"`
			Type     string `json:"type"`
		} `json:"sender_chat"`
		Chat struct {
			ID       int64  `json:"id"`
			Title    string `json:"title"`
			Username string `json:"username"`
			Type     string `json:"type"`
		} `json:"chat"`
		Date int    `json:"date"`
		Text string `json:"text"`
	} `json:"result"`
}

var apiLink string = os.Getenv("api_link")
var botToken string = os.Getenv("bot_token")

// час и минуты начала проверки (Узбекистан +5 часов, поэтому проверка в 5 часов по 000 UTC, в Ташкенте будет 10 часов)
var hour int = 5
var minute int = 0

func main() {

	//запуск сервера для проверки
	go func() {
		http.HandleFunc("/health/", func(w http.ResponseWriter, r *http.Request) {
			w.Write([]byte("success"))
		})
		http.ListenAndServe(":80", nil)
	}()

	for range time.Tick(time.Second * 1) {

		// определим когда запустить проверку подтверждения цен
		// время сейчас
		timeNow := time.Now()
		// время проверки
		nextTimeToStart := time.Date(timeNow.Year(), timeNow.Month(), timeNow.Day(), hour, minute, 0, 0, time.Local)
		// разница между этими значениями
		difference := nextTimeToStart.Sub(timeNow).Milliseconds()

		// если разница меньше нуля, то обозначенное время проверки уже истекло и необх переопределить его и difference
		if difference < 0 {
			nextTimeToStart = nextTimeToStart.Add(time.Hour * 24)
			difference = nextTimeToStart.Sub(timeNow).Milliseconds()
		}
		fmt.Println(nextTimeToStart)
		// время, на которе необх приостановить процесс
		// как только оно истечёт - настанет время проверки
		tSleep := time.Duration(difference) * time.Millisecond
		time.Sleep(tSleep)

		// ПРОВЕРКА ПОДТВЕРЖДЕНИЯ ЦЕН

		// получим всех Поставщиков активных, неудалённых и с активным городом
		vendorsAll := getAllVendors()

		// получим данные админа
		admin := getAdmin()
		adminChatId := admin[0].TgID

		// если поставщики есть:
		// 1. у каждого проверим время подтверждения, оно должно быть позже 8:59 (Узбекистан +5) текущего дня
		// 2. если это не так, то price_confirmed=0 этого поставщика
		// 3. отправляем уведомление Админу, что этот поставщик не подтвердил цены в период 9:00-10:00

		if vendorsAll != nil {

			// с каким временем будем сравнивать (с 9:00 по Ташкенту) в юникс
			beginIntervalTime := nextTimeToStart.Add(time.Hour * -1).Unix()

			for _, vendor := range vendorsAll {
				// время подтверждения из юникс в timestamp
				// timePriceConfirmVendor := time.Unix(vendor.TimePriceConfirm, 0)

				message := ""

				// если поставщик нажал кнопку раньше, чем 9.00, то сбросим его подтверждение цен
				if vendor.TimePriceConfirm < beginIntervalTime {

					// price_confirmed=0 этого поставщика
					changePriceConfirm(vendor)

					// отправляем уведомление Админу, что этот поставщик не подтвердил цены в период 9:00-10:00
					message = "Поставщик " + vendor.Name + ", город " + vendor.CityName + " не подтвердил цены в 10:00"

					sendTelegramMessage(message, adminChatId)
				}
			}
		}

		time.Sleep(time.Second * 5)
	}
}

// отправка сообщения в телегу
func sendTelegramMessage(message string, chatId int) bool {

	var response SendMessageResponseT

	requestStr := "https://api.telegram.org/bot" + botToken + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=" + message
	resp, err := http.Get(requestStr)

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при отправке сообщения в телеграм: " + err.Error())
		return false
	}

	data, err := io.ReadAll(resp.Body)

	if err != nil {
		fmt.Println("Произошла ошибка при обработке полученных данных от телеграм (отправка сообщения): " + err.Error())
	}

	json.Unmarshal(data, &response)

	if response.Ok {
		return true
	}

	fmt.Println("Произошла ошибка при отправке сообщения в телеграм (ошибочный результат): " + requestStr)
	return false
}

// функция получения данных из таблицы Поставщиков
func getAllVendors() []Vendor {
	var vendors []Vendor

	// получаем всех поставщиков активных, неудалённых, c активным и неудалённым городом и с ролью 2 (поставщик)
	resp, err := http.Get("http://" + apiLink + "/api/vendors/get-with-details.php?deleted=0&is_active=1&city_deleted=0&city_active=1&role=2")

	if err != nil {
		fmt.Println("Произошла ошибка http.Get: ", err)
		return nil
	}

	// считаем данные в переменную data
	data, err := io.ReadAll(resp.Body)

	if err != nil {
		fmt.Println("Произошла ошибка io.ReadAll(resp.Body): ", err)
		return nil
	}

	// разнесём по полям в переменную типа Vendor
	json.Unmarshal(data, &vendors)

	return vendors
}

// получим данные админа
func getAdmin() []Vendor {
	var admin []Vendor

	// получаем данные админа с  ролью 1 (админ) первую запись (тк у нас только 1 админ)
	resp, err := http.Get("http://" + apiLink + "/api/vendors/get-with-details.php?deleted=0&role=1&limit=1")

	if err != nil {
		fmt.Println("Произошла ошибка http.Get: ", err)
		return nil
	}

	// считаем данные в переменную data
	data, err := io.ReadAll(resp.Body)

	if err != nil {
		fmt.Println("Произошла ошибка io.ReadAll(resp.Body): ", err)
		return nil
	}

	// разнесём по полям в переменную типа Vendor
	json.Unmarshal(data, &admin)

	return admin
}

// функция изменения поля price_confirmed таблицы Поставщиков
func changePriceConfirm(vendor Vendor) {
	data := []byte(`{"id":"` + strconv.Itoa(vendor.ID) + `", "price_confirmed":"0"}`)
	r := bytes.NewReader(data)
	http.Post("http://"+apiLink+"/api/vendors.php", "application/json", r)
}
