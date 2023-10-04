<?php
require "connect.php";

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

// Check if 'student_id' exists in the POST request
if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // Set UTF-8 encoding
    mysqli_set_charset($conn, "utf8");

    $sql = "SELECT * FROM `student` WHERE student_id = ?";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        die("Prepare statement error: " . mysqli_error($conn));
    }

    // Bind the student_id parameter to the SQL statement
    mysqli_stmt_bind_param($stmt, "s", $student_id);

    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        // Get the result set
        $result = mysqli_stmt_get_result($stmt);

        // Fetch data into an associative array
        $studentData = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $studentData[] = $row;
        }

        // Return JSON response with UTF-8 encoding
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($studentData, JSON_UNESCAPED_UNICODE);
    } else {
        die("Query execution error: " . mysqli_error($conn));
    }

} else {
    // Handle the case when 'student_id' is not provided in the POST request
    echo "Student ID is missing in the POST request.";
}
?>
