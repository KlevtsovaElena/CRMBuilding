package common

import (
	"encoding/json"
	"log"
	"net/http"
	"strconv"
)

func userIsBlocked(user *UserT) bool {

	// Создаем GET-запрос
	resp, err := http.Get("http://" + link + "/api/customers.php?tg_id=" + strconv.Itoa(user.ID))
	if err != nil {
		log.Fatal("Ошибка при выполнении запроса:", err)
	}
	defer resp.Body.Close()

	var userInfo []UserT
	err = json.NewDecoder(resp.Body).Decode(&userInfo)
	if err == nil {
		// Используем полученные данные
		for _, item := range userInfo {
			if item.Blocked == 1 {
				return true
			}
		}
	}

	return false
}
