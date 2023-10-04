<?php
require "connect.php";

if (!$conn) {
    echo "connection error";
    exit; // จบการทำงานทันทีถ้าไม่สามารถเชื่อมต่อกับฐานข้อมูลได้
}

// Set UTF-8 encoding
mysqli_set_charset($conn, "utf8");

// Query to get data from the 'timetest' table
$sql = "SELECT * FROM `timetest`
        WHERE DATE_FORMAT(timeTest_date, '%Y%m%d') >= CONCAT(YEAR(CURDATE()) + 543, LPAD(MONTH(CURDATE()), 2, '0'), LPAD(DAY(CURDATE()), 2, '0'))
        ORDER BY timeTest_date ASC, start_time ASC";

$result = $conn->query($sql);

if ($result === false) {
    die("Query execution error: " . mysqli_error($conn));
}

// Fetch data into an associative array
$timeTest = array();
while ($row = $result->fetch_assoc()) {
    $timeTest[] = $row;
}

// Return JSON response with UTF-8 encoding
header('Content-Type: application/json; charset=utf-8');
echo json_encode($timeTest, JSON_UNESCAPED_UNICODE);

// Close the database connection
$conn->close();
?>
