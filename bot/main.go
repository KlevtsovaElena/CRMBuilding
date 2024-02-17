package main

//подключение требуемых пакетов
import (
	"encoding/json"
	"net/http"
	"os"
	"sync"
	"time"
)

// переменные для подключения к боту
var host string = "https://api.telegram.org/bot"
var token string = os.Getenv("BOT_TOKEN")
var link string = os.Getenv("API_LINK")
var domen string = os.Getenv("SERVER_URI")

// данные всеx пользователей
var usersDB = make(map[int]UserT)

// главная функция работы бота
func main() {

	//запуск скрвера для проверки
	go func() {
		http.HandleFunc("/health/", func(w http.ResponseWriter, r *http.Request) {
			w.Write([]byte("success"))
		})
		http.ListenAndServe(":80", nil)
	}()

	//достаем юзеров из кэша при перезапуске контейнера
	getUsers()

	//обнуление последнего id сообщения
	lastMessage := 0

	//для блокировки доступа к массиву с юзерами
	var mutex sync.Mutex

	//цикл для проверки на наличие новых сообщений
	for range time.Tick(time.Second * 1) {

		//отправляем запрос к Telegram API на получение сообщений
		data := getUpdates(lastMessage)

		//посмотреть данные
		//fmt.Println(string(data))

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

		var wg sync.WaitGroup
		wg.Add(number)

		//в цикле доставать инормацию по каждому сообщению
		for i := 0; i < number; i++ {

			//обработка одного сообщения
			go processMessage(responseObj.Result[i], need.Result[i], &wg, &mutex)

		}

		wg.Wait()

		//запоминаем update_id  последнего сообщения
		lastMessage = responseObj.Result[number-1].UpdateID + 1

		//сохраняем данные о юзерах в кэщ после проверки всех сообщений
		saveUsers()

	}
}
