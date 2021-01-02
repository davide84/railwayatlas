<?php
include '../helpers.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$db = db_connect_or_die_500();

die_500_if(!isset($_POST['id']), "Invalid input (missing field 'id')");

$id = $_POST['id'];
if (isset($_POST['name'])) { $name = $_POST['name']; } else { $name = null; }

die_500_if(!$db->updateObject($id, $name), "Unspecified error");

http_response_code(200);
echo json_encode(array("message" => "OK"));

?>
