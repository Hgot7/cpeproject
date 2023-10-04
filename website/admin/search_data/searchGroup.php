<?php
require_once "../../connect.php";

if (isset($_POST['query'])) {
    $inputText = $_POST['query'];
    $sql = "SELECT * FROM `groups`
        WHERE group_name LIKE :inputText";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['inputText' => '%' . $inputText . '%']);
    $result = $stmt->fetchAll();

    $found = false; // เพิ่มตัวแปรเพื่อตรวจสอบว่าเจอตัวที่ตรงคำค้นหาหรือไม่
    $uniqueGroup = []; // เพิ่มตัวแปรเพื่อเก็บอาจารย์ที่ปรึกษาหลักไม่ซ้ำกัน

    if ($result) {
        foreach ($result as $row) {   
          $Groupname = $row['group_name'];
            if (strpos($row['group_name'], $inputText) !== false) {
                     if (!in_array($Groupname, $uniqueGroup)) {
                    $uniqueGroup[] = $Groupname; // เพิ่มปีนี้ในรายการ uniqueTeacherAdvisor เพื่อระบุว่ามีปีนี้แล้ว
                    echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                    echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $row['group_name']);
                    $found = true;
                }
            }else {
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