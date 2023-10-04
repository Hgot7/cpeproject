<?php
session_start();
require_once "../connect.php";

if (isset($_POST['submit'])) {
  // collect value of input fields
  $inputproject_id = $_POST['inputproject_id'];
  $inputproject_nameTH = $_POST['inputproject_nameTH'];
  $inputproject_nameENG = $_POST['inputproject_nameENG'];
  $inputstudent_id1 = $_POST['inputstudent_id1'];
  $inputstudent_id2 = $_POST['inputstudent_id2'];
  $inputstudent_id3 = $_POST['inputstudent_id3'];
  $inputteacher_id1 = $_POST['inputteacher_id1'];
  $inputteacher_id2 = $_POST['inputteacher_id2'];
  $inputreferee_id = $_POST['inputreferee_id'];
  $inputreferee_id1 = $_POST['inputreferee_id1'];
  $inputreferee_id2 = $_POST['inputreferee_id2'];
  $inputgroup_id = $_POST['input_group_id'];

  if (isset($_FILES["inputboundary_path"]) && $_FILES["inputboundary_path"]["size"] > 0) {
    $targetDir = "uploadfileBoundary/";
    $inputboundary_path = basename($_FILES["inputboundary_path"]["name"]);
    $targetFile = $targetDir . $inputboundary_path;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if ($uploadOk == 1) {
      // ดำเนินการตรวจสอบชื่อไฟล์ซ้ำ
      $stmt = $conn->prepare("SELECT boundary_path FROM `project` WHERE boundary_path = :inputboundary_path");
      $stmt->bindParam(':inputboundary_path', $inputboundary_path);
      $stmt->execute();
      $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($existingFile) {
        $_SESSION['error'] = 'ชื่อไฟล์ซ้ำกันในระบบ ไม่สามารถอัปโหลดได้';
        header('location: projectmanage.php');
        exit;
      } else {
        if (move_uploaded_file($_FILES["inputboundary_path"]["tmp_name"], $targetFile)) {
          // เพิ่มข้อมูลลงในฐานข้อมูล
        } else {
          $_SESSION['error'] = 'ขออภัย, ไฟล์ไม่ได้ถูกอัปโหลด';
          header('location: projectmanage.php');
          exit;
        }
      }
    } else {
      $_SESSION['error'] = 'กรุณาเลือกไฟล์ใหม่ที่ต้องการอัปโหลด';
      header('location: projectmanage.php');
      exit;
    }
  } else {
    $inputboundary_path = ""; // กำหนดค่าเป็นค่าว่างเมื่อไม่มีไฟล์ถูกอัปโหลด
  }

  // $inputgrade = $_POST['inputgrade'];
  $inputyear = $_POST['inputyear'];
  $inputterm = $_POST['inputterm'];

  if (empty($inputproject_id)) {
    $_SESSION['error'] = 'กรุณากรอก project_id';
    header('location: projectmanage.php');
    exit;
  } 
  if (empty($inputproject_nameTH)) {
    $_SESSION['error'] = 'กรุณากรอก project_nameTH';
    header('location: projectmanage.php');
    exit;
  } 
  if (empty($inputstudent_id1)) {$inputstudent_id1 = null;} 
  if (empty($inputstudent_id2)) {$inputstudent_id2 = null;} 
  if (empty($inputstudent_id3)) {$inputstudent_id3 = null; } 
  if (empty($inputteacher_id1)) {$inputteacher_id1 = null;}
  if (empty($inputteacher_id2)) {$inputteacher_id2 = null;}
  if (empty($inputreferee_id)) {$inputreferee_id = null;}
  if (empty($inputreferee_id1)) {$inputreferee_id1 = null;}
  if (empty($inputreferee_id2)) {$inputreferee_id2 = null;}
  if (empty($inputgroup_id)) {$inputgroup_id = null;}
  if (empty($inputyear)) {$inputyear = null;}

  try {
    $stmt = $conn->prepare("INSERT INTO `project` (project_id, project_nameTH, project_nameENG, student_id1, student_id2, student_id3, teacher_id1, teacher_id2, referee_id, referee_id1, referee_id2, group_id, boundary_path, year, term) 
      VALUES (:inputproject_id, :inputproject_nameTH, :inputproject_nameENG, :inputstudent_id1, :inputstudent_id2, :inputstudent_id3, :inputteacher_id1, :inputteacher_id2, :inputreferee_id, :inputreferee_id1, :inputreferee_id2, :inputgroup_id, :inputboundary_path, :inputyear, :inputterm)");
    $stmt->bindParam(':inputproject_id', $inputproject_id);
    $stmt->bindParam(':inputproject_nameTH', $inputproject_nameTH);
    $stmt->bindParam(':inputproject_nameENG', $inputproject_nameENG);
    $stmt->bindParam(':inputstudent_id1', $inputstudent_id1);
    $stmt->bindParam(':inputstudent_id2', $inputstudent_id2);
    $stmt->bindParam(':inputstudent_id3', $inputstudent_id3);
    $stmt->bindParam(':inputteacher_id1', $inputteacher_id1);
    $stmt->bindParam(':inputteacher_id2', $inputteacher_id2);
    $stmt->bindParam(':inputreferee_id', $inputreferee_id);
    $stmt->bindParam(':inputreferee_id1', $inputreferee_id1);
    $stmt->bindParam(':inputreferee_id2', $inputreferee_id2);
    $stmt->bindParam(':inputgroup_id', $inputgroup_id);
    $stmt->bindParam(':inputboundary_path', $inputboundary_path);
    // $stmt->bindParam(':inputgrade', $inputgrade);
    $stmt->bindParam(':inputyear', $inputyear);
    $stmt->bindParam(':inputterm', $inputterm);

    if ($stmt->execute()) {
      $_SESSION['success'] = 'เพิ่มข้อมูลรหัสกลุ่มโครงงาน '.$inputproject_id.' เสร็จสมบูรณ์!!';
      header('location: projectmanage.php');
      exit;
    } else {
      $_SESSION['error'] = 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล';
      header('location: projectmanage.php');
      exit;
    }
  } catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header('location: projectmanage.php');
    exit;
  }
}
?>
