<?php
session_start();
require_once "../connect.php";

if (isset($_POST['submit'])) {
  // collect value of input fields
  $inputnews_head = $_POST['inputnews_head'];
  $inputnews_text = $_POST['inputnews_text'];
  $inputyear = $_POST['inputyear'];
  $inputterm = $_POST['inputterm'];

  if (empty($inputnews_head)) {
    $_SESSION['error'] = 'กรุณากรอก news_head';
    header('location: Newsmanage.php');
  } elseif (empty($inputnews_text)) {
    $_SESSION['error'] = 'กรุณากรอก news_text';
    header('location: Newsmanage.php');
  } else {
    $inputyear = empty($inputyear) ? null : $inputyear;
    $inputterm = empty($inputterm) ? null : $inputterm;

    try {
      $stmt = $conn->prepare("INSERT INTO `news` (news_head, news_text, news_date, year, term) 
      VALUES (:inputnews_head, :inputnews_text, CONCAT(YEAR(NOW()) + 543, DATE_FORMAT(NOW(), '-%m-%d %H:%i:%s')), :inputyear, :inputterm)");

      $stmt->bindParam(':inputnews_head', $inputnews_head);
      $stmt->bindParam(':inputnews_text', $inputnews_text);
      $stmt->bindParam(':inputyear', $inputyear);
      $stmt->bindParam(':inputterm', $inputterm);

      $stmt->execute();
      $_SESSION['success'] = 'เพิ่มข้อมูลข่าวสารเสร็จสมบูรณ์!!';
      header('location: Newsmanage.php');
    } catch (PDOException $e) {
      $_SESSION['error'] = $e->getMessage();
      header('location: Newsmanage.php');
    }
  }
}
?>
