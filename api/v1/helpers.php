<?php
include 'class_database.php';

function die_500_if($condition, $message) {
    if ($condition) {
        http_response_code(500);
        echo json_encode(array("message" => $message));
        die();
    }
}

function db_connect_or_die_500() {
    $db = new Database();
    $db->connect();
    die_500_if(!$db->connect(), "Can't connect to database: " . $db->getLastErrorMessage());
    return $db;
}

?>
