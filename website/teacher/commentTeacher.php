<?php
session_start();
require_once "../connect.php";

if (isset($_GET['fileId']) && isset($_POST['comment'])) {
    $file_id = $_GET["fileId"];
    $comment = $_POST['comment'];
    $project_id = $_POST["project_id"];

    if (empty($comment)) {
        $_SESSION['error'] = "กรุณาใส่ข้อความความคิดเห็น";
        header("Location: ./STDUploadfile.php?id=$project_id");
        exit;
    }

    try {
        // Insert comment into the database
        $stmt = $conn->prepare("INSERT INTO `comments` (comment, comment_time, teacher_id, file_id) 
                                VALUES (:comment, CONCAT(YEAR(NOW()) + 543, DATE_FORMAT(NOW(), '-%m-%d %H:%i:%s')), :teacher_id, :file_id)");
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':teacher_id', $_SESSION['teacher_id']);
        $stmt->bindParam(':file_id', $file_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'แสดงความคิดเห็นสำเร็จ';

            // Fetch file data
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
            $stmt->bindParam(':project_id', $project_id);
            $stmt->execute();
            $projectdata = $stmt->fetch(PDO::FETCH_ASSOC);

            // Collect student IDs
            $student_id = array();
            $student_id[] = $projectdata['student_id1'];
            $student_id[] = $projectdata['student_id2'];

            if (!empty($projectdata['student_id3'])) {
                $student_id[] = $projectdata['student_id3'];
            }

            // Send email to each student
            foreach ($student_id as $data) {
                $stmt = $conn->prepare("SELECT * FROM `student` WHERE student_id = :student_id");
                $stmt->bindParam(':student_id', $data);
                $stmt->execute();
                $studentdata = $stmt->fetch(PDO::FETCH_ASSOC);

                $to = $studentdata['email'];
                $subject = "อาจารย์ที่ปรึกษาแสดงความคิดเห็นกับ $file_chapter";
                $message = "<html><body>";
                $message .= "<h2>อาจารย์ที่ปรึกษาแสดงความคิดเห็นกับ $file_chapter</h2>";
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
            $_SESSION['error'] = 'มีบางอย่างผิดพลาดในการเพิ่มข้อมูล';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header("Location: ./STDUploadfile.php?id=$project_id");
exit;
