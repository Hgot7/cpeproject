<?php
  session_start();
  require_once "../connect.php";


if (isset($_POST['submit'])) {
  // collect value of input field
  $topic_section = isset($_POST['topic_section']) ? $_POST['topic_section'] : null;
  $topic_section_weight = isset($_POST['topic_section_weight']) ? $_POST['topic_section_weight'] : 0;
  $topic_section_level = isset($_POST['topic_section_level']) ? $_POST['topic_section_level'] : 0;
  $topic_section_format = isset($_POST['topic_section_format']) ? $_POST['topic_section_format'] : 0;
  $topic_section_status = isset($_POST['topic_section_status']) ? $_POST['topic_section_status'] : 0;
  // $inputurole = $_POST['inputurole'];
if($topic_section_weight == 0){
  $topic_section_status = 0;
}
  if(empty($topic_section)){
    $_SESSION['error'] = 'กรุณากรอก ส่วนของการประเมินโครงงาน';
    header('location: topicSectionmanage.php');

 
  }else {
    try {
       if(!isset($_SESSION['error'])){
        $stmt = $conn->prepare("INSERT INTO `topicsection` (topic_section, topic_section_weight, topic_section_level, topic_section_format, topic_section_status) VALUES (:topic_section, :topic_section_weight, :topic_section_level, :topic_section_format, :topic_section_status)");
        $stmt->bindParam(':topic_section', $topic_section);
        $stmt->bindParam(':topic_section_weight', $topic_section_weight, PDO::PARAM_INT);
        $stmt->bindParam(':topic_section_level', $topic_section_level, PDO::PARAM_INT);
        $stmt->bindParam(':topic_section_format', $topic_section_format, PDO::PARAM_INT);
        $stmt->bindParam(':topic_section_status', $topic_section_status, PDO::PARAM_INT);

        $stmt->execute();
        $_SESSION['success'] = 'เพิ่มข้อมูลส่วนของการประเมินโครงงานสำเร็จ';
        header('location: topicSectionmanage.php');

    }else {
        $_SESSION['error'] = 'มีบางอย่างผิดพลาดเพิ่มข้อมูลส่วนของการประเมินโครงงานไม่สำเร็จ';
        header('location: topicSectionmanage.php');
    }

    } catch(PDOException $e){
      $_SESSION['error'] = $e->getMessage();
    }
  }
}

  // Prepare and bind SQL statement
  
 

?>