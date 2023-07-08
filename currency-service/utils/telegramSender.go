package utils

import (
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"os"
	"strconv"

	"github.com/joho/godotenv"
)

var channelName string = os.Getenv("telegram_channel")
var token string = ""

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

type PinMessageResponseT struct {
	Ok     bool `json:"ok"`
	Result bool `json:"result"`
}

func SendMessage(text string) int {

	err := godotenv.Load()
	if err != nil {
		fmt.Println("Error loading .env file")
	}
	token = os.Getenv("CURRENCY_BOT_TOKEN")

	if len(channelName) == 0 {
		fmt.Println("Не удалось отправить сообщение: не указано имя канала.")
		return -1
	}

	if len(token) == 0 {
		fmt.Println("Не удалось закрепить сообщение: не указан токен.")
		return -1
	}

	var response SendMessageResponseT

	resp, err := http.Get("https://api.telegram.org/bot" + token + "/sendMessage?chat_id=" + channelName + "&text=" + text)

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при отправке сообщения.")
		return -1
	}

	data, err := io.ReadAll(resp.Body)

	if err != nil {
		fmt.Println("Произошла ошибка при обработке полученных данных от телеграм. (отправка сообщения)")
	}

	json.Unmarshal(data, &response)

	if response.Ok {
		return response.Result.MessageID
	}

	fmt.Println("Произошла ошибка при отправке сообщения.")
	return -1
}

func PinMessage(messageId int) bool {

	if messageId == -1 {
		return false
	}
	if len(channelName) == 0 {
		fmt.Println("Не удалось закрепить сообщение: не указано имя канала.")
		return false
	}

	if len(token) == 0 {
		fmt.Println("Не удалось закрепить сообщение: не указан токен.")
		return false
	}

	var response PinMessageResponseT

	resp, err := http.Get("https://api.telegram.org/bot" + token + "/pinChatMessage?chat_id=" + channelName + "&message_id=" + strconv.Itoa(messageId))

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при закреплении сообщения.")
		return false
	}

	data, err := io.ReadAll(resp.Body)

	if err != nil {
		fmt.Println("Произошла ошибка при обработке полученных данных от телеграм (закрепление сообщения).")
	}

	json.Unmarshal(data, &response)

	if response.Ok {
		return response.Result
	}

	return false
}

func UnpinAllMessages() bool {

	if len(channelName) == 0 {
		fmt.Println("Не удалось открепить сообщения: не указано имя канала.")
		return false
	}

	if len(token) == 0 {
		fmt.Println("Не удалось открепить сообщения: не указан токен.")
		return false
	}

	var response PinMessageResponseT

	resp, err := http.Get("https://api.telegram.org/bot" + token + "/unpinAllChatMessages?chat_id=" + channelName)

	if err != nil {
		fmt.Println("Произошла сетевая ошибка при откреплении всех сообщений.")
		return false
	}

	data, err := io.ReadAll(resp.Body)

	if err != nil {
		fmt.Println("Произошла ошибка при обработке полученных данных от телеграм (открепление сообщений).")
	}

	json.Unmarshal(data, &response)

	if response.Ok {
		return response.Result
	}

	return false
}
