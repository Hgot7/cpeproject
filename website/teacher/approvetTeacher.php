<?php
session_start();
require_once "../connect.php";

if (isset($_GET['fileId'])) {
    $file_id = $_GET["fileId"];
    $project_id = $_GET["project_id"];
    $file_chapter = $_GET["file_chapter"];


    $sql = "SELECT * FROM `file` WHERE file_id = :file_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
    $stmt->execute();
    $FILE = $stmt->fetch(PDO::FETCH_ASSOC); // Use fetch instead of fetchAll

    if ($FILE && isset($FILE['file_status'])) {
        $file_status = 0;

        try {
            $updateStmt = $conn->prepare("UPDATE `file` SET file_status = :file_status WHERE project_id = :project_id and file_chapter = :file_chapter");
            $updateStmt->bindParam(':project_id', $project_id);
            $updateStmt->bindParam(':file_chapter', $file_chapter, PDO::PARAM_INT); // หรือ PDO::PARAM_STR ถ้า file_chapter เป็น string
            $updateStmt->bindParam(':file_status', $file_status, PDO::PARAM_INT);


            if ($updateStmt->execute()) {
                $file_status = ($FILE['file_status'] == 1) ? 0 : 1;
                $updateStmt = $conn->prepare("UPDATE `file` SET file_status = :file_status WHERE file_id = :file_id");
                $updateStmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
                $updateStmt->bindParam(':file_status', $file_status, PDO::PARAM_INT);
                $updateStmt->execute();
                
                if ($file_status) {
                    $_SESSION['success'] = 'อนุมัติเอกสารสำเร็จ';
                }else $_SESSION['success'] = 'ยกเลิกอนุมัติเอกสารสำเร็จ';
                
            } else {
                $_SESSION['error'] = '<a class="alert alert-danger" role="alert">Something went wrong. Failed to update data.</a>';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    } else {
        $_SESSION['error'] = '<a class="alert alert-danger" role="alert">File not found.</a>';
    }
}

header("Location: ./STDUploadfile.php?id=$project_id");
exit;
