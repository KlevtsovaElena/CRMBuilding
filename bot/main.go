package main

//подключение требуемых пакетов
import (
	"bytes"
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

	"github.com/joho/godotenv"
)

// структура для приходящих сообщений и обычных кнопок
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

// структура для инлайн кнопок
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

// структура пользователя
type UserT struct {
	ID          int    `json:"id"`
	FirstName   string `json:"first_name"`
	LastName    string `json:"last_name"`
	Username    string `json:"tg_username"`
	Tg_id       int    `json:"tg_id"`
	PhoneNumber string `json:"phone"`
	City        int    `json:"city_id"`
}

// структура заказа
type Order struct {
	CustomerID  int                    `json:"customer_id"`
	OrderDate   int64                  `json:"order_date"`
	Products    map[int]int            `json:"products"`
	Coordinates map[string]interface{} `json:"coordinates"`
}

// структура корзины в заказе
type OrderItem struct {
	ProductID int
	Quantity  int
}

// структура геопозиции
type Coordinates struct {
	Latitude  float64 `json:"latitude"`
	Longitude float64 `json:"longitude"`
}

// структура городов
type Cities struct {
	ID   int    `json:"id"`
	Name string `json:"name"`
}

// структура категорий
type Categories struct {
	ID           int    `json:"id"`
	CategoryName string `json:"category_name"`
}

// структура брендов
type Brands struct {
	ID        int    `json:"id"`
	BrandName string `json:"brand_name"`
}

// структура товара
type Product struct {
	ID          int    `json:"id"`
	Name        string `json:"name"`
	Description string `json:"description"`
	Photo       string `json:"photo"`
	Price       int    `json:"price"`
	MaxPrice    int    `json:"max_price"`
}

// переменные для подключения к боту
var host string = "https://api.telegram.org/bot"
var token string = ""

var tel string
var FirstName string
var LastName string

// карты для определения шага в боте (слежка за шагом пользователя или шагом поставщика)
var providerStep = make(map[int]int)
var userSteps = make(map[int]int)

// карта корзины
var products = make(map[int]int)

// переменная для запросов к API
var client = http.Client{}

// главная функция работы бота
func main() {

	// достаём токен бота из .env файла
	err := godotenv.Load()
	if err != nil {
		log.Fatal("Error loading .env file")
	}
	token = os.Getenv("BOT_TOKEN")

	//обнуление последнего id сообщения
	lastMessage := 0

	//цикл для проверки на наличие новых сообщений
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

		//парсим данные из json
		var responseObj ResponseT
		json.Unmarshal(data, &responseObj)

		//парсим данные из json  (для нажатия на инлайн кнопку)
		var need InlineButton
		json.Unmarshal(data, &need)

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

// функция для отправки POST запроса
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

// функция для отправки сообщения пользователю
func sendMessage(chatId int, id int, mesIdInline int, mesIdRepl int, messageTime int, text string, button string, phone string, firstName string, lastName string, username string, latitude float64, longitude float64) {

	fmt.Println(text)

	// Проверяем, есть ли параметр после "/start"
	if strings.HasPrefix(text, "/start ") {
		// Извлекаем значение параметра
		paramValue := strings.TrimPrefix(text, "/start ")

		// Проверяем значение параметра
		if strings.Contains(paramValue, "provider") {

			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Здравствуйте, отправьте местоположение склада, выбрав его на карте")
			providerStep[chatId] += 1
		}

	} else {

		switch {

		// кейс для начального сообщения для пользователя
		case text == "/start":

			userSteps[chatId] = 1

			//собираем объект клавиатуры для выбора языка
			buttons := [][]map[string]interface{}{
				{{"text": "Русский 🇷🇺", "callback_data": "russian"}},
				{{"text": "O'zbekcha 🇺🇿", "callback_data": "uzbekistan"}},
				{{"text": "Ўзбекча 🇺🇿", "callback_data": "usbecha"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			//кодирование клавиатуры в json для отправки
			inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

			// http.Get(host + token + "/deleteMessage?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesId))
			//отправка сообщения
			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Здравствуйте, добро пожаловать в Стройбот. Выберите язык&reply_markup=" + string(inlineKeyboardJSON))

			//следующий шаг
			userSteps[chatId] += 1
			break

		// кейс после отправки локации для поставщика
		case providerStep[chatId] == 2:
			fmt.Println(longitude, latitude)
			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Локация вашего склада записана")
			providerStep[chatId] = 1

		// кейс для получения номера телефона
		case userSteps[id] == 2:

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

			// следующий шаг
			userSteps[id] += 1
			break

		// кейс для обработки отказа от отправки телефона
		case userSteps[chatId] == 3 && text == "Нет":

			// создаём объект клавиатуры
			buttons := [][]map[string]interface{}{
				{{"text": "Назад 🔙", "callback_data": "backToPhone"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// кодируем клавиатуру в json
			inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=К сожалению вы не сможете пройти дальше, если не укажите номер телефона&reply_markup=" + string(inlineKeyboardJSON))

			// уходим на предыдущий шаг
			userSteps[chatId] -= 1
			break

		// кейс для вывода городов для выбора
		case userSteps[chatId] == 3:

			fmt.Println(userSteps[chatId])

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

			// Используем полученные данные и подставляем их в кнопки
			for _, city := range cities {
				button := []map[string]interface{}{
					{
						"text":          city.Name,
						"callback_data": city.ID,
					},
				}
				buttons = append(buttons, button)
			}

			// создаём объект клавиатуры
			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// кодируем клавиатуру в json
			inlineKeyboardJSON, err := json.Marshal(inlineKeyboard)
			if err != nil {
				log.Fatal("Ошибка при маршалинге данных в формат JSON:", err)
			}

			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Выберите свой город&reply_markup=" + string(inlineKeyboardJSON))

			// следующий шаг
			userSteps[chatId] += 1
			break

		// кейс для вывода меню пользователю и запись или обновление пользователя в бд
		case userSteps[id] == 4:

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
				user.Tg_id = id
				user.PhoneNumber = tel
				user.City, _ = strconv.Atoi(button)
				// Создаем тело запроса в виде строки JSON
				requestBody := `{"first_name":"` + FirstName + `", "last_name":"` + LastName + `", "phone":"` + tel + `", "city_id":` + button + `, "tg_username":"` + username + `", "tg_id":` + strconv.Itoa(id) + `}`
				fmt.Println(requestBody)

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

			userSteps[id] += 1
			break

		// кейс для возращения пользователя в меню
		case button == "backToMenu":
			userSteps[id] = 4
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
			userSteps[id] += 1
			break

		// кейс для вывода категорий товаров на выбор
		case userSteps[chatId] == 5 && text == "Заказать 🛍":
			userSteps[chatId] = 5
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

			// Используем полученные данные и подставляем их в кнопки
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

			// создаём объект клавиатуры
			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// кодируем клавиатуру в json
			inlineKeyboardJSON, err := json.Marshal(inlineKeyboard)
			if err != nil {
				log.Fatal("Ошибка при маршалинге данных в формат JSON:", err)
			}

			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=Выберите материал&reply_markup=" + string(inlineKeyboardJSON))

			// следующий шаг
			userSteps[chatId] += 1
			break

		// кейс для возращения к категориям товаров
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

			// Используем полученные данные и подставляем их в кнопки
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

			// создаём объект клавиатуры
			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// кодируем клавиатуру в json
			inlineKeyboardJSON, err := json.Marshal(inlineKeyboard)
			if err != nil {
				log.Fatal("Ошибка при маршалинге данных в формат JSON:", err)
			}

			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Выберите материал&reply_markup=" + string(inlineKeyboardJSON))

			// следующий шаг
			userSteps[id] = 6
			break

		// кейс для вывода брендов товаров для пользователя
		case userSteps[id] == 6:

			userSteps[id] = 6
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

			// Используем полученные данные и подставляем их в кнопки
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

			//создаём объект клавиатуры
			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			//кодируем клавиатуру в json
			inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Бренд&reply_markup=" + string(inlineKeyboardJSON))

			// следующий шаг
			userSteps[id] += 1
			break

		// кейс для отображения выбранных товаров по фильтрам
		case userSteps[id] == 7:
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

				// создаём объект клавиатуры
				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// кодируем клавиатуру в json
				inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

				fmt.Println(product.Photo)

				//создание запроса
				caption := url.QueryEscape("<b><u>" + product.Name + "</u></b>\n" + "Цена среднерыночная \n<b>" + strconv.Itoa(product.MaxPrice) + " сум</b>\nЦена Стройбота \n<b>" + strconv.Itoa(product.Price) + " сум</b>")
				apiURL := "https://api.telegram.org/bot" + token + "/sendPhoto?chat_id=" + strconv.Itoa(id) + "&caption=" + caption + "&photo=" + product.Photo + "&parse_mode=HTML&reply_markup=" + string(inlineKeyboardJSON)
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

			// следующий шаг
			userSteps[id] += 1
			break

		// кейс для отображения корзины покупателя
		case userSteps[id] == 8 && button == "goToCart":
			finalPrice := 0
			benefit := 0
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

				cartText += product.Name + "\n" + strconv.Itoa(products[ID]) + " ✖️ " + strconv.Itoa(product.Price) + "сум/шт = " + strconv.Itoa(products[ID]*product.Price) + " сум\n"
				finalPrice += product.Price * products[ID]
				benefit += product.MaxPrice*products[ID] - product.Price*products[ID]

			}

			buttons := [][]map[string]interface{}{
				{{"text": "Оформить заказ", "callback_data": "buy"}},
				{{"text": "Назад", "callback_data": "backToGoods"}},
			}

			// Создаем объект инлайн клавиатуры
			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// кодируем клавиатуру в json
			inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

			encodedCartText := url.QueryEscape(cartText)
			encodedText := url.QueryEscape("\nИтого цена бота \n"+strconv.Itoa(finalPrice)+" сум\nВы сэкономили\n<b>"+strconv.Itoa(benefit)) + " сум"
			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=" + encodedCartText + encodedText + "</b>&parse_mode=HTML&reply_markup=" + string(inlineKeyboardJSON))

			// следующий шаг
			userSteps[id] += 1
			break

		// кейс для покупки выбранных товаров пользователем
		case userSteps[id] == 9 && button == "buy":
			buttons := [][]map[string]interface{}{
				{{"text": "Заказать на свой адрес", "callback_data": "myAdress"}},
				{{"text": "Заказать на другой адрес", "callback_data": "anotherAdress"}},
			}

			// Создаем объект инлайн клавиатуры
			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// кодируем клавиатуру в json
			inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Укажите удобный для Вас адрес&reply_markup=" + string(inlineKeyboardJSON))

			// следующий шаг
			userSteps[id] += 1
			break

		// кейс при нажатии на указание своего адреса
		case userSteps[id] == 10 && button == "myAdress":

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

			// следующий шаг
			userSteps[id] += 1
			break

		// кейс при нажатии на указание другого адреса
		case userSteps[id] == 10 && button == "anotherAdress":
			// Создаем объект клавиатуры
			keyboard := map[string]interface{}{
				"keyboard": [][]map[string]interface{}{
					{
						{
							"text": "Отказаться",
						},
					},
				},
				"resize_keyboard":   true,
				"one_time_keyboard": true,
			}

			// Преобразуем клавиатуру в JSON
			keyboardJSON, _ := json.Marshal(keyboard)
			// Отправляем сообщение с клавиатурой
			http.Get(host + token + "/sendMessage?chat_id=" + strconv.Itoa(id) + "&text=Отправьте другой адрес?&reply_markup=" + string(keyboardJSON))

			// следующий шаг
			userSteps[id] += 1
			break

		// кейс для вывода сообщения о заказе и его отправка на бекенд
		case userSteps[chatId] == 11:

			time := time.Now().Unix()
			coordinates := Coordinates{
				Latitude:  latitude,
				Longitude: longitude,
			}
			jsonProducts, _ := json.Marshal(products)
			jsonCoordinates, _ := json.Marshal(coordinates)

			// Создаем GET-запрос
			resp, err := http.Get("http://nginx:80/api/customers.php?tg_id=" + strconv.Itoa(chatId))
			if err != nil {
				log.Fatal("Ошибка при выполнении запроса:", err)
			}
			defer resp.Body.Close()

			var user []UserT
			err = json.NewDecoder(resp.Body).Decode(&user)
			if err != nil {
				log.Fatal("Ошибка при декодировании JSON:", err)
			}

			// Используем полученные данные
			for _, user := range user {
				// Создаем тело запроса в виде строки JSON
				requestBody := `{"customer_id":` + strconv.Itoa(user.ID) + `, "order_date":` + strconv.Itoa(int(time)) + `, "products":` + string(jsonProducts) + `, "location": ` + string(jsonCoordinates) + `}`

				fmt.Println(requestBody)
				sendPost(requestBody, "http://nginx:80/api/orders/create-with-vendor-calc.php")
			}

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

			// обнуляем корзину
			products = make(map[int]int)

			// следующий шаг
			userSteps[chatId] = 5
			break
		}

		// кейс при нажатии на + в карточке товара
		if strings.SplitN(button, ":", 2)[0] == "add" {
			productStr := strings.Split(button, ":")[1]
			productID, _ := strconv.Atoi(productStr)
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

					// создаём объект клавиатуры
					inlineKeyboard := map[string]interface{}{
						"inline_keyboard": buttons,
					}

					// кодируем клавиатуру в json
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

				//создаём объект клавиатуры
				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// кодируем клавиатуру в json
				inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

				http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
			}
		}

		// кейс для - в карточке товаров
		if strings.SplitN(button, ":", 2)[0] == "minus" {
			productStr := strings.Split(button, ":")[1]
			productID, _ := strconv.Atoi(productStr)
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
		}

		// кейс при нажатии на кнопку актуальные цены
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

		// кейс при нажатии на кнопку актуальный курс
		if text == "Актуальный курс 💹" {

			channelURL := "https://t.me/stroybotchannel2"

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

		// кейс при нажатии на кнопку настройки
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

		// кейс при нажатии на кнопку справка
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

		// кейс при нажатии на кнопку изменить город
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

			userSteps[id] = 4
		}

		// кейс при нажатии на кнопку мои заказы
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

		// кейс при нажатии на кнопку связаться
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
}
