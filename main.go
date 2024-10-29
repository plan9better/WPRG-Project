package main

import (
	"database/sql"
	"encoding/json"
	"flag"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"strings"
	"time"

	_ "github.com/mattn/go-sqlite3"
)

var infoLog *log.Logger
var errorLog *log.Logger
var verbose *bool

func updateRoutes(db *sql.DB) {
	type Data struct {
		LastUpdate string  `json:"lastUpdate"`
		Routes     []Route `json:"routes"`
	}
	type ResponseData map[string]Data

	url := "https://ckan.multimediagdansk.pl/dataset/c24aa637-3619-4dc2-a171-a23eec8f2172/resource/22313c56-5acf-41c7-a5fd-dc5dc72b3851/download/routes.json"
	resp, err := http.Get(url)
	if err != nil {
		errorLog.Println("Error making get request to update routes", err.Error())
		return
	}
	defer resp.Body.Close()

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		errorLog.Println("Error reading response body while updating routes", err.Error())
		return
	}

	var responseData ResponseData
	err = json.Unmarshal(body, &responseData)
	if err != nil {
		errorLog.Println("Error unmarshaling JSON while updating routes", err.Error())
		return
	}

	stmt, err := db.Prepare("UPDATE Route SET agencyId = ?, shortName = ?, longName = ?, activationDate = ?, routeType = ? WHERE routeId = ?")
	if err != nil {
		errorLog.Println("Error preparing SQL statement for route insertion", err.Error())
		return
	}
	defer stmt.Close()

	/* for some reason the dataset returns duplicates sometimes,
	 * so it's fine if it errors on failed constraint unique
	 * since we don't want them in the database
	 */
	for _, data := range responseData {
		for _, route := range data.Routes {
			_, err := stmt.Exec(route.AgencyId, route.RouteShortName, route.RouteLongName, route.ActivationDate, route.RouteType, route.RouteId)
			if err != nil && !strings.Contains(err.Error(), "UNIQUE constraint failed") {
				errorLog.Println("Error executing query for route insertion", err.Error())
			}
		}
	}
	myInfoLog("UPDATED:\tRoute")
}

func updateBuses(db *sql.DB) {
	type Data struct {
		LastUpdate string `json:"lastUpdate"`
		Vehicles   []Bus  `json:"vehicles"`
	}

	url := "https://ckan2.multimediagdansk.pl/gpsPositions?v=2"
	resp, err := http.Get(url)
	if resp == nil || resp.Body == nil {
		errorLog.Println("Nil pointer dereference in HTTP response for buses")
		return
	}
	if err != nil {
		errorLog.Println("Error making get request to update buses", err.Error())
		return
	}
	defer resp.Body.Close()

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		errorLog.Println("Error reading response body while updating buses", err.Error())
		return
	}

	var responseData Data
	err = json.Unmarshal(body, &responseData)
	if err != nil {
		errorLog.Println("Error unmarshaling JSON while updating buses", err.Error())
		return
	}

	// duplicates again but it doesn't really matter for now since we just update
	stmt, err := db.Prepare("UPDATE Bus SET gen = ?, routeShortName = ?, tripId = ?, routeId = ?, headsign = ?, vehicleService = ?, vehicleId = ?, speed = ?, direction = ?, delay = ?, scheduledTripStartTime = ?, lat = ?, lon = ?, gpsQuality = ? WHERE vehicleCode = ? ")
	if err != nil {
		errorLog.Println("Error preparing SQL statement for bus insertion", err.Error())
		return
	}
	defer stmt.Close()

	for _, bus := range responseData.Vehicles {
		_, err := stmt.Exec(bus.Generated, bus.RouteShortName, bus.TripId, bus.RouteId, bus.Headsign, bus.VehicleService, bus.VehicleId, bus.Speed, bus.Direction, bus.Delay, bus.ScheduledTripStartTime, bus.Lat, bus.Lon, bus.GpsQuality, bus.VehicleCode)
		if err != nil {
			errorLog.Println("Error executing query for bus insertion", err.Error())
		}
	}
	myInfoLog("UPDATED:\tBus")
}

func updateInfo(db *sql.DB) {
	type Data struct {
		Results []Vehicle `json:"results"`
	}

	url := "https://files.cloudgdansk.pl/d/otwarte-dane/ztm/baza-pojazdow.json?v=2"
	resp, err := http.Get(url)
	if err != nil {
		errorLog.Println("Error making get request to update vehicles info", err.Error())
		return
	}
	defer resp.Body.Close()

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		errorLog.Println("Error reading response body while updating vehicles info", err.Error())
		return
	}

	var responseData Data
	err = json.Unmarshal(body, &responseData)
	if err != nil {
		errorLog.Println("Error unmarshaling JSON while updating vehicles info", err.Error())
		return
	}

	stmt, err := db.Prepare("UPDATE Vehicle SET Photo = ?, Carrier = ?, TransportationType = ?, VehicleCharacteristics = ?, Bidirectional = ?, HistoricVehicle = ?, Length = ?, Brand = ?, Model = ?, ProductionYear = ?, Seats = ?, StandingPlaces = ?, AirConditioning = ?, Monitoring = ?, InternalMonitor = ?, FloorHeight = ?, KneelingMechanism = ?, WheelchairsRamp = ?, USB = ?, VoiceAnnouncements = ?, AED = ?, BikeHolders = ?, TicketMachine = ?, Patron = ?, URL = ?, PassengersDoors = ? WHERE vehicleCode = ?")
	if err != nil {
		errorLog.Println("Error preparing SQL statement for vehicle insertion", err.Error())
		return
	}
	defer stmt.Close()

	for _, vehicle := range responseData.Results {
		_, err := stmt.Exec(vehicle.Photo, vehicle.Carrier, vehicle.TransportationType, vehicle.VehicleCharacteristics, vehicle.Bidirectional, vehicle.HistoricVehicle, vehicle.Length, vehicle.Brand, vehicle.Model, vehicle.ProductionYear, vehicle.Seats, vehicle.StandingPlaces, vehicle.AirConditioning, vehicle.Monitoring, vehicle.InternalMonitor, vehicle.FloorHeight, vehicle.KneelingMechanism, vehicle.WheelchairsRamp, vehicle.USB, vehicle.VoiceAnnouncements, vehicle.AED, vehicle.BikeHolders, vehicle.TicketMachine, vehicle.Patron, vehicle.URL, vehicle.PassengersDoors, vehicle.VehicleCode)
		if err != nil {
			errorLog.Println("Error executing query for vehicle insertion", err.Error())
		}
	}
	myInfoLog("UPDATED:\tInfo")
}

func myInfoLog(text string) {
	if *verbose {
		infoLog.Println(text)
	}
}

func main() {

	refreshRate := flag.Int("n", 5, "How often (in seconds) should requests to API be made")
	dbFile := flag.String("d", "analytics.sqlite", "SQLite database file")
	verbose = flag.Bool("v", false, "SQLite database file")
	flag.Parse()

	infoLog = log.New(os.Stdout, "INFO\t", log.Ldate|log.Ltime)
	errorLog = log.New(os.Stderr, "ERROR\t", log.Ldate|log.Ltime|log.Lshortfile)

	db, err := sql.Open("sqlite3", *dbFile)
	if err != nil {
		panic("Failed to connect to database " + err.Error())
	}
	myInfoLog("Connected to SQLite database successfully")
	defer db.Close()

	infoLog.Println("Starting fetcher, type 'q' to exit")
	go func() {
		i := 0
		for {
			if i%100 == 0 {
				updateInfo(db)
				time.Sleep(time.Second * time.Duration(*refreshRate))
				updateRoutes(db)
			}
			updateBuses(db)
			time.Sleep(time.Second * time.Duration(*refreshRate))
		}
	}()

	input := ""
	for input != "q" {
		fmt.Scanf("%s", &input)
	}
	infoLog.Println("Exiting")
}
