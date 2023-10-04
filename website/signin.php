
<?php

session_start();
require_once "connect.php";

header("Access-Control-Allow-Origin: *");
header('content-type: application/json; charset=utf-8');

//ตำแหน่งท่านอาจารย์ 
function giveTeacherPositionById($Position)
{
  switch ($Position) {
    case "ศาสตราจารย์":
      return $Position = "ศ.";
      break;
    case "ศาสตราจารย์ ดร.":
      return $Position = "ศ.ดร.";
      break;
    case "รองศาสตราจารย์":
      return $Position = "รศ.";
      break;
    case "รองศาสตราจารย์ ดร.":
      return $Position = "รศ.ดร.";
      break;
    case "ผู้ช่วยศาสตราจารย์":
      return $Position = "ผศ.";
      break;
    case "ผู้ช่วยศาสตราจารย์ ดร.":
      return $Position = "ผศ.ดร.";
      break;
    case "อาจารย์":
      return $Position = "อ.";
      break;
    case "ดร.":
      return $Position = "ดร.";
      break;
    default:
      return $Position = $Position;
  }
}

if (isset($_POST['signin'])) {
    $username = $_POST['inputusername'];
    $password = $_POST['inputpassword'];


    if (empty($username)) {
        $_SESSION['error'] = 'กรุณากรอก username';
        header('location: index.php');
    } else if (empty($password)) {
        $_SESSION['error'] = 'กรุณากรอก Password 6 ตัวท้ายรหัสประชาชน';
        header('location: index.php');
    } else {
        try {
            // Student query
            $data_student = $conn->prepare("SELECT * FROM `student` WHERE student_id = :username");
            $data_student->bindParam(":username", $username);
            $data_student->execute();
            $std = $data_student->fetch(PDO::FETCH_ASSOC);

            // Teacher query
            $data_teacher = $conn->prepare("SELECT * FROM `teacher` WHERE teacher_username = :username");
            $data_teacher->bindParam(":username", $username);
            $data_teacher->execute();
            $teacher = $data_teacher->fetch(PDO::FETCH_ASSOC);


            if (!empty($_POST['remember'])) {
                if(!empty($_COOKIE['user_loginbuffer'])){
                    
                }
                setcookie("user_login", $username, time() + (30 * 24 * 60 * 60)); // ให้คุกกี้หมดอายุใน 30 วัน
                setcookie("user_password", $password, time() + (30 * 24 * 60 * 60)); // ให้คุกกี้หมดอายุใน 30 วัน
                setcookie("user_loginbuffer", $username, time() + (30 * 24 * 60 * 60));
                setcookie("user_passwordbuffer", $password, time() + (30 * 24 * 60 * 60));
                
            } else {
                if (isset($_COOKIE['user_login'])) {
                    setcookie("user_login", '');
                }
                if (isset($_COOKIE['user_password'])) {
                    setcookie("user_password", '');
                }
                if (isset($_COOKIE['user_loginbuffer'])) {
                    setcookie("user_loginbuffer", '');
                }
                if (isset($_COOKIE['user_passwordbuffer'])) {
                    setcookie("user_passwordbuffer", '');
                }
            }
            if ($data_student->rowCount() > 0) {
                if ($username == $std['student_id']) {
                    if (password_verify($password, $std['student_password'])) {
                        $_SESSION['student_login'] = $std['firstname'];
                        $_SESSION['student_id'] = $std['student_id'];

                        $url = "student/Stdpage.php?id=" . urlencode($std['student_id']);
                        header("location: $url");
                        exit();
                    } else {
                        setcookie("user_login", '');
                        setcookie("user_password", '');
                        $_SESSION['error'] = 'รหัสผ่านผิดสำหรับนักศึกษา';
                        header("location: index.php");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = 'ชื่อผู้ใช้งานผิด';
                    header("location: index.php");
                    exit();
                }
            } elseif ($data_teacher->rowCount() > 0) {
                if ($username == $teacher['teacher_username'] && $teacher['level_id'] == 0) {
                    if (password_verify($password, $teacher['teacher_password'])) {
                        $_SESSION['admin_login'] = 'admin';
                        header("location: admin/adminpage.php");
                        exit();
                    } else {
                        setcookie("user_login", '');
                        setcookie("user_password", '');
                        $_SESSION['error'] = 'รหัสผ่านผิด';
                        header("location: index.php");
                        exit();
                    }
                } elseif ($username == $teacher['teacher_username']) {
                    if (password_verify($password, $teacher['teacher_password'])) {
                        //อาจารย์ที่ปรึกษาหลัก
                        $sql = "SELECT * FROM `teacher` WHERE teacher_username = :teacher_username";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_username', $username);
                        $stmt->execute();
                        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
                        $teachername = giveTeacherPositionById($teacher['position']) . $teacher['firstname'];

                        $_SESSION['teacher_login'] = $teachername;
                        $_SESSION['teacher_id'] = $teacher['teacher_id'];
                        $url = "teacher/Teacherpage.php?id=" . urlencode($teacher['teacher_id']);
                        header("location: $url");
                        exit();
                    } else {
                        setcookie("user_login", '');
                        setcookie("user_password", '');
                        $_SESSION['error'] = 'รหัสผ่านผิดสำหรับอาจารย์';
                        header("location: index.php");
                        exit();
                    }
                } else {
                    setcookie("user_login", '');
                    setcookie("user_password", '');
                    $_SESSION['error'] = 'ชื่อผู้ใช้งานผิด';
                    header("location: index.php");
                    exit();
                }
            } else {
                setcookie("user_login", '');
                setcookie("user_password", '');
                $_SESSION['error'] = "ไม่มีข้อมูลในระบบ";
                header("location: index.php");
                exit();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>