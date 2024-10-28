<?php
class Bus
{
    private $routeName, $lat, $lon, $speed, $destination, $conn;
    private static $DB_PATH = 'analytics.sqlite';

    public function __construct($routeName, $lat, $lon, $speed, $destination)
    {
        $this->conn = new SQLite3(self::$DB_PATH, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        if (!$this->conn) {
            die("Connection failed: " . $this->conn->lastErrorMsg());
        }

        $this->routeName = $routeName;
        $this->lat = $lat;
        $this->lon = $lon;
        $this->speed = $speed;
        $this->destination = $destination;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new Exception("Property '$name' does not exist");
        }
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            throw new Exception("Property '$name' does not exist");
        }
    }

    public static function getInfo()
    {
        $db = new SQLite3(self::$DB_PATH, SQLITE3_OPEN_READONLY);
        if (!$db) {
            return [];
        }

        $sql = "SELECT * FROM Bus INNER JOIN Vehicles ON Bus.vehicleCode = Vehicles.VehicleCode";
        $result = $db->query($sql);
        $active = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $active[] = $row;
        }

        $db->close();
        return $active;
    }

    public static function getActive()
    {
        $db = new SQLite3(self::$DB_PATH, SQLITE3_OPEN_READONLY);
        if (!$db) {
            return [];
        }

        $sql = "SELECT routeShortName FROM Bus";
        $result = $db->query($sql);
        $active = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $active[] = $row['routeShortName'];
        }

        $db->close();
        return $active;
    }

    public static function getUniqueActive()
    {
        $active = self::getActive();
        $unique = array_count_values($active);
        return $unique;
    }

    public static function getVehiclesByRoute($route)
    {
        $db = new SQLite3(self::$DB_PATH, SQLITE3_OPEN_READONLY);
        if (!$db) {
            return [];
        }

        $stmt = $db->prepare("SELECT * FROM Bus WHERE routeShortName = :route");
        $stmt->bindValue(':route', $route, SQLITE3_TEXT);
        $result = $stmt->execute();
        $vehicles = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $vehicles[] = $row;
        }

        $db->close();
        return $vehicles;
    }

    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
