package main

import (
	"currency-service/utils"
	"fmt"
	"net/http"
	"time"
)

func main() {

	//запуск сервера для проверки
	go func() {
		http.HandleFunc("/health/", func(w http.ResponseWriter, r *http.Request) {
			w.Write([]byte("success"))
		})
		http.ListenAndServe(":80", nil)
	}()

	finished := make(chan bool)
	go utils.Start(finished, process)

	fmt.Println("Wait all tasks...")
	<-finished
	fmt.Println("All tasks completed.")
}

func process() {
	buyBankName, buyBankValue, buyError := utils.GetBuyBankWithValue()

	if buyError != nil {
		fmt.Print("Произошла ошибка при получении данных о банке с лучшим курсом покупки")
		return
	}

	sellBankName, sellBankValue, sellError := utils.GetSellBankWithValue()

	if sellError != nil {
		fmt.Print("Произошла ошибка при получении данных о банке с лучшим курсом продажи")
		return
	}

	cbValue, cbError := utils.GetCentralBankWithValue()

	if cbError != nil {
		fmt.Print("Произошла ошибка при получении данных о центральном банке")
		return
	}

	messageId := utils.SendMessage("Курс ЦБ на " + time.Now().Format("02.01.2006") + " " + cbValue + " сум." + "\nЛучшие условия в Ташкенте\nПокупка: " + buyBankName + " - " + buyBankValue + ".\nПродажа: " + sellBankName + " - " + sellBankValue + ".")

	if messageId != -1 {
		utils.UnpinAllMessages()
		utils.PinMessage(messageId)
	}
}
