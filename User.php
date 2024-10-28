<?php
class User
{
    private $uid, $username, $session, $favs, $conn;
    private static $DB_PATH = 'analytics.sqlite';
    
    public function __construct($session){
        $this->initializeDatabase();
        $this->conn = new SQLite3(self::$DB_PATH, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        if (!$this->conn) {
            die("Connection failed: " . $this->conn->lastErrorMsg());
        }

        $this->session = $session;
        $this->uid = $this->checkSession($session);
        $this->favs = $this->fetchFavs($this->uid);
    }

    public function initializeDatabase() {
        $db = new SQLite3('analytics.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        
        // Create the User table
        $db->exec('CREATE TABLE IF NOT EXISTS User (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL
        )');
        
        // Create the Sessions table
        $db->exec('CREATE TABLE IF NOT EXISTS Sessions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            userId INTEGER NOT NULL,
            token TEXT UNIQUE,
            expires DATE NOT NULL,
            FOREIGN KEY (userId) REFERENCES User(id)
        )');
        
        // Create the Favs table
        $db->exec('CREATE TABLE IF NOT EXISTS Favs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            userId INTEGER NOT NULL,
            name TEXT NOT NULL,
            type TEXT NOT NULL,
            FOREIGN KEY (userId) REFERENCES User(id)
        )');
    
        // Create the Routes table
        $db->exec('CREATE TABLE IF NOT EXISTS Routes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            routeId INTEGER,
            agencyId INTEGER NOT NULL,
            shortName TEXT NOT NULL,
            longName TEXT NOT NULL,
            activationDate DATE NOT NULL,
            routeType TEXT CHECK(routeType IN ("BUS", "TRAM", "UNKNOWN", "FERRY")) NOT NULL
        )');
    
        // Create the Bus table
        $db->exec('CREATE TABLE IF NOT EXISTS Bus (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            gen TEXT,
            routeShortName TEXT,
            tripId INTEGER,
            routeId INTEGER,
            headsign TEXT,
            vehicleCode TEXT,
            vehicleService TEXT,
            vehicleId INTEGER,
            speed INTEGER,
            direction INTEGER,
            delay INTEGER,
            scheduledTripStartTime TEXT,
            lat REAL,
            lon REAL,
            gpsQuality INTEGER
        )');
    
        // Create the ExtendedInfo table
        $db->exec('CREATE TABLE IF NOT EXISTS ExtendedInfo (
            photo TEXT,
            vehicleCode TEXT PRIMARY KEY,
            carrier TEXT,
            transportationType TEXT,
            vehicleCharacteristics TEXT,
            bidirectional INTEGER CHECK(bidirectional IN (0, 1)),
            historicVehicle INTEGER CHECK(historicVehicle IN (0, 1)),
            length REAL,
            brand TEXT,
            model TEXT,
            productionYear INTEGER,
            seats INTEGER,
            standingPlaces INTEGER,
            airConditioning INTEGER CHECK(airConditioning IN (0, 1)),
            monitoring INTEGER CHECK(monitoring IN (0, 1)),
            internalMonitor INTEGER CHECK(internalMonitor IN (0, 1)),
            floorHeight TEXT,
            kneelingMechanism INTEGER CHECK(kneelingMechanism IN (0, 1)),
            wheelchairsRamp INTEGER CHECK(wheelchairsRamp IN (0, 1)),
            usb INTEGER CHECK(usb IN (0, 1)),
            voiceAnnouncements INTEGER CHECK(voiceAnnouncements IN (0, 1)),
            aed INTEGER CHECK(aed IN (0, 1)),
            bikeHolders INTEGER,
            ticketMachine INTEGER CHECK(ticketMachine IN (0, 1)),
            patron TEXT,
            url TEXT,
            passengersDoors INTEGER
        )');
    
        // Create the Vehicles table
        $db->exec('CREATE TABLE IF NOT EXISTS Vehicles (
            Photo TEXT NOT NULL,
            VehicleCode TEXT PRIMARY KEY,
            Carrier TEXT NOT NULL,
            TransportationType TEXT NOT NULL,
            VehicleCharacteristics TEXT NOT NULL,
            Bidirectional INTEGER CHECK(Bidirectional IN (0, 1)) NOT NULL,
            HistoricVehicle INTEGER CHECK(HistoricVehicle IN (0, 1)) NOT NULL,
            Length REAL NOT NULL,
            Brand TEXT NOT NULL,
            Model TEXT NOT NULL,
            ProductionYear INTEGER NOT NULL,
            Seats INTEGER NOT NULL,
            StandingPlaces INTEGER NOT NULL,
            AirConditioning INTEGER CHECK(AirConditioning IN (0, 1)) NOT NULL,
            Monitoring INTEGER CHECK(Monitoring IN (0, 1)) NOT NULL,
            InternalMonitor INTEGER CHECK(InternalMonitor IN (0, 1)) NOT NULL,
            FloorHeight TEXT NOT NULL,
            KneelingMechanism INTEGER CHECK(KneelingMechanism IN (0, 1)) NOT NULL,
            WheelchairsRamp INTEGER CHECK(WheelchairsRamp IN (0, 1)) NOT NULL,
            USB INTEGER CHECK(USB IN (0, 1)) NOT NULL,
            VoiceAnnouncements INTEGER CHECK(VoiceAnnouncements IN (0, 1)) NOT NULL,
            AED INTEGER CHECK(AED IN (0, 1)) NOT NULL,
            BikeHolders INTEGER NOT NULL,
            TicketMachine INTEGER CHECK(TicketMachine IN (0, 1)) NOT NULL,
            Patron TEXT NOT NULL,
            URL TEXT NOT NULL,
            PassengersDoors INTEGER NOT NULL
        )');
    
        $db->close();
    }


    public function getUid(){
        return $this->uid;
    }
    
    public function getUsername(){
        return $this->username;
    }
    
    public function getFavs(){
        $this->favs = $this->fetchFavs();
        return $this->favs;
    }

    public function addFav($fav){
        $stmt = $this->conn->prepare("INSERT INTO Favs(userId, name, type) VALUES (:uid, :fav, 'Route')");
        if ($stmt === false) {
            return false;
        }
        
        $stmt->bindValue(':uid', $this->uid, SQLITE3_INTEGER);
        $stmt->bindValue(':fav', $fav, SQLITE3_TEXT);
        
        $result = $stmt->execute();
        return $result !== false;
    }

    private function checkSession($session)
    {
        // First clean expired sessions
        $stmt = $this->conn->prepare("DELETE FROM Sessions WHERE expires < date('now')");
        if ($stmt === false) {
            return false;
        }
        $result = $stmt->execute();

        // Then check current session
        $stmt = $this->conn->prepare("SELECT * FROM Sessions WHERE token = :token");
        if ($stmt === false) {
            return false;
        }
        
        $stmt->bindValue(':token', $session, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        if ($result === false) {
            return false;
        }

        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ? $row["userId"] : false;
    }

    public function deleteSession($session)
    {
        $stmt = $this->conn->prepare("DELETE FROM Sessions WHERE token = :token");
        if ($stmt === false) {
            return false;
        }
        
        $stmt->bindValue(':token', $session, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result !== false;
    }

    public function fetchFavs(){
        if (!$this->uid) {
            return [];
        }

        $stmt = $this->conn->prepare("SELECT type, name FROM Favs WHERE userId = :uid");
        if ($stmt === false) {
            return [];
        }
        
        $stmt->bindValue(':uid', $this->uid, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        if ($result === false) {
            return [];
        }

        $favs = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            if ($row === false) {
                break;
            }
            array_push($favs, $row);
        }
        return $favs;
    }

    public static function checkUserExists($username): bool
    {  
        $db = new SQLite3(self::$DB_PATH, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        if (!$db) {
            return false;
        }
        
        $stmt = $db->prepare("SELECT * FROM User WHERE username = :username");
        if ($stmt === false) {
            $db->close();
            return false;
        }
        
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        if ($result === false) {
            $db->close();
            return false;
        }
        
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $db->close();
        
        return $row !== false;
    }

    public static function addUser($username, $password)
    {
        $db = new SQLite3(self::$DB_PATH, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        if (!$db) {
            return false;
        }
        
        $stmt = $db->prepare("INSERT INTO User(username, password) VALUES (:username, :password)");
        if ($stmt === false) {
            $db->close();
            return false;
        }
        
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $password, SQLITE3_TEXT);
        
        $result = $stmt->execute();
        $success = $result !== false;
        
        $db->close();
        return $success;
    }
    
    public static function login($username, $password)
    {
        $db = new SQLite3(self::$DB_PATH, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        if (!$db) {
            return null;
        }
        
        $stmt = $db->prepare("SELECT * FROM User WHERE username = :username AND password = :password");
        if ($stmt === false) {
            $db->close();
            return null;
        }
        
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $password, SQLITE3_TEXT);
        
        $result = $stmt->execute();
        if ($result === false) {
            $db->close();
            return null;
        }
        
        $user = $result->fetchArray(SQLITE3_ASSOC);
        if (!$user) {
            $db->close();
            return null;
        }

        // Check if multiple users exist with same credentials
        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row !== false) {
            error_log("Multiple users found with same credentials for username: " . $username);
        }

        $id = $user["id"];
        $token = uniqid();
        $user["session"] = $token;

        $expires = new DateTime("now");
        $expires->add(new DateInterval("P7D"));
        $expiresstr = $expires->format("Y-m-d");
        $user["expires"] = $expires;

        // Insert new session
        $stmt = $db->prepare("INSERT INTO Sessions (userId, token, expires) VALUES (:id, :token, :expires)");
        if ($stmt === false) {
            $db->close();
            return null;
        }
        
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':token', $token, SQLITE3_TEXT);
        $stmt->bindValue(':expires', $expiresstr, SQLITE3_TEXT);
        
        $result = $stmt->execute();
        if ($result === false) {
            $db->close();
            return null;
        }
        
        $db->close();
        return $user;
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}