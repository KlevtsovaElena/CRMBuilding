package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"io/ioutil"
	"log"
	"mime/multipart"
	"net/http"
	"net/url"
	"os"
	"path/filepath"
	"strconv"
	"time"
)

type ResponseT struct {
	Ok     bool `json:"ok"`
	Result []struct {
		UpdateID int `json:"update_id"`
		Message  struct {
			MessageID int `json:"message_id"`
			From      struct {
				ID           int    `json:"id"`
				IsBot        bool   `json:"is_bot"`
				FirstName    string `json:"first_name"`
				LastName     string `json:"last_name"`
				Username     string `json:"username"`
				LanguageCode string `json:"language_code"`
			} `json:"from"`
			Chat struct {
				ID        int    `json:"id"`
				FirstName string `json:"first_name"`
				LastName  string `json:"last_name"`
				Username  string `json:"username"`
				Type      string `json:"type"`
			} `json:"chat"`
			Date    int `json:"date"`
			Contact struct {
				PhoneNumber string `json:"phone_number"`
			} `json:"contact"`
			Text string `json:"text"`
			Data string `json:"data"`
		} `json:"message"`
	} `json:"result"`
}

type InlineButton struct {
	Ok     bool `json:"ok"`
	Result []struct {
		UpdateID      int `json:"update_id"`
		CallbackQuery struct {
			ID   string `json:"id"`
			From struct {
				ID           int    `json:"id"`
				IsBot        bool   `json:"is_bot"`
				FirstName    string `json:"first_name"`
				Username     string `json:"username"`
				LanguageCode string `json:"language_code"`
			} `json:"from"`
			Message struct {
				MessageID int `json:"message_id"`
				From      struct {
					ID        int64  `json:"id"`
					IsBot     bool   `json:"is_bot"`
					FirstName string `json:"first_name"`
					Username  string `json:"username"`
				} `json:"from"`
				Chat struct {
					ID        int    `json:"id"`
					FirstName string `json:"first_name"`
					Username  string `json:"username"`
					Type      string `json:"type"`
				} `json:"chat"`
				Date        int    `json:"date"`
				Text        string `json:"text"`
				ReplyMarkup struct {
					InlineKeyboard [][]struct {
						Text         string `json:"text"`
						CallbackData string `json:"callback_data"`
					} `json:"inline_keyboard"`
				} `json:"reply_markup"`
			} `json:"message"`
			ChatInstance string `json:"chat_instance"`
			Data         string `json:"data"`
		} `json:"callback_query"`
	} `json:"result"`
}

type UserT struct {
	ID          string
	FirstName   string
	LastName    string
	RegDate     int
	PhoneNumber string
}

var host string = "https://api.telegram.org/bot"
var token string = "6251938024:AAG84w6ZyxcVqUxmRRUW0Ro8d4ej7FpU83o"

var tel string

var capacity int = 1

func main() {

	lastMessage := 0

	for range time.Tick(time.Second * 1) {

		//отправляем запрос к Telegram API на получение сообщений
		var url string = host + token + "/getUpdates?offset=" + strconv.Itoa(lastMessage)
		response, err := http.Get(url)
		if err != nil {
			fmt.Println(err)
		}
		data, _ := ioutil.ReadAll(response.Body)

		//посмотреть данные
		fmt.Println(string(data))

		// var responseObj ResponseT
		//парсим данные из json
		var responseObj ResponseT
		json.Unmarshal(data, &responseObj)

		var need InlineButton
		json.Unmarshal(data, &need)
		//fmt.Println(responseObj)

		//считаем количество новых сообщений
		number := len(responseObj.Result)

		//если сообщений нет - то дальше код не выполняем
		if number < 1 {
			continue
		}

		//в цикле доставать инормацию по каждому сообщению
		for i := 0; i < number; i++ {

			text := responseObj.Result[i].Message.Text
			chatId := responseObj.Result[i].Message.From.ID
			messageTime := responseObj.Result[i].Message.Date
			firstName := responseObj.Result[i].Message.From.FirstName
			mesIdRepl := responseObj.Result[i].Message.MessageID
			phone := responseObj.Result[i].Message.Contact.PhoneNumber
			button := need.Result[i].CallbackQuery.Data
			id := need.Result[i].CallbackQuery.From.ID
			mesIdInline := need.Result[i].CallbackQuery.Message.MessageID

			//пишем бизнес логику ----------- мозги

			//отвечаем пользователю на его сообщение
			go sendMessage(chatId, id, mesIdInline, mesIdRepl, messageTime, text, firstName, button, phone)

		}

		//запоминаем update_id  последнего сообщения
		lastMessage = responseObj.Result[number-1].UpdateID + 1

	}
}

func sendMessage(chatId int, id int, mesIdInline int, mesIdRepl int, messageTime int, text string, firstName string, button string, phone string) {

	fmt.Println(text)

	if text == "/start" {

		buttons := [][]map[string]interface{}{
			{{"text": "Русский 🇷🇺", "callback_data": "russian"}},
			{{"text": "Узбекский 🇺🇿", "callback_data": "uzbekistan"}},
			{{"text": "English 🇬🇧", "callback_data": "english"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		// http.Get(host + token + "/deleteMessage?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesId))
		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Здравствуйте, добро пожаловать в Стройбот. Выберите язык&reply_markup=" + string(inlineKeyboardJSON))

	}

	if button == "russian" {
		// Создаем объект клавиатуры
		keyboard := map[string]interface{}{
			"keyboard": [][]map[string]interface{}{
				{
					{
						"text":            "Да",
						"request_contact": true,
					},
				},
				{
					{
						"text": "Нет",
					},
				},
			},
			"resize_keyboard":   true,
			"one_time_keyboard": true,
		}

		// Преобразуем клавиатуру в JSON
		keyboardJSON, _ := json.Marshal(keyboard)
		// Отправляем сообщение с клавиатурой
		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Поделится номером телефона&reply_markup=" + string(keyboardJSON))
	}

	if button == "backToPhone" {
		// Создаем объект клавиатуры
		keyboard := map[string]interface{}{
			"keyboard": [][]map[string]interface{}{
				{
					{
						"text":            "Да",
						"request_contact": true,
					},
				},
				{
					{
						"text": "Нет",
					},
				},
			},
			"resize_keyboard":   true,
			"one_time_keyboard": true,
		}

		// Преобразуем клавиатуру в JSON
		keyboardJSON, _ := json.Marshal(keyboard)
		// Отправляем сообщение с клавиатурой
		http.Get(host + token + "/deleteMessage?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline))
		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Поделится номером телефона&reply_markup=" + string(keyboardJSON))
	}

	if phone != "" {
		tel = phone
		fmt.Println(tel)
		buttons := [][]map[string]interface{}{
			{{"text": "Город", "callback_data": "city"}},
			{{"text": "Город", "callback_data": "city"}},
			{{"text": "Город", "callback_data": "city"}},
			{{"text": "Город", "callback_data": "city"}},
			{{"text": "Город", "callback_data": "city"}},
			{{"text": "Город", "callback_data": "city"}},
			{{"text": "Город", "callback_data": "city"}},
			{{"text": "Город", "callback_data": "city"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/deleteMessage?chat_id=" + strconv.Itoa(chatId) + "&message_id=" + strconv.Itoa(mesIdRepl-1))
		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Выберите свой город&reply_markup=" + string(inlineKeyboardJSON))
	}

	if text == "Нет" {
		buttons := [][]map[string]interface{}{
			{{"text": "Назад 🔙", "callback_data": "backToPhone"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/deleteMessage?chat_id=" + strconv.Itoa(chatId) + "&message_id=" + strconv.Itoa(mesIdRepl))
		http.Get(host + token + "/deleteMessage?chat_id=" + strconv.Itoa(chatId) + "&message_id=" + strconv.Itoa(mesIdRepl-1))
		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=К сожалению вы не сможете пройти дальше, если не укажите номер телефона&reply_markup=" + string(inlineKeyboardJSON))
	}

	if button == "city" || button == "backToMenu" {

		// Создаем объект клавиатуры
		keyboard := map[string]interface{}{
			"keyboard": [][]map[string]interface{}{
				{
					{
						"text": "Заказать 🛍",
					},
				},
				{
					{
						"text": "Корзина 🗑",
					},
				},
				{
					{
						"text": "Выбрать язык 🇷🇺 🇺🇿 🇬🇧",
					},
				},
				{
					{
						"text": "Назад 🔙",
					},
				},
			},
			"resize_keyboard":   true,
			"one_time_keyboard": true,
		}

		// Преобразуем клавиатуру в JSON
		keyboardJSON, _ := json.Marshal(keyboard)
		// Отправляем сообщение с клавиатурой
		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Главное меню&reply_markup=" + string(keyboardJSON))
	}

	if text == "Заказать 🛍" {
		buttons := [][]map[string]interface{}{
			{{"text": "Гипсокартон", "callback_data": "gips"}},
			{{"text": "Штукатурка", "callback_data": "shtuk"}},
			{{"text": "Шпатлевка", "callback_data": "shpat"}},
			{{"text": "Грунтовка", "callback_data": "grunt"}},
			{{"text": "Назад 🔙", "callback_data": "backToMenu"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Выберите материал&reply_markup=" + string(inlineKeyboardJSON))
	}

	if button == "backToOffer" {
		buttons := [][]map[string]interface{}{
			{{"text": "Гипсокартон", "callback_data": "gips"}},
			{{"text": "Штукатурка", "callback_data": "shtuk"}},
			{{"text": "Шпатлевка", "callback_data": "shpat"}},
			{{"text": "Грунтовка", "callback_data": "grunt"}},
			{{"text": "Назад 🔙", "callback_data": "backToMenu"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Выберите материал&reply_markup=" + string(inlineKeyboardJSON))
	}

	if button == "gips" || button == "backToGips" {
		buttons := [][]map[string]interface{}{
			{{"text": "Потолочный", "callback_data": "gipsPotol"}},
			{{"text": "Стеновый", "callback_data": "gipsSten"}},
			{{"text": "Обычный", "callback_data": "gipsDef"}},
			{{"text": "Назад 🔙", "callback_data": "backToOffer"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Тип гипсокартона&reply_markup=" + string(inlineKeyboardJSON))
	}

	if button == "gipsPotol" {
		buttons := [][]map[string]interface{}{
			{{"text": "Форус", "callback_data": "gipsForus"}},
			{{"text": "AZIA", "callback_data": "gipsAzia"}},
			{{"text": "КНАУФ", "callback_data": "gipsKnauf"}},
			{{"text": "VERO", "callback_data": "gipsVero"}},
			{{"text": "Назад 🔙", "callback_data": "backToGips"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Бренд&reply_markup=" + string(inlineKeyboardJSON))
	}

	if button == "gipsKnauf" {

		// Создаем объект инлайн клавиатуры
		buttons := [][]map[string]interface{}{
			{
				{"text": "➖", "callback_data": "minus"},
				{"text": "1", "callback_data": "capacity"},
				{"text": "➕", "callback_data": "plus"},
			},
			{{"text": "Добавить в корзину 🛒", "callback_data": "button4"}},
			{{"text": "Перейти в корзину 🗑", "callback_data": "button5"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		fmt.Println(inlineKeyboard)

		imagePath := "bot/img/knauf.jpg"
		// Создание буфера для запроса с изображением
		bodyBuf := &bytes.Buffer{}
		bodyWriter := multipart.NewWriter(bodyBuf)

		// Открытие файла изображения
		file, err := os.Open(imagePath)
		if err != nil {
			log.Fatal(err)
		}
		defer file.Close()

		// Создание формы для файла
		fileWriter, err := bodyWriter.CreateFormFile("photo", filepath.Base(imagePath))
		if err != nil {
			log.Fatal(err)
		}

		// Копирование содержимого файла в форму
		_, err = io.Copy(fileWriter, file)
		if err != nil {
			log.Fatal(err)
		}

		// Закрытие формы
		contentType := bodyWriter.FormDataContentType()
		bodyWriter.Close()

		// Создание URL запроса
		apiURL := fmt.Sprintf("https://api.telegram.org/bot%s/sendPhoto?chat_id=%s&caption=Гипсокартон кнауф потолочный влагостойкий (9.5) Среднерыночная цена в городе Ташкент 50 000 сум Цена Стройбота 45 000 сум &reply_markup="+string(inlineKeyboardJSON), token, strconv.Itoa(id))
		requestURL, err := url.Parse(apiURL)
		if err != nil {
			log.Fatal(err)
		}

		// Создание HTTP POST-запроса с изображением
		request, err := http.NewRequest("POST", requestURL.String(), bodyBuf)
		if err != nil {
			log.Fatal(err)
		}
		request.Header.Set("Content-Type", contentType)

		// Отправка запроса
		client := &http.Client{}
		response, err := client.Do(request)
		if err != nil {
			log.Fatal(err)
		}
		defer response.Body.Close()

		// Чтение ответа
		responseData, err := ioutil.ReadAll(response.Body)
		if err != nil {
			log.Fatal(err)
		}

		// Вывод конечной ссылки запроса
		finalURL := request.URL.String()
		fmt.Println("Final URL:", finalURL)

		// Вывод ответа от сервера
		fmt.Println("Response:", string(responseData))
	}

	if button == "plus" {

		capacity += 1

		// Создаем новую инлайн клавиатуру с обновленным числом
		buttons := [][]map[string]interface{}{
			{
				{"text": "➖", "callback_data": "minus"},
				{"text": capacity, "callback_data": "capacity"},
				{"text": "➕", "callback_data": "plus"},
			},
			{{"text": "Добавить в корзину 🛒", "callback_data": "button4"}},
			{{"text": "Перейти в корзину 🗑", "callback_data": "button5"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
	}

	if button == "minus" {
		capacity -= 1

		if capacity < 1 {

			capacity += 1
			// Создаем новую инлайн клавиатуру с обновленным числом
			buttons := [][]map[string]interface{}{
				{
					{"text": "➖", "callback_data": "minus"},
					{"text": capacity, "callback_data": "capacity"},
					{"text": "➕", "callback_data": "plus"},
				},
				{{"text": "Добавить в корзину 🛒", "callback_data": "button4"}},
				{{"text": "Перейти в корзину 🗑", "callback_data": "button5"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

			http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
		} else {
			// Создаем новую инлайн клавиатуру с обновленным числом
			buttons := [][]map[string]interface{}{
				{
					{"text": "➖", "callback_data": "minus"},
					{"text": capacity, "callback_data": "capacity"},
					{"text": "➕", "callback_data": "plus"},
				},
				{{"text": "Добавить в корзину 🛒", "callback_data": "button4"}},
				{{"text": "Перейти в корзину 🗑", "callback_data": "button5"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

			http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
		}
	}
}
