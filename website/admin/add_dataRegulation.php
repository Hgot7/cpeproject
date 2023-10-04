<?php
session_start();
require_once "../connect.php";

if (isset($_POST['submit'])) {
  // collect value of input fields
  $regulationFile_path = $_POST['regulationFile_path'];

  if (isset($_FILES["regulationFile_path"]) && $_FILES["regulationFile_path"]["size"] > 0) {
    $targetDir = "uploadfileRegulation/";
    $regulationFile_path = basename($_FILES["regulationFile_path"]["name"]);
    $targetFile = $targetDir . $regulationFile_path;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if ($uploadOk == 1) {
      // ดำเนินการตรวจสอบชื่อไฟล์ซ้ำ
      $stmt = $conn->prepare("SELECT regulationFile_path FROM `regulation` WHERE regulationFile_path = :regulationFile_path");
      $stmt->bindParam(':regulationFile_path', $regulationFile_path);
      $stmt->execute();
      $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($existingFile) {
        $_SESSION['error'] = 'ชื่อไฟล์ซ้ำกันในระบบ ไม่สามารถอัปโหลดได้';
        header('location: regulationmanage.php');
        exit;
      } else {
        if (move_uploaded_file($_FILES["regulationFile_path"]["tmp_name"], $targetFile)) {
          // เพิ่มข้อมูลลงในฐานข้อมูล
        } else {
          $_SESSION['error'] = 'ขออภัย, ไฟล์ไม่ได้ถูกอัปโหลด';
          header('location: regulationmanage.php');
          exit;
        }
      }
    } else {
      $_SESSION['error'] = 'กรุณาเลือกไฟล์ใหม่ที่ต้องการอัปโหลด';
      header('location: regulationmanage.php');
      exit;
    }
  } else {
    $regulationFile_path = ""; // กำหนดค่าเป็นค่าว่างเมื่อไม่มีไฟล์ถูกอัปโหลด
  }


  $inputregulation_text = $_POST['inputregulation_text'];
  $inputyear = $_POST['inputyear'];
  $inputterm = $_POST['inputterm']; 
    try {
      $regulationFile_path = empty($regulationFile_path) ? null : $regulationFile_path;
      $inputregulation_text = empty($inputregulation_text) ? null : $inputregulation_text;
      $inputyear = empty($inputyear) ? null : $inputyear;
      $inputterm = empty($inputterm) ? null : $inputterm;
      
      $stmt = $conn->prepare("INSERT INTO `regulation` (regulationFile_path, regulation_text, year, term ) 
                VALUES (:regulationFile_path, :inputregulation_text, :inputyear, :inputterm)");
      $stmt->bindParam(':regulationFile_path', $regulationFile_path);
      $stmt->bindParam(':inputregulation_text', $inputregulation_text);
      $stmt->bindParam(':inputyear',$inputyear);
        $stmt->bindParam(':inputterm', $inputterm);
     
      $stmt->execute();
      $_SESSION['success'] = 'เพิ่มข้อมูลเสร็จสมบูรณ์!!';
      header('location: regulationmanage.php');
    } catch (PDOException $e) {
      $_SESSION['error'] = $e->getMessage();
      header('location: regulationmanage.php');
    }
  }
