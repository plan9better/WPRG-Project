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
    public static function getActive()
    {
        include "secret/MYSQL.php";

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
        $sql = "SELECT name FROM Active";
        $result = $conn->query($sql);

        $active = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($active, $row["name"]);
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
}
