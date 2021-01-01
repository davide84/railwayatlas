<?php
  
$host = "localhost";
$dbname = "railway_atlas";
$username = "atlas";
$password = "Showmethemaps!";
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

$nl = "<br>\n";

function getIdFromName($conn, $name) {
    $id_return = null;
    $query = "SELECT id FROM maps_objects WHERE Name='" . $name . "';";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $id_return = $id;
    }
    return $id_return;
}

$query = "SELECT * FROM maps_stretches;";
$stmt = $conn->prepare($query);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    extract($row);
    $name = str_replace("Mestre", "Venezia Mestre", $name);
    $name = str_replace("Venezia", "Venezia Santa Lucia", $name);
    $name = str_replace("Castelfranco", "Castelfranco Veneto", $name);
    $name = str_replace("", "", $name);
    $name = str_replace("", "", $name);
    $name = str_replace("", "", $name);
    $name = str_replace("", "", $name);
    $name = str_replace("", "", $name);
    $name = str_replace("", "", $name);
    $names = explode(' - ', $name);
    if (count($names) < 2) {
        echo "Dati insufficienti: " . $name . $nl;
        continue;
    }
    $id_from = getIdFromName($conn, $names[0]);
    if (is_null($id_from)) { 
        echo "Impossibile ricavare id stazione partenza: " . $names[0] . $nl;
        continue;
    }
    $id_to = getIdFromName($conn, $names[1]);
    if (is_null($id_to)) { 
        echo "Impossibile ricavare id stazione arrivo: " . $names[1] . $nl;
        continue;
    }
    #echo $name . " :: " . $id_from . " -> " . $id_to . $nl;
}

?>
