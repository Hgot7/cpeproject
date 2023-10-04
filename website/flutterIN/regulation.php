<?php
require "connect.php";

if (!$conn) {
    echo "connection error";
} else {
    // Set UTF-8 encoding for the connection
    mysqli_set_charset($conn, "utf8");

    // Query to get data from the 'regulation' table
    $sql = "SELECT * FROM `regulation` 
        WHERE year = (SELECT year FROM `defaultsystem` WHERE default_system_id = ?) 
        AND term = (SELECT term FROM `defaultsystem` WHERE default_system_id = ?)
        ORDER BY regulation_text ";

    $stmt = $conn->prepare($sql);
    $defaultSystemId = 1;
    $stmt->bind_param('ii', $defaultSystemId, $defaultSystemId);
    $stmt->execute();

    // Fetch data into an associative array
    $result = $stmt->get_result();
    $rulesList = array();
    while ($row = $result->fetch_assoc()) {
        $rulesList[] = $row;
    }

    // Return JSON response with UTF-8 encoding
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rulesList, JSON_UNESCAPED_UNICODE);
}

?>
