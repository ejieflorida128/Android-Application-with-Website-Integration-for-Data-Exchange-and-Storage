<?php
/**
* DB configuration variables
*/
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASSWORD","");
define("DB_DATABASE", "pink_lunary");

class DB_Connect {
    private $conn;

    // Connecting to database
    public function connect() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }
}

$connForDatabase = mysqli_connect("localhost","root","","pink_lunary");

    
?>
