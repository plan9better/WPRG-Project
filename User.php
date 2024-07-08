<?php
class User
{
    
    private $uid, $username, $session, $favs, $conn;
    public function __construct($session){
        include "/var/www/html/project/secret/MYSQL.php";
        $this->conn = new mysqli(
            $MYSQL_SERVERNAME,
            $MYSQL_USERNAME,
            $MYSQL_PASSWORD,
            $MYSQL_DBNAME
        );
        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        $this->session = $session;
        $this->uid = $this->checkSession($session);
        $this->favs = $this->fetchFavs($this->uid);
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
        $sql = "INSERT INTO Favs(userId, name, type) VALUES($this->uid, '$fav', 'Route');";
        $result = $this->conn->query($sql);
    }

    private function checkSession($session)
    {
        $sql = "DELETE FROM Sessions WHERE expires < CURRENT_DATE();";
        $result = $this->conn->query($sql);
        $sql = "SELECT * FROM Sessions WHERE token='$session';";
        $result = $this->conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            return $row["userId"];
        }
        return false;
    }
    public function deleteSession($session)
    {
        $sql = "DELETE FROM Sessions WHERE token='$session';";
        $result = $this->conn->query($sql);
    }
    public function fetchFavs(){
        $sql = "SELECT type,name FROM Favs WHERE userId='$this->uid';";
        $result = $this->conn->query($sql);
        $favs = [];
        while ($row = $result->fetch_assoc()) {
            array_push($favs, $row);
        }

        return $favs;
    }
    public static function checkUserExists($username): bool
    {  include "/var/www/html/project/secret/MYSQL.php";
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
        $sql = "SELECT * FROM User WHERE username='$username';";
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            return true;
        }
        return false;
    }
    public static function addUser($username, $password)
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
        $sql = "INSERT INTO User(username, password) VALUES('$username', '$password');";
        $result = $conn->query($sql);
        return true;
    }
    
    public static function login($username, $password)
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
        $sql = "SELECT * FROM User WHERE username='$username' AND password='$password';";
        $result = $conn->query($sql);

        $user = [];
        if ($result->num_rows > 0) {
            if ($result->num_rows > 1) {
                echo "Found 2 users with matching credentials";
            }
            $user = $result->fetch_assoc();

            $id = $user["id"];
            $token = uniqid();
            $user["session"] = $token;

            $expires = new DateTime("now");
            // Plus 7 days
            $expires->add(new DateInterval("P7D"));
            $expiresstr = $expires->format("Y-m-d");
            $user["expires"] = $expires;

            $result = $conn->query(
                "INSERT INTO Sessions (userId, token, expires) VALUES ($id, '$token', '$expiresstr');"
            );
        } else {
            return null;
        }
        $conn->close();

        return $user;
    }
}
