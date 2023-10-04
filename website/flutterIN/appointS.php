<?php
require "connect.php";

// ตรวจสอบว่ามีการส่ง student_id มาใน SESSION request

    $id = $_POST['student_id'];

    // ตรวจสอบการเชื่อมต่อกับฐานข้อมูล
    if (!$conn) {
        echo json_encode(["error" => "Connection error"]);
    } else {
        // ตั้งค่า encoding เป็น UTF-8 สำหรับการเชื่อมต่อ
        mysqli_set_charset($conn, "utf8");

        // เตรียมคำสั่ง SQL
        $sql = "SELECT * FROM `appoint` 
                WHERE (DATE_FORMAT(appoint_date, '%Y%m%d') >= CONCAT(YEAR(CURDATE()) + 543, LPAD(MONTH(CURDATE()), 2, '0'), LPAD(DAY(CURDATE()), 2, '0'))) 
                AND ((group_id = (SELECT group_id FROM `student` WHERE student_id = ?)) OR (group_id IS NULL)) 
                ORDER BY appoint_date ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();

        // Fetch data into an associative array and encode it as JSON
        $result = $stmt->get_result();
        $appointments = array();

        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }

        // ส่งข้อมูล JSON กลับไปยังแอพพลิเคชัน
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($appointments, JSON_UNESCAPED_UNICODE);
    }

?>
