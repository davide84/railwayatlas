<?php
include 'class_database.php';

$db = new Database();
$db->connect();
if ($db->isConnected()) {
    echo "Connected Successfully";
}
?>
