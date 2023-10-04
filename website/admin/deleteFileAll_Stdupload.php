<?php
session_start();
require '../connect.php'; // แก้ชื่อไฟล์ config.php ให้ถูกต้อง

if (isset($_GET['id'])) {
    $project_id = $_GET['id'];
    

    $checkGradeStmt = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id");
    $checkGradeStmt->bindParam(':project_id', $project_id);
    $checkGradeStmt->execute();
    $project = $checkGradeStmt->fetch(PDO::FETCH_ASSOC);
  
    $checkGradeStmt = $conn->prepare("SELECT * FROM `student` WHERE (student_id = :student_id1 OR student_id = :student_id2 OR student_id = :student_id3) AND (grade = 'i' OR grade = 'I')");
    $checkGradeStmt->bindParam(':student_id1', $project['student_id1']);
    $checkGradeStmt->bindParam(':student_id2', $project['student_id2']);
    $checkGradeStmt->bindParam(':student_id3', $project['student_id3']);
    $checkGradeStmt->execute();
    $projectWithGradeI = $checkGradeStmt->fetch(PDO::FETCH_ASSOC);

    // Check if project has grade 'I'
    // $checkGradeStmt = $conn->prepare("SELECT * FROM project WHERE project_id = :project_id AND (grade = 'i' OR grade = 'I')");
    // $checkGradeStmt->bindParam(':project_id', $project_id);
    // $checkGradeStmt->execute();
    // $projectWithGradeI = $checkGradeStmt->fetch(PDO::FETCH_ASSOC);

    if ($projectWithGradeI) {
        $_SESSION['error'] = "ไม่สามารถลบข้อมูลได้เนื่องจากมีเกรด I";
    } else {
        $deleteFilesStmt = $conn->prepare("SELECT * FROM `file` WHERE (project_id = :project_id AND file_chapter != 14) AND file_chapter != 13 ");
        $deleteFilesStmt->bindParam(':project_id', $project_id);
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

// Redirect back to Stduploadfile.php with project_id
header("location: ./Stduploadfile.php?id=" . $project_id);
