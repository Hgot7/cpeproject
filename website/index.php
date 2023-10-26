<?php

session_start();
require_once "connect.php";

//ตำแหน่งท่านอาจารย์ 
function giveTeacherPositionById($Position)
                      {
                        switch ($Position) {
                          case "ศาสตราจารย์":
                            return $Position = "ศ.";
                            break;
                          case "ศาสตราจารย์ ดร.":
                            return $Position = "ศ.ดร.";
                            break;
                          case "รองศาสตราจารย์":
                            return $Position = "รศ.";
                            break;
                          case "รองศาสตราจารย์ ดร.":
                            return $Position = "รศ.ดร.";
                            break;
                          case "ผู้ช่วยศาสตราจารย์":
                            return $Position = "ผศ.";
                            break;
                          case "ผู้ช่วยศาสตราจารย์ ดร.":
                            return $Position = "ผศ.ดร.";
                            break;
                          case "อาจารย์":
                            return $Position = "อ.";
                            break;
                          case "อาจารย์ ดร.":
                            return $Position = "อ.ดร.";
                            break;
                          case "ดร.":
                            return $Position = "ดร.";
                            break;
                          default:
                            return $Position = $Position;
                        }
                      }

// ตรวจสอบ cookie มีอยู่หรือไม่
if (isset($_COOKIE['user_loginbuffer']) && isset($_COOKIE['user_passwordbuffer'])) {
  setcookie("user_login", $_COOKIE['user_loginbuffer']);
  setcookie("user_password", $_COOKIE['user_passwordbuffer']);
}

if (!isset($_COOKIE['user_login'])) {
  setcookie("user_login", '');
  setcookie("user_password", '');
} elseif (isset($_COOKIE['user_login'])) {
  $username = $_COOKIE['user_login'];
  $password = $_COOKIE['user_password'];

  // setcookie("user_loginbuffer", '');
  // setcookie("user_passwordbuffer", '');

  // Student query
  $data_student = $conn->prepare("SELECT * FROM student WHERE student_id = :username");
  $data_student->bindParam(":username", $username);
  $data_student->execute();
  $std = $data_student->fetch(PDO::FETCH_ASSOC);
  // Teacher query
  $data_teacher = $conn->prepare("SELECT * FROM `teacher` WHERE teacher_username = :username");
  $data_teacher->bindParam(":username", $username);
  $data_teacher->execute();
  $teacher = $data_teacher->fetch(PDO::FETCH_ASSOC);

  if ($data_student->rowCount() > 0) {
    if ($username == $std['student_id']) {
      if (password_verify($password, $std['student_password'])) {
        $_SESSION['student_login'] = $std['firstname'];
        $_SESSION['student_id'] = $std['student_id'];

        $url = "student/Stdpage.php?id=" . urlencode($std['student_id']);
        header("location: $url");
        exit();
      } else {
        $_SESSION['error'] = 'รหัสผ่านผิดสำหรับนักศึกษา';
        header("location: index.php");
        exit();
      }
    } else {
      $_SESSION['error'] = 'ชื่อผู้ใช้งานผิด';
      header("location: index.php");
      exit();
    }
  } elseif ($data_teacher->rowCount() > 0) {
    if ($username == $teacher['teacher_username'] && $teacher['level_id'] == 0) {
      if (password_verify($password, $teacher['teacher_password'])) {
        $_SESSION['admin_login'] = 'admin';
        header("location: admin/adminpage.php");
        exit();
      } else {
        $_SESSION['error'] = 'รหัสผ่านผิด';
        header("location: index.php");
        exit();
      }
    } elseif ($username == $teacher['teacher_username']) {
      if (password_verify($password, $teacher['teacher_password'])) {
        //อาจารย์ที่ปรึกษาหลัก
        $sql = "SELECT * FROM `teacher` WHERE teacher_username = :teacher_username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':teacher_username', $username);
        $stmt->execute();
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
        $teachername = giveTeacherPositionById($teacher['position']) . $teacher['firstname'];

        $_SESSION['teacher_login'] = $teachername;
        $_SESSION['teacher_id'] = $teacher['teacher_id'];
        $url = "teacher/Teacherpage.php?id=" . urlencode($teacher['teacher_id']);
        header("location: $url");
        exit();
      } else {
        $_SESSION['error'] = 'รหัสผ่านผิดสำหรับอาจารย์';
        header("location: index.php");
        exit();
      }
    } else {
      $_SESSION['error'] = 'ชื่อผู้ใช้งานผิด';
      header("location: index.php");
      exit();
    }
  } else {
    $_SESSION['error'] = "ไม่มีข้อมูลในระบบ";
    header("location: index.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Link to custom Bootstrap CSS -->
  <link rel="stylesheet" href="./css/custom.css">
  <script src="./component.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  <!-- Link to icon -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
  <title>Login</title>
</head>
<!--  -------------------------------------------Header------------------------------------------- -->

<body class="text-center" id="backgroundlogin">
  <div class="HeaderBg">
    <div class="container">
      <!-- <navbar_index-component></navbar_index-component> -->
    </div>
  </div>
  <!-- ------------------------------------------------------------------------------------------------------------ -->
  <!-- loging -->
  <div class="container-fluid d-flex align-items-center justify-content-center" id="hanging-icons">

    <!-- <div class="row">
      <div class="col-6">
        <div class="card shadow h-100" id="cardlogin">
          <div class="card-body d-flex justify-content-center align-items-center flex-row p-0">
            <div class="text-white text-center">
              <h3 class="fs-1">Project Management System</h3>
              <p class="fs-2 text-white">Computer Engineering</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card shadow text-white text-center p-0" id="cardlogindetail">
          <div class="card-body justify-content-center align-items-center">
            <h3 class="fs-1">Welcome Page</h3>
            <p class="fs-2">Login to continue access</p>
            <a href="index.php">
              <button type="button" class="btn btn-outline-primary me-2 fs-2">Login</button>
            </a>
          </div>
        </div>
      </div>
    </div> -->
    <div class="container">
      <div class="container mt-5">
        <div class="row justify-content-center" id="boxlogin">
          <div class="card shadow col-4 bg-secondary">
            <main class="form-signin w-100 m-auto text-white">
              <a href="index.php" class="d-flex align-items-center text-white text-decoration-none">
                <h2 class="text-start" style="font-size: 56px;">Project Management System</h2>
              </a>
            </main>
          </div>

          <div class="col-4">
            <div class="card shadow" id="cardloginright">
              <main class="form-signin w-100 m-auto mb-3">
                <form action="signin.php" method="post">
                  <img class="mb-4" src="picture/logoRMUTT.png" alt="" width="40%" height="20%">
                  <h1 class="h3 mb-3 fw-normal">Please Login</h1>
                  <?php if (isset($_SESSION['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                      <?php
                      echo $_SESSION['error'];
                      unset($_SESSION['error']);
                      ?></div>
                  <?php  } ?>
                  <?php if (isset($_SESSION['success'])) { ?>
                    <div class="alert alert-success" role="alert">
                      <?php
                      echo $_SESSION['success'];
                      unset($_SESSION['success']);
                      ?></div>
                  <?php  } ?>
                  <div class="form-floating">
                    <input type="text" name="inputusername" class="form-control" id="floatingInput" value="<?= htmlspecialchars($_COOKIE['user_loginbuffer'] ?? '') ?>" placeholder="name@example.com">
                    <label for="floatingInput">Username</label>
                  </div>
                  <div class="form-floating mt-2">
                    <input type="password" name="inputpassword" class="form-control" id="floatingPassword" value="<?= htmlspecialchars($_COOKIE['user_passwordbuffer'] ?? '') ?>" placeholder="Password">
                    <label for="floatingPassword">Password</label>
                  </div>

                  <div class="form-check text-start my-3">
                    <input class="form-check-input" type="checkbox" name="remember" <?php if (isset($_COOKIE['user_loginbuffer'])) { ?> checked <?php } ?> id="flexCheckDefault">
                    <label class="form-check-label" for="flexCheckDefault">
                      Remember me
                    </label>
                  </div>

                  <button class="w-100 btn btn-lg btn-primary" name="signin" type="submit">Login</button>

                  <!-- <p class="mt-5 mb-3 text-muted">😎© 2023–2025</p> -->
                </form>



              </main>
            </div>
          </div>
        </div>
      </div>





      <!-- <main class="form-signin w-100 m-auto vh-100">


        <form action="signin.php" method="post">
          <img class="mb-4" src="picture/logoRMUTT.png" alt="" width="40%" height="20%">
          <h1 class="h3 mb-3 fw-normal">Please Login</h1>
          <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger" role="alert">
              <?php
              echo $_SESSION['error'];
              unset($_SESSION['error']);
              ?></div>
          <?php  } ?>
          <?php if (isset($_SESSION['success'])) { ?>
            <div class="alert alert-success" role="alert">
              <?php
              echo $_SESSION['success'];
              unset($_SESSION['success']);
              ?></div>
          <?php  } ?>
          <div class="form-floating">
            <input type="text" name="inputusername" class="form-control" id="floatingInput" value="<?= htmlspecialchars($_COOKIE['user_loginbuffer'] ?? '') ?>" placeholder="name@example.com">
            <label for="floatingInput">Username</label>
          </div>
          <div class="form-floating mt-2">
            <input type="password" name="inputpassword" class="form-control" id="floatingPassword" value="<?= htmlspecialchars($_COOKIE['user_passwordbuffer'] ?? '') ?>" placeholder="Password">
            <label for="floatingPassword">Password</label>
          </div>

          <div class="form-check text-start my-3">
            <input class="form-check-input" type="checkbox" name="remember" <?php if (isset($_COOKIE['user_loginbuffer'])) { ?> checked <?php } ?> id="flexCheckDefault">
            <label class="form-check-label" for="flexCheckDefault">
              Remember me
            </label>
          </div>

          <button class="w-100 btn btn-lg btn-primary" name="signin" type="submit">Login</button>

          <p class="mt-5 mb-3 text-muted">😎© 2023–2025</p>
        </form>
      </main> -->
    </div>
  </div>



</body>

</html>