<?php
  session_start();
  require_once "../connect.php";


if (isset($_POST['submit'])) {
  // collect value of input field
  $inputstd_id = $_POST['inputstd_id'];
  $inputpassword = substr($inputstd_id, -6);
  $inputname = $_POST['inputname'];
  $inputlastname = $_POST['inputlastname'];
  $inputyear = $_POST['inputyear'];
  $inputterm = $_POST['inputterm'];
  $email = $inputstd_id."@mail.rmutt.ac.th";
  $inputphone = $_POST['inputphone'];
  $inputgroup = $_POST['inputgroup'];
  $inputgrade = $_POST['inputgrade'];
   // check empty is null

   if (empty($inputstd_id)) {
    $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    header("location: studentmanage.php");
    exit();
  }
  if (empty($inputname)) {
    $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    header("location: studentmanage.php");
    exit();
  }
  if (empty($inputlastname)) {
    $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    header("location: studentmanage.php");
    exit();
  }
  if (empty($inputyear)) {
    $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    header("location: studentmanage.php");
    exit();
  }
  if (empty($inputterm)) {
    $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    header("location: studentmanage.php");
    exit();
  }
  if (empty($inputgroup)) {
    $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    header("location: studentmanage.php");
    exit();
  }


  if(empty($inputpassword)){
    $passwordHash = null;
  }else {
    $passwordHash = password_hash($inputpassword, PASSWORD_DEFAULT);
  }
  $inputname = empty($inputname) ? null : $inputname;
  $inputlastname = empty($inputlastname) ? null : $inputlastname;
  $inputyear = empty($inputyear) ? null : $inputyear;
  $inputterm = empty($inputterm) ? null : $inputterm;
  $email = empty($email) ? null : $email;
  $inputphone = empty($inputphone) ? null : $inputphone;
  $inputgroup = empty($inputgroup) ? null : $inputgroup;
  $inputgrade = empty($inputgrade) ? null : $inputgrade;

  if(empty($inputstd_id)){
    $_SESSION['error'] = 'กรุณากรอก ID';
    header('location: studentmanage.php');

  }else {
    try {
       if(!isset($_SESSION['error'])){
        $stmt = $conn->prepare("INSERT INTO `student` (student_id, student_password,firstname,lastname,year,term,email,phone,group_id,grade) 
                VALUES (:inputstd_id ,:passwordHash,:inputname,:inputlastname,:inputyear,:inputterm,:email,:inputphone,:inputgroup,:inputgrade)");
        $stmt->bindParam(':inputstd_id', $inputstd_id);
        $stmt->bindParam(':passwordHash', $passwordHash);
        $stmt->bindParam(':inputname', $inputname);
        $stmt->bindParam(':inputlastname', $inputlastname);
        $stmt->bindParam(':inputyear',$inputyear);
        $stmt->bindParam(':inputterm', $inputterm);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':inputphone', $inputphone);
        $stmt->bindParam(':inputgroup', $inputgroup);
        $stmt->bindParam(':inputgrade', $inputgrade);

        $stmt->execute();
        $_SESSION['success'] = 'เพิ่มข้อมูลนักศึกษารหัส '.$inputstd_id.' สำเร็จ ';
        header('location: studentmanage.php');

    }else {
        $_SESSION['error'] = 'มีบางอย่างผิดพลาดเพิ่มข้อมูลนักศึกษาไม่สำเร็จ';
        header('location: studentmanage.php');
    }

    } catch(PDOException $e){
      $_SESSION['error'] = $e->getMessage();
      header('location: studentmanage.php');
      exit();
    }
  }
}

  // Prepare and bind SQL statement
  
 

?>