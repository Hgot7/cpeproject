<?php
session_start();
// unset($_SESSION['teacher_login']);
// unset($_SESSION['selectedYear']);
// unset($_SESSION['selectedTerm']);
// unset($_SESSION['selectedGroup']);
session_destroy();
setcookie("user_login", '');
setcookie("user_password", '');
header('Location: ./index.php');
exit();
?>