<?php
session_start();
require '../connect.php'; // เปลี่ยนเป็นตำแหน่งและชื่อไฟล์ config.php ที่ถูกต้อง

if (isset($_GET['id'])) {
  $document_id = $_GET['id'];
  $stmt = $conn->prepare("SELECT * FROM `document` WHERE document_id = :document_id");
  $stmt->bindParam(':document_id', $document_id);
  $stmt->execute();
  $delete_File = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($delete_File) {
    $deletestmt = $conn->prepare("UPDATE `document` SET document_path = null WHERE document_id = :document_id");
  $deletestmt->bindParam(':document_id', $document_id);
  if ($deletestmt->execute()) {
    $_SESSION['success'] = "ลบข้อมูลเสร็จสมบูรณ์";
    header("location: ./editDocument.php");
  } else {
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
    header("location: ./editDocument.php");
  }
    unlink("uploadfileDocument/" . $delete_File["document_path"]);
    $_SESSION['success'] = "ลบไฟล์เสร็จสมบูรณ์";
    header("location: ./editDocument.php");
  }
}

// ส่งค่า document_id ไปยังไฟล์ editStudent.php
header("location: ./editDocument.php?id=" . $document_id);
