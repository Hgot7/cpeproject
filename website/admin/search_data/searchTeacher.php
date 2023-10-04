<?php
require_once "../../connect.php";

if (isset($_POST['query'])) {
    $inputText = $_POST['query'];
    $sql = "SELECT *
    FROM `teacher`
    WHERE teacher_id LIKE :inputText
       OR CONCAT(position, ' ', firstname, ' ', lastname) LIKE :inputText
       OR email LIKE :inputText
       OR phone LIKE :inputText";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['inputText' => '%' . $inputText . '%']);
    $result = $stmt->fetchAll();
    
    $found = false; // เพิ่มตัวแปรเพื่อตรวจสอบว่าเจอตัวที่ตรงคำค้นหาหรือไม่

    if ($result) {
        $uniqueTeacherAdvisor = []; // เพิ่มตัวแปรเพื่อเก็บอาจารย์ที่ปรึกษาหลักไม่ซ้ำกัน
        $uniqueTeacherAdvisorCoop = []; // เพิ่มตัวแปรเพื่อเก็บอาจารย์ที่ปรึกษาร่วมที่ไม่ซ้ำกัน
        $uniqueReferee = true;  // เพิ่มตัวแปรเพื่อเก็บอาจารย์ที่ปรึกษาที่ไม่ซ้ำกัน

        foreach ($result as $row) {
            // ตรวจสอบและเน้นเนื้อหาที่ตรงกับ $inputText
            if (strpos($row['position'].' '. $row['firstname'] . ' ' . $row['lastname'], $inputText) !== false) {
                echo '<a class="list-group-item list-group-item-action border-1">';
                echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $row['position'].' '. $row['firstname'] . ' ' . $row['lastname']);
                $found = true;
            } else {
                break;
            }
            echo '</a>';
        }
    }
    if (!$found) {
        // หากไม่เจอตัวที่ตรงคำค้นหาเลย แสดง "No record."
        echo '<p class="list-group-item border-1 disabled">No record.</p>';
    }
} else {
    echo '<p class="list-group-item border-1 disabled">No record.</p>';
}
