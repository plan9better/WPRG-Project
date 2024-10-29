package main

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

type Route struct {
	RouteId        int    `json:"routeId"`
	AgencyId       int    `json:"agencyId"`
	RouteShortName string `json:"routeShortName"`
	RouteLongName  string `json:"routeLongName"`
	ActivationDate string `json:"activationDate"`
	RouteType      string `json:"routeType"`
}
