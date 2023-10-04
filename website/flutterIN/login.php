<?php
session_start();
require "connect.php"; // 

if (!$conn) {
    echo json_encode(["error" => "connection error"]);
} else {
    $input = json_decode(file_get_contents('php://input'), true);

    if(isset($input['username']) && isset($input['password'])&& isset($input['is_signed_in'])){
        $username = $input['username'];
        $password = $input['password'];
        $isSignedIn = $input['is_signed_in'];

    if (empty($username)) {
        echo json_encode(["error" => "กรุณากรอก username"]);
    } elseif (empty($password)) {
        echo json_encode(["error" => "กรุณากรอก Password"]);
    } else {
        // Prepare the SQL statement with placeholders to prevent SQL injection
        $sql_student = "SELECT * FROM `student` WHERE student_id = ?;";
        $stmt_student = mysqli_prepare($conn, $sql_student);
        mysqli_stmt_bind_param($stmt_student, "s", $username);
        mysqli_stmt_execute($stmt_student);
        $result_student = mysqli_stmt_get_result($stmt_student);
        $count_student = mysqli_num_rows($result_student);

        if ($count_student == 1) {
            $row = mysqli_fetch_array($result_student);
            $hashedPassword = $row['student_password'];

            if (password_verify($password, $hashedPassword)) {
                // บันทึก student_id ลงใน session
                
                $_SESSION['student_id'] = $row['student_id'];
                
                echo json_encode(["message" => "Student Success", "student_id" => $_SESSION['student_id']]);
            } else {
                echo json_encode(["error" => "รหัสผ่านไม่ถูกต้อง"]);
            }
        } else {
            $sql_teacher = "SELECT * FROM `teacher` WHERE teacher_id = ?;";
            $stmt_teacher = mysqli_prepare($conn, $sql_teacher);
            mysqli_stmt_bind_param($stmt_teacher, "s", $username);
            mysqli_stmt_execute($stmt_teacher);
            $result_teacher = mysqli_stmt_get_result($stmt_teacher);

            if (mysqli_num_rows($result_teacher) == 1) {
                $row = mysqli_fetch_array($result_teacher);
                $hashedPassword = $row['teacher_password'];

                if (password_verify($password, $hashedPassword)) {
                    $_SESSION['teacher_id'] = $row['teacher_id'];
                    $_SESSION['teacher_password'] = $row['teacher_password'];
                    $_SESSION['level_id'] = $row['level_id'];

                    if ($_SESSION['level_id'] == 1) {
                        echo json_encode(["message" => "Teacher Success", "teacher_id" => $_SESSION['teacher_id']]);
                    }
                } else {
                    echo json_encode(["error" => "รหัสผ่านไม่ถูกต้อง"]);
                }
            } else {
                echo json_encode(["error" => "ไม่พบข้อมูลผู้ใช้"]);
            }
        }
    }
}}

?>
