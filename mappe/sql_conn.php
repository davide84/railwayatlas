<?php
$conn = mysqli_connect('localhost', 'atlas', 'Showmethemaps!');
if (!$conn) {
    die('Could not connect: ' . mysqli_error());
}
// selezione database
$my_db = "railway_atlas";
$database = mysqli_select_db($conn, $my_db);
// selects correct character set
//mysqli_unbuffered_query($conn, "SET NAMES utf8;");
?>
