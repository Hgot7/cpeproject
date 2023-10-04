<?php

session_start();
require_once "../connect.php";


if (isset($_POST['submit'])) {
    $oldpassword = $_POST['oldpassword'];
    $newpassword = $_POST['newpassword'];
    $cnewpassword = $_POST['cnewpassword'];

    // Fetch the hashed password from the database
    $sql = "SELECT student_password FROM `student` WHERE student_id = :student_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':student_id', $_SESSION['student_id']);
    $stmt->execute();
    $hashedPassword = $stmt->fetchColumn();

    // Verify the old password
    if (!password_verify($oldpassword, $hashedPassword)) {
        $_SESSION['error'] = 'รหัสผ่านปัจจุบันไม่ถูกต้อง';
        header("Location: ./profileStd.php");
        exit();
    }

    if ($newpassword != $cnewpassword) {
        $_SESSION['error'] = 'รหัสผ่านใหม่ไม่ตรงกับรหัสผ่านใหม่ที่ยืนยัน';
        header("Location: ./profileStd.php");
        exit();
    }

    // Hash the new password before updating
    $newHashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);

    // Update the password in the database
    $sql = $conn->prepare("UPDATE `student` SET student_password = :student_password WHERE student_id = :student_id");
    $sql->bindParam(':student_id', $_SESSION['student_id']);
    $sql->bindParam(':student_password', $newHashedPassword);
    $sql->execute();
}
$_SESSION['success'] = 'เปลี่ยนรหัสผ่านใหม่สำเร็จ';
header("Location: ./profileStd.php");
exit();

?>
