package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"net/url"
	"os"
	"strconv"
	"time"
)

var token string = os.Getenv("bot_token")
var serverUri string = os.Getenv("server_uri")
var apiLink string = os.Getenv("api_link")

type OrderVendor struct {
	ID                int     `json:"id"`
	OrderID           int     `json:"order_id"`
	VendorID          int     `json:"vendor_id"`
	Status            int     `json:"status"`
	Archive           int     `json:"archive"`
	TotalPrice        int     `json:"total_price"`
	Distance          float64 `json:"distance"`
	NotificationCount int     `json:"notification_count"`
}

type Vendor struct {
	ID             int    `json:"id"`
	Name           string `json:"name"`
	CityID         int    `json:"city_id"`
	Phone          string `json:"phone"`
	Email          string `json:"email"`
	TgUsername     string `json:"tg_username"`
	TgID           int    `json:"tg_id"`
	Role           int    `json:"role"`
	Comment        string `json:"comment"`
	DateReg        int    `json:"date_reg"`
	HashString     string `json:"hash_string"`
	IsActive       int    `json:"is_active"`
	Password       string `json:"password"`
	Token          string `json:"token"`
	Percent        string `json:"percent"`
	Deleted        int    `json:"deleted"`
	PriceConfirmed int    `json:"price_confirmed"`
	CurrencyDollar int    `json:"currency_dollar"`
	Rate           int    `json:"rate"`
}

type Order struct {
	ID         int   `json:"id"`
	CustomerID int   `json:"customer_id"`
	OrderDate  int64 `json:"order_date"`
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

func main() {
	validateEnvVariables()

	for range time.Tick(time.Second * 10) {
		process()
	}
}

func validateEnvVariables() {
	if len(token) == 0 {
		panic("Не указан токен телеграм бота.")
	}

	if len(serverUri) == 0 {
		panic("Не указан URI адрес сервера.")
	}
}

func process() {
	orderVendors := getAllOrderVendors()
	admins := getAllAdmins()

	if orderVendors == nil {
		return
	}

	for _, orderVendor := range orderVendors {

		// Рассматриваем только новые заказы
		if orderVendor.Status == 0 {
			// Первое уведомление (при создании заказа)
			if orderVendor.NotificationCount == 0 {
				orderIdStr := strconv.Itoa(orderVendor.OrderID)
				message := "Новый заказ №" + orderIdStr + "\n" + serverUri + "/pages/vendor-order.php?id=" + orderIdStr

				vendor := getVendorById(orderVendor.VendorID)

				if vendor.ID != 0 {
					isOk := sendTelegramMessage(vendor.TgID, message)

					if isOk {
						incrementOrderVendorNotificationCount(orderVendor)
					}
				}
			}

			// Второе оповещение (через 5 минут)
			if orderVendor.NotificationCount == 1 {
				order := getOrderById(orderVendor.OrderID)

				difference := time.Now().Unix() - order.OrderDate

				if difference >= 300 {
					orderIdStr := strconv.Itoa(orderVendor.OrderID)
					message := "У вас непросмотренный заказ №" + orderIdStr + "\n" + serverUri + "/pages/vendor-order.php?id=" + orderIdStr

					vendor := getVendorById(orderVendor.VendorID)

					if vendor.ID != 0 {
						isOk := sendTelegramMessage(vendor.TgID, message)

						if isOk {
							incrementOrderVendorNotificationCount(orderVendor)
						}
					}
				}
			}

			// Третье оповещение (через 10)
			if orderVendor.NotificationCount == 2 {
				order := getOrderById(orderVendor.OrderID)

				difference := time.Now().Unix() - order.OrderDate

				if difference >= 600 {
					orderIdStr := strconv.Itoa(orderVendor.OrderID)
					message := "У вас непросмотренный заказ №" + orderIdStr + "\n" + serverUri + "/pages/vendor-order.php?id=" + orderIdStr

					vendor := getVendorById(orderVendor.VendorID)

					if vendor.ID != 0 {
						isOk := sendTelegramMessage(vendor.TgID, message)

						if isOk {
							incrementOrderVendorNotificationCount(orderVendor)
						}
					}
				}

			}

			// Четвертое оповещение (через 20 минут)
			if orderVendor.NotificationCount == 3 {
				order := getOrderById(orderVendor.OrderID)

				difference := time.Now().Unix() - order.OrderDate

				if difference >= 1200 {
					orderIdStr := strconv.Itoa(orderVendor.OrderID)
					message := "У вас непросмотренный заказ №" + orderIdStr + "\n" + serverUri + "/pages/vendor-order.php?id=" + orderIdStr
					vendor := getVendorById(orderVendor.VendorID)
					adminMessage := "Заказ №" + orderIdStr + " в течении 20 минут не просмотрен поставщиком '" + vendor.Name + "'."

					if vendor.ID != 0 {
						isOk := sendTelegramMessage(vendor.TgID, message)

						if isOk {
							for _, admin := range admins {
								sendTelegramMessage(admin.TgID, adminMessage)
							}

							incrementOrderVendorNotificationCount(orderVendor)
						}
					}
				}
			}
		} else {
			// Если просмотрел в результате 4-го оповещения
			if orderVendor.NotificationCount == 4 {
				orderIdStr := strconv.Itoa(orderVendor.OrderID)
				vendor := getVendorById(orderVendor.VendorID)
				adminMessage := "Заказ №" + orderIdStr + " просмотрен поставщиком '" + vendor.Name + "'."

				incrementOrderVendorNotificationCount(orderVendor)
				for _, admin := range admins {
					sendTelegramMessage(admin.TgID, adminMessage)
				}
			}
		}
	}
}

func getAllOrderVendors() []OrderVendor {
	var vendors []OrderVendor

	resp, err := http.Get("http://" + apiLink + "/api/ordervendors.php")

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при отправке сообщения 1.")
		return nil
	}

	data, err := io.ReadAll(resp.Body)

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при обработке ответа 1.")
		return nil
	}

	json.Unmarshal(data, &vendors)

	return vendors
}

func getAllAdmins() []Vendor {
	var admins []Vendor

	resp, err := http.Get("http://" + apiLink + "/api/vendors.php?role=1&is_active=1&deleted=0")

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при отправке сообщения: " + err.Error())
		return nil
	}

	data, err := io.ReadAll(resp.Body)

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при обработке ответа: " + err.Error())
		return nil
	}

	json.Unmarshal(data, &admins)

	return admins
}

func getVendorById(id int) Vendor {
	var vendor Vendor

	resp, err := http.Get("http://" + apiLink + "/api/vendors.php?id=" + strconv.Itoa(id))

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при отправке сообщения: " + err.Error())
		return vendor
	}

	data, err := io.ReadAll(resp.Body)

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при обработке ответа: " + err.Error())
		return vendor
	}

	json.Unmarshal(data, &vendor)

	return vendor
}

func getOrderById(id int) Order {
	var order Order

	resp, err := http.Get("http://" + apiLink + "/api/orders.php?id=" + strconv.Itoa(id))

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при отправке сообщения: " + err.Error())
		return order
	}

	data, err := io.ReadAll(resp.Body)

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при обработке ответа: " + err.Error())
		return order
	}

	json.Unmarshal(data, &order)

	return order
}

func sendTelegramMessage(chatId int, text string) bool {

	if len(token) == 0 {
		fmt.Println("Не удалось отправить сообщение: не указан токен.")
		return false
	}

	if chatId == 0 {
		fmt.Println("Не удалось отправить сообщение: не указан chatId.")
		return false
	}

	var response SendMessageResponseT

	requestStr := "https://api.telegram.org/bot" + token + "/sendMessage?disable_notification=True&chat_id=" + url.QueryEscape(strconv.Itoa(chatId)) + "&text=" + url.QueryEscape(text)
	resp, err := http.Get(requestStr)

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при отправке сообщения в телеграм: " + err.Error())
		return false
	}

	data, err := io.ReadAll(resp.Body)

	if err != nil {
		fmt.Println("Произошла ошибка при обработке полученных данных от телеграм. (отправка сообщения): " + err.Error())
	}

	json.Unmarshal(data, &response)

	if response.Ok {
		return true
	}

	fmt.Println("Произошла ошибка при отправке сообщения в телеграм (ошибочный результат): " + requestStr)
	return false
}

func incrementOrderVendorNotificationCount(orderVendor OrderVendor) {
	data := []byte(`{"id":"` + strconv.Itoa(orderVendor.ID) + `", "notification_count":"` + strconv.Itoa(orderVendor.NotificationCount+1) + `"}`)
	r := bytes.NewReader(data)
	http.Post("http://"+apiLink+"/api/ordervendors.php", "application/json", r)
}
