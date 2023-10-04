<?php
session_start();
require_once "../connect.php";

if (isset($_GET['fileId']) && isset($_POST['comment'])) {
    $file_id = $_GET["fileId"];
    $comment = $_POST['comment'];
if(empty($comment)){
    $_SESSION['error'] = "ไม่มีข้อความการแสดงความคิดเห็น";
    header('Location: ./Stduploadfile.php');
    exit;
}
    try {
        $stmt = $conn->prepare("INSERT INTO `comments` (comment, comment_time, student_id, file_id) VALUES (:comment, CONCAT(YEAR(NOW()) + 543, DATE_FORMAT(NOW(), '-%m-%d %H:%i:%s')), :student_id, :file_id)");
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':student_id', $_SESSION['student_id']);
        $stmt->bindParam(':file_id', $file_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'แสดงความคิดสำเร็จ';
        } else {
            $_SESSION['error'] = '<a class="alert alert-danger" role="alert">มีบางอย่างผิดพลาด fail! add data</a>';
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header('Location: ./Stduploadfile.php');
exit;
?>
