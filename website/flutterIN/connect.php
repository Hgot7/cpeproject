<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$db_name = "zmlszwhh_cpepms";
$db_user = "zmlszwhh_cpepms";
$db_pass = "Nanbowin_2030";
$db_host = "localhost";

global $conn;
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
