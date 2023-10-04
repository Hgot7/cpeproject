<?php
require "connect.php";

if (!$conn) {
    echo "connection error";
} else {
    // Set UTF-8 encoding for the connection
    mysqli_set_charset($conn, "utf8");

    // Query to get data from the 'appointST' table
    $sql = "SELECT * FROM `appoint` WHERE (DATE_FORMAT(appoint_date, '%Y%m%d') >= CONCAT(YEAR(CURDATE()) + 543, LPAD(MONTH(CURDATE()), 2, '0'), LPAD(DAY(CURDATE()), 2, '0')))
    ORDER BY appoint_date ASC, group_id ASC ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch data into an associative array
    $result = $stmt->get_result();
    $appointT = array();
    while ($row = $result->fetch_assoc()) {
        $appointT[] = $row;
    }

    // Return JSON response with UTF-8 encoding
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($appointT, JSON_UNESCAPED_UNICODE);
}


?>
