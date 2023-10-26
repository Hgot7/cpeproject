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

            
            $stmt = $conn->prepare("SELECT * FROM `file` WHERE file_id = :file_id");
            $stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
            $stmt->execute();
            $filedata = $stmt->fetch(PDO::FETCH_ASSOC);

            switch ($filedata['file_chapter']) {
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

            // Fetch project data
            $stmt = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id");
            $stmt->bindParam(':project_id', $filedata['project_id']);
            $stmt->execute();
            $projectdata = $stmt->fetch(PDO::FETCH_ASSOC);

            // Collect student IDs
            $teacher_id = array();
            $teacher_id[] = $projectdata['teacher_id1'];

            if (!empty($projectdata['teacher_id2'])) {
                $teacher_id[] = $projectdata['teacher_id2'];
            }

            // Send email to each student
            foreach ($teacher_id as $data) {
                $stmt = $conn->prepare("SELECT * FROM `teacher` WHERE teacher_id = :teacher_id");
                $stmt->bindParam(':teacher_id', $data);
                $stmt->execute();
                $teacherdata = $stmt->fetch(PDO::FETCH_ASSOC);
                if(empty($teacherdata['email'])){continue;}
                $to = $teacherdata['email'];
                $subject = "นักศึกษาที่ปรึกษาแสดงความคิดเห็นกับ $file_chapter";
                $message = "<html><body>";
                $message .= "<h2>นักศึกษาที่ปรึกษาแสดงความคิดเห็นกับ $file_chapter</h2>";
                $message .= '<p>ชื่อเอกสาร : ' . $filedata['file_path'] . '</p>';
                $message .= "<p>ข้อความ : " . htmlspecialchars($comment) . '</p>';
                $message .= "</body></html>";

                $headers = "From: cpeproject@cpeproject.shop\r\n";
                $headers .= "Reply-To: cpeproject@cpeproject.shop\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                // Send email
                if (mail($to, $subject, $message, $headers)) {
                    $_SESSION['success'] = "แสดงความคิดเห็นสำเร็จ";
                } else {
                    $_SESSION['error'] = "มีปัญหาในการแสดงความคิดเห็น";
                }
            }

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
