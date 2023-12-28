package main

//подключение требуемых пакетов
import (
	"bytes"
	"crypto/md5"
	"encoding/hex"
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
	Category_id string      `json:"category_id"`
	Hash        string      `json:"hash_string"`
	Vendor_id   int         `json:"vendor_ids"`
	Language    string      `json:"language"`
	Blocked     int         `json:"is_blocked"`
}

// структура для деталей о юзере
type UserDetails struct {
	ID         int    `json:"id"`
	FirstName  string `json:"first_name"`
	LastName   string `json:"last_name"`
	Phone      string `json:"phone"`
	CityID     int    `json:"city_id"`
	CityName   string `json:"city_name"`
	TgID       int    `json:"tg_id"`
	TgUsername string `json:"tg_username"`
}

// структура ответа от сервера
type ServerResponce struct {
	OK      bool   `json:"ok"`
	PayLoad int    `json:"payLoad"`
	Error   string `json:"error"`
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

type Settings struct {
	ID    int    `json:"id"`
	Name  string `json:"name"`
	Value string `json:"value"`
}

// структура товара
type Product struct {
	ID          int    `json:"id"`
	Name        string `json:"name_language"`
	Description string `json:"description_language"`
	Photo       string `json:"photo"`
	Price       int    `json:"price"`
	MaxPrice    int    `json:"max_price"`
	CityName    string `json:"city_name"`
}

// словарь с переводом на разные языки
var languages = map[string]map[string]string{
	"ru": {
		"change_number":                          "Изменить номер",
		"change_city":                            "Изменить город",
		"change_language":                        "Изменить язык",
		"public_offer":                           "Публичная офферта",
		"information":                            "Информация",
		"become_partner":                         "Стать партнёром",
		"back":                                   "Назад",
		"main_menu":                              "Главное меню",
		"order":                                  "Заказать",
		"my_orders":                              "Мои заказы",
		"settings":                               "Настройки",
		"contact":                                "Связаться",
		"cart":                                   "Корзина",
		"current_prices":                         "Актуальные Цены на строительные материалы",
		"current_exchange_rate":                  "Актуальный курс валют",
		"order_to_your_address":                  "Заказать на свой адрес",
		"order_to_another_address":               "Заказать на другой адрес",
		"specify_convenient_address":             "Укажите удобный для Вас адрес",
		"share_location":                         "Поделится местоположением?",
		"decline":                                "Отказаться",
		"send_required_geoposition_via_telegram": "Отправьте нужную геопозицию через телеграмм",
		"your_city":                              "Ваш город",
		"product_location_city":                  "Город, в котором находится товар",
		"cant_order_these_products":              "Вы не сможете заказать эти товары, выберите другие",
		"thank_you_for_choosing_stroybot":        "Благодарим Вас за то, что выбрали Стройбот, с вами свяжутся в течении часа",
		"confirm_order":                          "Оформить заказ",
		"empty_cart":                             "Корзина пуста",
		"average_market_price":                   "Итого средняя цена на рынке",
		"bot_total_price":                        "Итого цена бота",
		"you_saved":                              "Вы сэкономили",
		"yes":                                    "Да",
		"no":                                     "Нет",
		"share_phone_number":                     "Поделится номером телефона",
		"unable_to_continue_without_phone":       "К сожалению вы не сможете пройти дальше, если не укажите номер телефона",
		"choose_your_city":                       "Выберите свой город",
		"no_products_for_your_request":           "Товаров по вашему запросу нет",
		"choose_material":                        "Выберите материал",
		"choose_brand":                           "Выберите бренд",
		"go_to_cart":                             "Перейти в корзину",
		"go_to":                                  "Перейти",
		"feedback":                               "Обратная связь",
		"choose_language":                        "Выберите язык",
		"market_price":                           "Среднерыночная цена",
		"bot_price":                              "STROYBOT цена",
		"drop_cart":                              "Очистить корзину",
		"dropped_cart":                           "Корзина очищена",
		"by_phone":                               "С помощью звонка",
		"by_chat":                                "Написать в чате",
		"choose_way":                             "Выберите удобный способ связи",
		"go_to_chat":                             "Перейти в чат",
		"send_your_number":                       "Введите свой номер телефона в формате +998 00 000 00 00 без пробелов",
		"current_number":                         "Ваш текущий номер телефона: ",
		"incorrect_number_format":                "Вы ввели телефон в неправильном формате. Повторите попытку",
		"succesfully_changed_number":             "Номер телефона успешно изменен",
		"new_number":                             "Новый номер: ",
	},
	"uz": {
		"change_number":                          "Raqamni o’zgartirish",
		"change_city":                            "Shaharni o’zgartirish",
		"change_language":                        "Tilni o’zgartirish",
		"public_offer":                           "Ommaviy taklif",
		"information":                            "Ma'lumot",
		"become_partner":                         "Hamkor bo'lish",
		"back":                                   "Ortga",
		"main_menu":                              "Asosiy menyu",
		"order":                                  "Buyurtma berish",
		"my_orders":                              "Mening buyurtmalarim",
		"settings":                               "Sozlamalar",
		"contact":                                "Bog'lanish",
		"cart":                                   "Savat",
		"current_prices":                         "Qurilish materiallarining amaldagi narxlari",
		"current_exchange_rate":                  "Joriy valyuta kursi",
		"order_to_your_address":                  "O’z manzilga buyurtma berish",
		"order_to_another_address":               "Boshqa manzilga buyurtma berish",
		"specify_convenient_address":             "O’zingizga qulay bo’lgan manzilni ko’rsating",
		"share_location":                         "Joylashuvingizni baham koʻrasizmi?",
		"decline":                                "Rad etish",
		"send_required_geoposition_via_telegram": "Telegram orqali kerakli geografik joylashuv (geopozitsiya)ni yuboring",
		"your_city":                              "Shahringiz",
		"product_location_city":                  "Tovar joylashgan shahar",
		"cant_order_these_products":              "Ushbu tovarlarga buyurtma bera olmaysiz, boshqalarni tanlang",
		"thank_you_for_choosing_stroybot":        "Stroybotni tanlaganingiz uchun tashakkur, siz bilan bir soat ichida bog'lanishadi",
		"confirm_order":                          "Buyurtmani rasmiylashtirish",
		"empty_cart":                             "Savat bo'sh",
		"average_market_price":                   "Bozorda jami o'rtacha narx",
		"bot_total_price":                        "Botning umumiy narxi",
		"you_saved":                              "Tejab qoldingiz",
		"yes":                                    "Ha",
		"no":                                     "Yo’q",
		"share_phone_number":                     "Telefon raqamini ulashish",
		"unable_to_continue_without_phone":       "Afsuski, agar siz telefon raqamini ko'rsatmasangiz, keyinchalik davom eta olmaysiz",
		"choose_your_city":                       "Shahringizni tanlang",
		"no_products_for_your_request":           "So'rovingiz bo'yicha tovarlar yo'q",
		"choose_material":                        "Materialni tanlang",
		"choose_brand":                           "Brendni tanlang",
		"go_to_cart":                             "Savatga o’tish",
		"go_to":                                  "O’tish",
		"feedback":                               "Fikr-muolоҳaza",
		"choose_language":                        "Tilni tanlang",
		"market_price":                           "O’rtacha bozor narxi",
		"bot_price":                              "STROYBOT narxi",
		"drop_cart":                              "Savatni tozalash",
		"dropped_cart":                           "Savat tozalangan",
		"by_phone":                               "Qo’ng’iroq yordamida",
		"by_chat":                                "Chatga yozish",
		"choose_way":                             "Qulay bo’lgan aloqa usulini tanlang",
		"go_to_chat":                             "Chatga o’tish",
		"send_your_number":                       "Telefon raqamingizni +998 00 000 00 00 formatda kiriting, siz bo’shliqlarsiz",
		"current_number":                         "Joriy telefon raqamingiz: ",
		"incorrect_number_format":                "Siz telefonni noto’g’ri formatda kiritdingiz. Qayta urinib ko’ring",
		"succesfully_changed_number":             "Telefon raqami muvaffaqiyatli o’zgartirildi",
		"new_number":                             "Yangi raqam: ",
	},
	"uzkcha": {
		"change_number":                          "Рақамни ўзгартириш",
		"change_city":                            "Шаҳарни ўзгартириш",
		"change_language":                        "Тилни ўзгартириш",
		"public_offer":                           "Оммавий таклиф",
		"information":                            "Маълумот",
		"become_partner":                         "Хамкор бўлиш)",
		"back":                                   "Ортга",
		"main_menu":                              "Асосий меню",
		"order":                                  "Буюртма бериш",
		"my_orders":                              "Mенинг буюртмаларим",
		"settings":                               "Созламалар",
		"contact":                                "Боғланиш",
		"cart":                                   "Сават",
		"current_prices":                         "Қурилиш материалларининг амалдаги нархлари",
		"current_exchange_rate":                  "Жорий валюта курси",
		"order_to_your_address":                  "Ўз манзилга буюртма бериш",
		"order_to_another_address":               "Бошқа манзилга буюртма бериш",
		"specify_convenient_address":             "Ўзингизга қулай бўлган манзилни кўрсатинг",
		"share_location":                         "Жойлашувингизни баҳам кўрасизми?",
		"decline":                                "Рад этиш",
		"send_required_geoposition_via_telegram": "Telegram орқали керакли географик жойлашув (геопозиция)ни юборинг",
		"your_city":                              "Шаҳрингиз",
		"product_location_city":                  "Товар жойлашган шаҳар",
		"cant_order_these_products":              "Ушбу товарларга буюртма бера олмаисиз, бошқаларни танланг",
		"thank_you_for_choosing_stroybot":        "Стройботни танлаганингиз учун ташаккур, сиз билан бир соат ичида боғланишади",
		"confirm_order":                          "Буюртмани расмийлаштириш",
		"empty_cart":                             "Сават бўш",
		"average_market_price":                   "Бозорда жами ўртача нарх",
		"bot_total_price":                        "Ботнинг умумий нархи",
		"you_saved":                              "Тежаб қолдингиз",
		"yes":                                    "Ҳа",
		"no":                                     "Йўқ",
		"share_phone_number":                     "Телефон рақамини улашиш",
		"unable_to_continue_without_phone":       "Афсуски, агар сиз телефон рақамини кўрсатмасангиз, кейинчалик давом эта олмайсиз",
		"choose_your_city":                       "Шаҳрингизни танланг",
		"no_products_for_your_request":           "Сўровингиз бўйича товарлар йўқ",
		"choose_material":                        "Материални танланг",
		"choose_brand":                           "Брендни танланг",
		"go_to_cart":                             "Саватга ўтиш",
		"go_to":                                  "Ўтиш",
		"feedback":                               "Фикр-мулоҳаза",
		"choose_language":                        "Тилни танланг",
		"market_price":                           "Ўртача бозор нархи",
		"bot_price":                              "СТРОЙБОТ нархи",
		"drop_cart":                              "Саватни тозалаш",
		"dropped_cart":                           "Сават тозаланган",
		"by_phone":                               "Қўнғироқ ёрдамида",
		"by_chat":                                "Чатга ёзиш",
		"choose_way":                             "Қулай бўлган алоқа усулини танланг",
		"go_to_chat":                             "Чатга ўтиш",
		"send_your_number":                       "Телефон рақамингизни +998 00 000 00 00 форматда киритинг, сиз бўшлиқларсиз",
		"current_number":                         "Жорий телефон рақамингиз: ",
		"incorrect_number_format":                "Сиз телефонни нотўғри форматда киритдингиз. Қайта уриниб кўринг",
		"succesfully_changed_number":             "Телефон рақами муваффақиятли ўзгартирилди",
		"new_number":                             "Янги рақам: ",
	},
}

// переменные для подключения к боту
var host string = "https://api.telegram.org/bot"
var token string = os.Getenv("BOT_TOKEN")
var link string = os.Getenv("API_LINK")
var domen string = os.Getenv("SERVER_URI")

// данные всеx пользователей
var usersDB map[int]UserT

// переменная для запросов к API
var client = http.Client{}

// главная функция работы бота
func main() {

	//для мониторинга работы бота
	go sendLiveSignal()

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
func sendPost(requestBody string, url string) ([]byte, error) {
	// Создаем новый POST-запрос
	req, err := http.NewRequest("POST", url, bytes.NewBufferString(requestBody))
	if err != nil {
		return nil, fmt.Errorf("Ошибка при создании запроса: %v", err)
	}

	// Устанавливаем заголовок Content-Type для указания типа данных в теле запроса
	req.Header.Set("Content-Type", "application/json")

	// Отправляем запрос с использованием стандартного клиента HTTP
	client := &http.Client{}
	resp, err := client.Do(req)
	if err != nil {
		return nil, fmt.Errorf("Ошибка при выполнении запроса: %v", err)
	}
	defer resp.Body.Close()

	// Проверяем код состояния HTTP-ответа
	if resp.StatusCode == http.StatusOK {
		// Успешный запрос, читаем тело ответа
		body, err := ioutil.ReadAll(resp.Body)
		if err != nil {
			return nil, fmt.Errorf("Ошибка при чтении тела ответа: %v", err)
		}
		return body, nil
	} else {
		// Обработка ошибки при некорректном статусе HTTP-ответа
		return nil, fmt.Errorf("Некорректный код состояния HTTP: %s", resp.Status)
	}
}

// функция для отправки сообщения пользователю
func sendMessage(chatId int, text string, keyboard map[string]interface{}) {
	request_url := host + token + "/sendMessage?chat_id=" + strconv.Itoa(chatId) + "&text=" + text
	if keyboard != nil {
		// Преобразуем клавиатуру в JSON
		keyboardJSON, _ := json.Marshal(keyboard)
		request_url += "&reply_markup=" + string(keyboardJSON)
	}
	//http.Get(url)
	requestURL, err := url.Parse(request_url)
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
	fmt.Println(response)
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
	hashString := ""

	// Проверяем, есть ли параметр после "/start"
	if strings.HasPrefix(text, "/start ") {
		// Извлекаем значение параметра
		paramValue := strings.TrimPrefix(text, "/start ")

		// Проверяем значение параметра
		if strings.Contains(paramValue, "provider") {

			isProvider = true
			hashString = strings.SplitN(text, "_", 2)[1]

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
		user.Hash = hashString
		user.Step = 1

		usersDB[chatId] = user

	}

	file, _ := os.Create("db.json")
	jsonString, _ := json.Marshal(usersDB)
	file.Write(jsonString)

	// Создаем GET-запрос
	resp, err := http.Get("http://" + link + "/api/customers.php?tg_id=" + strconv.Itoa(chatId))
	if err != nil {
		log.Fatal("Ошибка при выполнении запроса:", err)
	}
	defer resp.Body.Close()

	var userInfo []UserT
	err = json.NewDecoder(resp.Body).Decode(&userInfo)
	if err == nil {
		// Используем полученные данные
		for _, user := range userInfo {
			if user.Blocked == 1 {
				continue
			}
		}
	} else {

		if usersDB[chatId].IsProvider {

			switch {

			case usersDB[chatId].Step == 1:

				requestBody := `{"tg_username": "` + usersDB[chatId].Username + `", "tg_id":"` + strconv.Itoa(chatId) + `", "hash_string":"` + usersDB[chatId].Hash + `"}`

				response, _ := sendPost(requestBody, "http://"+link+"/api/vendors.php")

				// Используйте переменную response для обработки ответа
				fmt.Println("Ответ сервера:", string(response))

				//посмотреть данные
				fmt.Println(string(response))

				//парсим данные из json
				var serverResr ServerResponce
				json.Unmarshal(response, &serverResr)

				status := serverResr.OK
				payLoad := serverResr.PayLoad
				serverMessage := serverResr.Error

				if status {

					sendMessage(chatId, "Здравствуйте, отправьте местоположение склада, выбрав его на карте", nil)
					user := usersDB[chatId]
					user.Vendor_id = payLoad
					user.Step += 1
					usersDB[chatId] = user

				} else if serverMessage == "Поставщик с таким telegram id уже зарегистрирован" {

					sendMessage(chatId, serverMessage, nil)

				} else {

					sendMessage(chatId, serverMessage, nil)

				}

			case usersDB[chatId].Step == 2:

				sendMessage(chatId, "Локация вашего склада записана", nil)

				coordinates := Coordinates{
					Latitude:  latitude,
					Longitude: longitude,
				}

				jsonCoordinates, _ := json.Marshal(coordinates)

				requestBody := `{"id": "` + strconv.Itoa(usersDB[chatId].Vendor_id) + `", "coordinates":` + string(jsonCoordinates) + `, "hash_string":"` + usersDB[chatId].Hash + `"}`
				fmt.Println(requestBody)

				sendPost(requestBody, "http://"+link+"/api/vendors.php")

				user := usersDB[chatId]
				user.Step = 1
				usersDB[chatId] = user
				break

			}

		} else {
			switch {
			// кейс для начального сообщения для пользователя
			case text == "/start" || usersDB[chatId].Step == 1:

				// Создаем GET-запрос
				resp, err := http.Get("http://" + link + "/api/customers.php?tg_id=" + strconv.Itoa(chatId))
				if err != nil {
					log.Fatal("Ошибка при выполнении запроса:", err)
				}
				defer resp.Body.Close()

				var personExist []UserT
				err = json.NewDecoder(resp.Body).Decode(&personExist)
				if err != nil {
					user := usersDB[chatId]
					user.Step = 1
					usersDB[chatId] = user

					//собираем объект клавиатуры для выбора языка
					buttons := [][]map[string]interface{}{
						{{"text": "Русский 🇷🇺", "callback_data": "ru"}},
						{{"text": "O'zbekcha 🇺🇿", "callback_data": "uz"}},
						{{"text": "Ўзбекча 🇺🇿", "callback_data": "uzkcha"}},
					}

					inlineKeyboard := map[string]interface{}{
						"inline_keyboard": buttons,
					}

					// Отправляем сообщение с клавиатурой и перезаписываем шаг
					sendMessage(chatId, "Здравствуйте, добро пожаловать в Стройбот. Выберите язык 👇", inlineKeyboard)

					//следующий шаг
					user.Step += 1
					usersDB[chatId] = user
					break
				} else {
					user := usersDB[chatId]
					user.Step = 4
					usersDB[chatId] = user

					if button == "ru" || button == "uz" || button == "uzkcha" {
						user.Language = button
						usersDB[chatId] = user
					}

					// формируем json и отправляем данные пользователя на бэк
					requestBody := `{"first_name":"` + usersDB[chatId].FirstName + `", "last_name":"` + usersDB[chatId].LastName + `", "phone":"` + usersDB[chatId].PhoneNumber + `", "city_id":` + button + `, "tg_username":"` + usersDB[chatId].Username + `", "tg_id":` + strconv.Itoa(chatId) + `}`
					fmt.Println(requestBody)

					sendPost(requestBody, "http://"+link+"/api/customers.php")

					// Создаем объект клавиатуры
					keyboard := map[string]interface{}{
						"keyboard": [][]map[string]interface{}{
							{{"text": languages[usersDB[chatId].Language]["order"] + " 🛍"}},

							{{"text": languages[usersDB[chatId].Language]["current_exchange_rate"] + " 💹"},
								{"text": languages[usersDB[chatId].Language]["settings"] + " ⚙️"},
							},
							{{"text": languages[usersDB[chatId].Language]["my_orders"] + " 📕"},
								{"text": languages[usersDB[chatId].Language]["current_prices"] + " 📈"},
							},
							{{"text": languages[usersDB[chatId].Language]["contact"] + " 📞"},
								{"text": languages[usersDB[chatId].Language]["cart"] + " 🗑"},
							},
						},
						"resize_keyboard":   true,
						"one_time_keyboard": false,
					}

					// Создаем GET-запрос
					resp, err := http.Get("http://" + link + "/api/customers/get-with-details.php?tg_id=" + strconv.Itoa(chatId))
					if err != nil {
						log.Fatal("Ошибка при выполнении запроса:", err)
					}
					defer resp.Body.Close()

					var userdetails []UserDetails
					err = json.NewDecoder(resp.Body).Decode(&userdetails)
					if err != nil {
						log.Fatal("Ошибка при декодировании JSON:", err)
					}

					// Используем полученные данные и подставляем их в кнопки
					for _, userdetail := range userdetails {

						menuText := url.QueryEscape("\n" + languages[usersDB[chatId].Language]["your_city"] + ": ")
						// Отправляем сообщение с клавиатурой и перезаписываем шаг
						sendMessage(chatId, languages[usersDB[chatId].Language]["main_menu"]+menuText+userdetail.CityName, keyboard)

					}

					user.Step += 1
					usersDB[chatId] = user
					break
				}

			// кейс для получения номера телефона
			case usersDB[chatId].Step == 2 || button == "backToPhone":

				// Создаем объект клавиатуры
				keyboard := map[string]interface{}{
					"keyboard": [][]map[string]interface{}{
						{
							{
								"text":            languages[usersDB[chatId].Language]["yes"],
								"request_contact": true,
							},
						},
						{
							{
								"text": languages[usersDB[chatId].Language]["no"],
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
				user.Language = button
				usersDB[chatId] = user
				break

			// кейс для обработки отказа от отправки телефона
			case usersDB[chatId].Step == 3 && text == "Нет":

				// создаём объект клавиатуры
				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToPhone"}},
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
				resp, err := http.Get("http://" + link + "/api/cities.php?deleted=0&is_active=1")
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
				sendMessage(chatId, languages[usersDB[chatId].Language]["choose_your_city"]+" 👇", inlineKeyboard)
				user.Step += 1
				usersDB[chatId] = user
				break

			// кейс для вывода меню пользователю и запись или обновление пользователя в бд
			case usersDB[chatId].Step == 4:

				user := usersDB[chatId]
				user.Step = 4
				user.City, _ = strconv.Atoi(button)

				if button == "ru" || button == "uz" || button == "uzkcha" {
					user.Language = button
					usersDB[chatId] = user
				} else if button != "ru" && text == "" {
					fmt.Println("FIRST")
					// формируем json и отправляем данные пользователя на бэк
					requestBody := `{"first_name":"` + usersDB[chatId].FirstName + `", "last_name":"` + usersDB[chatId].LastName + `", "phone":"` + usersDB[chatId].PhoneNumber + `", "city_id":` + button + `, "tg_username":"` + usersDB[chatId].Username + `", "tg_id":` + strconv.Itoa(chatId) + `}`
					fmt.Println(requestBody)

					sendPost(requestBody, "http://"+link+"/api/customers.php")
				} else if strings.Contains(text, "998") {
					resultString := strings.ReplaceAll(text, "+", "")
					if len(resultString) == 12 {
						user.PhoneNumber = resultString
						usersDB[chatId] = user
						// формируем json и отправляем данные пользователя на бэк
						requestBody := `{"phone":"` + usersDB[chatId].PhoneNumber + `", "tg_id":` + strconv.Itoa(chatId) + `}`
						fmt.Println(requestBody)

						sendPost(requestBody, "http://"+link+"/api/customers.php")
						sendMessage(chatId, url.QueryEscape(languages[usersDB[chatId].Language]["succesfully_changed_number"]+"\n"+languages[usersDB[chatId].Language]["new_number"]+text), nil)
					} else {
						sendMessage(chatId, languages[usersDB[chatId].Language]["incorrect_number_format"], nil)
						break
					}

				} // else {
				// 	sendMessage(chatId, "Вы ввели телефон в неправильном формате. Попробуйте ещё раз", nil)
				// 	break
				// }

				// Создаем объект клавиатуры
				keyboard := map[string]interface{}{
					"keyboard": [][]map[string]interface{}{
						{{"text": languages[usersDB[chatId].Language]["order"] + " 🛍"}},

						{{"text": languages[usersDB[chatId].Language]["current_exchange_rate"] + " 💹"},
							{"text": languages[usersDB[chatId].Language]["settings"] + " ⚙️"},
						},
						{{"text": languages[usersDB[chatId].Language]["my_orders"] + " 📕"},
							{"text": languages[usersDB[chatId].Language]["current_prices"] + " 📈"},
						},
						{{"text": languages[usersDB[chatId].Language]["contact"] + " 📞"},
							{"text": languages[usersDB[chatId].Language]["cart"] + " 🗑"},
						},
					},
					"resize_keyboard":   true,
					"one_time_keyboard": false,
				}

				// Создаем GET-запрос
				resp, err := http.Get("http://" + link + "/api/customers/get-with-details.php?tg_id=" + strconv.Itoa(chatId))
				if err != nil {
					log.Fatal("Ошибка при выполнении запроса:", err)
				}
				defer resp.Body.Close()

				var userdetails []UserDetails
				err = json.NewDecoder(resp.Body).Decode(&userdetails)
				if err != nil {
					log.Fatal("Ошибка при декодировании JSON:", err)
				}

				// Используем полученные данные и подставляем их в кнопки
				for _, userdetail := range userdetails {

					menuText := url.QueryEscape("\n" + languages[usersDB[chatId].Language]["your_city"] + ": ")
					// Отправляем сообщение с клавиатурой и перезаписываем шаг
					sendMessage(chatId, languages[usersDB[chatId].Language]["main_menu"]+menuText+userdetail.CityName, keyboard)

				}

				user.Step += 1
				usersDB[chatId] = user
				break

			// кейс для возращения пользователя в меню
			case button == "backToMenu":
				user := usersDB[chatId]
				user.Step = 4

				keyboard := map[string]interface{}{
					"keyboard": [][]map[string]interface{}{
						{{"text": languages[usersDB[chatId].Language]["order"] + " 🛍"}},

						{{"text": languages[usersDB[chatId].Language]["current_exchange_rate"] + " 💹"},
							{"text": languages[usersDB[chatId].Language]["settings"] + " ⚙️"},
						},
						{{"text": languages[usersDB[chatId].Language]["my_orders"] + " 📕"},
							{"text": languages[usersDB[chatId].Language]["current_prices"] + " 📈"},
						},
						{{"text": languages[usersDB[chatId].Language]["contact"] + " 📞"},
							{"text": languages[usersDB[chatId].Language]["cart"] + " 🗑"},
						},
					},
					"resize_keyboard":   true,
					"one_time_keyboard": false,
				}

				// Создаем GET-запрос
				resp, err := http.Get("http://" + link + "/api/customers/get-with-details.php?tg_id=" + strconv.Itoa(chatId))
				if err != nil {
					log.Fatal("Ошибка при выполнении запроса:", err)
				}
				defer resp.Body.Close()

				var userdetails []UserDetails
				err = json.NewDecoder(resp.Body).Decode(&userdetails)
				if err != nil {
					log.Fatal("Ошибка при декодировании JSON:", err)
				}

				// Используем полученные данные и подставляем их в кнопки
				for _, userdetail := range userdetails {

					menuText := url.QueryEscape("\n" + languages[usersDB[chatId].Language]["your_city"] + ": ")
					// Отправляем сообщение с клавиатурой и перезаписываем шаг
					sendMessage(chatId, languages[usersDB[chatId].Language]["main_menu"]+menuText+userdetail.CityName, keyboard)

				}

				user.Step += 1
				usersDB[chatId] = user
				break

			// кейс для вывода категорий товаров на выбор
			case (usersDB[chatId].Step == 5 && text == languages[usersDB[chatId].Language]["order"]+" 🛍") || (button == "backToGoods"):

				user := usersDB[chatId]
				user.Step = 5

				buttons := [][]map[string]interface{}{}
				// Создаем GET-запрос
				resp, err := http.Get("http://" + link + "/api/categories/get-all-by-exist-products.php?city_id=" + strconv.Itoa(usersDB[chatId].City))
				if err != nil {
					log.Fatal("Ошибка при выполнении запроса:", err)
				}
				defer resp.Body.Close()

				var categories []Category
				err = json.NewDecoder(resp.Body).Decode(&categories)
				if err != nil {

					buttons := [][]map[string]interface{}{
						{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToMenu"}},
					}

					inlineKeyboard := map[string]interface{}{
						"inline_keyboard": buttons,
					}

					sendMessage(chatId, languages[usersDB[chatId].Language]["no_products_for_your_request"], inlineKeyboard)
				}

				// Используем полученные данные и подставляем их в кнопки
				for _, category := range categories {
					button := []map[string]interface{}{
						{
							"text":          category.CategoryName,
							"callback_data": category.CategoryName + " " + strconv.Itoa(category.ID),
						},
					}
					buttons = append(buttons, button)
				}
				buttons = append(buttons, []map[string]interface{}{
					{
						"text":          languages[usersDB[chatId].Language]["back"] + " 🔙",
						"callback_data": "backToMenu",
					},
				})

				// создаём объект клавиатуры
				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["choose_material"]+" 👇", inlineKeyboard)
				user.Step += 1
				usersDB[chatId] = user
				break

			// кейс для вывода брендов товаров для пользователя
			case usersDB[chatId].Step == 6 || button == "backToBrands":

				user := usersDB[chatId]
				user.Step = 6
				// Разбиваем строку на две части по пробелу
				parts := strings.Split(button, " ")
				firstCategoryName := parts[0]
				secondCategoryID := parts[1]
				if button != "backToBrands" {
					user.Category_id = secondCategoryID
					sendMessage(chatId, "Вы выбрали: "+firstCategoryName, nil)
				}
				usersDB[chatId] = user
				buttons := [][]map[string]interface{}{}
				// Создаем GET-запрос
				resp, err := http.Get("http://" + link + "/api/brands/get-by-category.php?category_id=" + usersDB[chatId].Category_id + "&city_id=" + strconv.Itoa(usersDB[chatId].City))
				if err != nil {
					log.Fatal("Ошибка при выполнении запроса:", err)
				}
				defer resp.Body.Close()

				var brands []Brand
				err = json.NewDecoder(resp.Body).Decode(&brands)
				if err != nil {

					buttons := [][]map[string]interface{}{
						{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToGoods"}},
					}

					inlineKeyboard := map[string]interface{}{
						"inline_keyboard": buttons,
					}

					sendMessage(chatId, languages[usersDB[chatId].Language]["no_products_for_your_request"], inlineKeyboard)
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
						"text":          languages[usersDB[chatId].Language]["back"] + " 🔙",
						"callback_data": "backToGoods",
					},
				})

				//создаём объект клавиатуры
				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["choose_brand"]+" 👇", inlineKeyboard)

				user.Step += 1
				usersDB[chatId] = user
				break

			// кейс для отображения выбранных товаров по фильтрам
			case usersDB[chatId].Step == 7:

				var chozen_language string = ""
				if usersDB[chatId].Language == "ru" {
					chozen_language = "1"
				} else if usersDB[chatId].Language == "uz" {
					chozen_language = "2"
				} else {
					chozen_language = "3"
				}

				// Создаем GET-запрос
				resp, err := http.Get("http://" + link + "/api/customers/get-with-details.php?tg_id=" + strconv.Itoa(chatId))
				if err != nil {
					log.Fatal("Ошибка при выполнении запроса:", err)
				}
				defer resp.Body.Close()

				var userdetails []UserDetails
				err = json.NewDecoder(resp.Body).Decode(&userdetails)
				if err != nil {
					log.Fatal("Ошибка при декодировании JSON:", err)
				}

				// Используем полученные данные и подставляем их в кнопки
				for _, userdetail := range userdetails {

					// Создаем GET-запрос
					resp, err := http.Get("http://" + link + "/api/products/get-with-details-language.php?deleted=0&vendor_active=1&is_active=1&price_confirmed=1&is_confirm=1&vendor_deleted=0&category_id=" + usersDB[chatId].Category_id + "&brand_id=" + button + "&city_id=" + strconv.Itoa(userdetail.CityID) + "&language=" + chozen_language)
					if err != nil {
						log.Fatal("Ошибка при выполнении запроса:", err)
					}
					defer resp.Body.Close()

					var product []Product
					err = json.NewDecoder(resp.Body).Decode(&product)

					// Используем полученные данные
					for _, product := range product {
						// Создаем объект инлайн клавиатуры
						buttons := [][]map[string]interface{}{
							{
								{"text": "➖ 1", "callback_data": "minusone:" + strconv.Itoa(product.ID)},
								{"text": "0", "callback_data": "quantity"},
								{"text": "➕ 1", "callback_data": "addone:" + strconv.Itoa(product.ID)},
							},
							{
								{"text": "➖ 10", "callback_data": "minus:" + strconv.Itoa(product.ID)},
								{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToBrands"},
								{"text": "➕ 10", "callback_data": "add:" + strconv.Itoa(product.ID)},
							},
							{{"text": languages[usersDB[chatId].Language]["go_to_cart"] + " 🗑", "callback_data": "goToCart"}},
						}

						// создаём объект клавиатуры
						inlineKeyboard := map[string]interface{}{
							"inline_keyboard": buttons,
						}

						// кодируем клавиатуру в json
						inlineKeyboardJSON, _ := json.Marshal(inlineKeyboard)

						fmt.Println(product.Photo)

						//создание запроса
						caption := url.QueryEscape("<b><u>" + product.Name + "</u></b>\n" + languages[usersDB[chatId].Language]["market_price"] + "\n<b>" + strconv.Itoa(product.MaxPrice) + " сум</b>\n" + languages[usersDB[chatId].Language]["bot_price"] + "\n<b>" + strconv.Itoa(product.Price) + " сум</b>")
						apiURL := ""

						if strings.Contains(product.Photo, "http") {
							apiURL = "https://api.telegram.org/bot" + token + "/sendPhoto?chat_id=" + strconv.Itoa(id) + "&caption=" + caption + "&photo=" + product.Photo + "&parse_mode=HTML&reply_markup=" + string(inlineKeyboardJSON)
						} else {
							apiURL = "https://api.telegram.org/bot" + token + "/sendPhoto?chat_id=" + strconv.Itoa(id) + "&caption=" + caption + "&photo=" + domen + product.Photo + "&parse_mode=HTML&reply_markup=" + string(inlineKeyboardJSON)
						}

						fmt.Println(domen)
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

				}

				// перезаписываем шаг
				user := usersDB[chatId]
				user.Step += 1
				usersDB[chatId] = user
				break

			// кейс для отображения корзины покупателя
			case usersDB[chatId].Step == 8 && button == "goToCart" || text == languages[usersDB[chatId].Language]["cart"]+" 🗑":

				user := usersDB[chatId]
				finalPrice := 0
				user.Step = 8
				benefit := 0
				marketPrice := 0
				cartText := ""
				for ID := range usersDB[chatId].Cart {

					fmt.Println(ID)
					// Создаем GET-запрос
					resp, err := http.Get("http://" + link + "/api/products.php?deleted=0&id=" + strconv.Itoa(ID))
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

				//если человек переходит в корзину из главного меню
				if text == languages[usersDB[chatId].Language]["cart"]+" 🗑" {

					// если товаров нет
					if finalPrice == 0 {

						buttons := [][]map[string]interface{}{
							{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToMenu"}},
						}

						// Создаем объект инлайн клавиатуры
						inlineKeyboard := map[string]interface{}{
							"inline_keyboard": buttons,
						}

						sendMessage(chatId, languages[usersDB[chatId].Language]["empty_cart"], inlineKeyboard)
					} else {

						buttons := [][]map[string]interface{}{

							{{"text": languages[usersDB[chatId].Language]["confirm_order"] + " ✅", "callback_data": "buy"}},
							{{"text": languages[usersDB[chatId].Language]["drop_cart"] + " ❌", "callback_data": "dropCart"}},

							{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToMenu"}},
						}

						// Создаем объект инлайн клавиатуры
						inlineKeyboard := map[string]interface{}{
							"inline_keyboard": buttons,
						}

						encodedCartText := url.QueryEscape(cartText)
						encodedText := url.QueryEscape(languages[usersDB[chatId].Language]["average_market_price"]+"\n<s>"+strconv.Itoa(marketPrice)+"</s> cум\n"+languages[usersDB[chatId].Language]["bot_total_price"]+"\n"+strconv.Itoa(finalPrice)+" сум\n"+languages[usersDB[chatId].Language]["you_saved"]+"\n<b>"+strconv.Itoa(benefit)) + "</b> сум&parse_mode=HTML"
						finalText := encodedCartText + encodedText

						// Отправляем сообщение с клавиатурой и перезаписываем шаг
						sendMessage(chatId, finalText, inlineKeyboard)

					}

					// если пользователь смотрит коризину после списка товаров
				} else {

					// если товаров нет
					if finalPrice == 0 {

						buttons := [][]map[string]interface{}{
							{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToMenu"}},
						}

						// Создаем объект инлайн клавиатуры
						inlineKeyboard := map[string]interface{}{
							"inline_keyboard": buttons,
						}

						sendMessage(chatId, languages[usersDB[chatId].Language]["empty_cart"], inlineKeyboard)

					} else {

						buttons := [][]map[string]interface{}{

							{{"text": languages[usersDB[chatId].Language]["confirm_order"] + " ✅", "callback_data": "buy"}},
							{{"text": languages[usersDB[chatId].Language]["drop_cart"] + " ❌", "callback_data": "dropCart"}},

							{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToGoods"}},
						}

						// Создаем объект инлайн клавиатуры
						inlineKeyboard := map[string]interface{}{
							"inline_keyboard": buttons,
						}

						encodedCartText := url.QueryEscape(cartText)
						encodedText := url.QueryEscape(languages[usersDB[chatId].Language]["average_market_price"]+"\n<s>"+strconv.Itoa(marketPrice)+"</s> cум\n"+languages[usersDB[chatId].Language]["bot_total_price"]+"\n"+strconv.Itoa(finalPrice)+"\n<s>"+strconv.Itoa(marketPrice)+"</s> cум\n"+languages[usersDB[chatId].Language]["bot_total_price"]+"\n"+strconv.Itoa(finalPrice)+" сум\n"+languages[usersDB[chatId].Language]["you_saved"]+"\n<b>"+strconv.Itoa(benefit)) + "</b> сум&parse_mode=HTML"
						finalText := encodedCartText + encodedText

						// Отправляем сообщение с клавиатурой и перезаписываем шаг
						sendMessage(chatId, finalText, inlineKeyboard)
					}
				}

				user.Step += 1
				usersDB[chatId] = user
				break

			// кейс для покупки выбранных товаров пользователем
			case usersDB[chatId].Step == 9 && button == "buy":
				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["order_to_your_address"], "callback_data": "myAdress"}},
					{{"text": languages[usersDB[chatId].Language]["order_to_another_address"], "callback_data": "anotherAdress"}},
				}

				// Создаем объект инлайн клавиатуры
				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["specify_convenient_address"], inlineKeyboard)
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
								"text":             languages[usersDB[chatId].Language]["yes"],
								"request_location": true,
							},
						},
						{
							{
								"text": languages[usersDB[chatId].Language]["no"],
							},
						},
					},
					"resize_keyboard":   true,
					"one_time_keyboard": true,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["share_location"], keyboard)
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
								"text": languages[usersDB[chatId].Language]["decline"],
							},
						},
					},
					"resize_keyboard":   true,
					"one_time_keyboard": true,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["send_required_geoposition_via_telegram"], keyboard)
				user := usersDB[chatId]
				user.Step += 1
				usersDB[chatId] = user
				break

			// кейс для вывода сообщения о заказе и его отправка на бекенд
			case usersDB[chatId].Step == 11:

				user := usersDB[chatId]

				// Создаем GET-запрос
				res, err := http.Get("http://" + link + "/api/customers/get-with-details.php?tg_id=" + strconv.Itoa(chatId))
				if err != nil {
					log.Fatal("Ошибка при выполнении запроса:", err)
				}
				defer res.Body.Close()

				var userdetails []UserDetails
				err = json.NewDecoder(res.Body).Decode(&userdetails)
				if err != nil {
					log.Fatal("Ошибка при декодировании JSON:", err)
				}

				// Используем полученные данные и подставляем их в кнопки
				for _, userdetail := range userdetails {

					for ID := range user.Cart {

						// Создаем GET-запрос
						res, err := http.Get("http://" + link + "/api/products/get-with-details.php?id=" + strconv.Itoa(ID))
						if err != nil {
							log.Fatal("Ошибка при выполнении запроса:", err)
						}
						defer res.Body.Close()

						var product []Product
						err = json.NewDecoder(res.Body).Decode(&product)
						if err != nil {
							log.Fatal("Ошибка при декодировании JSON:", err)
						}

						// Используем полученные данные и подставляем их в кнопки
						for _, product := range product {
							if product.CityName != userdetail.CityName {

								// Создаем объект клавиатуры
								keyboard := map[string]interface{}{
									"keyboard": [][]map[string]interface{}{
										{{"text": languages[usersDB[chatId].Language]["order"] + " 🛍"}},

										{{"text": languages[usersDB[chatId].Language]["current_exchange_rate"] + " 💹"},
											{"text": languages[usersDB[chatId].Language]["settings"] + " ⚙️"},
										},
										{{"text": languages[usersDB[chatId].Language]["my_orders"] + " 📕"},
											{"text": languages[usersDB[chatId].Language]["current_prices"] + " 📈"},
										},
										{{"text": languages[usersDB[chatId].Language]["contact"] + " 📞"},
											{"text": languages[usersDB[chatId].Language]["cart"] + " 🗑"},
										},
									},
									"resize_keyboard":   true,
									"one_time_keyboard": false,
								}

								// обнуляем корзину
								user.Cart = map[int]int{}
								errorText := url.QueryEscape("\n" + languages[usersDB[chatId].Language]["your_city"] + ": " + userdetail.CityName + "\n" + languages[usersDB[chatId].Language]["product_location_city"] + ": " + product.CityName + "\n" + languages[usersDB[chatId].Language]["cant_order_these_products"] + " 🙏")
								// Отправляем сообщение с клавиатурой и перезаписываем шаг
								sendMessage(chatId, languages[usersDB[chatId].Language]["main_menu"]+errorText, keyboard)

								user.Step = 5
								usersDB[chatId] = user
								break
							} else {
								time := time.Now().Unix()
								coordinates := Coordinates{
									Latitude:  latitude,
									Longitude: longitude,
								}
								jsonProducts, _ := json.Marshal(usersDB[chatId].Cart)
								jsonCoordinates, _ := json.Marshal(coordinates)

								// Создаем GET-запрос
								resp, err := http.Get("http://" + link + "/api/customers.php?tg_id=" + strconv.Itoa(chatId))
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
									sendPost(requestBody, "http://"+link+"/api/orders/create-with-vendor-calc.php")
								}

								// Создаем объект клавиатуры
								keyboard := map[string]interface{}{
									"keyboard": [][]map[string]interface{}{
										{{"text": languages[usersDB[chatId].Language]["order"] + " 🛍"}},

										{{"text": languages[usersDB[chatId].Language]["current_exchange_rate"] + " 💹"},
											{"text": languages[usersDB[chatId].Language]["settings"] + " ⚙️"},
										},
										{{"text": languages[usersDB[chatId].Language]["my_orders"] + " 📕"},
											{"text": languages[usersDB[chatId].Language]["current_prices"] + " 📈"},
										},
										{{"text": languages[usersDB[chatId].Language]["contact"] + " 📞"},
											{"text": languages[usersDB[chatId].Language]["cart"] + " 🗑"},
										},
									},
									"resize_keyboard":   true,
									"one_time_keyboard": false,
								}

								// обнуляем корзину
								user.Cart = map[int]int{}

								// Отправляем сообщение с клавиатурой и перезаписываем шаг
								sendMessage(chatId, languages[usersDB[chatId].Language]["thank_you_for_choosing_stroybot"], keyboard)
								user.Step = 5
								usersDB[chatId] = user
								break
							}
						}
					}
				}

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
								{"text": "➖ 1", "callback_data": "minusone:" + strconv.Itoa(ID)},
								{"text": strconv.Itoa(usersDB[chatId].Cart[ID]), "callback_data": "quantity"},
								{"text": "➕ 1", "callback_data": "addone:" + strconv.Itoa(ID)},
							},
							{
								{"text": "➖ 10", "callback_data": "minus:" + strconv.Itoa(ID)},
								{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToBrands"},
								{"text": "➕ 10", "callback_data": "add:" + strconv.Itoa(ID)},
							},
							{{"text": languages[usersDB[chatId].Language]["go_to_cart"] + " 🗑", "callback_data": "goToCart"}},
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
							{"text": "➖ 1", "callback_data": "minusone:" + productStr},
							{"text": "1", "callback_data": "quantity"},
							{"text": "➕ 1", "callback_data": "addone:" + productStr},
						},
						{
							{"text": "➖ 10", "callback_data": "minus:" + productStr},
							{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToBrands"},
							{"text": "➕ 10", "callback_data": "add:" + productStr},
						},
						{{"text": languages[usersDB[chatId].Language]["go_to_cart"] + " 🗑", "callback_data": "goToCart"}},
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
								{"text": "➖ 1", "callback_data": "minusone:" + strconv.Itoa(ID)},
								{"text": strconv.Itoa(usersDB[chatId].Cart[ID]), "callback_data": "quantity"},
								{"text": "➕ 1", "callback_data": "addone:" + strconv.Itoa(ID)},
							},
							{
								{"text": "➖ 10", "callback_data": "minus:" + strconv.Itoa(ID)},
								{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToBrands"},
								{"text": "➕ 10", "callback_data": "add:" + strconv.Itoa(ID)},
							},
							{{"text": languages[usersDB[chatId].Language]["go_to_cart"] + " 🗑", "callback_data": "goToCart"}},
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
							{"text": "➖ 1", "callback_data": "minusone:" + productStr},
							{"text": "10", "callback_data": "quantity"},
							{"text": "➕ 1", "callback_data": "addone:" + productStr},
						},
						{
							{"text": "➖ 10", "callback_data": "minus:" + productStr},
							{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToBrands"},
							{"text": "➕ 10", "callback_data": "add:" + productStr},
						},
						{{"text": languages[usersDB[chatId].Language]["go_to_cart"] + " 🗑", "callback_data": "goToCart"}},
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
								{"text": "➖ 1", "callback_data": "minusone:" + productStr},
								{"text": strconv.Itoa(usersDB[chatId].Cart[ID]), "callback_data": quantity},
								{"text": "➕ 1", "callback_data": "addone:" + productStr},
							},
							{
								{"text": "➖ 10", "callback_data": "minus:" + productStr},
								{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToBrands"},
								{"text": "➕ 10", "callback_data": "add:" + productStr},
							},
							{{"text": languages[usersDB[chatId].Language]["go_to_cart"] + " 🗑", "callback_data": "goToCart"}},
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

			// кейс для - в карточке товаров
			if strings.SplitN(button, ":", 2)[0] == "minusone" {
				user := usersDB[chatId]
				productStr := strings.Split(button, ":")[1]
				productID, _ := strconv.Atoi(productStr)
				quantity := 1

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
								{"text": "➖ 1", "callback_data": "minusone:" + productStr},
								{"text": strconv.Itoa(usersDB[chatId].Cart[ID]), "callback_data": quantity},
								{"text": "➕ 1", "callback_data": "addone:" + productStr},
							},
							{
								{"text": "➖ 10", "callback_data": "minus:" + productStr},
								{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToBrands"},
								{"text": "➕ 10", "callback_data": "add:" + productStr},
							},
							{{"text": languages[usersDB[chatId].Language]["go_to_cart"] + " 🗑", "callback_data": "goToCart"}},
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

			if button == "dropCart" {

				user := usersDB[chatId]
				// обнуляем корзину
				user.Cart = map[int]int{}
				usersDB[chatId] = user

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["dropped_cart"], nil)

			}

			// кейс при нажатии на кнопку актуальные цены
			if text == languages[usersDB[chatId].Language]["current_prices"]+" 📈" {

				channelURL := "t.me/stroy_bot_prices"

				// Получаем текущую дату и время
				currentTime := time.Now()

				// Создаем объект временной зоны GMT+5
				location := time.FixedZone("GMT+5", 5*60*60)

				// Устанавливаем временную зону для текущего времени
				currentTime = currentTime.In(location)

				// Форматируем дату и время в нужном формате
				formattedTime := currentTime.Format("01-02-2006 15:04:05")

				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["go_to"] + " 🌐", "url": channelURL}},
					{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToMenu"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["current_prices"]+" "+formattedTime, inlineKeyboard)
			}

			// кейс при нажатии на кнопку актуальный курс
			if text == languages[usersDB[chatId].Language]["current_exchange_rate"]+" 💹" {

				channelURL := "t.me/stroybotchannel2"

				// Получаем текущую дату и время
				currentTime := time.Now()

				// Создаем объект временной зоны GMT+5
				location := time.FixedZone("GMT+5", 5*60*60)

				// Устанавливаем временную зону для текущего времени
				currentTime = currentTime.In(location)

				// Форматируем дату и время в нужном формате
				formattedTime := currentTime.Format("01-02-2006 15:04:05")

				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["go_to"] + " 🌐", "url": channelURL}},
					{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToMenu"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["current_exchange_rate"]+" "+formattedTime, inlineKeyboard)
			}

			// кейс при нажатии на кнопку мои заказы
			if text == languages[usersDB[chatId].Language]["my_orders"]+" 📕" {

				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToMenu"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["my_orders"], inlineKeyboard)
			}

			// кейс при нажатии на кнопку связаться
			if text == languages[usersDB[chatId].Language]["contact"]+" 📞" {

				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["by_phone"] + " 📲", "callback_data": "withPhone"}},
					{{"text": languages[usersDB[chatId].Language]["by_chat"] + " 💬", "callback_data": "withСhat"}},
					{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToMenu"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["choose_way"]+" 👇", inlineKeyboard)

			}

			if button == "withPhone" {

				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToMenu"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// // Создаем GET-запрос
				// resp, err := http.Get("http://" + link + "/api/settings.php?name=phone")
				// if err != nil {
				// 	log.Fatal("Ошибка при выполнении запроса:", err)
				// }
				// defer resp.Body.Close()

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, url.QueryEscape("+998903726322"), inlineKeyboard)

				user := usersDB[chatId]
				user.Step = 4
				usersDB[chatId] = user

			}

			if button == "withСhat" {

				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToMenu"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, "@stroybotuz_admin", inlineKeyboard)

				user := usersDB[chatId]
				user.Step = 4
				usersDB[chatId] = user

			}

			// кейс при нажатии на кнопку настройки
			if text == languages[usersDB[chatId].Language]["settings"]+" ⚙️" || button == "backToSettings" {

				user := usersDB[chatId]
				// обнуляем корзину
				user.Cart = map[int]int{}
				usersDB[chatId] = user

				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["change_number"], "callback_data": "number"},
						{"text": languages[usersDB[chatId].Language]["change_city"], "callback_data": "city"}},

					{{"text": languages[usersDB[chatId].Language]["change_language"], "callback_data": "language"},
						{"text": languages[usersDB[chatId].Language]["public_offer"], "callback_data": "oferta"}},

					{{"text": languages[usersDB[chatId].Language]["information"], "callback_data": "info"},
						{"text": languages[usersDB[chatId].Language]["become_partner"], "callback_data": "partnership"}},

					{{"text": languages[usersDB[chatId].Language]["feedback"], "callback_data": "book"}},
				}

				buttons = append(buttons, []map[string]interface{}{
					{
						"text":          languages[usersDB[chatId].Language]["back"] + " 🔙",
						"callback_data": "backToMenu",
					},
				})

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["settings"]+" ⚙️", inlineKeyboard)
			}

			// кейс при нажатии на кнопку справка
			if button == "info" {

				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToSettings"}},
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
					{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToSettings"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["become_partner"], inlineKeyboard)
			}

			// кейс при нажатии на кнопку обратная связь
			if button == "book" {

				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToSettings"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["feedback"], inlineKeyboard)
			}

			// кейс при нажатии на кнопку оферта
			if button == "oferta" {

				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToSettings"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, "Оферта", inlineKeyboard)
			}

			// кейс при нажатии на кнопку язык
			if button == "language" {

				//собираем объект клавиатуры для выбора языка
				buttons := [][]map[string]interface{}{
					{{"text": "Русский 🇷🇺", "callback_data": "1"}},
					{{"text": "O'zbekcha 🇺🇿", "callback_data": "2"}},
					{{"text": "Ўзбекча 🇺🇿", "callback_data": "3"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Отправляем сообщение с клавиатурой и перезаписываем шаг
				sendMessage(chatId, languages[usersDB[chatId].Language]["choose_language"]+" 👇", inlineKeyboard)

				user := usersDB[chatId]
				user.Step = 4
				usersDB[chatId] = user

			}

			// кейс при нажатии на кнопку изменить город
			if button == "city" {
				buttons := [][]map[string]interface{}{}
				// Создаем GET-запрос
				resp, err := http.Get("http://" + link + "/api/cities.php?deleted=0&is_active=1")
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
				sendMessage(chatId, languages[usersDB[chatId].Language]["choose_your_city"]+" 👇", inlineKeyboard)
				user := usersDB[chatId]
				user.Step = 4
				usersDB[chatId] = user
			}

			// кейс при нажатии на кнопку изменить телефон
			if button == "number" {

				buttons := [][]map[string]interface{}{
					{{"text": languages[usersDB[chatId].Language]["back"] + " 🔙", "callback_data": "backToSettings"}},
				}

				inlineKeyboard := map[string]interface{}{
					"inline_keyboard": buttons,
				}

				// Создаем GET-запрос
				resp, err := http.Get("http://" + link + "/api/customers/get-with-details.php?tg_id=" + strconv.Itoa(chatId))
				if err != nil {
					log.Fatal("Ошибка при выполнении запроса:", err)
				}
				defer resp.Body.Close()

				var userdetails []UserDetails
				err = json.NewDecoder(resp.Body).Decode(&userdetails)
				if err != nil {
					log.Fatal("Ошибка при декодировании JSON:", err)
				}

				// Используем полученные данные и подставляем их в кнопки
				for _, userdetail := range userdetails {

					phoneText := url.QueryEscape("\n" + languages[usersDB[chatId].Language]["current_number"] + userdetail.Phone)

					// Отправляем сообщение с клавиатурой и перезаписываем шаг
					sendMessage(chatId, url.QueryEscape(languages[usersDB[chatId].Language]["send_your_number"])+phoneText, inlineKeyboard)

				}

				user := usersDB[chatId]
				user.Step = 4
				usersDB[chatId] = user
			}

		}
	}
}

func generateMD5Hash(input string) string {
	// Convert the input string to a byte slice (required by md5.Sum).
	data := []byte(input)

	// Create an MD5 hash instance.
	hash := md5.Sum(data)

	// Convert the hash to a hexadecimal string representation.
	// Use hex.EncodeToString() to convert the byte slice to a string.
	hashString := hex.EncodeToString(hash[:])

	return hashString
}

func sendLiveSignal() {
	serviceName, _ := os.LookupEnv("SERVICE_NAME")
	token := generateMD5Hash(serviceName)
	url := "http://linkholder.ru/monitoring/post.php?token=" + token
	for range time.Tick(time.Second * 5) {
		http.Get(url)
	}
}
