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
function giveGroupById($conn, $group_id)
{
    $sql = "SELECT * FROM `groups` WHERE group_id = :group_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':group_id', $group_id);
    $stmt->execute();
    return $stmt->fetch();
}
$sql = "SELECT * FROM `teacher`";
$stmt = $conn->prepare($sql);
$stmt->execute();
$teacherdatas = $stmt->fetchAll();


foreach ($teacherdatas as $teacherdata) {
    // สร้างคำสั่ง SQL ด้วย PDO
    $sql = "SELECT * FROM `appoint`
    WHERE DATE(appoint_date) BETWEEN DATE(:startDate) AND DATE(:endDate);";
    $stmt = $conn->prepare($sql);
    date_default_timezone_set('Asia/Bangkok');
    $currentDate = convertToBuddhistEra(date('Y-m-d'));
    $endDate = date('Y-m-d', strtotime($currentDate . ' + 1 days'));
    $stmt->bindParam(':startDate', $currentDate, PDO::PARAM_STR);
    $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
    $stmt->execute();
    $appointdatas = $stmt->fetchAll();
    foreach ($appointdatas as $appointdata) {
        if (empty($teacherdata['email'])) {
            continue;
        }

        $title = !empty($appointdata['title']) ? $appointdata['title'] : '';
        $description = !empty($appointdata['description']) ? $appointdata['description'] : '';
        $appoint_date = !empty($appointdata['appoint_date']) ? $appointdata['appoint_date'] : '';
        

        if (isset($appointdata['group_id'])) {
            $group_id = giveGroupById($conn, $appointdata['group_id']);
        } else {
            $group_id = 'ทุกกลุ่มเรียน';
        }
        
        $to = $teacherdata['email'];
        $subject = "แจ้งเตือนกำหนดการ : " . $title;
        $message = "<html><body>";
        $message .= "<h2>หัวข้อกำหนดการ : " . $title . "</h2>";
        $message .= "<p>กลุ่มเรียน : " . $group_id['group_name'] . "</p>";
        $message .= "<p>เนื้อหากำหนดการ : " . $description . "</p>";
        $message .= "<p>วันเวลาที่สิ้นสุดกำหนดการ(YYYY-MM-DD hh:mm:ss) : " . $appoint_date . "</p>";
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
