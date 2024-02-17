package main

import (
	"bytes"
	"fmt"
	"io/ioutil"
	"net/http"
)

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
