<?php
require "connect.php";
session_start();

if (!$conn) {
    echo "connection error";
}

// Set UTF-8 encoding
mysqli_set_charset($conn, "utf8");

// Query to get data from the 'timetest' table
$sql = "SELECT * FROM `timeTest`
WHERE DATE_FORMAT(timeTest_date, '%Y%m%d') >= CONCAT(YEAR(CURDATE()) + 543, LPAD(MONTH(CURDATE()), 2, '0'), LPAD(DAY(CURDATE()), 2, '0'))
ORDER BY timeTest_date ASC, start_time ASC";
$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    echo "query error";
} else {
    // Fetch all rows as an associative array
    $timeTest = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Close the database connection
    mysqli_close($conn);

    // Function to retrieve project by ID
    function giveProjectById($conn, $project_id)
    {
        $sql = "SELECT * FROM `project` WHERE project_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $project_id); // 'i' หมายถึง integer
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Function to retrieve student by ID
    function giveStudentById($conn, $student_id)
    {
        $sql = "SELECT * FROM `student` WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $student_id); // 'i' หมายถึง integer
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Function to retrieve teacher by ID
    function giveTeacherById($conn, $teacher_id)
    {
        $sql = "SELECT * FROM `teacher` WHERE teacher_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $teacher_id); // 'i' หมายถึง integer
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Function to format teacher position
    function giveTeacherPositionById($Position)
    {
        switch ($Position) {
            case "รองศาสตราจารย์":
                return "รศ.";
                break;
            case "ผู้ช่วยศาสตราจารย์":
                return "ผศ.";
                break;
            case "ผู้ช่วยศาสตราจารย์ ดร.":
                return "ผศ.ดร.";
                break;
            case "อาจารย์":
                return "อ.";
                break;
            case "ดร.":
                return "ดร.";
                break;
            default:
                return $Position;
        }
    }

    // Return JSON response with UTF-8 encoding
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($timeTest, JSON_UNESCAPED_UNICODE);
}
?>
