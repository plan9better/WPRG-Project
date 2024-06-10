package main

import (
	"flag"
	"io"
	"log"
	"net/http"
	"os"
	"time"
)

func main() {
	refreshRate := flag.Int("n", 5, "How often (in seconds) should requests to API be made")
	flag.Parse()

	infoLog := log.New(os.Stdout, "INFO\t", log.Ldate|log.Ltime)
	errorLog := log.New(os.Stderr, "ERROR\t", log.Ldate|log.Ltime|log.Lshortfile)

	errorCounter := 0
	// Exit after 10 unsuccessful tries in a row
	for errorCounter < 10 {
		resp, err := http.Get("https://ckan2.multimediagdansk.pl/gpsPositions?v=2")
		if err != nil {
			errorLog.Println(err)
			errorCounter += 1
		} else {
			infoLog.Println("Request sent")
			errorCounter = 0
		}

		defer resp.Body.Close()
		body, err := io.ReadAll(resp.Body)
		if err != nil {
			errorLog.Println(err)
		}

		err = os.WriteFile("data/positions.json", body, 0644)
		if err != nil {
			errorLog.Println(err)
		}

		time.Sleep(time.Second * time.Duration(*refreshRate))
	}
}
