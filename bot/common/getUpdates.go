package common

import (
	"fmt"
	"io"
	"net/http"
	"strconv"
)

// получение новых сообщений вы боте
func getUpdates(lastMessage int) []byte {
	var url string = host + token + "/getUpdates?offset=" + strconv.Itoa(lastMessage)
	response, err := http.Get(url)
	if err != nil {
		fmt.Println(err)
	}
	data, _ := io.ReadAll(response.Body)

	return data
}
