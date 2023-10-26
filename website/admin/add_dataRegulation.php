<?php
session_start();
require_once "../connect.php";

if (isset($_POST['submit'])) {
  // Validate and collect input
  $inputregulation_text = $_POST['inputregulation_text'];
  $inputyear = $_POST['inputyear'];
  $inputterm = $_POST['inputterm'];

  if (empty($inputregulation_text)) {
    $_SESSION['error'] = 'กรุณากรอก กฎข้อบังคับในรายวิชา';
    header('location: regulationmanage.php');
    exit;
  }

  $regulationFile_path = null;

  if (isset($_FILES["regulationFile_path"]) && $_FILES["regulationFile_path"]["size"] > 0) {
    $targetDir = "uploadfileRegulation/";
    $regulationFile_name = basename($_FILES["regulationFile_path"]["name"]);
    $targetFile = $targetDir . $regulationFile_name;

    // Check if the file already exists and rename if necessary
    $counter = 1;
    while (file_exists($targetFile)) {
      $filename = pathinfo($regulationFile_name, PATHINFO_FILENAME);
      $file_extension = pathinfo($regulationFile_name, PATHINFO_EXTENSION);
      $regulationFile_name = $filename . '_' . $counter . '.' . $file_extension;
      $targetFile = $targetDir . $regulationFile_name;
      $counter++;
    }

    if (move_uploaded_file($_FILES["regulationFile_path"]["tmp_name"], $targetFile)) {
      $regulationFile_path = $regulationFile_name;

      // Insert data into the database
      
    } else {
      $_SESSION['error'] = 'ขออภัย, ไฟล์ไม่ได้ถูกอัปโหลด';
      header('location: regulationmanage.php');
      $regulationFile_path = null;

    }
  }
  try {
    $stmt = $conn->prepare("INSERT INTO `regulation` (regulationFile_path, regulation_text, year, term) 
      VALUES (:regulationFile_path, :inputregulation_text, :inputyear, :inputterm)");
    $stmt->bindParam(':regulationFile_path', $regulationFile_path);
    $stmt->bindParam(':inputregulation_text', $inputregulation_text);
    $stmt->bindParam(':inputyear', $inputyear);
    $stmt->bindParam(':inputterm', $inputterm);

    $stmt->execute();
    $_SESSION['success'] = 'เพิ่มข้อมูลเสร็จสมบูรณ์!!';
    header('location: regulationmanage.php');
  } catch (PDOException $e) {
    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูลในฐานข้อมูล: ' . $e->getMessage();
    header('location: regulationmanage.php');
  }
}
?>
