package main

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
