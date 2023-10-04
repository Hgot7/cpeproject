<?php
require_once "./connect.php";

if (isset($_POST['query'])) {
    $inputText = $_POST['query'];
    $inputTextUpper = strtoupper($inputText);
    $inputTextLower = strtolower($inputText);
    $sql = "SELECT project.*, 
               student1.firstname AS student1_firstname,            
               student2.firstname AS student2_firstname,             
               student3.firstname AS student3_firstname,           
               CONCAT(teacher1.position, ' ', teacher1.firstname) AS teacher1_name,
               CONCAT(teacher2.position, ' ', teacher2.firstname) AS teacher2_name,
               CONCAT(referee.position, ' ', referee.firstname) AS referee_name,
               CONCAT(referee1.position, ' ', referee1.firstname) AS referee1_name,
               CONCAT(referee2.position, ' ', referee2.firstname) AS referee2_name
        FROM `project`
        LEFT JOIN `student` AS student1 ON project.student_id1 = student1.student_id
        LEFT JOIN `student` AS student2 ON project.student_id2 = student2.student_id
        LEFT JOIN `student` AS student3 ON project.student_id3 = student3.student_id
        LEFT JOIN `teacher` AS teacher1 ON project.teacher_id1 = teacher1.teacher_id
        LEFT JOIN `teacher` AS teacher2 ON project.teacher_id2 = teacher2.teacher_id
        LEFT JOIN `teacher` AS referee ON project.referee_id = referee.teacher_id
        LEFT JOIN `teacher` AS referee1 ON project.referee_id1 = referee1.teacher_id
        LEFT JOIN `teacher` AS referee2 ON project.referee_id2 = referee2.teacher_id
        WHERE project.project_nameTH LIKE :inputText
           OR project.project_nameENG LIKE :inputText
           OR student1.firstname LIKE :inputText      
           OR student2.firstname LIKE :inputText
           OR student3.firstname LIKE :inputText   
           OR CONCAT(teacher1.position, ' ', teacher1.firstname) LIKE :inputText
           OR CONCAT(teacher2.position, ' ', teacher2.firstname) LIKE :inputText
           OR CONCAT(referee.position, ' ', referee.firstname) LIKE :inputText
           OR CONCAT(referee1.position, ' ', referee1.firstname) LIKE :inputText
           OR CONCAT(referee2.position, ' ', referee2.firstname) LIKE :inputText";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['inputText' => '%' . $inputText . '%']);
    $result = $stmt->fetchAll();


    $found = false; // เพิ่มตัวแปรเพื่อตรวจสอบว่าเจอตัวที่ตรงคำค้นหาหรือไม่

    if ($result) {
        $uniqueTeacherAdvisor = []; // เพิ่มตัวแปรเพื่อเก็บอาจารย์ที่ปรึกษาหลักที่ไม่ซ้ำกัน
        $uniqueTeacherAdvisorCoop = []; // เพิ่มตัวแปรเพื่อเก็บอาจารย์ที่ปรึกษาร่วมที่ไม่ซ้ำกัน
        $uniqueReferee = true;  // เพิ่มตัวแปรเพื่อเก็บอาจารย์ที่ปรึกษาที่ไม่ซ้ำกัน

        foreach ($result as $project) {
            $TeacherAdvisor = $project['teacher1_name'];
            $TeacherAdvisorCoop = $project['teacher2_name'];
            // ตรวจสอบและเน้นเนื้อหาที่ตรงกับ $inputText
            if (strpos($project['project_nameTH'], $inputText) !== false) {
                echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $project['project_nameTH']);
                $found = true;
            } elseif (stripos($project['project_nameENG'], $inputTextUpper) !== false) {
                echo '<a class="list-group-item list-group-item-action border-1">';
                echo str_ireplace($inputTextUpper, '<strong>' . $inputTextUpper . '</strong>', $project['project_nameENG']);
                $found = true;
            } elseif (strpos($project['student1_firstname'], $inputText) !== false) {
                echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                echo str_replace($inputText, '<strong>' . $inputText . '</strong>',  $project['student1_firstname']);
                echo ' <span style="font-size: 0.8em;">(นักศึกษา)</span>';
                $found = true;
            } elseif (strpos($project['student2_firstname'], $inputText) !== false) {
                echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $project['student2_firstname']);
                echo ' <span style="font-size: 0.8em;">(นักศึกษา)</span>';
                $found = true;
            } elseif (strpos($project['student3_firstname'], $inputText) !== false) {
                echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $project['student3_firstname']);
                echo ' <span style="font-size: 0.8em;">(นักศึกษา)</span>';
                $found = true;
            } elseif (strpos($project['teacher1_name'], $inputText) !== false) {

                // ตรวจสอบว่าปีนี้ยังไม่มีอยู่ในรายการ uniqueTeacherAdvisor หรือไม่
                if (!in_array($TeacherAdvisor, $uniqueTeacherAdvisor)) {
                    $uniqueTeacherAdvisor[] = $TeacherAdvisor; // เพิ่มปีนี้ในรายการ uniqueTeacherAdvisor เพื่อระบุว่ามีปีนี้แล้ว
                    echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                    echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $project['teacher1_name']);
                    echo ' <span style="font-size: 0.8em;">(เป็นอาจารย์ที่ปรึกษาหลัก)</span>';
                    $found = true;
                }
            } elseif (strpos($project['teacher2_name'], $inputText) !== false) {
                if (!in_array($TeacherAdvisorCoop, $uniqueTeacherAdvisorCoop)) {
                    $uniqueTeacherAdvisorCoop[] = $TeacherAdvisorCoop;
                    echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                    echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $project['teacher2_name']);
                    echo ' <span style="font-size: 0.8em;">(เป็นอาจารย์ที่ปรึกษาร่วม)</span>';
                    $found = true;
                }
            } elseif (strpos($project['referee_name'], $inputText) !== false && $uniqueReferee) {
                echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $project['referee_name']);
                echo ' <span style="font-size: 0.8em;">(เป็นกรรมการ)</span>';
                $uniqueReferee = false;
                $found = true;
            } elseif (strpos($project['referee1_name'], $inputText) !== false && $uniqueReferee) {
                echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $project['referee1_name']);
                echo ' <span style="font-size: 0.8em;">(เป็นกรรมการ)</span>';
                $uniqueReferee = false;
                $found = true;
            } elseif (strpos($project['referee2_name'], $inputText) !== false && $uniqueReferee) {
                echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
                echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $project['referee2_name']);
                echo ' <span style="font-size: 0.8em;">(เป็นกรรมการ)</span>';
                $uniqueReferee = false;
                $found = true;
            } else {
                break;
            }
            echo '</a>';
        }
        // foreach ($group as $group) {
        //     if (strpos($group['group_name'], $inputText) !== false) {
        //         // ตรวจสอบว่าปีนี้ยังไม่มีอยู่ในรายการ uniqueGroups หรือไม่
        //         echo '<a class="list-group-item list-group-item-action border-1">'; //////////////
        //         echo str_replace($inputText, '<strong>' . $inputText . '</strong>', $group['group_name']);
        //         $found = true;
        //     } else {
        //         echo '<p class="list-group-item border-1 disabled">No record.</p>';
        //         break;
        //     }
        // }
    }

    $isStudent = strpos($inputText, '(นักศึกษา)') !== false;
    $isTeacherAdvisor = strpos($inputText, '(เป็นอาจารย์ที่ปรึกษาหลัก)') !== false;
    $isTeacherAdvisorCoop = strpos($inputText, '(เป็นอาจารย์ที่ปรึกษาร่วม)') !== false;
    $isReferee = strpos($inputText, '(เป็นกรรมการ)') !== false;

    if (!$found && !$isStudent && !$isTeacherAdvisor && !$isTeacherAdvisorCoop && !$isReferee) {
        // หากไม่เจอตัวที่ตรงคำค้นหาเลย และไม่เจอคำที่ตรงกับเงื่อนไข แสดง "No record."
        echo '<p class="list-group-item border-1 disabled">No record.</p>';
    }
} else {
    echo '<p class="list-group-item border-1 disabled">No record.</p>';
}
