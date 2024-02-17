package main

import (
	"encoding/json"
	"os"
)

func saveUsers() {
	file, _ := os.Create("db.json")
	defer file.Close()
	jsonString, _ := json.Marshal(usersDB)
	file.Write(jsonString)
}
