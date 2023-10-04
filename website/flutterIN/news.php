<?php
require "connect.php";
header('Content-Type: application/json; charset=utf-8');
if (!$conn) {
    echo "connection error";
} else {
    // Set UTF-8 encoding for the connection
    mysqli_set_charset($conn, "utf8");

    // Query to get data from the 'news' table
    $sql = "SELECT * FROM `news` WHERE year = (SELECT year FROM `defaultsystem` WHERE default_system_id = ?) and term = (SELECT term FROM `defaultsystem` WHERE default_system_id = ?)
    ORDER BY news_date DESC ";


    $stmt = $conn->prepare($sql);
    $defaultSystemId = 1;
    $stmt->bind_param('ii', $defaultSystemId, $defaultSystemId);
    $stmt->execute();

    // Fetch data into an associative array
    $result = $stmt->get_result();
    $newsList = array();
    while ($row = $result->fetch_assoc()) {
        $newsList[] = $row;
    }

    // Return JSON response with UTF-8 encoding
   
    echo json_encode($newsList, JSON_UNESCAPED_UNICODE);
}

?>
