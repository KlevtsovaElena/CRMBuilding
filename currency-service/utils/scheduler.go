package utils

import (
	"fmt"
	"os"
	"strconv"
	"time"
)

var hourOsEnvName string = "task_start_hour"
var minuteOsEnvName string = "task_start_minute"

func Start(finished chan bool, task func()) {
	hour, hErr := strconv.Atoi(os.Getenv(hourOsEnvName))

	if hErr != nil {
		fmt.Println("Не удалось преобразовать значение переменной среды \"" + hourOsEnvName + "\"")
		finished <- true
		return
	}

	minute, mErr := strconv.Atoi(os.Getenv(minuteOsEnvName))

	if mErr != nil {
		fmt.Println("Не удалось преобразовать значение переменной среды \"" + minuteOsEnvName + "\"")
		finished <- true
		return
	}

	for {
		timeNow := time.Now()
		nextTimeToStart := time.Date(timeNow.Year(), timeNow.Month(), timeNow.Day(), hour, minute, 0, 0, time.Local)
		difference := nextTimeToStart.Sub(timeNow).Milliseconds()

		if difference < 0 {
			nextTimeToStart := nextTimeToStart.Add(time.Hour * 24)
			difference = nextTimeToStart.Sub(timeNow).Milliseconds()
		}
		nextStart := time.UnixMilli(time.Now().UnixMilli() + difference)

		fmt.Print("Запланирован следующий запуск на ")
		fmt.Println(nextStart)

		t := time.Duration(difference) * time.Millisecond
		time.Sleep(t)

		fmt.Print("Выполнение задания от ")
		fmt.Print(time.Now())
		fmt.Print("...")
		go task()
		fmt.Println()

		time.Sleep(time.Second * 5)
	}
}
