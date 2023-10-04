<?php
require_once "../../connect.php";

if (isset($_POST['query'])) {
    $inputText = $_POST['query'];
    $sql = "SELECT student.*, groups.group_name 
    FROM `student` LEFT JOIN `groups` ON student.group_id = groups.group_id
    WHERE student.student_id LIKE :inputText
       OR CONCAT(student.firstname, ' ', student.lastname) LIKE :inputText";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['inputText' => '%' . $inputText . '%']);
    $result = $stmt->fetchAll();

    $found = false; // เพิ่มตัวแปรเพื่อตรวจสอบว่าเจอตัวที่ตรงคำค้นหาหรือไม่
    if ($result) {
        foreach ($result as $row) {   
            // ตรวจสอบและเน้นเนื้อหาที่ตรงกับ $inputText
            if (strpos($row['student_id'], $inputText) !== false) {
                echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $row['student_id']);
                $found = true;
            } elseif (strpos($row['firstname'] . ' ' . $row['lastname'], $inputText) !== false) {
                echo '<a class="list-group-item list-group-item-action border-1">';
                echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $row['firstname'] . ' ' . $row['lastname']);
                $found = true;
            }
             elseif (strpos($row['email'], $inputText) !== false) {
                echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $row['email']);
                $found = true;
            } elseif (strpos($row['phone'], $inputText) !== false) {
                echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $row['phone']);
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

?>