package main

import (
	"currency-service/utils"
	"fmt"
	"time"
)

func main() {
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

	messageId := utils.SendMessage("Лучший курс USD на " + time.Now().Format("02.01.2006") + ". Покупка: " + buyBankName + " - " + buyBankValue + ". Продажа: " + sellBankName + " - " + sellBankValue + ".")

	if messageId != -1 {
		utils.UnpinAllMessages()
		utils.PinMessage(messageId)
	}
}
