<?php
session_start();
require '../connect.php'; // เปลี่ยนเป็นตำแหน่งและชื่อไฟล์ config.php ที่ถูกต้อง

if (isset($_GET['projects'])) {
    $projects = json_decode(urldecode($_GET['projects']), true);
    foreach ($projects as $project) {

  
    $checkGradeStmt = $conn->prepare("SELECT * FROM `student` WHERE (student_id = :student_id1 OR student_id = :student_id2 OR student_id = :student_id3) AND (grade = 'i' OR grade = 'I')");
    $checkGradeStmt->bindParam(':student_id1', $project['student_id1']);
    $checkGradeStmt->bindParam(':student_id2', $project['student_id2']);
    $checkGradeStmt->bindParam(':student_id3', $project['student_id3']);
    $checkGradeStmt->execute();
    $projectWithGradeI = $checkGradeStmt->fetch(PDO::FETCH_ASSOC);

        // $checkGradeStmt = $conn->prepare("SELECT * FROM project WHERE project_id = :project_id AND (grade = 'i' OR grade = 'I')");
        // $checkGradeStmt->bindParam(':project_id', $project['project_id']);
        // $checkGradeStmt->execute();
        // $projectWithGradeI = $checkGradeStmt->fetch(PDO::FETCH_ASSOC);
    
        if ($projectWithGradeI) {
           continue;
        } else {
            $deleteFilesStmt = $conn->prepare("SELECT * FROM `file` WHERE (project_id = :project_id AND file_chapter != 14) AND file_chapter != 13");
            $deleteFilesStmt->bindParam(':project_id', $project['project_id']);
            $deleteFilesStmt->execute();
            $deleteFiles = $deleteFilesStmt->fetchAll(PDO::FETCH_ASSOC);

            $deleteFilesStmt = $conn->prepare("SELECT * FROM `file` WHERE project_id = :project_id AND (file_chapter = 14 OR file_chapter = 13) AND file_status = 0;");
        $deleteFilesStmt->bindParam(':project_id', $project_id);
        $deleteFilesStmt->execute();
        $deleteFiles2 = $deleteFilesStmt->fetchAll(PDO::FETCH_ASSOC);

        $deleteFiles = array_merge($deleteFiles, $deleteFiles2); 
    
            foreach ($deleteFiles as $deleteFile) {
                $file_id = $deleteFile['file_id'];
                // Delete associated comments
                $deleteCommentsStmt = $conn->prepare("DELETE FROM `comments` WHERE file_id = :file_id");
                $deleteCommentsStmt->bindParam(':file_id', $file_id);
                $deleteCommentsSuccess = $deleteCommentsStmt->execute();
    
                // Delete file and update session messages
                $file_path = "../student/fileUpload/" . $deleteFile["file_path"];
                $deleteFileStmt = $conn->prepare("DELETE FROM `file` WHERE file_id = :file_id");
                $deleteFileStmt->bindParam(':file_id', $file_id);
    
                if ($deleteCommentsSuccess && $deleteFileStmt->execute()) {
                    unlink($file_path);
                    $_SESSION['success'] = "ลบข้อมูลและไฟล์เสร็จสมบูรณ์";
                } else {
                    $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
                }
            }
    
            // Redirect with appropriate session message
            $_SESSION['success'] = "ลบข้อมูลเสร็จสมบูรณ์";
        }
    }
}

// ส่งค่า project_id ไปยังไฟล์ projectmanage.php
$project_ids = implode(',', array_column($projects, 'project_id'));
header("location: ./projectmanage.php?id=" . $project_ids);
