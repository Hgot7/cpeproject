<?php
session_start();
require_once "../connect.php";

if (isset($_GET['id']) && isset($_POST['file_chapter'])) {
  $id = $_GET["id"];
  $file_chapter = $_POST['file_chapter'];

  function giveProject_id($conn, $id)
  {
    $sql = "SELECT project_id FROM `project` WHERE ((student_id1 = :id OR student_id2 = :id OR student_id3 = :id));";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch();
  }

  if (isset($_FILES["file_path"]) && $_FILES["file_path"]["size"] > 0) {
    $targetDir = "fileUpload/";
    $file_path = basename($_FILES["file_path"]["name"]);
    $targetFile = $targetDir . $file_path;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if ($uploadOk == 1) {
      $project_id = giveProject_id($conn, $id);

      $stmt = $conn->prepare("SELECT file_path FROM `file` WHERE file_path = :file_path ");
      $stmt->bindParam(':file_path', $file_path);
      $stmt->execute();
      $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);

      // ตรวจสอบว่ามีไฟล์ที่มีชื่อเดียวกันอยู่แล้ว
      if ($existingFile) {
        $filename = pathinfo($file_path, PATHINFO_FILENAME); // ดึงชื่อไฟล์แต่ไม่รวมนามสกุล
        $file_extension = pathinfo($file_path, PATHINFO_EXTENSION); // ดึงนามสกุลไฟล์

        $counter = 1;
        while (file_exists($targetFile)) {
          // เพิ่มเลขต่อท้ายชื่อไฟล์
          $file_path = $filename . '_' . $counter . '.' . $file_extension;
          $targetFile = $targetDir . $file_path;
          $counter++;
        }
      }
      if (move_uploaded_file($_FILES["file_path"]["tmp_name"], $targetFile)) {
        // เพิ่มข้อมูลลงในฐานข้อมูล
        echo "<script>hideLoading();</script>"; // เรียกใช้ฟังก์ชันเพื่อซ่อน Popup Loading

        try {
          $file_status = "0";
          $stmt = $conn->prepare("INSERT INTO `file` (file_date, file_path, file_status, file_chapter, project_id) 
              VALUES (CONCAT(YEAR(NOW()) + 543, DATE_FORMAT(NOW(), '-%m-%d %H:%i:%s')), :file_path, :file_status, :file_chapter, :project_id)");
          $stmt->bindParam(':file_path', $file_path);
          $stmt->bindParam(':file_status', $file_status);
          $stmt->bindParam(':file_chapter', $file_chapter);
          $stmt->bindParam(':project_id', $project_id['project_id']);

          if ($stmt->execute()) {
            $_SESSION['success'] = 'เพิ่มข้อมูลเสร็จสมบูรณ์';

            switch ($file_chapter) {
              case 1:
                $file_chapter = "เอกสารหน้าปก";
                break;
              case 2:
                $file_chapter = "เอกสารลายเซ็นกรรมการ";
                break;
              case 3:
                $file_chapter = "เอกสารบทคัดย่อ";
                break;
              case 4:
                $file_chapter = "เอกสารสารบัญ";
                break;
              case 5:
                $file_chapter = "เอกสารโครงงานบทที่ 1";
                break;
              case 6:
                $file_chapter = "เอกสารโครงงานบทที่ 2";
                break;
              case 7:
                $file_chapter = "เอกสารโครงงานบทที่ 3";
                break;
              case 8:
                $file_chapter = "เอกสารโครงงานบทที่ 4";
                break;
              case 9:
                $file_chapter = "เอกสารโครงงานบทที่ 5";
                break;
              case 10:
                $file_chapter = "เอกสารบรรณานุกรม";
                break;
              case 11:
                $file_chapter = "เอกสารภาคผนวก";
                break;
              case 12:
                $file_chapter = "เอกสารประวัติผู้จัดทำ";
                break;
              case 13:
                $file_chapter = "เอกสารโปสเตอร์";
                break;
              case 14:
                $file_chapter = "เอกสารรูปเล่มฉบับเต็ม";
                break;
              default:
                $file_chapter = "ไม่พบข้อมูลที่ต้องการ";
                break;
            }

            $sql = "SELECT * FROM `project` WHERE project_id = :id;";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $project_id['project_id']);
            $stmt->execute();
            $projectdata = $stmt->fetch();

            $teacher_id = array();
            $teacher_id[] = $projectdata['teacher_id1'];
            if (isset($projectdata['teacher_id1'])) {
              $teacher_id[] = $projectdata['teacher_id2'];
            }



            foreach ($teacher_id as $data) {
              $stmt = $conn->prepare("SELECT * FROM `teacher` WHERE teacher_id = :teacher_id");
              $stmt->bindParam(':teacher_id', $data);
              $stmt->execute();
              $teacherdata = $stmt->fetch(PDO::FETCH_ASSOC);

              if(empty($teacherdata['email'])){continue;}
              $to = $teacherdata['email'];
              $subject = "นักศึกษาอัปโหลดไฟล์ $file_chapter รหัสโครงงาน: " . $project_id['project_id'];
              $message = "<html><body>";
              $message .= "<h2>นักศึกษาอัปโหลดไฟล์ $file_chapter</h2>";
              $message .= "<p>รหัสโครงงาน: " . $project_id['project_id'] . "</p>";
              $message .= "<p>ชื่อโครงงาน: " . $projectdata['project_nameTH'] . "</p>";
              $message .= "<p>ชื่อเอกสาร: " . $file_path . "</p>";
              $message .= "</body></html>";

              $headers = "From: cpeproject@cpeproject.shop\r\n";
              $headers .= "Reply-To: cpeproject@cpeproject.shop\r\n";
              $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

              // Send email
              if (mail($to, $subject, $message, $headers)) {
                $_SESSION['success'] = "เพิ่มข้อมูลเสร็จสมบูรณ์";
              } else {
                $_SESSION['error'] = "มีปัญหาในการแจ้งเตือน";
              }
            }

            header("location: ./Stduploadfile.php?id=" . $id);
            exit;
          } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล';
            header("location: ./Stduploadfile.php?id=" . $id);
            exit;
          }
        } catch (PDOException $e) {
          $_SESSION['error'] = $e->getMessage();
          header("location: ./Stduploadfile.php?id=" . $id);
          exit;
        }
      } else {
        $_SESSION['error'] = 'ขออภัย, ไฟล์ไม่ได้ถูกอัปโหลด';
        header("location: ./Stduploadfile.php?id=" . $id);
        exit;
      }
    }
  } else {
    $_SESSION['error'] = 'กรุณาเลือกไฟล์ใหม่ที่ต้องการอัปโหลด';
    header("location: ./Stduploadfile.php?id=" . $id);
    exit;
  }
} else {
  $file_path = "";
  $_SESSION['error'] = 'กรุณาเลือกไฟล์ใหม่ที่ต้องการอัปโหลด';
  header("location: ./Stduploadfile.php?id=" . $id);
  exit; // กำหนดค่าเป็นค่าว่างเมื่อไม่มีไฟล์ถูกอัปโหลด
}

header("location: ./Stduploadfile.php?id=" . $id);
exit;
