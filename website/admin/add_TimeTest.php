<?php
session_start();
require_once "../connect.php";

if (isset($_POST['submit'])) {
  function convertToBuddhistEra($date)
  {
    if((((int)date('Y', strtotime($date)))-543) < 2000){
       // แปลงวันที่ในรูปแบบคริสต์ศักราช (AD) เป็นวันที่ในรูปแบบพุทธศักราช (พ.ศ.)
       $buddhistYear = (int)date('Y', strtotime($date)) + 543;
       $buddhistDate = $buddhistYear . date('-m-d', strtotime($date));
       return $buddhistDate;
    }else {return $date;}
     
  }
  // $inputtimeTest_id = $_POST['timeTest_id'];
  $inputtimeTest_date = convertToBuddhistEra($_POST['timeTest_date']);
  $inputstart_time = $_POST['start_time'];
  $inputstop_time = $_POST['stop_time'];
  $inputroom_number = $_POST['room_number'];
  $inputproject_id = $_POST['project_id'];

   // check empty is null
  $inputtimeTest_date = empty($inputtimeTest_date) ? null : $inputtimeTest_date;
  $inputstart_time = empty($inputstart_time) ? null : $inputstart_time;
  $inputstop_time = empty($inputstop_time) ? null : $inputstop_time;
  $inputroom_number = empty($inputroom_number) ? null : $inputroom_number;
  $inputproject_id = empty($inputproject_id) ? null : $inputproject_id;

  
  $stmt = $conn->prepare("SELECT * FROM `timetest` WHERE project_id = :project_id");
  $stmt->bindParam(':project_id', $inputproject_id);
  $stmt->execute();
  $project_iddata = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!empty($project_iddata)) {
    $_SESSION['error'] = 'รหัสโครงงานนี้มีข้อมูลเวลาสอบในระบบอยู่แล้ว';
    header('location: TimeTestmanage.php');
    exit;
  }


  if (empty($inputproject_id)) {
    $_SESSION['error'] = 'กรุณากรอก รหัสโครงงาน';
    header('location: TimeTestmanage.php');
  } else {
    try {
      if (!isset($_SESSION['error'])) {
        $stmt = $conn->prepare("INSERT INTO `timetest` (timeTest_date, start_time, stop_time, room_number, project_id) 
                VALUES (:inputtimeTest_date, :inputstart_time, :inputstop_time, :inputroom_number, :inputproject_id)");
        // $stmt->bindParam(':inputtimeTest_id', $inputtimeTest_id);
        $stmt->bindParam(':inputtimeTest_date', $inputtimeTest_date);
        $stmt->bindParam(':inputstart_time', $inputstart_time);
        $stmt->bindParam(':inputstop_time', $inputstop_time);
        $stmt->bindParam(':inputroom_number', $inputroom_number);
        $stmt->bindParam(':inputproject_id', $inputproject_id);

        $stmt->execute();
        $_SESSION['success'] = 'เพิ่มเวลาสอบสำเร็จ!!';
        header('location: TimeTestmanage.php');
      } else {
        $_SESSION['error'] = 'มีบางอย่างผิดพลาดเพิ่มเวลาสอบไม่สำเร็จ';
        header('location: TimeTestmanage.php');
      }
    } catch (PDOException $e) {
      $_SESSION['error'] = $e->getMessage();
      header('location: TimeTestmanage.php');
      exit();
    }
  }
}
?>
