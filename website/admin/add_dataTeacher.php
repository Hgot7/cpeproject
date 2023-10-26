<?php
  session_start();
  require_once "../connect.php";


if (isset($_POST['submit'])) {
  // collect value of input field
  $inputteacher_id = $_POST['inputteacher_id'];
  $inputteacher_username = $_POST['inputteacher_username'];
  $inputteacher_password = $_POST['inputteacher_password'];
  $inputposition = $_POST['inputposition'];
  $inputfirstname = $_POST['inputfirstname'];
  $inputlastname = $_POST['inputlastname'];
  $inputemail = $_POST['inputemail'];
  $inputphone = $_POST['inputphone'];
  $inputlevel_id = $_POST['inputlevel_id'];

  // check empty is null
  $inputteacher_username = empty($inputteacher_username) ? null : $inputteacher_username;
  $inputteacher_password = empty($inputteacher_password) ? null : $inputteacher_password;
  $inputposition = empty($inputposition) ? null : $inputposition;
  $inputfirstname = empty($inputfirstname) ? null : $inputfirstname;
  $inputlastname = empty($inputlastname) ? null : $inputlastname;
  $inputemail = empty($inputemail) ? null : $inputemail;
  $inputphone = empty($inputphone) ? null : $inputphone;
  $inputlevel_id = empty($inputlevel_id) ? null : $inputlevel_id;

  if(empty($inputteacher_username)){
    $_SESSION['error'] = 'กรุณากรอก ชื่อผู้ใช้งานระบบ';
    header('location: teachermanage.php');
    exit;
  }

    try {
       if(!isset($_SESSION['error'])){
        $passwordHash = password_hash($inputteacher_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO `teacher` (teacher_id,teacher_username, teacher_password, position, firstname, lastname, email, phone, level_id) 
                VALUES (:inputteacher_id, :inputteacher_username, :passwordHash, :inputposition, :inputfirstname, :inputlastname, :inputemail, :inputphone, :inputlevel_id)");
        $stmt->bindParam(':inputteacher_id', $inputteacher_id);
        $stmt->bindParam(':inputteacher_username', $inputteacher_username);
        $stmt->bindParam(':passwordHash', $passwordHash);
        $stmt->bindParam(':inputposition', $inputposition);
        $stmt->bindParam(':inputfirstname',$inputfirstname);
        $stmt->bindParam(':inputlastname', $inputlastname);
        $stmt->bindParam(':inputemail', $inputemail);
        $stmt->bindParam(':inputphone', $inputphone);
        $stmt->bindParam(':inputlevel_id', $inputlevel_id);

        $stmt->execute();
        $_SESSION['success'] = 'เพิ่มข้อมูลอาจารย์สำเร็จ';
        header('location: teachermanage.php');

    }else {
        $_SESSION['error'] = 'มีบางอย่างผิดพลาดเพิ่มข้อมูลอาจารย์ไม่สำเร็จ';
        header('location: teachermanage.php');
    }

    } catch(PDOException $e){
      $_SESSION['error'] = $e->getMessage();
      header('location: teachermanage.php');
    }
  }
// }
  // Prepare and bind SQL statement
?>