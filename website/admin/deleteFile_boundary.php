<?php
session_start();
require '../connect.php'; // เปลี่ยนเป็นตำแหน่งและชื่อไฟล์ config.php ที่ถูกต้อง

if (isset($_GET['id'])) {
  $project_id = $_GET['id'];
  $stmt = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id");
  $stmt->bindParam(':project_id', $project_id);
  $stmt->execute();
  $delete_File = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($delete_File) {
    $deletestmt = $conn->prepare("UPDATE `project` SET boundary_path = null WHERE project_id = :project_id");
  $deletestmt->bindParam(':project_id', $project_id);
  if ($deletestmt->execute()) {
    $_SESSION['success'] = "ลบข้อมูลเสร็จสมบูรณ์";
    header("location: ./projectmanage.php");
  } else {
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
    header("location: ./projectmanage.php");
  }
    unlink("uploadfileBoundary/" . $delete_File["boundary_path"]);
    $_SESSION['success'] = "ลบไฟล์เสร็จสมบูรณ์";
  }
}

// ส่งค่า project_id ไปยังไฟล์ editStudent.php
header("location: ./editproject.php?id=" . $project_id);
