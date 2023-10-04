<?php
  session_start();
  require_once "../connect.php";


if (isset($_POST['submit'])) {
  // collect value of input field
  $document_path = $_POST['document_path'];

  if (isset($_FILES["document_path"]) && $_FILES["document_path"]["size"] > 0) {
    $targetDir = "uploadfileDocument/";
    $document_path = basename($_FILES["document_path"]["name"]);
    $targetFile = $targetDir . $document_path;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if ($uploadOk == 1) {
      // ดำเนินการตรวจสอบชื่อไฟล์ซ้ำ
      $stmt = $conn->prepare("SELECT document_path FROM `document` WHERE document_path = :document_path");
      $stmt->bindParam(':document_path', $document_path);
      $stmt->execute();
      $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($existingFile) {
        $_SESSION['error'] = 'ชื่อไฟล์ซ้ำกันในระบบ ไม่สามารถอัปโหลดได้';
        header('location: documentmanage.php');
        exit;
      } else {
        if (move_uploaded_file($_FILES["document_path"]["tmp_name"], $targetFile)) {
          // เพิ่มข้อมูลลงในฐานข้อมูล
        } else {
          $_SESSION['error'] = 'ขออภัย, ไฟล์ไม่ได้ถูกอัปโหลด';
          header('location: documentmanage.php');
          exit;
        }
      }
    } else {
      $_SESSION['error'] = 'กรุณาเลือกไฟล์ใหม่ที่ต้องการอัปโหลด';
      header('location: documentmanage.php');
      exit;
    }
  } else {
    $document_path = ""; // กำหนดค่าเป็นค่าว่างเมื่อไม่มีไฟล์ถูกอัปโหลด
  }


  $document_name = $_POST['document_name'];
  $year = $_POST['year'];
  $term = $_POST['term'];
  

  // if(empty($topic_section)){
  //   $_SESSION['error'] = 'กรุณากรอก ส่วนของการประเมินโครงงาน';
  //   header('location: documentmanage.php');
    try {
       if(!isset($_SESSION['error'])){
        $stmt = $conn->prepare("INSERT INTO `document` (document_path, document_name, document_date, year, term) VALUES (:document_path, :document_name, CONCAT(YEAR(NOW()) + 543, DATE_FORMAT(NOW(), '-%m-%d %H:%i:%s')), :year, :term)");
        $stmt->bindParam(':document_path', $document_path);
        $stmt->bindParam(':document_name', $document_name);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':term', $term);
        
    

        $stmt->execute();
        $_SESSION['success'] = 'เพิ่มข้อมูลเอกสารสำเร็จ';
        header('location: documentmanage.php');

    }else {
        $_SESSION['error'] = 'มีบางอย่างผิดพลาดเพิ่มข้อมูลเอกสารไม่สำเร็จ';
        header('location: documentmanage.php');
    }

    } catch(PDOException $e){
      $_SESSION['error'] = $e->getMessage();
    }
  }


  // Prepare and bind SQL statement
  
 

?>