<?php

session_start();
require_once "../connect.php";
// $decoded_url = urldecode($_GET['id']);

// if( $_SESSION['student_id'] != $_GET['id']){
//   http_response_code(404);
//   exit('Page not found');
// }

if (!isset($_SESSION['teacher_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('Location: ../index.php');
    exit();
  }



  $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_id', $_SESSION['teacher_id']);
                        $stmt->execute();
                        $data = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link to custom Bootstrap CSS -->
    <link rel="stylesheet" href="../css/custom.css">
    <script src="../component.js"></script>

    <!-- Link to Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

    <!-- Link to icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">

    <title>โปรไฟล์ของอาจารย์</title>

</head>

<body>
    <!-- -------------------------------------------------Header------------------------------------------------- -->
    <div class="HeaderBg shadow">
        <div class="container">
        <navbar_teacher-component></navbar_teacher-component>
        </div>
    </div>
    <div class="container-fluid justify-content-around">

        <div class="row">

            <?php include("sidebarTeacherComponent.php"); ?>

            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">

                <!-- Modal -->
                <div class="modal fade" id="changepassword" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="changepasswordLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-4 text-primary" id="changepasswordLabel">เปลี่ยนรหัสผ่าน</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                            <form action="./NewPasswordTeacher.php" method="post" enctype="multipart/form-data">

                                <div class="col-12 mb-1">
                                    <label for="lastName" class="form-label">รหัสผ่านปัจจุบัน</label>
                                    <input type="password" placeholder="ใส่รหัสผ่านปัจจุบันที่ใช้งานอยู่" class="form-control" name="oldpassword">
                                </div>
                                <div class="col-12 mb-1">
                                    <label for="lastName" class="form-label">รหัสผ่านใหม่</label>
                                    <input type="password" placeholder="ใส่รหัสผ่านที่ต้องการความยาวตั้งแต่ 6-20 ตัวอักษร" class="form-control" name="newpassword" >
                                </div>
                                <div class="col-12 mb-1">
                                    <label for="lastName" class="form-label">ยืนยันรหัสใหม่</label>
                                    <input type="password" placeholder="ยืนยันรหัสผ่านใหม่ให้เหมือนกับรหัสผ่านใหม่ด้านบน" class="form-control" name="cnewpassword" >
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                <button type="submit" name="submit" class="btn btn-primary">ตกลง</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="row g-5">
                    <div class="col-md-7 col-lg-8">
                        <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลโปรไฟล์ของอาจารย์</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb fs-5 mt-3 ms-3">
                                <li class="breadcrumb-item"><a href="./Teacherpage.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active" aria-current="page">โปรไฟล์ของอาจารย์</li>
                            </ol>
                        </nav>
                        <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger" role="alert">
              <?php
              echo $_SESSION['error'];
              unset($_SESSION['error']);
              ?>
            </div>
          <?php } ?>
          <?php if (isset($_SESSION['success'])) { ?>
            <div class="alert alert-success" role="alert">
              <?php
              echo $_SESSION['success'];
              unset($_SESSION['success']);
              ?>
            </div>
          <?php } ?>
                        <form class="needs-validation" novalidate="">
                            <div class="row g-3">
                            <div class="col-12">
                                    <label for="Position" class="form-label">ตำแหน่งทางวิชาการ</label>
                                    <div class="input-group has-validation">
                                        <input type="text" class="form-control" id="Position" placeholder="Position" value="<?php echo $data['position']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="firstName" class="form-label">ชื่อ</label>
                                    <input type="text" class="form-control" id="firstName" placeholder="" value="<?php echo $data['firstname']; ?>" readonly>
                                </div>

                                <div class="col-sm-6">
                                    <label for="lastName" class="form-label">นามสกุล</label>
                                    <input type="text" class="form-control" id="lastName" placeholder="" value="<?php echo $data['lastname']; ?>" readonly>
                                </div>

                                <div class="col-12">
                                    <label for="username" class="form-label">ชื่อผู้ใช้งาน</label>
                                    <div class="input-group has-validation">
                                        <input type="text" class="form-control" id="username" placeholder="Username" value="<?php echo $data['teacher_id']; ?>" readonly>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="email" class="form-label">อีเมล</label>
                                    <input type="email" class="form-control" id="email" placeholder="you@mail.rmutt.ac.th" value="<?php echo $data['email']; ?>" readonly>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address for shipping updates.
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="address2" class="form-label">เบอร์โทร</label>
                                    <input type="text" class="form-control" id="address2" placeholder="Apartment or suite" value="<?php echo $data['phone']; ?>" readonly>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label">เปลี่ยนรหัสผ่านใหม่</label>
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary d-flex" data-bs-toggle="modal" data-bs-target="#changepassword">
                                        เปลี่ยนรหัสผ่าน
                                    </button>
                                </div>





            </main>

        </div>
    </div>
</body>

</html>