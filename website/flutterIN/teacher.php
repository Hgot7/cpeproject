<?php
require "connect.php";

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

// Check if 'teacher_id' exists in the POST request
if (isset($_POST['teacher_id'])) {
    $teacher_id = $_POST['teacher_id'];

    // Set UTF-8 encoding
    mysqli_set_charset($conn, "utf8");

    $sql = "SELECT * FROM `teacher` WHERE teacher_id = ?";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        die("Prepare statement error: " . mysqli_error($conn));
    }

    // Bind the teacher_id parameter to the SQL statement
    mysqli_stmt_bind_param($stmt, "s", $teacher_id);

    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        // Get the result set
        $result = mysqli_stmt_get_result($stmt);

        // Fetch data into an associative array
        $teacherData = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $teacherData[] = $row;
        }

        // Return JSON response with UTF-8 encoding
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($teacherData, JSON_UNESCAPED_UNICODE);
    } else {
        die("Query execution error: " . mysqli_error($conn));
    }

} else {
    // Handle the case when 'teacher_id' is not provided in the POST request
    echo "teacher_id is missing in the POST request.";
}
?>
