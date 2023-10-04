<?php
require_once "../../connect.php";

if (isset($_POST['query'])) {
    $inputText = $_POST['query'];
    $sql = "SELECT * FROM `appoint`
        WHERE title LIKE :inputText";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['inputText' => '%' . $inputText . '%']);
    $result = $stmt->fetchAll();
    $found = false; // เพิ่มตัวแปรเพื่อตรวจสอบว่าเจอตัวที่ตรงคำค้นหาหรือไม่

    if ($result) {
        foreach ($result as $row) {   
          $Groupname = $row['title'];
            if (strpos($row['title'], $inputText) !== false) {
                    echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                    echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $row['title']);
                    $found = true;
                
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