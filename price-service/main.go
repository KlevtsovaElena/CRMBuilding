package main

import (
	"encoding/json"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"net/url"
	"os"
	"strconv"
	"time"
)

// структура категорий
type Category struct {
	ID           int    `json:"id"`
	CategoryName string `json:"category_name"`
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

// переменные телеграм канала и бота, который отправляет сообщения
var channelName string = os.Getenv("TELEGRAM_PRICE_CHANEL")
var token string = os.Getenv("PRICE_BOT_TOKEN")

// переменные времени начала рассылки
var planed_hour string = os.Getenv("price_task_start_hour")
var planed_minute string = os.Getenv("price_task_start_minute")

// переменная для подключения к API
var link string = os.Getenv("API_LINK")

// основная функция для проверки на начало отсылки
func main() {

	//цикл для проверки времени
	for range time.Tick(time.Second * 1) {

		//создаём переменную настоящего времени
		timeNow := time.Now()

		//сравниваем текущее время (часы и минуты) и время рассылки
		if strconv.Itoa(timeNow.Hour()+3) == planed_hour && strconv.Itoa(timeNow.Minute()) == planed_minute {

			//запускаем функцию отправки сообщения
			makeGoodsList()

			//останавливаем функцию на минуту, чтобы не было спама
			time.Sleep(60 * time.Second)

		}

	}
}

// функция отправки сообщения в канал
func makeGoodsList() {

	fmt.Println("makeGoodsList")
	// Создаем GET-запрос
	resp, err := http.Get("http://" + link + "/api/categories/get-all-by-exist-products.php")
	if err != nil {
		log.Fatal("Ошибка при выполнении запроса:", err)
	}
	defer resp.Body.Close()

	var categories []Category
	json.NewDecoder(resp.Body).Decode(&categories)

	// Используем полученные данные и берём значения категорий
	for _, category := range categories {

		fmt.Println("enter in categories")

		var caption string = "<b>" + category.CategoryName + "</b>"
		var product_photo string

		// Создаем GET-запрос
		resp, err := http.Get("http://" + link + "/api/products.php?category_id=" + strconv.Itoa(category.ID))
		if err != nil {
			log.Fatal("Ошибка при выполнении запроса:", err)
		}
		defer resp.Body.Close()

		var products []Product
		json.NewDecoder(resp.Body).Decode(&products)

		for _, product := range products {

			fmt.Println("enter in products")

			caption += url.QueryEscape("\n<u>" + product.Name + " - " + strconv.Itoa(product.Price) + "</u>\n")
			product_photo = product.Photo

		}

		apiURL := "https://api.telegram.org/bot" + token + "/sendPhoto?chat_id=" + url.QueryEscape(channelName) + "&caption=" + caption + "&photo=" + product_photo + "&parse_mode=HTML"

		fmt.Println(product_photo)

		sendMessage(apiURL)

	}

}

func sendMessage(apiURL string) {

	fmt.Println("sendMessage")
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
