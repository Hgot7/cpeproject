<?php
session_start();
require_once "connect.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
function convertToBuddhistEra($datetime)
{
  $date = date('Y-m-d', strtotime($datetime));

  if ((((int)date('Y', strtotime($date))) - 543) < 2000) {
    // แปลงวันที่ในรูปแบบคริสต์ศักราช (AD) เป็นวันที่ในรูปแบบพุทธศักราช (พ.ศ.)
    $buddhistYear = (int)date('Y', strtotime($date)) + 543;
    $buddhistDate = $buddhistYear . date('-m-d', strtotime($date));
    return $buddhistDate;
  } else {
    return $datetime;
  }
}
function giveStatusById($conn, $project_id, $teacherId)
{

  $teacherCheckQuery = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id AND (teacher_id1 = :teacher_id OR teacher_id2 = :teacher_id)");
  $teacherCheckQuery->bindParam(':project_id', $project_id, PDO::PARAM_STR);
  $teacherCheckQuery->bindParam(':teacher_id', $teacherId, PDO::PARAM_STR);
  $teacherCheckQuery->execute();
  $teacherCheckResult = $teacherCheckQuery->fetchAll();

  $isTeacher = !empty($teacherCheckResult);

  $level = $isTeacher ? 1 : 0;

  $topicSectionQuery = $conn->prepare("SELECT COUNT(DISTINCT topic_section_id) AS unique_topic_section_count FROM `topicsection` WHERE (topic_section_level = :level or topic_section_level = 2) AND topic_section_status = 1");
  $topicSectionQuery->bindParam(':level', $level, PDO::PARAM_INT);
  $topicSectionQuery->execute();
  $topicSectionData = $topicSectionQuery->fetch();

  $assessmentQuery = $conn->prepare("SELECT COUNT(DISTINCT t.topic_section_id) AS unique_topic_section_count
        FROM `assessment` a
        INNER JOIN `topic` t ON a.topic_id = t.topic_id
        WHERE a.project_id = :project_id
        AND a.referee_id = :referee_id");
  $assessmentQuery->bindParam(':project_id', $project_id, PDO::PARAM_STR);
  $assessmentQuery->bindParam(':referee_id', $teacherId, PDO::PARAM_STR);
  $assessmentQuery->execute();
  $assessmentData = $assessmentQuery->fetch();

  return ($assessmentData['unique_topic_section_count'] == $topicSectionData['unique_topic_section_count']) ? 1 : 0;
}

function giveStatusNameById($conn, $project_id, $teacherId)
{

  $teacherCheckQuery = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id AND (teacher_id1 = :teacher_id OR teacher_id2 = :teacher_id)");
  $teacherCheckQuery->bindParam(':project_id', $project_id, PDO::PARAM_STR);
  $teacherCheckQuery->bindParam(':teacher_id', $teacherId, PDO::PARAM_STR);
  $teacherCheckQuery->execute();
  $teacherCheckResult = $teacherCheckQuery->fetchAll();

  $isTeacher = !empty($teacherCheckResult);

  $level = $isTeacher ? 1 : 0;

  $topicSectionQuery = $conn->prepare("SELECT DISTINCT topic_section_id FROM `topicsection` WHERE (topic_section_level = :level OR topic_section_level = 2) AND topic_section_status = 1");
  $topicSectionQuery->bindParam(':level', $level, PDO::PARAM_INT);
  $topicSectionQuery->execute();
  $topicSectionData = $topicSectionQuery->fetchAll(PDO::FETCH_COLUMN);

  $assessmentQuery = $conn->prepare("SELECT DISTINCT t.topic_section_id 
    FROM `assessment` a
    INNER JOIN `topic` t ON a.topic_id = t.topic_id
    WHERE a.project_id = :project_id
    AND a.referee_id = :referee_id");
  $assessmentQuery->bindParam(':project_id', $project_id, PDO::PARAM_STR);
  $assessmentQuery->bindParam(':referee_id', $teacherId, PDO::PARAM_STR);
  $assessmentQuery->execute();
  $assessmentData = $assessmentQuery->fetchAll(PDO::FETCH_COLUMN);

  $results = array_diff($topicSectionData, $assessmentData);


  $result = array(); // สร้างอาร์เรย์เพื่อเก็บข้อมูล topic_section

  foreach ($results as $data) {
    $topicSectionQuery = $conn->prepare("SELECT topic_section FROM `topicsection` WHERE topic_section_id = :topic_section_id");
    $topicSectionQuery->bindParam(':topic_section_id', $data, PDO::PARAM_INT);
    $topicSectionQuery->execute();
    $topicSectionData = $topicSectionQuery->fetch(PDO::FETCH_COLUMN);

    if ($topicSectionData !== false) { // ตรวจสอบว่าพบข้อมูลหรือไม่
      $result[] = $topicSectionData; // เพิ่มข้อมูลลงในอาร์เรย์ $result
    }
  }



  $result = implode(", ", $result);

  return $result;
}

// สร้างคำสั่ง SQL ด้วย PDO
$sql = "SELECT * FROM `timetest`
    WHERE DATE(timeTest_date) < DATE(:currentDate);";
$stmt = $conn->prepare($sql);
date_default_timezone_set('Asia/Bangkok');
$currentDate = convertToBuddhistEra(date('Y-m-d'));
$stmt->bindParam(':currentDate', $currentDate, PDO::PARAM_STR);
$stmt->execute();
$timeTestdatas = $stmt->fetchAll();
foreach ($timeTestdatas as $timeTestdata) {
  $sql = "SELECT grade FROM `student` WHERE student_id = (SELECT student_id1 FROM `project` WHERE project_id = :project_id);";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':project_id', $timeTestdata['project_id']);
  $stmt->execute();
  $studentGrade = $stmt->fetchColumn();
  if (isset($studentGrade)) {
    continue;
  }

  $sql = "SELECT * FROM `project` WHERE project_id = :project_id;";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':project_id', $timeTestdata['project_id']);
  $stmt->execute();
  $project = $stmt->fetch();

  $teacherIdAll = array();
  $teacherIdAll[0] = $project['teacher_id1'];

  if (!empty($project['teacher_id2'])) {
    $teacherIdAll[1] = $project['teacher_id2'];
    $teacherIdAll[2] = $project['referee_id'];
    $teacherIdAll[3] = $project['referee_id1'];
    $teacherIdAll[4] = $project['referee_id2'];
  } else {
    $teacherIdAll[1] = $project['referee_id'];
    $teacherIdAll[2] = $project['referee_id1'];
    $teacherIdAll[3] = $project['referee_id2'];
  }

  $teacherNo = array();
  $i = 0;
  foreach ($teacherIdAll as $teacher_id) {
    $j = giveStatusById($conn, $project['project_id'], $teacher_id);
    if ($j  == 0) {
      $teacherNo[$i] = $teacher_id;
      $i++;
    }
  }

  foreach ($teacherNo as $data) {
    $stmt = $conn->prepare("SELECT * FROM `teacher` WHERE teacher_id = :teacher_id");
    $stmt->bindParam(':teacher_id', $data);
    $stmt->execute();
    $teacherdata = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($teacherdata['email'])) {
      continue;
    }



    $to = $teacherdata['email'];
    $subject = "แจ้งเตือนรายการตกค้างที่ต้องประเมิน รหัสโครงงาน : " . $project['project_id'];
    $message = "<html><body>";
    $message .= "<h2>แจ้งเตือนรายการตกค้างที่ต้องประเมิน รหัสโครงงาน : " . $project['project_id'] . "</h2>";
    $message .= "<p>ชื่อโครงงาน : " . $project['project_nameTH'] . "</p>";
    $text = giveStatusNameById($conn, $project['project_id'], $data);
    $message .= "<p>ส่วนที่ไม่ประเมิน : " . $text . "</p>";
    $message .= "<p>ปีการศึกษา : " . $project['year'] . "</p>";
    $message .= "<p>ภาคการศึกษา : " . $project['term'] . "</p>";
    $message .= "</body></html>";

    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->SMTPDebug  = 2;
    $mail->Host       = "cpeproject.shop";
    $mail->Port       = 465;
    $mail->SMTPSecure = "ssl";
    $mail->SMTPAuth   = true;
    $mail->Username   = "cpeproject@cpeproject.shop";
    $mail->Password   = "Nanbowin_2030";
    $mail->addReplyTo("cpeproject@cpeproject.shop");
    $mail->setFrom("cpeproject@cpeproject.shop", "cpeproject@cpeproject.shop");
    $mail->addAddress($to);
    $mail->Subject  = $subject;
    $mail->isHTML(true);
    $mail->Body = $message;

    $mail->CharSet = "UTF-8"; // เพิ่มบรรทัดนี้เพื่อรองรับ UTF-8

    // Check if email sent successfully
    if ($mail->send()) {
      $_SESSION['success'] = "ส่งอีเมลสำเร็จ";
    } else {
      $_SESSION['error'] = "มีปัญหาในการส่งอีเมล: " . $mail->ErrorInfo;
    }
  }
}
