<?php
session_start();
require_once "connect.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // ตรวจสอบว่าตำแหน่งของไฟล์ autoload.php ถูกต้อง
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

function giveGroupById($conn, $group_id)
{
    $sql = "SELECT group_name FROM `groups` WHERE group_id = :group_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':group_id', $group_id);
    $stmt->execute();
    return $stmt->fetchColumn();
}


$sql = "SELECT * FROM `student` WHERE year = (SELECT year FROM `defaultsystem` WHERE default_system_id = :id) and term = (SELECT term FROM `defaultsystem` WHERE default_system_id = :id)";
$stmt = $conn->prepare($sql);
$defaultSystemId = 1;
$stmt->bindParam(':id', $defaultSystemId, PDO::PARAM_INT);
$stmt->execute();
$studentdatas = $stmt->fetchAll();


foreach ($studentdatas as $studentdata) {
    // สร้างคำสั่ง SQL ด้วย PDO
    $sql = "SELECT * FROM `appoint`
    WHERE DATE(appoint_date) BETWEEN DATE(:startDate) AND DATE(:endDate)
    AND ((group_id = (SELECT group_id FROM `student` WHERE student_id = :student_id)) OR (group_id IS NULL));
    ";
    $stmt = $conn->prepare($sql);
    date_default_timezone_set('Asia/Bangkok');
    $currentDate = convertToBuddhistEra(date('Y-m-d'));
    $endDate = date('Y-m-d', strtotime($currentDate . ' + 1 days'));
    $stmt->bindParam(':startDate', $currentDate, PDO::PARAM_STR);
    $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
    $stmt->bindParam(':student_id', $studentdata['student_id']);
    $stmt->execute();
    $appointdatas = $stmt->fetchAll();
    foreach ($appointdatas as $appointdata) {
        if (empty($studentdata['email'])) {
            continue;
        }

        $to = $studentdata['email'];
        $subject = "แจ้งเตือนกำหนดการ : " . ($appointdata['title'] ?? '');
        $message = "<html><body>";
        $message .= "<h2>หัวข้อกำหนดการ : " . ($appointdata['title'] ?? '') . "</h2>";

        $group_id = (!empty($appointdata['group_id'])) ? giveGroupById($conn, $appointdata['group_id']) : 'ทุกกลุ่มเรียน';
        
        $message .= "<p>กลุ่มเรียน : " . $group_id . "</p>";
        $message .= "<p>เนื้อหากำหนดการ : " . ($appointdata['description'] ?? '') . "</p>";
        $message .= "<p>วันเวลาที่สิ้นสุดกำหนดการ(YYYY-MM-DD hh:mm:ss) : " . ($appointdata['appoint_date'] ?? '') . "</p>";
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

    $mail->CharSet = "UTF-8";

        // Check if email sent successfully
        if ($mail->send()) {
            $_SESSION['success'] = "ส่งอีเมลสำเร็จ";
        } else {
            $_SESSION['error'] = "มีปัญหาในการส่งอีเมล: " . $mail->ErrorInfo;
        }
    }
}
