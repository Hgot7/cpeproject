<?php
  session_start();
  require_once "../connect.php";


if (isset($_POST['submitgroup'])) {
  // collect value of input field
  $inputgroup_name = $_POST['inputgroup_name'];
  // $inputurole = $_POST['inputurole'];

  if(empty($inputgroup_name)){
    $_SESSION['error'] = 'กรุณากรอกกลุ่มเรียน';
    header('Location: ./groupmanage.php');

 
  }else {
    try {
       if(!isset($_SESSION['error'])){
        $stmt = $conn->prepare("INSERT INTO `groups` (group_name) VALUES (:inputgroup_name)");
        $stmt->bindParam(':inputgroup_name', $inputgroup_name);
        
    

        $stmt->execute();
        $_SESSION['success'] = 'เพิ่มข้อมูลกลุ่มเรียนสำเร็จ';
        header('Location: ./groupmanage.php');

    }else {
        $_SESSION['error'] = 'มีบางอย่างผิดพลาดเพิ่มข้อมูลกลุ่มเรียนไม่สำเร็จ';
        header('location: studentmanage.php');
    }

    } catch(PDOException $e){
        $_SESSION['error'] = $e->getMessage();
        header('Location: ./groupmanage.php');
        exit();
    }
  }
}

  // Prepare and bind SQL statement
?>