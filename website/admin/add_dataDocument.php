<?php
session_start();
require_once "../connect.php";

if (isset($_POST['submit'])) {
  $document_name = $_POST['document_name'];
  $year = $_POST['year'];
  $term = $_POST['term'];

  if (empty($document_name) || empty($year) || empty($term)) {
    $_SESSION['error'] = 'กรุณากรอกข้อมูลทุกช่อง';
    header('location: documentmanage.php');
    exit;
  }

  if (isset($_FILES["document_path"]) && $_FILES["document_path"]["size"] > 0) {
    $targetDir = "uploadfileDocument/";
    $document_path = basename($_FILES["document_path"]["name"]);
    $targetFile = $targetDir . $document_path;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if ($uploadOk == 1) {
      // ดำเนินการตรวจสอบชื่อไฟล์ซ้ำ
      if (file_exists($targetFile)) {
        $filename = pathinfo($document_path, PATHINFO_FILENAME); // ดึงชื่อไฟล์แต่ไม่รวมนามสกุล
        $file_extension = pathinfo($document_path, PATHINFO_EXTENSION); // ดึงนามสกุลไฟล์

        $counter = 1;
        while (file_exists($targetFile)) {
          // เพิ่มเลขต่อท้ายชื่อไฟล์
          $document_path = $filename . '_' . $counter . '.' . $file_extension;
          $targetFile = $targetDir . $document_path;
          $counter++;
        }
      }
      if (move_uploaded_file($_FILES["document_path"]["tmp_name"], $targetFile)) {
        // เพิ่มข้อมูลลงในฐานข้อมูล
        try {
          $stmt = $conn->prepare("INSERT INTO `document` (document_path, document_name, document_date, year, term) VALUES (:document_path, :document_name, NOW(), :year, :term)");
          $stmt->bindParam(':document_path', $document_path);
          $stmt->bindParam(':document_name', $document_name);
          $stmt->bindParam(':year', $year);
          $stmt->bindParam(':term', $term);

          $stmt->execute();
          $_SESSION['success'] = 'เพิ่มข้อมูลเอกสารสำเร็จ';
          header('location: documentmanage.php');
        } catch (PDOException $e) {
          $_SESSION['error'] = 'มีบางอย่างผิดพลาดเพิ่มข้อมูลเอกสารไม่สำเร็จ';
          header('location: documentmanage.php');
        }
      } else {
        $_SESSION['error'] = 'ขออภัย, ไฟล์ไม่ได้ถูกอัปโหลด';
        header('location: documentmanage.php');
        exit;
      }
    } else {
      $_SESSION['error'] = 'กรุณาเลือกไฟล์ใหม่ที่ต้องการอัปโหลด';
      header('location: documentmanage.php');
      exit;
    }
  } else {
    $_SESSION['error'] = 'กรุณาอัปโหลดเอกสารในรายวิชา';
    header('location: documentmanage.php');
    exit;
  }
}
?>
