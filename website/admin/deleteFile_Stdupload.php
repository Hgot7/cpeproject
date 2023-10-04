<?php
session_start();
require '../connect.php'; // ปรับเปลี่ยนเป็นตำแหน่งและชื่อไฟล์ connect.php ที่ถูกต้อง

if (isset($_GET['id'])) {
  $file_id = $_GET['id'];
  
  // Fetch project_id for the given file_id
$projectIDFromFile = $conn->prepare("SELECT project_id FROM `file` WHERE file_id = :file_id");
$projectIDFromFile->bindParam(':file_id', $file_id);
$projectIDFromFile->execute();
$project_id = $projectIDFromFile->fetchColumn();

// Fetch project information
$checkGradeStmt = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id");
$checkGradeStmt->bindParam(':project_id', $project_id);
$checkGradeStmt->execute();
$project = $checkGradeStmt->fetch(PDO::FETCH_ASSOC);
  
$checkGradeStmt = $conn->prepare("SELECT * FROM `student` WHERE (student_id = :student_id1 OR student_id = :student_id2 OR student_id = :student_id3) AND (grade = 'i' OR grade = 'I')");
$checkGradeStmt->bindParam(':student_id1', $project['student_id1']);
$checkGradeStmt->bindParam(':student_id2', $project['student_id2']);
$checkGradeStmt->bindParam(':student_id3', $project['student_id3']);
$checkGradeStmt->execute();
$projectWithGradeI = $checkGradeStmt->fetch(PDO::FETCH_ASSOC);

  if ($projectWithGradeI) {
    $_SESSION['error'] = "ไม่สามารถลบข้อมูลได้เนื่องจากมีเกรด I";
    header("location: ./Stduploadfile.php?id=" . $project_id);
    exit;
  }else{
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
      $filePath = ".././student/fileUpload/" . $deleteFile["file_path"];
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
  }




  header("location: ./Stduploadfile.php?id=" . $project_id);
} else {
  $_SESSION['error'] = "ไม่พบรหัสไฟล์";
  header("location: ./Stduploadfile.php?id=" . $project_id);
}

