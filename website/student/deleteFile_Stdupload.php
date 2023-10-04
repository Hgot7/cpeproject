<?php
session_start();
require '../connect.php'; // ปรับเปลี่ยนเป็นตำแหน่งและชื่อไฟล์ connect.php ที่ถูกต้อง

if (isset($_GET['id'])) {
  $file_id = $_GET['id'];
  
  // ลบความคิดเห็นก่อน
  $deleteCommentsStmt = $conn->prepare("DELETE FROM `comments` WHERE file_id = :file_id");
  $deleteCommentsStmt->bindParam(':file_id', $file_id);
  $deleteCommentsSuccess = $deleteCommentsStmt->execute();
  
  // ตรวจสอบว่าลบความคิดเห็นสำเร็จหรือไม่
  if ($deleteCommentsSuccess) {
    // ลบไฟล์
    $deleteFileStmt = $conn->prepare("SELECT * FROM `file` WHERE file_id = :file_id");
    $deleteFileStmt->bindParam(':file_id', $file_id);
    $deleteFileStmt->execute();
    $deleteFile = $deleteFileStmt->fetch(PDO::FETCH_ASSOC);

    if ($deleteFile) {
      // ลบไฟล์จากฮาร์ดดิสก์
      $filePath = "fileUpload/" . $deleteFile["file_path"];
      if (unlink($filePath)) {
        // ลบข้อมูลไฟล์ออกจากฐานข้อมูล
        $deleteFileDataStmt = $conn->prepare("DELETE FROM `file` WHERE file_id = :file_id");
        $deleteFileDataStmt->bindParam(':file_id', $file_id);
        $deleteFileDataSuccess = $deleteFileDataStmt->execute();

        if ($deleteFileDataSuccess) {
          $_SESSION['success'] = "ลบข้อมูลและไฟล์เสร็จสมบูรณ์";
        } else {
          $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูลในฐานข้อมูล";
        }
      } else {
        $_SESSION['error'] = "ไม่สามารถลบไฟล์ได้";
      }
    } else {
      $_SESSION['error'] = "ไม่พบข้อมูลไฟล์";
    }
  } else {
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบความคิดเห็น";
  }

  header("location: ./Stduploadfile.php");
} else {
  $_SESSION['error'] = "ไม่พบรหัสไฟล์";
  header("location: ./Stduploadfile.php");
}
