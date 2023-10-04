<?php
require "connect.php";

// Check if 'project_id' exists in the POST request
if (isset($_POST['project_id'])) {
    $project_id = $_POST['project_id'];

    if (!$conn) {
        die("Connection error: " . mysqli_connect_error());
    }

    // Set UTF-8 encoding
    mysqli_set_charset($conn, "utf8");

    $sql = "SELECT * FROM `project` WHERE project_id = ?";

    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        die("Prepare statement error: " . mysqli_error($conn));
    }

    // Bind the project_id parameter to the SQL statement
    mysqli_stmt_bind_param($stmt, "s", $project_id);

    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        // Get the result set
        $result = mysqli_stmt_get_result($stmt);

        // Fetch data into an associative array
        $projectData = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $projectData[] = $row;
        }

        // Close the database connection
        mysqli_close($conn);

        // Check if there is any data found
        if (!empty($projectData)) {
            // Return JSON response with UTF-8 encoding
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($projectData, JSON_UNESCAPED_UNICODE);
        } else {
            echo "No data found for project ID: $project_id";
        }
    } else {
        echo "Error executing SQL statement: " . mysqli_error($conn);
    }
} else {
    echo "No 'project_id' parameter found in POST request.";
}
?>
