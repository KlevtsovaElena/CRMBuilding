package main

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"net/url"
	"os"
	"strconv"
	"strings"
	"time"

	_ "github.com/go-sql-driver/mysql"
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
	ID          int
	FirstName   string
	LastName    string
	PhoneNumber string
	City        string
}

var host string = "https://api.telegram.org/bot"
var token string = "6251938024:AAG84w6ZyxcVqUxmRRUW0Ro8d4ej7FpU83o"

var step int = 1

var capacity int

var tel string
var FirstName string
var LastName string

var products = []string{}

// создаем соединение с БД
var Db, Err = sql.Open("mysql", "root:admin@tcp(mysql:3306)/crm-building")

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
			lastName := responseObj.Result[i].Message.From.LastName
			mesIdRepl := responseObj.Result[i].Message.MessageID
			phone := responseObj.Result[i].Message.Contact.PhoneNumber
			button := need.Result[i].CallbackQuery.Data
			id := need.Result[i].CallbackQuery.From.ID
			mesIdInline := need.Result[i].CallbackQuery.Message.MessageID

			//пишем бизнес логику ----------- мозги

			//отвечаем пользователю на его сообщение
			go sendMessage(chatId, id, mesIdInline, mesIdRepl, messageTime, text, button, phone, firstName, lastName)

		}

		//запоминаем update_id  последнего сообщения
		lastMessage = responseObj.Result[number-1].UpdateID + 1

	}
}

func sendMessage(chatId int, id int, mesIdInline int, mesIdRepl int, messageTime int, text string, button string, phone string, firstName string, lastName string) {

	fmt.Println(text)

	switch {
	case text == "/start":

		step = 1
		FirstName = firstName
		LastName = lastName

		buttons := [][]map[string]interface{}{
			{{"text": "Русский 🇷🇺", "callback_data": "russian"}},
			{{"text": "Узбекский 🇺🇿", "callback_data": "uzbekistan"}},
			{{"text": "Ўзбекча 🇺🇿", "callback_data": "usbecha"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		// http.Get(host + token + "/deleteMessage?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesId))
		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Здравствуйте, добро пожаловать в Стройбот. Выберите язык&reply_markup=" + string(inlineKeyboardJSON))

		step += 1
		break

	case step == 2:

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

		step += 1
		break

	case text == "Нет":

		buttons := [][]map[string]interface{}{
			{{"text": "Назад 🔙", "callback_data": "backToPhone"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=К сожалению вы не сможете пройти дальше, если не укажите номер телефона&reply_markup=" + string(inlineKeyboardJSON))

		step -= 1
		break

	case step == 3:

		step += 1
		fmt.Println(step)

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

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Выберите свой город&reply_markup=" + string(inlineKeyboardJSON))

	case step == 4 || button == "backToMenu":

		fmt.Println(FirstName)
		fmt.Println(LastName)

		//создали "бд юзеров"
		usersDB := make(map[int]UserT)

		//считываем из бд при включении
		dataFile, _ := ioutil.ReadFile("db.json")
		json.Unmarshal(dataFile, &usersDB)

		//определяем зарегистрирован ли пользователь
		_, exist := usersDB[id]
		if !exist {
			user := UserT{}
			user.ID = id
			user.FirstName = FirstName
			user.LastName = LastName
			user.PhoneNumber = tel
			user.City = button
			//если не зарегистрирован - добавляем в БД и сохраняем в ОП
			_, err := Db.Query("INSERT INTO `customers`(`id`, `first_name`,`last_name`, `phone`, `city`) VALUES(?,?, ?, ?,?)", id, FirstName, LastName, tel, button)
			if err != nil {
				fmt.Println("Ошибка сохранения пользователя ", err)
			} else {
				fmt.Println("пользователь добавлен")
			}

			usersDB[chatId] = user

			file, _ := os.Create("db.json")
			jsonString, _ := json.Marshal(usersDB)
			file.Write(jsonString)

		}

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
						"text": "Актуальные цены на рынке 📈",
					},
				},
				{
					{
						"text": "Актуальный курс 💹",
					},
				},
				{
					{
						"text": "Настройки ⚙️",
					},
				},
				{
					{
						"text": "Мои заказы 📕",
					},
				},
				{
					{
						"text": "Информация ℹ️",
					},
				},
				{
					{
						"text": "Связаться 📞",
					},
				},
				{
					{
						"text": "Корзина 🗑",
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

		step += 1
		break

	case step == 5 && text == "Заказать 🛍":
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

		step += 1
		break

	case step == 6 || button == "backToGips":

		buttons := [][]map[string]interface{}{}
		//запрос
		rows, err := Db.Query("SELECT category_name FROM categories")
		if err != nil {
			log.Fatal(err)
		}
		defer rows.Close()

		for rows.Next() {
			var category_name string
			if err := rows.Scan(&category_name); err != nil {
				fmt.Println("Ошибка чтения данных:", err.Error())
				return
			}
			button := []map[string]interface{}{
				{
					"text":          category_name,
					"callback_data": category_name,
				},
			}
			buttons = append(buttons, button)
		}

		buttons = append(buttons, []map[string]interface{}{
			{
				"text":          "Назад",
				"callback_data": "backToOffer",
			},
		})

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Тип гипсокартона&reply_markup=" + string(inlineKeyboardJSON))

		step += 1
		break

	case button == "backToOffer":
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

		step = 5
		break

	case step == 7 && button == "Потолочный":

		buttons := [][]map[string]interface{}{}
		//запрос
		rows, err := Db.Query("SELECT brand_name FROM brands")
		if err != nil {
			log.Fatal(err)
		}
		defer rows.Close()

		for rows.Next() {
			var brand_name string
			if err := rows.Scan(&brand_name); err != nil {
				fmt.Println("Ошибка чтения данных:", err.Error())
				return
			}
			button := []map[string]interface{}{
				{
					"text":          brand_name,
					"callback_data": brand_name,
				},
			}
			buttons = append(buttons, button)
		}

		buttons = append(buttons, []map[string]interface{}{
			{
				"text":          "Назад",
				"callback_data": "backToGips",
			},
		})

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Бренд&reply_markup=" + string(inlineKeyboardJSON))
		step += 1
		break

	case step == 8 && button == "КНАУФ":

		//запрос
		rows, err := Db.Query("SELECT id, name, description, photo, price, market_price FROM products WHERE brand_id = 3")
		if err != nil {
			log.Fatal(err)
		}
		defer rows.Close()

		for rows.Next() {
			var productId int
			var name string
			var description string
			var photo string
			var price int
			var market_price int
			if err := rows.Scan(&productId, &name, &description, &photo, &price, &market_price); err != nil {
				fmt.Println("Ошибка чтения данных:", err.Error())
				return
			}

			// Создаем объект инлайн клавиатуры
			buttons := [][]map[string]interface{}{
				{
					{"text": "➖", "callback_data": "minus"},
					{"text": "1", "callback_data": "capacity"},
					{"text": "➕", "callback_data": "plus"},
				},
				{{"text": "Добавить в корзину 🛒", "callback_data": "add:" + strconv.Itoa(productId)}},
				{{"text": "Перейти в корзину 🗑", "callback_data": "goToCart"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

			fmt.Println(photo)

			// Создание URL запроса
			apiURL := fmt.Sprintf("https://api.telegram.org/bot%s/sendPhoto?chat_id=%s&caption="+name+" кнауф "+description+" Среднерыночная цена в городе Ташкент "+strconv.Itoa(market_price)+" сум Цена Стройбота "+strconv.Itoa(price)+" сум &photo="+photo+"&reply_markup="+string(inlineKeyboardJSON), token, strconv.Itoa(id))
			requestURL, err := url.Parse(apiURL)
			if err != nil {
				log.Fatal(err)
			}

			// Создание HTTP GET-запроса с параметрами
			request, err := http.NewRequest("GET", requestURL.String(), nil)
			if err != nil {
				log.Fatal(err)
			}

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

		step += 1
		break

	case step == 9 && button == "goToCart":

	}

	if strings.SplitN(button, ":", 2)[0] == "add" {
		products = append(products, strings.Split(button, ":")[1])
		fmt.Println(products)
	}

	// if button == "plus" {

	// 	capacity += 1

	// 	// Создаем новую инлайн клавиатуру с обновленным числом
	// 	buttons := [][]map[string]interface{}{
	// 		{
	// 			{"text": "➖", "callback_data": "minus"},
	// 			{"text": capacity, "callback_data": "capacity"},
	// 			{"text": "➕", "callback_data": "plus"},
	// 		},
	// 		{{"text": "Добавить в корзину 🛒", "callback_data": "button4"}},
	// 		{{"text": "Перейти в корзину 🗑", "callback_data": "button5"}},
	// 	}

	// 	inlineKeyboard := map[string]interface{}{
	// 		"inline_keyboard": buttons,
	// 	}

	// 	inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

	// 	http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
	// }

	// if button == "minus" {
	// 	capacity -= 1

	// 	if capacity < 1 {

	// 		capacity += 1
	// 		// Создаем новую инлайн клавиатуру с обновленным числом
	// 		buttons := [][]map[string]interface{}{
	// 			{
	// 				{"text": "➖", "callback_data": "minus"},
	// 				{"text": capacity, "callback_data": "capacity"},
	// 				{"text": "➕", "callback_data": "plus"},
	// 			},
	// 			{{"text": "Добавить в корзину 🛒", "callback_data": "button4"}},
	// 			{{"text": "Перейти в корзину 🗑", "callback_data": "button5"}},
	// 		}

	// 		inlineKeyboard := map[string]interface{}{
	// 			"inline_keyboard": buttons,
	// 		}

	// 		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

	// 		http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
	// 	} else {
	// 		// Создаем новую инлайн клавиатуру с обновленным числом
	// 		buttons := [][]map[string]interface{}{
	// 			{
	// 				{"text": "➖", "callback_data": "minus"},
	// 				{"text": capacity, "callback_data": "capacity"},
	// 				{"text": "➕", "callback_data": "plus"},
	// 			},
	// 			{{"text": "Добавить в корзину 🛒", "callback_data": "button4"}},
	// 			{{"text": "Перейти в корзину 🗑", "callback_data": "button5"}},
	// 		}

	// 		inlineKeyboard := map[string]interface{}{
	// 			"inline_keyboard": buttons,
	// 		}

	// 		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

	// 		http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
	// 	}
	// }
}
