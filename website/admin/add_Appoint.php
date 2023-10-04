<?php
session_start();
require_once "../connect.php";

if (isset($_POST['submit'])) {
  function convertToBuddhistEra($datetime)
{
    $date = date('Y-m-d', strtotime($datetime));

    if ((((int)date('Y', strtotime($date))) - 543) < 2000) {
        // แปลงวันที่ในรูปแบบคริสต์ศักราช (AD) เป็นวันที่ในรูปแบบพุทธศักราช (พ.ศ.)
        $buddhistYear = (int)date('Y', strtotime($date)) + 543;
        $buddhistDate = $buddhistYear . date('-m-d', strtotime($date));
        return $buddhistDate . ' ' . date('H:i:s', strtotime($datetime));
    } else {
        return $datetime;
    }
}

  // collect value of input field
  // $inputappoint_id = $_POST['appoint_id'];
  $inputtitle = $_POST['title'];
  $inputdescription = $_POST['description'];
  $inputappoint_date = convertToBuddhistEra($_POST['appoint_date']);
  $inputgroup_id = $_POST['group_id'];

  // if (empty($inputappoint_id)) {
  //   $_SESSION['error'] = 'กรุณากรอก รหัสกำหนดการ';
  //   header('location: Appointmanage.php');
  // } else {
    try {
      $inputgroup_id  = empty($inputgroup_id ) ? null : $inputgroup_id ;
      if (!isset($_SESSION['error'])) {
        $stmt = $conn->prepare("INSERT INTO `appoint` ( title, description, appoint_date, group_id) 
                VALUES (:inputtitle, :inputdescription, :inputappoint_date, :inputgroup_id)");
        // $stmt->bindParam(':inputappoint_id', $inputappoint_id);
        $stmt->bindParam(':inputtitle', $inputtitle);
        $stmt->bindParam(':inputdescription', $inputdescription);
        $stmt->bindParam(':inputappoint_date', $inputappoint_date);
        $stmt->bindParam(':inputgroup_id', $inputgroup_id);

        $stmt->execute();
        $_SESSION['success'] = 'เพิ่มข้อมูลกำหนดการสำเร็จ';
        header('location: Appointmanage.php');
      } else {
        $_SESSION['error'] = 'มีบางอย่างผิดพลาดเพิ่มข้อมูลกำหนดการไม่สำเร็จ';
        header('location: Appointmanage.php');
      }
    } catch (PDOException $e) {
      $_SESSION['error'] = $e->getMessage();
    }
  }
// }
?>
