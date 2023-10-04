<?php
session_start();
unset($_SESSION['student_login']);
setcookie("user_login", '');
setcookie("user_password", '');
header('Location: ./index.php');
exit();
?>

