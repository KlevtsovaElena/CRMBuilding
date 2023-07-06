package utils

import (
	"errors"
	"io"
	"net/http"
	"strings"
)

var url = "https://bank.uz/currency"

func getPageContent() (string, error) {
	response, error := http.Get(url)

	if error != nil {
		return "", error
	}

	data, errorResp := io.ReadAll(response.Body)
	if errorResp != nil {
		return "", errorResp
	}

	return string(data), nil
}

func getBankName(innerBlockContent string) (string, error) {
	startNameString := "<span class=\"medium-text\">"
	endNameString := "</span>"

	startIndex := strings.Index(innerBlockContent, startNameString)
	endIndex := strings.Index(innerBlockContent[startIndex:], endNameString)

	if startIndex < 0 || endIndex < 0 {
		return "", errors.New("Block with bank namein wrong format.")
	}

	subString := innerBlockContent[startIndex : startIndex+endIndex]
	result := strings.TrimSpace(strings.Replace(subString, startNameString, "", -1))

	return result, nil
}

func getBankValue(innerBlockContent string) (string, error) {
	startNameString := "<span class=\"medium-text green-date\">"
	endNameString := "</span>"

	startIndex := strings.Index(innerBlockContent, startNameString)
	endIndex := strings.Index(innerBlockContent[startIndex:], endNameString)

	if startIndex < 0 || endIndex < 0 {
		return "", errors.New("Block with bank value in wrong format.")
	}

	subString := innerBlockContent[startIndex : startIndex+endIndex]
	result := strings.TrimSpace(strings.Replace(subString, startNameString, "", -1))

	return result, nil
}

func getBankNameWithValue(blockContent []string) (string, string, error) {

	if len(blockContent) < 2 {
		return "", "", errors.New("Block not found.")
	}

	buyBlockSplitRows := strings.Split(blockContent[1], "bc-inner-block-left-texts")

	if len(buyBlockSplitRows) < 2 {
		return "", "", errors.New("Row not found.")
	}

	bankName, errName := getBankName(buyBlockSplitRows[1])

	if errName != nil {
		return "", "", errName
	}

	bankValue, errValue := getBankValue(buyBlockSplitRows[1])

	if errValue != nil {
		return bankName, "", errValue
	}

	return bankName, bankValue, nil
}

func GetBuyBankWithValue() (string, string, error) {
	pageContent, err := getPageContent()

	if err != nil {
		return "", "", err
	}
	buyBlock := strings.Split(pageContent, "<div class=\"bc-inner-blocks-left\">")

	return getBankNameWithValue(buyBlock)
}

func GetSellBankWithValue() (string, string, error) {
	pageContent, err := getPageContent()

	if err != nil {
		return "", "", err
	}

	buyBlock := strings.Split(pageContent, "<div class=\"bc-inner-blocks-right\">")

	return getBankNameWithValue(buyBlock)
}