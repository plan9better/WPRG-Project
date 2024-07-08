<?php
class Bus
{
    private $routeName;
    private $lat;
    private $lon;
    private $speed;
    private $destination;

    public function __construct($routeName, $lat, $lon, $speed, $destination)
    {
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
        include "/var/www/html/project/secret/MYSQL.php";

        $conn = new mysqli(
            $MYSQL_SERVERNAME,
            $MYSQL_USERNAME,
            $MYSQL_PASSWORD,
            $MYSQL_DBNAME
        );
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql =
            "select * from Bus inner join Vehicles on Bus.vehicleCode=Vehicles.VehicleCode;";
        $result = $conn->query($sql);

        $active = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($active, $row);
            }
        }
        $conn->close();

        return $active;
    }
    public static function getActive()
    {
        include "/var/www/html/project/secret/MYSQL.php";

        $conn = new mysqli(
            $MYSQL_SERVERNAME,
            $MYSQL_USERNAME,
            $MYSQL_PASSWORD,
            $MYSQL_DBNAME
        );
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT routeShortName FROM Bus";
        $result = $conn->query($sql);

        $active = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($active, $row["routeShortName"]);
            }
        }
        $conn->close();

        return $active;
    }
    public static function getUniqueActive()
    {
        $unique = [];
        $active = Bus::getActive();
        foreach ($active as $bus) {
            if (isset($unique[$bus])) {
                $unique[$bus] += 1;
            } else {
                $unique[$bus] = 1;
            }
        }
        return $unique;
    }
    public static function getVehiclesByRoute($route)
    {
        include "/var/www/html/project/secret/MYSQL.php";

        $conn = new mysqli(
            $MYSQL_SERVERNAME,
            $MYSQL_USERNAME,
            $MYSQL_PASSWORD,
            $MYSQL_DBNAME
        );
        // Check connection
        if ($conn->connect_error) {
            echo "Connection failed: " . $conn->connect_error;
        }
        $sql = "SELECT * FROM Bus where routeShortName = $route";
        $result = $conn->query($sql);

        $vehicles = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($vehicles, $row);
            }
        }
        $conn->close();

        return $vehicles;
    }
}
