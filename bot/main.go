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
)

// структура для приходящих сообщений и обычных кнопок
type ResponseT struct {
	Ok     bool       `json:"ok"`
	Result []MessageT `json:"result"`
}

type MessageT struct {
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
}

// структура для инлайн кнопок
type ResponseInlineT struct {
	Ok     bool             `json:"ok"`
	Result []MessageInlineT `json:"result"`
}

type MessageInlineT struct {
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
}

// структура пользователя
type UserT struct {
	ID          int         `json:"id"`
	FirstName   string      `json:"first_name"`
	LastName    string      `json:"last_name"`
	Username    string      `json:"tg_username"`
	Step        int         `json:"step"`
	IsProvider  bool        `json:"is_provider"`
	Tg_id       int         `json:"tg_id"`
	PhoneNumber string      `json:"phone"`
	City        int         `json:"city_id"`
	Cart        map[int]int `json:"cart"`
	Category_id string
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
type City struct {
	ID   int    `json:"id"`
	Name string `json:"name"`
}

// структура категорий
type Category struct {
	ID           int    `json:"id"`
	CategoryName string `json:"category_name"`
}

// структура брендов
type Brand struct {
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
var token string = os.Getenv("BOT_TOKEN")

// данные всеx пользователей
var usersDB map[int]UserT

// переменная для запросов к API
var client = http.Client{}

// главная функция работы бота
func main() {

	//достаем юзеров из кэща
	getUsers()

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
		var need ResponseInlineT
		json.Unmarshal(data, &need)

		//считаем количество новых сообщений
		number := len(responseObj.Result)

		//если сообщений нет - то дальше код не выполняем
		if number < 1 {
			continue
		}

		//в цикле доставать инормацию по каждому сообщению
		for i := 0; i < number; i++ {

			//обработка одного сообщения
			go processMessage(responseObj.Result[i], need.Result[i])
		}

		//запоминаем update_id  последнего сообщения
		lastMessage = responseObj.Result[number-1].UpdateID + 1

	}
}

func getUsers() {
	//считываем из бд при включении
	dataFile, _ := ioutil.ReadFile("db.json")
	json.Unmarshal(dataFile, &usersDB)
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
func sendMessage(chatId int, text string, keyboard map[string]interface{}) {
	url := host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=" + text
	if keyboard != nil {
		// Преобразуем клавиатуру в JSON
		keyboardJSON, _ := json.Marshal(keyboard)
		url += "&reply_markup=" + string(keyboardJSON)
	}
	http.Get(url)
}

func processMessage(message MessageT, messageInline MessageInlineT) {

	text := message.Message.Text
	chatId := 0
	if messageInline.CallbackQuery.From.ID == 0 {
		chatId = message.Message.From.ID
	} else {
		chatId = messageInline.CallbackQuery.From.ID
	}

	firstName := message.Message.From.FirstName
	lastName := message.Message.From.LastName
	phone := message.Message.Contact.PhoneNumber
	latitude := message.Message.Location.Latitude
	longitude := message.Message.Location.Longitude
	username := message.Message.From.Username
	button := messageInline.CallbackQuery.Data
	id := messageInline.CallbackQuery.From.ID
	mesIdInline := messageInline.CallbackQuery.Message.MessageID

	isProvider := false

	// Проверяем, есть ли параметр после "/start"
	if strings.HasPrefix(text, "/start ") {
		// Извлекаем значение параметра
		paramValue := strings.TrimPrefix(text, "/start ")

		// Проверяем значение параметра
		if strings.Contains(paramValue, "provider") {

			isProvider = true

		}
	}

	//есть ли юзер
	_, exist := usersDB[chatId]
	if !exist {
		user := UserT{}
		user.ID = chatId
		user.FirstName = firstName
		user.LastName = lastName
		user.Username = username
		user.Tg_id = chatId
		user.PhoneNumber = phone
		user.City, _ = strconv.Atoi(button)
		user.Cart = make(map[int]int)

		user.IsProvider = isProvider
		user.Step = 1

		usersDB[chatId] = user

	}

	file, _ := os.Create("db.json")
	jsonString, _ := json.Marshal(usersDB)
	file.Write(jsonString)

	if usersDB[chatId].IsProvider {

		switch {

		case usersDB[chatId].Step == 1:
			sendMessage(chatId, "Здравствуйте, отправьте местоположение склада, выбрав его на карте", nil)
			user := usersDB[chatId]
			user.Step += 1
			usersDB[chatId] = user

		case usersDB[chatId].Step == 2:
			sendMessage(chatId, "Локация вашего склада записана", nil)
			user := usersDB[chatId]
			user.Step = 1
			usersDB[chatId] = user

		}

	} else {
		switch {
		// кейс для начального сообщения для пользователя
		case text == "/start" || usersDB[chatId].Step == 1:

			user := usersDB[chatId]
			user.Step = 1
			usersDB[chatId] = user

			//собираем объект клавиатуры для выбора языка
			buttons := [][]map[string]interface{}{
				{{"text": "Русский 🇷🇺", "callback_data": "russian"}},
				{{"text": "O'zbekcha 🇺🇿", "callback_data": "uzbekistan"}},
				{{"text": "Ўзбекча 🇺🇿", "callback_data": "usbecha"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Здравствуйте, добро пожаловать в Стройбот. Выберите язык", inlineKeyboard)

			//следующий шаг
			user.Step += 1
			usersDB[chatId] = user
			break

		// кейс для получения номера телефона
		case usersDB[chatId].Step == 2 || button == "backToPhone":

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

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Поделится номером телефона", keyboard)
			user := usersDB[chatId]
			user.Step += 1
			usersDB[chatId] = user
			break

		// кейс для обработки отказа от отправки телефона
		case usersDB[chatId].Step == 3 && text == "Нет":

			// создаём объект клавиатуры
			buttons := [][]map[string]interface{}{
				{{"text": "Назад 🔙", "callback_data": "backToPhone"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "К сожалению вы не сможете пройти дальше, если не укажите номер телефона", inlineKeyboard)
			user := usersDB[chatId]
			user.Step -= 1
			usersDB[chatId] = user
			break

		// кейс для вывода городов для выбора
		case usersDB[chatId].Step == 3:

			user := usersDB[chatId]
			user.PhoneNumber = phone
			user.Username = username
			usersDB[chatId] = user

			buttons := [][]map[string]interface{}{}
			// Создаем GET-запрос
			resp, err := http.Get("http://nginx:80/api/cities.php?deleted=0")
			if err != nil {
				log.Fatal("Ошибка при выполнении запроса:", err)
			}
			defer resp.Body.Close()

			var cities []City
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

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Выберите свой город", inlineKeyboard)
			user.Step += 1
			usersDB[chatId] = user
			break

		// кейс для вывода меню пользователю и запись или обновление пользователя в бд
		case usersDB[chatId].Step == 4:

			// формируем json и отправляем данные пользователя на бэк
			requestBody := `{"first_name":"` + usersDB[chatId].FirstName + `", "last_name":"` + usersDB[chatId].LastName + `", "phone":"` + usersDB[chatId].PhoneNumber + `", "city_id":` + button + `, "tg_username":"` + usersDB[chatId].Username + `", "tg_id":` + strconv.Itoa(chatId) + `}`
			fmt.Println(requestBody)

			sendPost(requestBody, "http://nginx:80/api/customers.php")

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

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Главное меню", keyboard)
			user := usersDB[chatId]
			user.Step += 1
			usersDB[chatId] = user
			break

		// кейс для возращения пользователя в меню
		case button == "backToMenu":
			user := usersDB[chatId]
			user.Step = 4

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

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Главное меню", keyboard)
			user.Step += 1
			usersDB[chatId] = user
			break

		// кейс для вывода категорий товаров на выбор
		case usersDB[chatId].Step == 5 && text == "Заказать 🛍" || button == "backToGoods":

			user := usersDB[chatId]
			user.Step = 5

			buttons := [][]map[string]interface{}{}
			// Создаем GET-запрос
			resp, err := http.Get("http://nginx:80/api/categories.php?deleted=0")
			if err != nil {
				log.Fatal("Ошибка при выполнении запроса:", err)
			}
			defer resp.Body.Close()

			var categories []Category
			err = json.NewDecoder(resp.Body).Decode(&categories)
			if err != nil {
				log.Fatal("Ошибка при декодировании JSON:", err)
			}

			// Используем полученные данные и подставляем их в кнопки
			for _, category := range categories {
				button := []map[string]interface{}{
					{
						"text":          category.CategoryName,
						"callback_data": category.ID,
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

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Выберите материал", inlineKeyboard)
			user.Step += 1
			usersDB[chatId] = user
			break

		// кейс для вывода брендов товаров для пользователя
		case usersDB[chatId].Step == 6:

			user := usersDB[chatId]
			user.Step = 6
			user.Category_id = button
			buttons := [][]map[string]interface{}{}
			// Создаем GET-запрос
			resp, err := http.Get("http://nginx:80/api/brands.php?deleted=0")
			if err != nil {
				log.Fatal("Ошибка при выполнении запроса:", err)
			}
			defer resp.Body.Close()

			var brands []Brand
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

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Выберите бренд", inlineKeyboard)
			user.Step += 1
			usersDB[chatId] = user
			break

		// кейс для отображения выбранных товаров по фильтрам
		case usersDB[chatId].Step == 7:

			user := usersDB[chatId]

			// Создаем GET-запрос
			resp, err := http.Get("http://nginx:80/api/products.php?deleted=0&category_id=" + usersDB[chatId].Category_id + "&brand_id=" + button)
			if err != nil {
				log.Fatal("Ошибка при выполнении запроса:", err)
			}
			defer resp.Body.Close()

			var product []Product
			err = json.NewDecoder(resp.Body).Decode(&product)
			if err != nil {

				buttons := [][]map[string]interface{}{
					{{"text": "Назад", "callback_data": "backToGoods"}},
				}

				// Создаем объект инлайн клавиатуры
				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, "Товаров по вашему запросу нет", inlineKeyboard)
				user.Step = 5
				usersDB[chatId] = user
				break
			}

			// Используем полученные данные
			for _, product := range product {
				// Создаем объект инлайн клавиатуры
				buttons := [][]map[string]interface{}{
					{
						{"text": "➖ 10", "callback_data": "minus:" + strconv.Itoa(product.ID)},
						{"text": "0", "callback_data": "quantity"},
						{"text": "➕ 10", "callback_data": "add:" + strconv.Itoa(product.ID)},
					},
					{{"text": "Добавить в корзину 🛒", "callback_data": "addone:" + strconv.Itoa(product.ID)}},
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

			// перезаписываем шаг
			user.Step += 1
			usersDB[chatId] = user
			break

		// кейс для отображения корзины покупателя
		case usersDB[chatId].Step == 8 && button == "goToCart":

			user := usersDB[chatId]
			finalPrice := 0
			benefit := 0
			marketPrice := 0
			cartText := ""
			for ID := range usersDB[chatId].Cart {

				fmt.Println(ID)
				// Создаем GET-запрос
				resp, err := http.Get("http://nginx:80/api/products.php?deleted=0&id=" + strconv.Itoa(ID))
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

				cartText += product.Name + "\n" + strconv.Itoa(usersDB[chatId].Cart[ID]) + " ✖️ " + strconv.Itoa(product.Price) + "сум/шт = " + strconv.Itoa(usersDB[chatId].Cart[ID]*product.Price) + " сум\n"
				finalPrice += product.Price * usersDB[chatId].Cart[ID]
				marketPrice += product.MaxPrice * usersDB[chatId].Cart[ID]
				benefit += product.MaxPrice*usersDB[chatId].Cart[ID] - product.Price*usersDB[chatId].Cart[ID]

			}

			buttons := [][]map[string]interface{}{
				{{"text": "Оформить заказ", "callback_data": "buy"}},
				{{"text": "Назад", "callback_data": "backToGoods"}},
			}

			// Создаем объект инлайн клавиатуры
			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			encodedCartText := url.QueryEscape(cartText)
			encodedText := url.QueryEscape("Итого средняя цена на рынке\n<s>"+strconv.Itoa(marketPrice)+"</s> cум\nИтого цена бота \n"+strconv.Itoa(finalPrice)+" сум\nВы сэкономили\n<b>"+strconv.Itoa(benefit)) + "</b> сум&parse_mode=HTML"
			finalText := encodedCartText + encodedText

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, finalText, inlineKeyboard)
			user.Step += 1
			usersDB[chatId] = user
			break

		// кейс для покупки выбранных товаров пользователем
		case usersDB[chatId].Step == 9 && button == "buy":
			buttons := [][]map[string]interface{}{
				{{"text": "Заказать на свой адрес", "callback_data": "myAdress"}},
				{{"text": "Заказать на другой адрес", "callback_data": "anotherAdress"}},
			}

			// Создаем объект инлайн клавиатуры
			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Укажите удобный для Вас адрес", inlineKeyboard)
			user := usersDB[chatId]
			user.Step += 1
			usersDB[chatId] = user
			break

		// кейс при нажатии на указание своего адреса
		case usersDB[chatId].Step == 10 && button == "myAdress":

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

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Поделится местоположением?", keyboard)
			user := usersDB[chatId]
			user.Step += 1
			usersDB[chatId] = user
			break

		// кейс при нажатии на указание другого адреса
		case usersDB[chatId].Step == 10 && button == "anotherAdress":
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

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Поделится местоположением?", keyboard)
			user := usersDB[chatId]
			user.Step += 1
			usersDB[chatId] = user
			break

		// кейс для вывода сообщения о заказе и его отправка на бекенд
		case usersDB[chatId].Step == 11:

			time := time.Now().Unix()
			coordinates := Coordinates{
				Latitude:  latitude,
				Longitude: longitude,
			}
			jsonProducts, _ := json.Marshal(usersDB[chatId].Cart)
			jsonCoordinates, _ := json.Marshal(coordinates)

			// Создаем GET-запрос
			resp, err := http.Get("http://nginx:80/api/customers.php?tg_id=" + strconv.Itoa(chatId))
			if err != nil {
				log.Fatal("Ошибка при выполнении запроса:", err)
			}
			defer resp.Body.Close()

			var userInfo []UserT
			err = json.NewDecoder(resp.Body).Decode(&userInfo)
			if err != nil {
				log.Fatal("Ошибка при декодировании JSON:", err)
			}

			// Используем полученные данные
			for _, user := range userInfo {
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

			user := usersDB[chatId]
			// обнуляем корзину
			user = UserT{Cart: map[int]int{}}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Благодарим Вас за то, что выбрали Стройбот, с вами свяжуться в течении часа", keyboard)
			user.Step = 5
			usersDB[chatId] = user
			break
		}

		// кейс при нажатии на + в карточке товара
		if strings.SplitN(button, ":", 2)[0] == "addone" {
			user := usersDB[chatId]
			productStr := strings.Split(button, ":")[1]
			productID, _ := strconv.Atoi(productStr)
			quantity := 1

			// Проверяем, есть ли товар с таким id в массиве
			found := false
			for ID := range user.Cart {
				if ID == productID {
					// Если товар найден, увеличиваем его количество
					user.Cart[ID] += quantity
					usersDB[chatId] = user
					found = true
					// Создаем новую инлайн клавиатуру с обновленным числом
					buttons := [][]map[string]interface{}{
						{
							{"text": "➖ 10", "callback_data": "minus:" + strconv.Itoa(ID)},
							{"text": strconv.Itoa(usersDB[chatId].Cart[ID]), "callback_data": "quantity"},
							{"text": "➕ 10", "callback_data": "add:" + strconv.Itoa(ID)},
						},
						{{"text": "Добавить в корзину 🛒", "callback_data": "addone:" + strconv.Itoa(ID)}},
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

			// Если товара с таким id нет в карте, добавляем его
			if !found {
				user := usersDB[chatId]
				// Проверяем, инициализирована ли карта `Cart`
				if usersDB[chatId].Cart == nil {
					user.Cart = make(map[int]int)
				}

				user.Cart[productID] = quantity
				usersDB[chatId] = user

				// Создаем новую инлайн клавиатуру с обновленным числом
				buttons := [][]map[string]interface{}{
					{
						{"text": "➖ 10", "callback_data": "minus:" + productStr},
						{"text": "1", "callback_data": "quantity"},
						{"text": "➕ 10", "callback_data": "add:" + productStr},
					},
					{{"text": "Добавить в корзину 🛒", "callback_data": "addone:" + productStr}},
					{{"text": "Перейти в корзину 🗑", "callback_data": "goToCart"}},
				}

				// Создаем объект клавиатуры
				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Кодируем клавиатуру в JSON
				inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

				http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
			}

		}

		// кейс при нажатии на + в карточке товара
		if strings.SplitN(button, ":", 2)[0] == "add" {
			user := usersDB[chatId]
			productStr := strings.Split(button, ":")[1]
			productID, _ := strconv.Atoi(productStr)
			quantity := 10

			// Проверяем, есть ли товар с таким id в массиве
			found := false
			for ID := range user.Cart {
				if ID == productID {
					// Если товар найден, увеличиваем его количество
					user.Cart[ID] += quantity
					usersDB[chatId] = user
					found = true
					// Создаем новую инлайн клавиатуру с обновленным числом
					buttons := [][]map[string]interface{}{
						{
							{"text": "➖ 10", "callback_data": "minus:" + strconv.Itoa(ID)},
							{"text": strconv.Itoa(usersDB[chatId].Cart[ID]), "callback_data": "quantity"},
							{"text": "➕ 10", "callback_data": "add:" + strconv.Itoa(ID)},
						},
						{{"text": "Добавить в корзину 🛒", "callback_data": "addone:" + strconv.Itoa(ID)}},
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

			// Если товара с таким id нет в карте, добавляем его
			if !found {
				user := usersDB[chatId]
				// Проверяем, инициализирована ли карта `Cart`
				if usersDB[chatId].Cart == nil {
					user.Cart = make(map[int]int)
				}

				user.Cart[productID] = quantity
				usersDB[chatId] = user

				// Создаем новую инлайн клавиатуру с обновленным числом
				buttons := [][]map[string]interface{}{
					{
						{"text": "➖ 10", "callback_data": "minus:" + productStr},
						{"text": "10", "callback_data": "quantity"},
						{"text": "➕ 10", "callback_data": "add:" + productStr},
					},
					{{"text": "Добавить в корзину 🛒", "callback_data": "addone:" + productStr}},
					{{"text": "Перейти в корзину 🗑", "callback_data": "goToCart"}},
				}

				// Создаем объект клавиатуры
				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Кодируем клавиатуру в JSON
				inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

				http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
			}

		}

		// кейс для - в карточке товаров
		if strings.SplitN(button, ":", 2)[0] == "minus" {
			user := usersDB[chatId]
			productStr := strings.Split(button, ":")[1]
			productID, _ := strconv.Atoi(productStr)
			quantity := 10

			for ID := range usersDB[chatId].Cart {
				if ID == productID {
					// Если товар найден, уменьшаем его количество
					if user.Cart[ID] <= quantity {
						user.Cart[ID] = 0
					} else {
						user.Cart[ID] -= quantity
					}
					usersDB[chatId] = user
					// Создаем новую инлайн клавиатуру с обновленным числом
					buttons := [][]map[string]interface{}{
						{
							{"text": "➖ 10", "callback_data": "minus:" + productStr},
							{"text": strconv.Itoa(usersDB[chatId].Cart[ID]), "callback_data": quantity},
							{"text": "➕ 10", "callback_data": "add:" + productStr},
						},
						{{"text": "Добавить в корзину 🛒", "callback_data": "addone:" + productStr}},
						{{"text": "Перейти в корзину 🗑", "callback_data": "goToCart"}},
					}

					inlineKeyboard := map[string]interface{}{
						"inline_keyboard": buttons,
					}

					inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

					http.Get(host + token + "/editMessageReplyMarkup?chat_id=" + strconv.Itoa(id) + "&message_id=" + strconv.Itoa(mesIdInline) + "&reply_markup=" + string(inlineKeyboardJSON))
					if usersDB[chatId].Cart[productID] == 0 {
						delete(usersDB[chatId].Cart, productID)
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

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Цена на строительные материалы "+formattedTime, inlineKeyboard)
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

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Актуальные курсы валют "+formattedTime, inlineKeyboard)
		}

		// кейс при нажатии на кнопку мои заказы
		if text == "Мои заказы 📕" {

			buttons := [][]map[string]interface{}{
				{{"text": "Назад 🔙", "callback_data": "backToMenu"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Мои заказы", inlineKeyboard)
		}

		// кейс при нажатии на кнопку связаться
		if text == "Связаться 📞" {

			buttons := [][]map[string]interface{}{
				{{"text": "Назад 🔙", "callback_data": "backToMenu"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Связаться", inlineKeyboard)
		}

		// кейс при нажатии на кнопку мои заказы
		if text == "Корзина 🗑" {

			buttons := [][]map[string]interface{}{
				{{"text": "Назад 🔙", "callback_data": "backToMenu"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Корзина", inlineKeyboard)
		}

		// кейс при нажатии на кнопку настройки
		if text == "Настройки ⚙️" || button == "backToSettings" {
			buttons := [][]map[string]interface{}{
				{{"text": "Изменить номер", "callback_data": "number"},
					{"text": "Изменить город", "callback_data": "city"}},

				{{"text": "Изменить язык", "callback_data": "language"},
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

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Настройки", inlineKeyboard)
		}

		// кейс при нажатии на кнопку справка
		if button == "info" {

			buttons := [][]map[string]interface{}{
				{{"text": "Назад 🔙", "callback_data": "backToSettings"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Информация о проекте", inlineKeyboard)
		}

		// кейс при нажатии на кнопку партнёр
		if button == "partnership" {

			buttons := [][]map[string]interface{}{
				{{"text": "Назад 🔙", "callback_data": "backToSettings"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Стать партнёром", inlineKeyboard)
		}

		// кейс при нажатии на кнопку обратная связь
		if button == "book" {

			buttons := [][]map[string]interface{}{
				{{"text": "Назад 🔙", "callback_data": "backToSettings"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Обратная связь", inlineKeyboard)
		}

		// кейс при нажатии на кнопку оферта
		if button == "oferta" {

			buttons := [][]map[string]interface{}{
				{{"text": "Назад 🔙", "callback_data": "backToSettings"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Оферта", inlineKeyboard)
		}

		// кейс при нажатии на кнопку язык
		if button == "language" {

			buttons := [][]map[string]interface{}{
				{{"text": "Назад 🔙", "callback_data": "backToSettings"}},
			}

			inlineKeyboard := map[string]interface{}{
				"inline_keyboard": buttons,
			}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Язык", inlineKeyboard)
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

			var cities []City
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

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Выберите свой город", inlineKeyboard)
			user := usersDB[chatId]
			user.Step = 4
			usersDB[chatId] = user
		}

		// кейс при нажатии на кнопку изменить город
		if button == "number" {
			// Создаем объект клавиатуры
			keyboard := map[string]interface{}{
				"keyboard": [][]map[string]interface{}{
					{
						{
							"text":            "Да",
							"request_contact": true,
						},
					},
				},
				"resize_keyboard":   true,
				"one_time_keyboard": true,
			}

			// Отправляем сообщение с клавиатурой и перезаписываем шаг
			sendMessage(chatId, "Поделится номером телефона", keyboard)

			user := usersDB[chatId]
			user.Step = 4
			usersDB[chatId] = user
		}

	}
}
