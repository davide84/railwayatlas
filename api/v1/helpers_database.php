<?php
include 'class_database.php';

function db_connect_or_die_500() {
    $db = new Database();
    $db->connect();
    if (!$db->connect()) {
        http_response_code(500);
        echo json_encode(
            array("message" => "Can't connect to database: " . $db->getLastErrorMessage())
        );
        die();
    }
    return $db;
}

?>
