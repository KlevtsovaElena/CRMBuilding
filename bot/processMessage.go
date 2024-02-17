package main

import (
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	"net/url"
	"strconv"
	"strings"
	"sync"
	"time"
)

func processMessage(message MessageT, messageInline MessageInlineT, wg *sync.WaitGroup, mutex *sync.Mutex) {

	defer wg.Done()

	//определение текста или кнопки клавиатуры
	text := message.Message.Text
	fmt.Println(text)
	chatId := 0
	if messageInline.CallbackQuery.From.ID == 0 {
		chatId = message.Message.From.ID
	} else {
		chatId = messageInline.CallbackQuery.From.ID
	}
	sendMessage(chatId, "Бот работает", nil)

	firstName := message.Message.From.FirstName
	lastName := message.Message.From.LastName
	phone := message.Message.Contact.PhoneNumber
	latitude := message.Message.Location.Latitude
	longitude := message.Message.Location.Longitude
	username := message.Message.From.Username
	button := messageInline.CallbackQuery.Data
	id := messageInline.CallbackQuery.From.ID
	mesIdInline := messageInline.CallbackQuery.Message.MessageID

	//начинаем работать с общим ресурсом и блокируем его до конца работы с ним
	mutex.Lock()
	defer mutex.Unlock()
	//есть ли юзер
	var user UserT
	_, exist := usersDB[chatId]
	if !exist {
		user.ID = chatId
		user.FirstName = firstName
		user.LastName = lastName
		user.Username = username
		user.Tg_id = chatId
		user.PhoneNumber = phone
		user.City, _ = strconv.Atoi(button)
		user.Cart = make(map[int]int)
		user.Step = 1
		usersDB[chatId] = user
	} else {
		user = usersDB[chatId]
	}

	//проверяем на блокировку
	blocked := userIsBlocked(&user)
	if blocked {
		sendMessage(user.ID, "Вы заблокированы", nil)
		return
	}

	//определение роли юзера и его доступа
	// Проверяем, есть ли параметр после "/start"
	if strings.HasPrefix(text, "/start ") {
		// Извлекаем значение параметра
		paramValue := strings.TrimPrefix(text, "/start ")

		// Проверяем значение параметра
		if strings.Contains(paramValue, "provider") {

			hashString := strings.SplitN(text, "_", 2)[1]
			if hashString != "" {
				user.IsProvider = true
				user.Hash = hashString
			}

		}
	}

	////////
	////////
	////////
	//Далее следует бизнес логика - блок логики ответов
	///////
	///////
	///////

	//если написал поставщик
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

		//если
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
					responseData, err := io.ReadAll(response.Body)
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
