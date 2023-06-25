package main

import (
	"bytes"
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
			Location struct {
				Latitude  float64 `json:"latitude"`
				Longitude float64 `json:"longitude"`
			} `json:"location"`
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

type Order struct {
	CustomerID  int                    `json:"customer_id"`
	OrderDate   int64                  `json:"order_date"`
	Products    map[int]int            `json:"products"`
	Coordinates map[string]interface{} `json:"coordinates"`
}

type OrderItem struct {
	ProductID int
	Quantity  int
}

type Coordinates struct {
	Latitude  float64 `json:"latitude"`
	Longitude float64 `json:"longitude"`
}

type Cities struct {
	ID   int    `json:"id"`
	Name string `json:"name"`
}

type Categories struct {
	ID           int    `json:"id"`
	CategoryName string `json:"category_name"`
}

type Brands struct {
	ID        int    `json:"id"`
	BrandName string `json:"brand_name"`
}

type Product struct {
	ID          int    `json:"id"`
	Name        string `json:"name"`
	Description string `json:"description"`
	Photo       string `json:"photo"`
	Price       int    `json:"price"`
	MaxPrice    int    `json:"max_price"`
}

var host string = "https://api.telegram.org/bot"
var token string = "6251938024:AAG84w6ZyxcVqUxmRRUW0Ro8d4ej7FpU83o"

var step int = 1

var tel string
var FirstName string
var LastName string

var products = make(map[int]int)
var client = http.Client{}

// var products = []int{}

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
			latitude := responseObj.Result[i].Message.Location.Latitude
			longitude := responseObj.Result[i].Message.Location.Longitude
			button := need.Result[i].CallbackQuery.Data
			id := need.Result[i].CallbackQuery.From.ID
			mesIdInline := need.Result[i].CallbackQuery.Message.MessageID
			username := need.Result[i].CallbackQuery.From.Username

			//пишем бизнес логику ----------- мозги

			//отвечаем пользователю на его сообщение
			go sendMessage(chatId, id, mesIdInline, mesIdRepl, messageTime, text, button, phone, firstName, lastName, username, latitude, longitude)

		}

		//запоминаем update_id  последнего сообщения
		lastMessage = responseObj.Result[number-1].UpdateID + 1

	}
}

func sendPost(requestBody string, url string) {
	// Создаем новый POST-запрос
	req, err := http.NewRequest("POST", url, bytes.NewBufferString(requestBody))
	if err != nil {
		fmt.Println("Ошибка при создании запроса:", err)
		return
	}

	// Устанавливаем заголовок Content-Type для указания типа данных в теле запроса
	req.Header.Set("Content-Type", "application/json")

	// Отправляем запрос с использованием стандартного клиента HTTP
	client := &http.Client{}
	resp, err := client.Do(req)
	if err != nil {
		fmt.Println("Ошибка при выполнении запроса:", err)
		return
	}
	defer resp.Body.Close()
}

func sendMessage(chatId int, id int, mesIdInline int, mesIdRepl int, messageTime int, text string, button string, phone string, firstName string, lastName string, username string, latitude float64, longitude float64) {

	fmt.Println(text)

	switch {
	case text == "/start":

		step = 1
		FirstName = firstName
		LastName = lastName

		buttons := [][]map[string]interface{}{
			{{"text": "Русский 🇷🇺", "callback_data": "russian"}},
			{{"text": "O'zbekcha 🇺🇿", "callback_data": "uzbekistan"}},
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

	case step == 3 && text == "Нет":

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

		tel = phone
		buttons := [][]map[string]interface{}{}
		// Создаем GET-запрос
		resp, err := http.Get("http://nginx:80/api/cities.php")
		if err != nil {
			log.Fatal("Ошибка при выполнении запроса:", err)
		}
		defer resp.Body.Close()

		var cities []Cities
		err = json.NewDecoder(resp.Body).Decode(&cities)
		if err != nil {
			log.Fatal("Ошибка при декодировании JSON:", err)
		}

		// Используем полученные данные
		for _, category := range cities {
			button := []map[string]interface{}{
				{
					"text":          category.Name,
					"callback_data": category.ID,
				},
			}
			buttons = append(buttons, button)
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, err := json.Marshal(inlineKeyboard)
		if err != nil {
			log.Fatal("Ошибка при маршалинге данных в формат JSON:", err)
		}

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
			// Создаем тело запроса в виде строки JSON
			requestBody := `{"first_name":` + FirstName + `, "last_name":` + LastName + `, "phone":` + tel + `, "city_id": ` + button + `, "tg_username": ` + username + `}`

			sendPost(requestBody, "http://nginx:80/api/customers.php")

			usersDB[id] = user

		} else {
			// Создаем тело запроса в виде строки JSON
			requestBody := `{"tg_id":` + strconv.Itoa(id) + `, "city_id": ` + button + `}`

			sendPost(requestBody, "http://nginx:80/api/customers.php")
		}

		file, _ := os.Create("db.json")
		jsonString, _ := json.Marshal(usersDB)
		file.Write(jsonString)

		// Создаем объект клавиатуры
		keyboard := map[string]interface{}{
			"keyboard": [][]map[string]interface{}{
				{{"text": "Заказать 🛍"}},

				{{"text": "Актуальный курс 💹"},
					{"text": "Настройки ⚙️"},
				},
				{{"text": "Мои заказы 📕"},
					{"text": "Актуальные цены на рынке 📈"},
				},
				{{"text": "Связаться 📞"},
					{"text": "Корзина 🗑"},
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
				{{"text": "Заказать 🛍"}},

				{{"text": "Актуальный курс 💹"},
					{"text": "Настройки ⚙️"},
				},
				{{"text": "Мои заказы 📕"},
					{"text": "Актуальные цены на рынке 📈"},
				},
				{{"text": "Связаться 📞"},
					{"text": "Корзина 🗑"},
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
		step = 5
		buttons := [][]map[string]interface{}{}
		// Создаем GET-запрос
		resp, err := http.Get("http://nginx:80/api/categories.php")
		if err != nil {
			log.Fatal("Ошибка при выполнении запроса:", err)
		}
		defer resp.Body.Close()

		var categories []Categories
		err = json.NewDecoder(resp.Body).Decode(&categories)
		if err != nil {
			log.Fatal("Ошибка при декодировании JSON:", err)
		}

		// Используем полученные данные
		for _, category := range categories {
			button := []map[string]interface{}{
				{
					"text":          category.CategoryName,
					"callback_data": category.CategoryName,
				},
			}
			buttons = append(buttons, button)
		}
		buttons = append(buttons, []map[string]interface{}{
			{
				"text":          "Назад",
				"callback_data": "backToMenu",
			},
		})

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, err := json.Marshal(inlineKeyboard)
		if err != nil {
			log.Fatal("Ошибка при маршалинге данных в формат JSON:", err)
		}

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Выберите материал&reply_markup=" + string(inlineKeyboardJSON))

		step += 1
		break

	case button == "backToGoods":
		buttons := [][]map[string]interface{}{}
		// Создаем GET-запрос
		resp, err := http.Get("http://nginx:80/api/categories.php")
		if err != nil {
			log.Fatal("Ошибка при выполнении запроса:", err)
		}
		defer resp.Body.Close()

		var categories []Categories
		err = json.NewDecoder(resp.Body).Decode(&categories)
		if err != nil {
			log.Fatal("Ошибка при декодировании JSON:", err)
		}

		// Используем полученные данные
		for _, category := range categories {
			button := []map[string]interface{}{
				{
					"text":          category.CategoryName,
					"callback_data": category.CategoryName,
				},
			}
			buttons = append(buttons, button)
		}
		buttons = append(buttons, []map[string]interface{}{
			{
				"text":          "Назад",
				"callback_data": "backToMenu",
			},
		})

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, err := json.Marshal(inlineKeyboard)
		if err != nil {
			log.Fatal("Ошибка при маршалинге данных в формат JSON:", err)
		}

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Выберите материал&reply_markup=" + string(inlineKeyboardJSON))

		step = 6
		break

	case step == 6:

		step = 6
		buttons := [][]map[string]interface{}{}
		// Создаем GET-запрос
		resp, err := http.Get("http://nginx:80/api/brands.php")
		if err != nil {
			log.Fatal("Ошибка при выполнении запроса:", err)
		}
		defer resp.Body.Close()

		var brands []Brands
		err = json.NewDecoder(resp.Body).Decode(&brands)
		if err != nil {
			log.Fatal("Ошибка при декодировании JSON:", err)
		}

		// Используем полученные данные
		for _, brand := range brands {
			button := []map[string]interface{}{
				{
					"text":          brand.BrandName,
					"callback_data": brand.ID,
				},
			}
			buttons = append(buttons, button)
		}
		buttons = append(buttons, []map[string]interface{}{
			{
				"text":          "Назад",
				"callback_data": "backToGoods",
			},
		})

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Бренд&reply_markup=" + string(inlineKeyboardJSON))
		step += 1
		break

	case step == 7:
		// Создаем GET-запрос
		resp, err := http.Get("http://nginx:80/api/products.php?brand_id=" + button)
		if err != nil {
			log.Fatal("Ошибка при выполнении запроса:", err)
		}
		defer resp.Body.Close()

		var product []Product
		err = json.NewDecoder(resp.Body).Decode(&product)
		if err != nil {
			log.Fatal("Ошибка при декодировании JSON:", err)
		}

		// Используем полученные данные
		for _, product := range product {
			// Создаем объект инлайн клавиатуры
			buttons := [][]map[string]interface{}{
				{
					{"text": "➖", "callback_data": "minus:" + strconv.Itoa(product.ID)},
					{"text": "0", "callback_data": "quantity"},
					{"text": "➕", "callback_data": "add:" + strconv.Itoa(product.ID)},
				},
				{{"text": "Добавить в корзину 🛒", "callback_data": "add:" + strconv.Itoa(product.ID)}},
				{{"text": "Перейти в корзину 🗑", "callback_data": "goToCart"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

			fmt.Println(product.Photo)

			// Создание URL запроса
			apiURL := fmt.Sprintf("https://api.telegram.org/bot%s/sendPhoto?chat_id=%s&caption="+product.Name+" кнауф "+product.Description+" Среднерыночная цена в городе Ташкент "+strconv.Itoa(product.MaxPrice)+" сум Цена Стройбота "+strconv.Itoa(product.Price)+" сум &photo="+product.Photo+"&reply_markup="+string(inlineKeyboardJSON), token, strconv.Itoa(id))
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

	case step == 8 && button == "goToCart":
		finalPrice := 0
		cartText := ""
		for ID := range products {

			fmt.Println(ID)
			// Создаем GET-запрос
			resp, err := http.Get("http://nginx:80/api/products.php?id=" + strconv.Itoa(ID))
			if err != nil {
				log.Fatal("Ошибка при выполнении запроса:", err)
			}
			defer resp.Body.Close()

			var product Product
			err = json.NewDecoder(resp.Body).Decode(&product)
			if err != nil {
				fmt.Println("Ошибка при декодировании JSON:", err)
				return
			}

			cartText += product.Name + " " + strconv.Itoa(products[ID]) + " ✖️ " + strconv.Itoa(product.Price)
			finalPrice += product.Price * products[ID]

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

		step += 1
		break

	case step == 9 && button == "buy":

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

	case step == 10:

		time := time.Now().Unix()
		coordinates := Coordinates{
			Latitude:  latitude,
			Longitude: longitude,
		}
		jsonProducts, _ := json.Marshal(products)
		jsonCoordinates, _ := json.Marshal(coordinates)

		// Создаем тело запроса в виде строки JSON
		requestBody := `{"customer_id":` + strconv.Itoa(chatId) + `, "order_date":` + strconv.Itoa(int(time)) + `, "products":` + string(jsonProducts) + `, "location": ` + string(jsonCoordinates) + `}`

		sendPost(requestBody, "http://nginx:80/api/orders/create-with-vendor-calc.php")

		// Создаем объект клавиатуры
		keyboard := map[string]interface{}{
			"keyboard": [][]map[string]interface{}{
				{{"text": "Заказать 🛍"}},

				{{"text": "Актуальный курс 💹"},
					{"text": "Настройки ⚙️"},
				},
				{{"text": "Мои заказы 📕"},
					{"text": "Актуальные цены на рынке 📈"},
				},
				{{"text": "Связаться 📞"},
					{"text": "Корзина 🗑"},
				},
			},
			"resize_keyboard":   true,
			"one_time_keyboard": true,
		}

		// Преобразуем клавиатуру в JSON
		keyboardJSON, _ := json.Marshal(keyboard)
		// Отправляем сообщение с клавиатурой
		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Благодарим Вас за то, что выбрали Стройбот, с вами свяжуться в течении часа&reply_markup=" + string(keyboardJSON))

		products = make(map[int]int)
		step = 5
		break
	}

	if strings.SplitN(button, ":", 2)[0] == "add" {
		productStr := strings.Split(button, ":")[1]
		productID, _ := strconv.Atoi(productStr)
		// products = append(products, productID)
		// fmt.Println(products)
		// Пример добавления товара с id=3 и количеством 2
		quantity := 1

		// Проверяем, есть ли товар с таким id в массиве
		found := false
		for ID := range products {
			if ID == productID {
				// Если товар найден, увеличиваем его количество
				products[ID] += quantity
				found = true
				// Создаем новую инлайн клавиатуру с обновленным числом
				buttons := [][]map[string]interface{}{
					{
						{"text": "➖", "callback_data": "minus:" + strconv.Itoa(ID)},
						{"text": strconv.Itoa(products[ID]), "callback_data": "quantity"},
						{"text": "➕", "callback_data": "add:" + strconv.Itoa(ID)},
					},
					{{"text": "Добавить в корзину 🛒", "callback_data": "add:" + strconv.Itoa(ID)}},
					{{"text": "Перейти в корзину 🗑", "callback_data": "goToCart"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

				http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
				break
			}
		}

		// Если товара с таким id нет в массиве, добавляем его
		if !found {
			products[productID] = quantity
			// Создаем новую инлайн клавиатуру с обновленным числом
			buttons := [][]map[string]interface{}{
				{
					{"text": "➖", "callback_data": "minus:" + productStr},
					{"text": "1", "callback_data": "quantity"},
					{"text": "➕", "callback_data": "add:" + productStr},
				},
				{{"text": "Добавить в корзину 🛒", "callback_data": "add:" + productStr}},
				{{"text": "Перейти в корзину 🗑", "callback_data": "goToCart"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

			http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
		}
		fmt.Println(products)
	}

	if strings.SplitN(button, ":", 2)[0] == "minus" {
		productStr := strings.Split(button, ":")[1]
		productID, _ := strconv.Atoi(productStr)
		// products = append(products, productID)
		// fmt.Println(products)
		// Пример добавления товара с id=3 и количеством 2
		quantity := 1

		for ID := range products {
			if ID == productID {
				// Если товар найден, уменьшаем его количество
				products[ID] -= quantity
				// Создаем новую инлайн клавиатуру с обновленным числом
				buttons := [][]map[string]interface{}{
					{
						{"text": "➖", "callback_data": "minus:" + productStr},
						{"text": strconv.Itoa(products[ID]), "callback_data": quantity},
						{"text": "➕", "callback_data": "add:" + productStr},
					},
					{{"text": "Добавить в корзину 🛒", "callback_data": "add:" + productStr}},
					{{"text": "Перейти в корзину 🗑", "callback_data": "goToCart"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

				http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
				if products[productID] == 0 {
					delete(products, productID)
				}
				break
			}
		}

		// // Если товара с таким id нет в массиве, добавляем его
		// if !found {
		// 	products = append(products, Product{ID: productID, Quantity: quantity})
		// }
		fmt.Println(products)
	}

	if text == "Актуальные цены на рынке 📈" {

		channelURL := "https://t.me/stroyb0t"

		// Получаем текущую дату и время
		currentTime := time.Now()

		// Создаем объект временной зоны GMT+5
		location := time.FixedZone("GMT+5", 5*60*60)

		// Устанавливаем временную зону для текущего времени
		currentTime = currentTime.In(location)

		// Форматируем дату и время в нужном формате
		formattedTime := currentTime.Format("01-02-2006 15:04:05")

		buttons := [][]map[string]interface{}{
			{{"text": "Перейти", "url": channelURL}},
			{{"text": "Назад 🔙", "callback_data": "backToMenu"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Цена на строительные материалы  " + formattedTime + "&reply_markup=" + string(inlineKeyboardJSON))

	}

	if text == "Актуальный курс 💹" {

		channelURL := "https://t.me/stroyb0t2"

		// Получаем текущую дату и время
		currentTime := time.Now()

		// Создаем объект временной зоны GMT+5
		location := time.FixedZone("GMT+5", 5*60*60)

		// Устанавливаем временную зону для текущего времени
		currentTime = currentTime.In(location)

		// Форматируем дату и время в нужном формате
		formattedTime := currentTime.Format("01-02-2006 15:04:05")

		buttons := [][]map[string]interface{}{
			{{"text": "Перейти", "url": channelURL}},
			{{"text": "Назад 🔙", "callback_data": "backToMenu"}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Актуальные курсы валют " + formattedTime + "&reply_markup=" + string(inlineKeyboardJSON))

	}

	if text == "Настройки ⚙️" {
		buttons := [][]map[string]interface{}{
			{{"text": "Изменить номер", "callback_data": "number"},
				{"text": "Изменить город", "callback_data": "city"}},

			{{"text": "Изменить язык", "callback_data": "backToMenu"},
				{"text": "Публичная оферта", "callback_data": "oferta"}},

			{{"text": "Информация", "callback_data": "info"},
				{"text": "Стать партнёром", "callback_data": "partnership"}},

			{{"text": "Обратная связь", "callback_data": "book"}},
		}

		buttons = append(buttons, []map[string]interface{}{
			{
				"text":          "Назад",
				"callback_data": "backToMenu",
			},
		})

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Настройки&reply_markup=" + string(inlineKeyboardJSON))

	}

	if button == "info" {

		buttons := [][]map[string]interface{}{
			{{"text": "Назад 🔙", "callback_data": ""}},
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

		http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Информация о проекте&reply_markup=" + string(inlineKeyboardJSON))

	}

	if button == "city" {
		buttons := [][]map[string]interface{}{}
		// Создаем GET-запрос
		resp, err := http.Get("http://nginx:80/api/cities.php")
		if err != nil {
			log.Fatal("Ошибка при выполнении запроса:", err)
		}
		defer resp.Body.Close()

		var cities []Cities
		err = json.NewDecoder(resp.Body).Decode(&cities)
		if err != nil {
			log.Fatal("Ошибка при декодировании JSON:", err)
		}

		// Используем полученные данные
		for _, category := range cities {
			button := []map[string]interface{}{
				{
					"text":          category.Name,
					"callback_data": category.ID,
				},
			}
			buttons = append(buttons, button)
		}

		inlineKeyboard := map[string]interface{}{
			"inline_keyboard": buttons,
		}

		inlineKeyboardJSON, err := json.Marshal(inlineKeyboard)
		if err != nil {
			log.Fatal("Ошибка при маршалинге данных в формат JSON:", err)
		}

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
