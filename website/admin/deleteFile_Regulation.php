<?php
session_start();
require '../connect.php'; // เปลี่ยนเป็นตำแหน่งและชื่อไฟล์ config.php ที่ถูกต้อง

if (isset($_GET['id'])) {
  $regulation_id = $_GET['id'];
  $stmt = $conn->prepare("SELECT * FROM `regulation` WHERE regulation_id = :regulation_id");
  $stmt->bindParam(':regulation_id', $regulation_id);
  $stmt->execute();
  $delete_File = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($delete_File) {
    $deletestmt = $conn->prepare("UPDATE `regulation` SET regulationFile_path = null WHERE regulation_id = :regulation_id");
  $deletestmt->bindParam(':regulation_id', $regulation_id);
  if ($deletestmt->execute()) {
    $_SESSION['success'] = "ลบข้อมูลเสร็จสมบูรณ์";
    header("location: ./editRegulation.php");
  } else {
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
    header("location: ./editRegulation.php");
  }
    unlink("uploadfileRegulation/" . $delete_File["regulationFile_path"]);
    $_SESSION['success'] = "ลบไฟล์เสร็จสมบูรณ์";
    header("location: ./editRegulation.php");
  }
}


header("location: ./editRegulation.php?id=" . $regulation_id);
