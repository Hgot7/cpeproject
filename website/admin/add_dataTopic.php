<?php
session_start();
require_once "../connect.php";

if (isset($_POST['submit'])) {
  $inputtopic_name = $_POST['topic_name'];
  $inputtopic_section_id = isset($_POST['topic_section_id']) ? $_POST['topic_section_id'] : null;
  // $inputtopic_level = isset($_POST['topic_level']) ? $_POST['topic_level'] : 0;
  $inputtopic_status = isset($_POST['topic_status']) ? $_POST['topic_status'] : 0;

  if (empty($inputtopic_name)) {
    $_SESSION['error'] = 'กรุณากรอกชื่อหัวข้อการประเมิน';
    header('location: topicmanage.php');
    exit(); // เพิ่มบรรทัดนี้เพื่อให้โปรแกรมหยุดทำงานทันที
  }

  try {
    $stmt = $conn->prepare("INSERT INTO `topic` (topic_name, topic_section_id, topic_status) 
                            VALUES (:inputtopic_name, :inputtopic_section_id, :inputtopic_status)");
    
    $stmt->bindParam(':inputtopic_name', $inputtopic_name);
    $stmt->bindParam(':inputtopic_section_id', $inputtopic_section_id, PDO::PARAM_INT);
    // $stmt->bindParam(':inputtopic_level', $inputtopic_level, PDO::PARAM_INT);
    $stmt->bindValue(':inputtopic_status', $inputtopic_status, PDO::PARAM_INT);

    $stmt->execute();
    
    $_SESSION['success'] = 'เพิ่มข้อมูลเรียบร้อยแล้ว';
    header('location: topicmanage.php');
    exit(); // เพิ่มบรรทัดนี้เพื่อให้โปรแกรมหยุดทำงานทันที
  } catch (PDOException $e) {
    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล '.$e->getMessage();
    header('location: topicmanage.php');
    exit(); // เพิ่มบรรทัดนี้เพื่อให้โปรแกรมหยุดทำงานทันที
  }
}
?>
