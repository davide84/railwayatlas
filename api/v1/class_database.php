<?php
class Database {
  
    private $host = "localhost";
    private $dbname = "railway_atlas";
    private $username = "atlas";
    private $password = "Showmethemaps!";
    private $conn = null;
    private $last_err_msg = "";

    public function isConnected() {
        return !is_null($this->conn);
    }

    public function getLastErrorMessage() {
        return $this->last_err_msg;
    }

    public function connect() {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            $this->last_err_msg = $e->getMessage();
            $this->conn = null;
            return false;
        }
        return true;
    }

    public function getStations($nelat, $nelng, $swlat, $swlng) {
        $query = "SELECT * FROM maps_objects WHERE (type=1 OR type=2)";
        if (!is_null($nelat)) { $query .= " AND lat<".$nelat; }
        if (!is_null($nelng)) { $query .= " AND lng<".$nelng; }
        if (!is_null($swlat)) { $query .= " AND lat>".$swlat; }
        if (!is_null($swlng)) { $query .= " AND lng>".$swlng; }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stations_arr=array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // extract row: this will make $row['name'] to just $name only
            extract($row);
            $station = array(  
                "id" => $id,
                "name" => $name,
                "type" => $type,
                "lat" => $lat,
                "lng" => $lng
            );
            array_push($stations_arr, $station);
        }
        return $stations_arr;
    }
 
} // end Database class

?>
