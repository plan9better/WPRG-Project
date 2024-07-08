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

	_ "github.com/go-sql-driver/mysql"
)

var infoLog *log.Logger
var errorLog *log.Logger

func updateRoutes(db *sql.DB) {
	// Define the Route struct
	type Route struct {
		RouteId        int    `json:"routeId"`
		AgencyId       int    `json:"agencyId"`
		RouteShortName string `json:"routeShortName"`
		RouteLongName  string `json:"routeLongName"`
		ActivationDate string `json:"activationDate"`
		RouteType      string `json:"routeType"`
	}

	// Define the Data struct which will hold the lastUpdate and routes
	type Data struct {
		LastUpdate string  `json:"lastUpdate"`
		Routes     []Route `json:"routes"`
	}

	// Define the main struct which holds the date keys
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
		errorLog.Println("Error reading resonse body while updating routes", err.Error())
		return
	}

	var responseData ResponseData

	err = json.Unmarshal(body, &responseData)
	if err != nil {
		errorLog.Println("Error unmarhsaling JSON while updating routes", err.Error())
		return
	}

	query := `INSERT INTO Routes (routeId, agencyId, shortName, longName, activationDate, routeType) VALUES `
	values := make([]string, 0)
	for _, data := range responseData {
		for _, route := range data.Routes {
			value := fmt.Sprintf("(%d, %d, '%s', '%s', '%s', '%s')", route.RouteId, route.AgencyId, route.RouteShortName, route.RouteLongName, route.ActivationDate, route.RouteType)
			values = append(values, value)
		}
	}

	joinedValues := strings.Join(values, ",")
	joinedValues = strings.Trim(joinedValues, ",")
	query += joinedValues

	db.Exec("DELETE FROM Routes WHERE 1=1")
	result, err := db.Exec(query)
	if err != nil {
		errorLog.Println("Error executin query to update routes", err.Error())
	}
	infoLog.Println("Inserting routes: ", result)
}

func updateBuses(db *sql.DB) {
	type Bus struct {
		Generated              string  `json:"generated"`
		RouteShortName         string  `json:"routeShortName"`
		TripId                 int     `json:"tripId"`
		RouteId                int     `json:"routeId"`
		Headsign               string  `json:"headsign"`
		VehicleCode            string  `json:"vehicleCode"`
		VehicleService         string  `json:"vehicleService"`
		VehicleId              int     `json:"vehicleId"`
		Speed                  int     `json:"speed"`
		Direction              int     `json:"Direction"`
		Delay                  int     `json:"Delay"`
		ScheduledTripStartTime string  `json:"scheduledTripStartTime"`
		Lat                    float64 `json:"lat"`
		Lon                    float64 `json:"lon"`
		GpsQuality             int     `json:"gpsQuality"`
	}
	type Data struct {
		LastUpdate string `json:"lastUpdate"`
		Vehicles   []Bus  `json:"vehicles"`
	}

	url := "https://ckan2.multimediagdansk.pl/gpsPositions?v=2"

	resp, err := http.Get(url)
	if err != nil {
		errorLog.Println("Error making get request to update buses", err.Error())
		return
	}
	defer resp.Body.Close()

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		errorLog.Println("Error reading resonse body while updating buses", err.Error())
		return
	}

	var responseData Data
	err = json.Unmarshal(body, &responseData)
	if err != nil {
		errorLog.Println("Error unmarhsaling JSON while updating buses", err.Error())
		return
	}

	query := `INSERT INTO Bus (gen, routeShortName, tripId, routeId, headsign, vehicleCode, vehicleService, vehicleId, speed, direction, delay, scheduledTripStartTime, lat, lon, gpsQuality) VALUES `
	values := make([]string, 0)
	for _, bus := range responseData.Vehicles {
		value := fmt.Sprintf("('%s', '%s', '%d', '%d', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%f', '%f', '%d')", bus.Generated, bus.RouteShortName, bus.TripId, bus.RouteId, bus.Headsign, bus.VehicleCode, bus.VehicleService, bus.VehicleId, bus.Speed, bus.Direction, bus.Delay, bus.ScheduledTripStartTime, bus.Lat, bus.Lon, bus.GpsQuality)
		values = append(values, value)
	}

	joinedValues := strings.Join(values, ",")
	joinedValues = strings.Trim(joinedValues, ",")
	query += joinedValues

	db.Exec("DELETE FROM Bus WHERE 1=1")
	result, err := db.Exec(query)
	if err != nil {
		errorLog.Println("Error executin query to update routes", err.Error())
	}
	infoLog.Println("Result of insert query: ", result)
}

func updateInfo(db *sql.DB) {
	type Vehicle struct {
		Photo                  string  `json:"photo"`
		VehicleCode            string  `json:"vehicleCode"`
		Carrier                string  `json:"carrier"`
		TransportationType     string  `json:"transportationType"`
		VehicleCharacteristics string  `json:"vehicleCharacteristics"`
		Bidirectional          bool    `json:"bidirectional"`
		HistoricVehicle        bool    `json:"historicVehicle"`
		Length                 float64 `json:"length"`
		Brand                  string  `json:"brand"`
		Model                  string  `json:"model"`
		ProductionYear         int     `json:"productionYear"`
		Seats                  int     `json:"seats"`
		StandingPlaces         int     `json:"standingPlaces"`
		AirConditioning        bool    `json:"airConditioning"`
		Monitoring             bool    `json:"monitoring"`
		InternalMonitor        bool    `json:"internalMonitor"`
		FloorHeight            string  `json:"floorHeight"`
		KneelingMechanism      bool    `json:"kneelingMechanism"`
		WheelchairsRamp        bool    `json:"wheelchairsRamp"`
		USB                    bool    `json:"usb"`
		VoiceAnnouncements     bool    `json:"voiceAnnouncements"`
		AED                    bool    `json:"aed"`
		BikeHolders            int     `json:"bikeHolders"`
		TicketMachine          bool    `json:"ticketMachine"`
		Patron                 string  `json:"patron"`
		URL                    string  `json:"url"`
		PassengersDoors        int     `json:"passengersDoors"`
	}
	type Metadata struct {
		Title          string `json:"string"`
		SourceDate     string `json:"sourceDate"`
		GenerationDate string `json:"generationDate"`
		ApiUrlHeader   string `json:"apiUrlHeader"`
	}
	type Data struct {
		Metadata Metadata  `json:"metadata"`
		Count    int       `json:"count"`
		Results  []Vehicle `json:"results"`
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
		errorLog.Println("Error reading resonse body while updating veihcles info", err.Error())
		return
	}

	var responseData Data
	err = json.Unmarshal(body, &responseData)
	if err != nil {
		errorLog.Println("Error unmarhsaling JSON while updating buses", err.Error())
		return
	}

	query := `INSERT INTO Vehicles (Photo, VehicleCode, Carrier, TransportationType, VehicleCharacteristics, Bidirectional, HistoricVehicle, Length, Brand, Model, ProductionYear, Seats, StandingPlaces, AirConditioning, Monitoring, InternalMonitor, FloorHeight, KneelingMechanism, WheelchairsRamp, USB, VoiceAnnouncements, AED, BikeHolders, TicketMachine, Patron, URL, PassengersDoors) VALUES `
	values := make([]string, 0)
	for _, vehicle := range responseData.Results {
		value := fmt.Sprintf("('%s', '%s', '%s', '%s', '%s', %v, %v, '%f', '%s', '%s', '%d', '%d', '%d', %v, %v, %v, '%s', %v, %v, %v, %v, %v, '%d', %v, '%s', '%s', '%d')",
			vehicle.Photo,
			vehicle.VehicleCode,
			vehicle.Carrier,
			vehicle.TransportationType,
			vehicle.VehicleCharacteristics,
			vehicle.Bidirectional,
			vehicle.HistoricVehicle,
			vehicle.Length,
			vehicle.Brand,
			// vehicle.Model,
			strings.ReplaceAll(vehicle.Model, "'", ""),
			vehicle.ProductionYear,
			vehicle.Seats,
			vehicle.StandingPlaces,
			vehicle.AirConditioning,
			vehicle.Monitoring,
			vehicle.InternalMonitor,
			vehicle.FloorHeight,
			vehicle.KneelingMechanism,
			vehicle.WheelchairsRamp,
			vehicle.USB,
			vehicle.VoiceAnnouncements,
			vehicle.AED,
			vehicle.BikeHolders,
			vehicle.TicketMachine,
			vehicle.Patron,
			vehicle.URL,
			vehicle.PassengersDoors,
		)
		values = append(values, value)
	}

	joinedValues := strings.Join(values, ",")
	joinedValues = strings.Trim(joinedValues, ",")
	query += joinedValues
	db.Exec(`DELETE FROM Vehicles WHERE 1=1;`)
	result, err := db.Exec(query)
	if err != nil {
		errorLog.Println("Error executin query to update vehicle info", err.Error())
	}
	infoLog.Println("Result of insert query: ", result)
}

func main() {
	// Logging
	infoLog = log.New(os.Stdout, "INFO\t", log.Ldate|log.Ltime)
	errorLog = log.New(os.Stderr, "ERROR\t", log.Ldate|log.Ltime|log.Lshortfile)

	// Flags
	refreshRate := flag.Int("n", 5, "How often (in seconds) should requests to API be made")
	uname := flag.String("u", "", "MySQL username")
	pwd := flag.String("p", "", "MySQL password")
	flag.Parse()
	fmt.Println(*uname, *pwd)
	if *uname == "" || *pwd == "" {
		panic("Please enter mysql username and password")
	}

	// DB connection
	db, err := sql.Open("mysql", fmt.Sprintf("%s:%s@tcp(127.0.0.1:3306)/bus", *uname, *pwd))
	if err != nil {
		panic("Failed to connect to database " + err.Error())
	}
	defer db.Close()
	infoLog.Println("Connected to database successfully")

	// Update buses
	go func() {
		for {
			updateBuses(db)
			time.Sleep(time.Second * time.Duration(*refreshRate))
		}
	}()
	time.Sleep(time.Duration(*refreshRate / 2))
	go func() {
		for {
			updateRoutes(db)
			time.Sleep(time.Second * time.Duration(*refreshRate))
		}
	}()
	go func(){
		for{
			updateInfo(db)
			time.Sleep(time.Second * 6);
		}
	}()

	input := ""
	for input != "q" {
		fmt.Scanf("%s", &input)
	}
}
