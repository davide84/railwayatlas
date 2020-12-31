<?php
include '../class_database.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$db = new Database();
$db->connect();
if (!$db->connect()) {
    http_response_code(500);
    echo json_encode(
        array("message" => "Can't connect to database: " . $db->getLastErrorMessage())
    );
    die();
}

http_response_code(200);
$nelat = null;
$nelng = null;
$swlat = null;
$swlng = null;
if (isset($_GET['nelat'])) { $nelat = $_GET['nelat']; }
if (isset($_GET['nelng'])) { $nelng = $_GET['nelng']; }
if (isset($_GET['swlat'])) { $swlat = $_GET['swlat']; }
if (isset($_GET['swlng'])) { $swlng = $_GET['swlng']; }
echo json_encode($db->getStations($nelat, $nelng, $swlat, $swlng));

?>
