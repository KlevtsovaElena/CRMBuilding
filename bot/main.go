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
	Username    string
	tg_id       int
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

var products = []int{}

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
			username := need.Result[i].CallbackQuery.From.Username

			//пишем бизнес логику ----------- мозги

			//отвечаем пользователю на его сообщение
			go sendMessage(chatId, id, mesIdInline, mesIdRepl, messageTime, text, button, phone, firstName, lastName, username)

		}

		//запоминаем update_id  последнего сообщения
		lastMessage = responseObj.Result[number-1].UpdateID + 1

	}
}

func sendMessage(chatId int, id int, mesIdInline int, mesIdRepl int, messageTime int, text string, button string, phone string, firstName string, lastName string, username string) {

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

		fmt.Println(step)

		var city_id int = 1
		tel = phone
		buttons := [][]map[string]interface{}{}
		//запрос
		rows, err := Db.Query("SELECT name FROM cities")
		if err != nil {
			log.Fatal(err)
		}
		defer rows.Close()

		for rows.Next() {
			var name string
			if err := rows.Scan(&name); err != nil {
				fmt.Println("Ошибка чтения данных:", err.Error())
				return
			}
			button := []map[string]interface{}{
				{
					"text":          name,
					"callback_data": city_id,
				},
			}
			buttons = append(buttons, button)

			city_id += 1
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Выберите свой город&reply_markup=" + string(inlineKeyboardJSON))
		step += 1
		break

	case step == 4:

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
			user.Username = username
			user.tg_id = id
			user.PhoneNumber = tel
			user.City = button
			//если не зарегистрирован - добавляем в БД и сохраняем в ОП
			_, err := Db.Query("INSERT INTO `customers`(`first_name`, `last_name`, `tg_username`, `tg_id`, `phone`, `city_id`) VALUES(?, ?, ?, ?, ?, ?)", FirstName, LastName, username, id, tel, button)
			if err != nil {
				fmt.Println("Ошибка сохранения пользователя ", err)
			} else {
				fmt.Println("пользователь добавлен")

			}

			usersDB[id] = user

		} else {

			fmt.Println(id)
			fmt.Println(button)
			//если зарегистрирован - обновляем в БД
			_, err := Db.Exec("UPDATE `customers` SET city_id = ? WHERE tg_id = ?", button, id)
			if err != nil {
				fmt.Println("Ошибка обновления пользователя ", err)
			} else {
				fmt.Println("пользователь обновлён")
			}
		}

		file, _ := os.Create("db.json")
		jsonString, _ := json.Marshal(usersDB)
		file.Write(jsonString)

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

	case button == "backToMenu":
		step = 4
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

	case step == 5 && text == "Заказать 🛍" || button == "backToGoods":
		step = 5
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

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Выберите материал&reply_markup=" + string(inlineKeyboardJSON))

		step += 1
		break

	case step == 6 || button == "backToGips":

		step = 6
		buttons := [][]map[string]interface{}{
			{{"text": "Потолочный", "callback_data": "Потолочный"}},
			{{"text": "Назад 🔙", "callback_data": "backToGoods"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Выберите материал&reply_markup=" + string(inlineKeyboardJSON))

		step += 1
		break

	case button == "backToGoods":
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

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Выберите материал&reply_markup=" + string(inlineKeyboardJSON))

		step = 5
		break

	case step == 7 && button == "Потолочный":

		step = 7
		buttons := [][]map[string]interface{}{}
		//запрос
		rows, err := Db.Query("SELECT * FROM brands")
		if err != nil {
			log.Fatal(err)
		}
		defer rows.Close()

		for rows.Next() {
			var brand_id int
			var brand_name string
			if err := rows.Scan(&brand_id, &brand_name); err != nil {
				fmt.Println("Ошибка чтения данных:", err.Error())
				return
			}
			button := []map[string]interface{}{
				{
					"text":          brand_name,
					"callback_data": brand_id,
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

	case step == 8:

		//запрос
		rows, err := Db.Query("SELECT id, name, description, photo, price, max_price FROM products WHERE brand_id = ?", button)
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
			var max_price int
			if err := rows.Scan(&productId, &name, &description, &photo, &price, &max_price); err != nil {
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
			apiURL := fmt.Sprintf("https://api.telegram.org/bot%s/sendPhoto?chat_id=%s&caption="+name+" кнауф "+description+" Среднерыночная цена в городе Ташкент "+strconv.Itoa(max_price)+" сум Цена Стройбота "+strconv.Itoa(price)+" сум &photo="+photo+"&reply_markup="+string(inlineKeyboardJSON), token, strconv.Itoa(id))
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
		// Проверка на наличие повторяющихся элементов
		hasDuplicates := false
		counts := make(map[int]int)
		quantity := make(map[int]int)
		finalPrice := 0
		cartText := ""

		for _, num := range products {
			counts[num]++
			if counts[num] > 1 {
				hasDuplicates = true
			}
		}

		if hasDuplicates {
			// Подсчет количества повторяющихся элементов и удаление повторений кроме одного
			for num, count := range counts {
				if count > 1 {
					fmt.Printf("Число %d повторяется %d раз(а)\n", num, count)
					counts[num] = 1
					quantity[num] = count
				}
			}

			// Формирование нового массива без повторений
			newArray := make([]int, 0, len(products))
			for _, num := range products {
				if counts[num] > 0 {
					newArray = append(newArray, num)
					counts[num] = 0
				}
			}

			fmt.Println("Массив после удаления повторений:", newArray)
			for _, num := range newArray {
				count := quantity[num]
				//запрос
				rows, err := Db.Query("SELECT name, price FROM products WHERE id = ?", num)
				if err != nil {
					log.Fatal(err)
				}
				defer rows.Close()

				for rows.Next() {
					var name string
					var price int
					if err := rows.Scan(&name, &price); err != nil {
						fmt.Println("Ошибка чтения данных:", err.Error())
						return
					}

					if count == 0 {
						cartText += name + " 1 ✖️ " + strconv.Itoa(price)
						finalPrice += price
					} else {
						cartText += name + " " + strconv.Itoa(count) + " ✖️ " + strconv.Itoa(price)
						finalPrice += price * count
					}

				}

			}
			// Создаем объект инлайн клавиатуры
			buttons := [][]map[string]interface{}{
				{{"text": "Оформить заказ", "callback_data": "buy"}},
				{{"text": "Назад", "callback_data": "backToGoods"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=" + cartText + " Итого: " + strconv.Itoa(finalPrice) + "&reply_markup=" + string(inlineKeyboardJSON))

		} else {
			for _, num := range products {
				rows, err := Db.Query("SELECT name, price FROM products WHERE id = ?", num)
				if err != nil {
					log.Fatal(err)
				}
				defer rows.Close()

				for rows.Next() {
					var name string
					var price int
					if err := rows.Scan(&name, &price); err != nil {
						fmt.Println("Ошибка чтения данных:", err.Error())
						return
					}

					cartText += name + " 1 ✖️ " + strconv.Itoa(price)
					finalPrice += price

				}
			}

			// Создаем объект инлайн клавиатуры
			buttons := [][]map[string]interface{}{
				{{"text": "Оформить заказ", "callback_data": "buy"}},
				{{"text": "Назад", "callback_data": "backToGoods"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=" + cartText + " Итого: " + strconv.Itoa(finalPrice) + "&reply_markup=" + string(inlineKeyboardJSON))
		}
		step += 1
		break

	case step == 10 && button == "buy":
		time := time.Now().Unix()
		//если не зарегистрирован - добавляем в БД и сохраняем в ОП
		_, err := Db.Query("INSERT INTO `orders`(`customer_id`,`order_date`) VALUES(?,?)", id, time)
		if err != nil {
			fmt.Println("Ошибка сохранения заказа ", err)
		} else {
			fmt.Println("заказ добавлен")
		}
		// Создаем объект клавиатуры
		keyboard := map[string]interface{}{
			"keyboard": [][]map[string]interface{}{
				{
					{
						"text":             "Да",
						"request_location": true,
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
		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Поделится местоположением?&reply_markup=" + string(keyboardJSON))
		step += 1
		break

	case step == 11:
		// //если не зарегистрирован - добавляем в БД и сохраняем в ОП
		// _, err := Db.Query("INSERT INTO `ordered_products`(`first_name`,`last_name`, `phone`, `city`) VALUES(?,?, ?, ?,?)", FirstName, LastName, tel, button)
		// if err != nil {
		// 	fmt.Println("Ошибка сохранения пользователя ", err)
		// } else {
		// 	fmt.Println("пользователь добавлен")
		// }

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
		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Благодарим Вас за то, что выбрали Стройбот, с вами свяжуться в течении часа&reply_markup=" + string(keyboardJSON))

		step += 1
		break
	}

	if strings.SplitN(button, ":", 2)[0] == "add" {
		productStr := strings.Split(button, ":")[1]
		productID, _ := strconv.Atoi(productStr)
		products = append(products, productID)
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

	if text == "Актуальные цены на рынке 📈" {

		dt := time.Now().Format("01-02-2006 15:04:05")
		buttons := [][]map[string]interface{}{
			{{"text": "Назад 🔙", "callback_data": "backToMenu"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Актуальные цены на " + dt + "&reply_markup=" + string(inlineKeyboardJSON))

	}

	if text == "Актуальный курс 💹" {

		dt := time.Now().Format("01-02-2006 15:04:05")
		buttons := [][]map[string]interface{}{
			{{"text": "Назад 🔙", "callback_data": "backToMenu"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Актуальный курс на " + dt + "&reply_markup=" + string(inlineKeyboardJSON))

	}

	if text == "Настройки ⚙️" {
		buttons := [][]map[string]interface{}{
			{{"text": "Мой номер", "callback_data": "number"}},
			{{"text": "Город", "callback_data": "city"}},
			{{"text": "Язык", "callback_data": "backToMenu"}},
			{{"text": "Оферта", "callback_data": "oferta"}},
			{{"text": "Жалобы и предложения", "callback_data": "book"}},
			{{"text": "Стать партнёром", "callback_data": "partnership"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Настройки&reply_markup=" + string(inlineKeyboardJSON))

	}

	if button == "city" {
		var city_id int = 1
		buttons := [][]map[string]interface{}{}
		//запрос
		rows, err := Db.Query("SELECT name FROM cities")
		if err != nil {
			log.Fatal(err)
		}
		defer rows.Close()

		for rows.Next() {
			var name string
			if err := rows.Scan(&name); err != nil {
				fmt.Println("Ошибка чтения данных:", err.Error())
				return
			}
			button := []map[string]interface{}{
				{
					"text":          name,
					"callback_data": city_id,
				},
			}
			buttons = append(buttons, button)
			city_id += 1
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Выберите свой город&reply_markup=" + string(inlineKeyboardJSON))

		step = 4
	}

	if text == "Мои заказы 📕" {

		buttons := [][]map[string]interface{}{
			{{"text": "Назад 🔙", "callback_data": "backToMenu"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Мои заказы &reply_markup=" + string(inlineKeyboardJSON))
	}

	if text == "Информация ℹ️" {

		buttons := [][]map[string]interface{}{
			{{"text": "Назад 🔙", "callback_data": "backToMenu"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Информация о проекте&reply_markup=" + string(inlineKeyboardJSON))

	}

	if text == "Связаться 📞" {

		buttons := [][]map[string]interface{}{
			{{"text": "Назад 🔙", "callback_data": "backToMenu"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Связаться &reply_markup=" + string(inlineKeyboardJSON))

	}
}
