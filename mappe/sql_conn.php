<?php
$conn = mysql_connect('62.149.150.109', 'Sql315398', '0b223a98');
if (!$conn) {
    die('Could not connect: ' . mysql_error());
}
// selezione database
$my_db = "Sql315398_5";
$database = mysql_select_db($my_db, $conn);
// selects correct character set
mysql_unbuffered_query("SET NAMES utf8;",$conn);
?>