<?php

session_start();
require_once "../connect.php";

// $decoded_url = urldecode($_GET['id']);

// if( $_SESSION['student_id'] != $_GET['id']){
//   http_response_code(404);
//   exit('Page not found');
// }

if (!isset($_SESSION['student_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบนักศึกษา';
    header('Location: ../index.php');
    exit();
}

$sql = "SELECT * FROM `project` WHERE student_id1 = :student_id or student_id2 = :student_id or student_id3 = :student_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':student_id', $_SESSION['student_id']);
$stmt->execute();
$project = $stmt->fetch();

function giveStudentdentNameById($conn, $student_id)
{
    $sql = "SELECT * FROM `student` WHERE student_id = :student_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();

    $result = $stmt->fetch(); // รับผลลัพธ์จากคำสั่ง SQL

    if ($result !== false) { // ตรวจสอบว่ามีข้อมูลหรือไม่
        return $result['firstname'] . " " . $result['lastname']; // ส่งกลับข้อมูลไฟล์
    }

    return null; // หรือส่งค่าอื่นที่เหมาะสมตามสถานการณ์
}

function assessmentCheck($conn, $project_id, $data, $teacher_id)
{
    $stmt = $conn->prepare("SELECT a.* 
        FROM `assessment` a
        INNER JOIN `topic` t ON a.topic_id = t.topic_id
        WHERE t.topic_section_id = :topic_section_id
        AND a.referee_id = :referee_id
        AND a.project_id = :project_id");

    $stmt->bindParam(':topic_section_id', $data);
    $stmt->bindParam(':referee_id', $teacher_id);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return count($result) > 0 ? 1 : 0;
}

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

    <title>สถานะโครงงาน</title>

</head>

<body>
    <!-- -------------------------------------------------Header------------------------------------------------- -->
    <div class="HeaderBg shadow">
        <div class="container">
            <navbar_std-component></navbar_std-component> <!-- component.js Navber-->
        </div>
    </div>
    <div class="container-fluid justify-content-around">
        <div class="row">
            <?php include("sidebarStdComponent.php"); ?>
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">
                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลสถานะโครงงาน</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb fs-5 mt-3 ms-3">
                        <li class="breadcrumb-item"><a href="./Stdpage.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active" aria-current="page">สถานะโครงงาน</li>
                    </ol>
                </nav>
                <?php if(!empty($project['project_id'])){ ?>
                <div class="row align-items-center justify-content-center">
                    <div class="col">
                        <a class="fs-4 text-decoration-none" style="font-family: 'IBM Plex Sans Thai', sans-serif;">รหัสกลุ่มโครงงาน : <?php echo $project['project_id'] ?></a><br>
                        <a class="fs-4 text-decoration-none" style="font-family: 'IBM Plex Sans Thai', sans-serif;">รายละเอียดโครงงาน : <?php echo $project['project_nameTH'] ?></a>
                    </div>
                </div>



                <hr class="my-4">
                <div class="row g-5">
                    <div class="col-md-4 col-lg-4">
                        <h4 class="mb-3">อาจารย์ที่ปรึกษาหลัก</h4>
                        <?php
                        $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_id', $project['teacher_id1']);
                        $stmt->execute();
                        $data = $stmt->fetch();

                        ?>
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="username" class="form-label">อาจารย์ที่ปรึกษาหลัก</label>
                                <div class="input-group has-validation">
                                    <span><?php echo $data['position'] . ' ' . $data['firstname'] . ' ' . $data['lastname']; ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เบอร์โทรติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['phone'])){echo $data['phone'];} else{echo '-';} ?></span>
                                </div>
                            </div>

                            <div class="col-16">
                                <label for="email" class="form-label">อีเมลติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['email'])){echo $data['email'];} else{echo '-';} ?></span>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <h4 class="mb-3">อาจารย์ที่ปรึกษาร่วม</h4>
                        <?php
                        $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_id', $project['teacher_id2']);
                        $stmt->execute();
                        $data = $stmt->fetch();

                        ?>
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="username" class="form-label">อาจารย์ที่ปรึกษาร่วม</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data)) {
                                                echo $data['position'] . ' ' . $data['firstname'] . ' ' . $data['lastname'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เบอร์โทรติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data)) {
                                                echo $data['phone'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">อีเมลติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data)) {
                                                echo $data['email'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                    </div>
                </div>
                <hr class="my-4">
                <div class="row g-5">
                    <div class="col-md-4 col-lg-4">
                        <h4 class="mb-3">ประธานกรรมการ</h4>
                        <?php
                        $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_id', $project['referee_id']);
                        $stmt->execute();
                        $data = $stmt->fetch();

                        ?>
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="username" class="form-label">ประธานกรรมการ</label>
                                <div class="input-group has-validation">
                                    <span><?php echo $data['position'] . ' ' . $data['firstname'] . ' ' . $data['lastname']; ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เบอร์โทรติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['phone'])){echo $data['phone'];} else{echo '-';} ?></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">อีเมลติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['email'])){echo $data['email'];} else{echo '-';} ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <h4 class="mb-3">กรรมการ 1</h4>
                        <?php
                        $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_id', $project['referee_id1']);
                        $stmt->execute();
                        $data = $stmt->fetch();

                        ?>
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="username" class="form-label">กรรมการ 1</label>
                                <div class="input-group has-validation">
                                    <span><?php echo $data['position'] . ' ' . $data['firstname'] . ' ' . $data['lastname']; ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เบอร์โทรติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['phone'])){echo $data['phone'];} else{echo '-';} ?></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">อีเมลติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['email'])){echo $data['email'];} else{echo '-';} ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <h4 class="mb-3">กรรมการ 2</h4>
                        <?php
                        $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_id', $project['referee_id2']);
                        $stmt->execute();
                        $data = $stmt->fetch();

                        ?>
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="username" class="form-label">กรรมการ 2</label>
                                <div class="input-group has-validation">
                                    <span><?php echo $data['position'] . ' ' . $data['firstname'] . ' ' . $data['lastname']; ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เบอร์โทรติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['phone'])){echo $data['phone'];} else{echo '-';} ?></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">อีเมลติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['email'])){echo $data['email'];} else{echo '-';} ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row g-5">
                    <div class="col-md-4 col-lg-4">
                        <h4 class="mb-3">นักศึกษา 1</h4>
                        <?php
                        $sql = "SELECT * FROM `student` WHERE student_id = :student_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':student_id', $project['student_id1']);
                        $stmt->execute();
                        $data = $stmt->fetch();
                        ?>
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="username" class="form-label">นักศึกษา 1</label>
                                <div class="input-group has-validation">
                                    <span><?php echo $data['firstname'] . ' ' . $data['lastname']; ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เบอร์โทรติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data['phone'])) {
                                                echo $data['phone'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="email" class="form-label">อีเมลติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data['email'])) {
                                                echo $data['email'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="username" class="form-label">เกรดการศึกษา</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data['grade'])) {
                                                echo $data['grade'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <h4 class="mb-3">นักศึกษา 2</h4>
                        <?php
                        $sql = "SELECT * FROM `student` WHERE student_id = :student_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':student_id', $project['student_id2']);
                        $stmt->execute();
                        $data = $stmt->fetch();

                        ?>
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="username" class="form-label">นักศึกษา 2</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data)) {
                                                echo $data['firstname'] . ' ' . $data['lastname'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เบอร์โทรติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data['phone'])) {
                                                echo $data['phone'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="email" class="form-label">อีเมลติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data['email'])) {
                                                echo $data['email'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="username" class="form-label">เกรดการศึกษา</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data['grade'])) {
                                                echo $data['grade'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <h4 class="mb-3">นักศึกษา 3</h4>
                        <?php
                        $sql = "SELECT * FROM `student` WHERE student_id = :student_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':student_id', $project['student_id3']);
                        $stmt->execute();
                        $data = $stmt->fetch();

                        ?>
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="username" class="form-label">นักศึกษา 3</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data)) {
                                                echo $data['firstname'] . ' ' . $data['lastname'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เบอร์โทรติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data['phone'])) {
                                                echo $data['phone'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="email" class="form-label">อีเมลติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data['email'])) {
                                                echo $data['email'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="username" class="form-label">เกรดการศึกษา</label>
                                <div class="input-group has-validation">
                                    <span><?php if (!empty($data['grade'])) {
                                                echo $data['grade'];
                                            } else {
                                                echo '-';
                                            } ?></span>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <hr class="my-4">


                <!-- <footer class="pt-1 text-body-secondary">
                    Project Management System © 2563
                </footer> -->
                <div class="accordion" id="accordionPanelsStayOpenExample" style="font-family: 'IBM Plex Sans Thai', sans-serif;">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                                <div class="row">
                                    <div class="ms-3 col-auto me-auto" style="font-family: 'IBM Plex Sans Thai', sans-serif;"> <strong>สถานะโครงงาน</strong></div>
                                    <div class="ms-3 col-auto me-auto"> </div>
                                    <div class="col-auto">
                                        <div class="text-end">
                                            <?php
                                            $sql = "SELECT COUNT(DISTINCT file_chapter) FROM `file` WHERE project_id = :project_id";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileChapter = $stmt->fetchColumn();

                                            $sql = "SELECT grade FROM `student` WHERE student_id = :student_id";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':student_id', $project['student_id1']);
                                            $stmt->execute();
                                            $studentGrade = $stmt->fetchColumn();

                                            ?>


                                            <?php
                                            if (isset($studentGrade)) {
                                            ?>
                                                <i class="bi bi-circle-fill text-success h4"></i> <i>สอบเสร็จสิ้น</i><?php } elseif (empty($fileChapter)) { ?>
                                                <i class="bi bi-circle-fill text-warning"></i> <i>ส่งเอกสารความคืบหน้า 0 จาก 14</i>
                                            <?php
                                                                                                                    } else { ?>
                                                <i class="bi bi-circle-fill text-warning"></i> <i>ส่งเอกสารความคืบหน้า <?php echo $fileChapter ?> จาก 14</i>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse">
                            <div class="accordion-body">

                                <div class="row g-3 mb-2 mt-2 align-items-center text-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>หน้าปก</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 1 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 1 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>ลายเซ็นกรรมการ</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 2 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 2 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>บทคัดย่อ</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 3 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 3 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>สารบัญ</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 4 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 4 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>เอกสารโครงงานบทที่ 1</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 5 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 5 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>เอกสารโครงงานบทที่ 2</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 6 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 6 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>เอกสารโครงงานบทที่ 3</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 7 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 7 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>เอกสารโครงงานบทที่ 4</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 8 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 8 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>เอกสารโครงงานบทที่ 5</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 9 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 9 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>บรรณานุกรม</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 10 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 10 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>ภาคผนวก</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 11 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 11 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>ประวัติผู้จัดทำปริญญานิพนธ์</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 12 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 12 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>โปสเตอร์</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 13 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 13 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3 mb-2 mt-2 align-items-center text-start justify-content-start">
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <div class="col ms-3">
                                            <h4>รูปเล่มปริญญานิพนธ์ฉบับเต็ม</h4>
                                            <?php
                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 14 and file_status = 1";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus1 = $stmt->fetch();

                                            $sql = "SELECT * FROM `file` WHERE project_id = :project_id and file_chapter = 14 and file_status = 0";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bindParam(':project_id', $project['project_id']);
                                            $stmt->execute();
                                            $fileStatus0 = $stmt->fetch();

                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-6 mb-4 mb-lg-0">
                                        <?php if (!empty($fileStatus1)) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-success h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                            </div>
                                        <?php } elseif ($fileStatus0) { ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow">
                                                    <i class="bi bi-circle-fill text-warning h4"></i>
                                                </div>
                                                <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <hr>

                            </div>
                        </div>
                    </div>
                </div>

                <hr>






                <?php
                $sql = "SELECT * FROM `project` 
                WHERE project_id = :project_id 
                AND year = (SELECT year FROM `defaultsystem` WHERE default_system_id = :id) 
                AND term = (SELECT term FROM `defaultsystem` WHERE default_system_id = :id)";

                $stmt = $conn->prepare($sql);
                $defaultSystemId = 1;
                $stmt->bindParam(':id', $defaultSystemId, PDO::PARAM_INT);
                $stmt->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                $stmt->execute();
                $testCheck = $stmt->fetchAll();
                if (!empty($testCheck)) {
                ?>


                    <?php
                    $teacherIdAll = array();
                    if (!empty($project['teacher_id1'])) { ?>
                        <?php
                        $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_id', $project['teacher_id1']);
                        $stmt->execute();
                        $teacherRow = $stmt->fetch();
                        ?>

                        <div class="card shadow-sm">
                            <h4 class="card-header">รายการการประเมินโครงงาน : <?php echo $teacherRow['position'] . ' ' . $teacherRow['firstname'] . ' ' . $teacherRow['lastname'] ?></h4>
                            <div class="card-body">
                                <div class="col-md-5">
                                    <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
                                </div>

                                <!-- -->
                                <?php
                                $teacher_id = $project['teacher_id1'];
                                $teacherCheck = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id AND (teacher_id1 = :teacher_id OR teacher_id2 = :teacher_id)");
                                $teacherCheck->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                $teacherCheck->bindParam(':teacher_id', $teacher_id, PDO::PARAM_STR);
                                $teacherCheck->execute();
                                $teacherCheckResult = $teacherCheck->fetchAll();

                                if (empty($teacherCheckResult)) {
                                    $stmt = $conn->prepare("SELECT * FROM `topicsection` WHERE (topic_section_level = 0 or topic_section_level = 2) AND topic_section_status = 1");
                                    // ให้ level เป็น 1 ถ้าไม่ใช่อาจารย์
                                } else {
                                    $stmt = $conn->prepare("SELECT * FROM `topicsection` WHERE  (topic_section_level = 1 or topic_section_level = 2) AND topic_section_status = 1");
                                    // ให้ level เป็น 0 ถ้าเป็นอาจารย์
                                }

                                // $stmt = $conn->prepare("SELECT * FROM `topicSection` WHERE topic_section_level != :topic_section_level AND topic_section_level != 0 ");
                                // $stmt->bindParam(':topic_section_level', $topicSectionLevel, PDO::PARAM_INT);
                                $stmt->execute();
                                $datas = $stmt->fetchAll();

                                $indextopic_section = 0;
                                foreach ($datas as $data) {
                                    $indextopic_section++;
                                    $sql = "SELECT * FROM `topic` WHERE topic_section_id = :topic_section_id AND topic_status = 1 ORDER BY topic_name";
                                    $projectpoint = $conn->prepare($sql);
                                    $projectpoint->bindParam(':topic_section_id', $data['topic_section_id'], PDO::PARAM_STR);
                                    $projectpoint->execute();
                                    $topic_datas = $projectpoint->fetchAll();
                                ?>

                                    <?php if ($data['topic_section_format'] == 1) { ?>
                                        <div class="row g-3 mb-4 d-flex align-items-center text-center">
                                            <div class="accordion" id="Assessment">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $indextopic_section; ?>teacher_id1">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $indextopic_section; ?>teacher_id1" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $indextopic_section; ?>">
                                                            <?php echo $data['topic_section']; ?>
                                                            <?php
                                                            $j = assessmentCheck($conn, $project['project_id'], $data['topic_section_id'], $teacher_id);
                                                            if ($j == 1) : $fileStatusHave = false; ?>
                                                                <div class="col-auto">
                                                                    <div class="text-end ms-3">
                                                                        <i class="bi bi-circle-fill text-success"></i>
                                                                        <i>ประเมินเสร็จสิ้น</i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </button>
                                                    </h2>
                                                    <div id="panelsStayOpen-collapse<?php echo $indextopic_section; ?>teacher_id1" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $indextopic_section; ?>teacher_id1">
                                                        <div class="accordion-body">
                                                            <form method="post" action="./assessment.php">
                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                <div class="col-md-6 text-start">
                                                                    <h5></h5>
                                                                </div>

                                                                <div class="col-1 mb-2">
                                                                    <div>น้อยมาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>น้อย</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>ปานกลาง</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มากที่สุด</div>
                                                                </div>
                                                            </div>
                                                                <?php
                                                                $index = 0;
                                                                foreach ($topic_datas as $topic_data) {
                                                                    $index++;

                                                                    $sql = "SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id";
                                                                    $assessmentpoint = $conn->prepare($sql);
                                                                    $assessmentpoint->bindParam(':topic_id', $topic_data['topic_id'], PDO::PARAM_STR);
                                                                    $assessmentpoint->bindParam(':referee_id', $teacher_id, PDO::PARAM_STR);
                                                                    $assessmentpoint->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR); // ควรเพิ่ม PDO::PARAM_STR ที่ parameter นี้
                                                                    $assessmentpoint->execute();
                                                                    $assessment_score = $assessmentpoint->fetch(PDO::FETCH_ASSOC); // ใช้ fetch แทน fetchAll เนื่องจากเราต้องการแถวเดียว

                                                                    // ตรวจสอบว่าคีย์ 'score' มีอยู่ใน $assessment_score หรือไม่
                                                                    if (isset($assessment_score['score'])) {
                                                                        $selected_score = $assessment_score['score'];
                                                                    } else {
                                                                        $selected_score = ''; // หรือค่าเริ่มต้นอื่นๆ ที่คุณต้องการให้มีค่า
                                                                    }
                                                                ?>
                                                                    <div class="mb-3">
                                                                        <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                            <div class="col-md-6 text-start">
                                                                                <h5><?php echo $topic_data['topic_name']; ?> </h5>
                                                                            </div>
                                                                            <?php

                                                                            // ลูปสร้างปุ่มรีดิโอ
                                                                            for ($i = 1; $i <= 5; $i++) {
                                                                                echo '<div class="col-1">';
                                                                                echo '<div class="form-check form-check-inline">';
                                                                                echo '<input class="form-check-input" type="radio" name="projectpoint' . $index . '" value="' . $i . '"';

                                                                                // เช็คว่าคะแนนในปุ่มรีดิโอตรงกับคะแนนที่ถูกเลือกหรือไม่
                                                                                if ($i == $selected_score) {
                                                                                    echo ' checked';
                                                                                }

                                                                                echo '>';
                                                                                echo '<label class="form-check-label" for="projectpoint' . $i . '">' . $i . '</label>';
                                                                                echo '</div>';
                                                                                echo '</div>';
                                                                            }
                                                                            ?>

                                                                        </div>
                                                                    </div>

                                                                <?php
                                                                }
                                                                ?>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <?php if ($data['topic_section_format'] == 0) { ?>
                                        <div class="row g-3 mb-4 d-flex align-items-center text-center">
                                            <div class="accordion" id="Assessment">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $indextopic_section; ?>teacher_id1">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $indextopic_section; ?>teacher_id1" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $indextopic_section; ?>teacher_id1">
                                                            <?php echo $data['topic_section']; ?> <?php
                                                                                                    $j = assessmentCheck($conn, $project['project_id'], $data['topic_section_id'], $teacher_id);
                                                                                                    if ($j == 1) : $fileStatusHave = false; ?>
                                                                <div class="col-auto">
                                                                    <div class="text-end ms-3">
                                                                        <i class="bi bi-circle-fill text-success"></i>
                                                                        <i>ประเมินเสร็จสิ้น</i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </button>
                                                    </h2>
                                                    <div id="panelsStayOpen-collapse<?php echo $indextopic_section; ?>teacher_id1" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $indextopic_section; ?>teacher_id1">
                                                        <div class="accordion-body">
                                                            <form method="post" action="./assessment2.php">
                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                <div class="col-md-6 text-start">
                                                                    <h5></h5>
                                                                </div>

                                                                <div class="col-1 mb-2">
                                                                    <div>น้อยมาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>น้อย</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>ปานกลาง</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มากที่สุด</div>
                                                                </div>
                                                            </div>
                                                                <?php
                                                                $index = 0;
                                                                foreach ($topic_datas as $topic_data) {
                                                                    $index++;
                                                                    $stdName = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id ");
                                                                    $stdName->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                                                    $stdName->execute();
                                                                    $stdName = $stdName->fetch();

                                                                    $stdNames = array(); // Initialize an empty array to store student data

                                                                    for ($i = 0; $i < 3; $i++) {
                                                                        $std = 'student_id' . ($i + 1); // Corrected the identifier
                                                                        if (isset($stdName[$std])) {
                                                                            $stdNames[$i] = $stdName[$std];
                                                                        }
                                                                    }
                                                                    $indexStdName = 0;
                                                                    foreach ($stdNames as $stdName) {
                                                                        $sql = "SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id AND student_id = :student_id";
                                                                        $assessmentpoint = $conn->prepare($sql);
                                                                        $assessmentpoint->bindParam(':topic_id', $topic_data['topic_id'], PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':referee_id', $teacher_id, PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':student_id', $stdNames[$indexStdName], PDO::PARAM_STR); // ควรเพิ่ม PDO::PARAM_STR ที่ parameter นี้
                                                                        $assessmentpoint->execute();
                                                                        $assessment_score = $assessmentpoint->fetch(PDO::FETCH_ASSOC); // ใช้ fetch แทน fetchAll เนื่องจากเราต้องการแถวเดียว

                                                                        // ตรวจสอบว่าคีย์ 'score' มีอยู่ใน $assessment_score หรือไม่
                                                                        if (isset($assessment_score['score'])) {
                                                                            $selected_score = $assessment_score['score'];
                                                                        } else {
                                                                            $selected_score = ''; // หรือค่าเริ่มต้นอื่นๆ ที่คุณต้องการให้มีค่า
                                                                        }
                                                                ?>
                                                                        <div class="mb-3">
                                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                                <div class="col-md-6 text-start">
                                                                                    <h5><?php echo $topic_data['topic_name']; ?> </h5>
                                                                                    <p><?php echo giveStudentdentNameById($conn, $stdNames[$indexStdName]); ?></p>
                                                                                </div>
                                                                                <?php


                                                                                // ลูปสร้างปุ่มรีดิโอ
                                                                                for ($i = 1; $i <= 5; $i++) {
                                                                                    echo '<div class="col-1">';
                                                                                    echo '<div class="form-check form-check-inline">';
                                                                                    echo '<input class="form-check-input" type="radio" name="projectpoint' . $index . '_' . $stdNames[$indexStdName] . '" value="' . $i . '"';

                                                                                    // เช็คว่าคะแนนในปุ่มรีดิโอตรงกับคะแนนที่ถูกเลือกหรือไม่
                                                                                    if ($i == $selected_score) {
                                                                                        echo ' checked';
                                                                                    }

                                                                                    echo '>';
                                                                                    echo '<label class="form-check-label" for="projectpoint' . $index . '_' . $stdNames[$indexStdName] . '">' . $i . '</label>';
                                                                                    echo '</div>';
                                                                                    echo '</div>';
                                                                                }
                                                                                $indexStdName++;
                                                                                ?>

                                                                            </div>
                                                                        </div>

                                                                <?php
                                                                    }
                                                                }
                                                                ?>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }

                                ?>



                            </div>
                        </div>
                        <hr>
                    <?php }
                    ?>








                    <?php
                    $teacherIdAll = array();
                    if (!empty($project['teacher_id2'])) { ?>
                        <?php
                        $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_id', $project['teacher_id2']);
                        $stmt->execute();
                        $teacherRow = $stmt->fetch();
                        ?>

                        <div class="card shadow-sm">
                            <h4 class="card-header">รายการการประเมินโครงงาน : <?php echo $teacherRow['position'] . ' ' . $teacherRow['firstname'] . ' ' . $teacherRow['lastname'] ?></h4>
                            <div class="card-body">
                                <div class="col-md-5">
                                    <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
                                </div>

                                <!-- -->
                                <?php
                                $teacher_id = $project['teacher_id2'];
                                $teacherCheck = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id AND (teacher_id1 = :teacher_id OR teacher_id2 = :teacher_id)");
                                $teacherCheck->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                $teacherCheck->bindParam(':teacher_id', $teacher_id, PDO::PARAM_STR);
                                $teacherCheck->execute();
                                $teacherCheckResult = $teacherCheck->fetchAll();

                                if (empty($teacherCheckResult)) {
                                    $stmt = $conn->prepare("SELECT * FROM `topicsection` WHERE (topic_section_level = 0 or topic_section_level = 2) AND topic_section_status = 1");
                                    // ให้ level เป็น 1 ถ้าไม่ใช่อาจารย์
                                } else {
                                    $stmt = $conn->prepare("SELECT * FROM `topicsection` WHERE  (topic_section_level = 1 or topic_section_level = 2) AND topic_section_status = 1");
                                    // ให้ level เป็น 0 ถ้าเป็นอาจารย์
                                }

                                // $stmt = $conn->prepare("SELECT * FROM `topicSection` WHERE topic_section_level != :topic_section_level AND topic_section_level != 0 ");
                                // $stmt->bindParam(':topic_section_level', $topicSectionLevel, PDO::PARAM_INT);
                                $stmt->execute();
                                $datas = $stmt->fetchAll();

                                $indextopic_section = 0;
                                foreach ($datas as $data) {
                                    $indextopic_section++;
                                    $sql = "SELECT * FROM `topic` WHERE topic_section_id = :topic_section_id AND topic_status = 1 ORDER BY topic_name";
                                    $projectpoint = $conn->prepare($sql);
                                    $projectpoint->bindParam(':topic_section_id', $data['topic_section_id'], PDO::PARAM_STR);
                                    $projectpoint->execute();
                                    $topic_datas = $projectpoint->fetchAll();
                                ?>

                                    <?php if ($data['topic_section_format'] == 1) { ?>
                                        <div class="row g-3 mb-4 d-flex align-items-center text-center">
                                            <div class="accordion" id="Assessment">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $indextopic_section; ?>teacher_id2">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $indextopic_section; ?>teacher_id2" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $indextopic_section; ?>teacher_id2">
                                                            <?php echo $data['topic_section']; ?>
                                                            <?php
                                                            $j = assessmentCheck($conn, $project['project_id'], $data['topic_section_id'], $teacher_id);
                                                            if ($j == 1) : $fileStatusHave = false; ?>
                                                                <div class="col-auto">
                                                                    <div class="text-end ms-3">
                                                                        <i class="bi bi-circle-fill text-success"></i>
                                                                        <i>ประเมินเสร็จสิ้น</i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </button>
                                                    </h2>
                                                    <div id="panelsStayOpen-collapse<?php echo $indextopic_section; ?>teacher_id2" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $indextopic_section; ?>teacher_id2">
                                                        <div class="accordion-body">
                                                            <form method="post" action="./assessment.php">
                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                <div class="col-md-6 text-start">
                                                                    <h5></h5>
                                                                </div>

                                                                <div class="col-1 mb-2">
                                                                    <div>น้อยมาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>น้อย</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>ปานกลาง</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มากที่สุด</div>
                                                                </div>
                                                            </div>
                                                                <?php
                                                                $index = 0;
                                                                foreach ($topic_datas as $topic_data) {
                                                                    $index++;

                                                                    $sql = "SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id";
                                                                    $assessmentpoint = $conn->prepare($sql);
                                                                    $assessmentpoint->bindParam(':topic_id', $topic_data['topic_id'], PDO::PARAM_STR);
                                                                    $assessmentpoint->bindParam(':referee_id', $teacher_id, PDO::PARAM_STR);
                                                                    $assessmentpoint->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR); // ควรเพิ่ม PDO::PARAM_STR ที่ parameter นี้
                                                                    $assessmentpoint->execute();
                                                                    $assessment_score = $assessmentpoint->fetch(PDO::FETCH_ASSOC); // ใช้ fetch แทน fetchAll เนื่องจากเราต้องการแถวเดียว

                                                                    // ตรวจสอบว่าคีย์ 'score' มีอยู่ใน $assessment_score หรือไม่
                                                                    if (isset($assessment_score['score'])) {
                                                                        $selected_score = $assessment_score['score'];
                                                                    } else {
                                                                        $selected_score = ''; // หรือค่าเริ่มต้นอื่นๆ ที่คุณต้องการให้มีค่า
                                                                    }
                                                                ?>
                                                                    <div class="mb-3">
                                                                        <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                            <div class="col-md-6 text-start">
                                                                                <h5><?php echo $topic_data['topic_name']; ?> </h5>
                                                                            </div>
                                                                            <?php

                                                                            // ลูปสร้างปุ่มรีดิโอ
                                                                            for ($i = 1; $i <= 5; $i++) {
                                                                                echo '<div class="col-1">';
                                                                                echo '<div class="form-check form-check-inline">';
                                                                                echo '<input class="form-check-input" type="radio" name="projectpoint' . $index . '" value="' . $i . '"';

                                                                                // เช็คว่าคะแนนในปุ่มรีดิโอตรงกับคะแนนที่ถูกเลือกหรือไม่
                                                                                if ($i == $selected_score) {
                                                                                    echo ' checked';
                                                                                }

                                                                                echo '>';
                                                                                echo '<label class="form-check-label" for="projectpoint' . $i . '">' . $i . '</label>';
                                                                                echo '</div>';
                                                                                echo '</div>';
                                                                            }
                                                                            ?>

                                                                        </div>
                                                                    </div>

                                                                <?php
                                                                }
                                                                ?>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <?php if ($data['topic_section_format'] == 0) { ?>
                                        <div class="row g-3 mb-4 d-flex align-items-center text-center">
                                            <div class="accordion" id="Assessment">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $indextopic_section; ?>teacher_id2">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $indextopic_section; ?>teacher_id2" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $indextopic_section; ?>teacher_id2">
                                                            <?php echo $data['topic_section']; ?> <?php
                                                                                                    $j = assessmentCheck($conn, $project['project_id'], $data['topic_section_id'], $teacher_id);
                                                                                                    if ($j == 1) : $fileStatusHave = false; ?>
                                                                <div class="col-auto">
                                                                    <div class="text-end ms-3">
                                                                        <i class="bi bi-circle-fill text-success"></i>
                                                                        <i>ประเมินเสร็จสิ้น</i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </button>
                                                    </h2>
                                                    <div id="panelsStayOpen-collapse<?php echo $indextopic_section; ?>teacher_id2" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $indextopic_section; ?>teacher_id2">
                                                        <div class="accordion-body">
                                                            <form method="post" action="./assessment2.php">
                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                <div class="col-md-6 text-start">
                                                                    <h5></h5>
                                                                </div>

                                                                <div class="col-1 mb-2">
                                                                    <div>น้อยมาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>น้อย</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>ปานกลาง</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มากที่สุด</div>
                                                                </div>
                                                            </div>
                                                                <?php
                                                                $index = 0;
                                                                foreach ($topic_datas as $topic_data) {
                                                                    $index++;
                                                                    $stdName = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id ");
                                                                    $stdName->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                                                    $stdName->execute();
                                                                    $stdName = $stdName->fetch();

                                                                    $stdNames = array(); // Initialize an empty array to store student data

                                                                    for ($i = 0; $i < 3; $i++) {
                                                                        $std = 'student_id' . ($i + 1); // Corrected the identifier
                                                                        if (isset($stdName[$std])) {
                                                                            $stdNames[$i] = $stdName[$std];
                                                                        }
                                                                    }
                                                                    $indexStdName = 0;
                                                                    foreach ($stdNames as $stdName) {
                                                                        $sql = "SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id AND student_id = :student_id";
                                                                        $assessmentpoint = $conn->prepare($sql);
                                                                        $assessmentpoint->bindParam(':topic_id', $topic_data['topic_id'], PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':referee_id', $teacher_id, PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':student_id', $stdNames[$indexStdName], PDO::PARAM_STR); // ควรเพิ่ม PDO::PARAM_STR ที่ parameter นี้
                                                                        $assessmentpoint->execute();
                                                                        $assessment_score = $assessmentpoint->fetch(PDO::FETCH_ASSOC); // ใช้ fetch แทน fetchAll เนื่องจากเราต้องการแถวเดียว

                                                                        // ตรวจสอบว่าคีย์ 'score' มีอยู่ใน $assessment_score หรือไม่
                                                                        if (isset($assessment_score['score'])) {
                                                                            $selected_score = $assessment_score['score'];
                                                                        } else {
                                                                            $selected_score = ''; // หรือค่าเริ่มต้นอื่นๆ ที่คุณต้องการให้มีค่า
                                                                        }
                                                                ?>
                                                                        <div class="mb-3">
                                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                                <div class="col-md-6 text-start">
                                                                                    <h5><?php echo $topic_data['topic_name']; ?> </h5>
                                                                                    <p><?php echo giveStudentdentNameById($conn, $stdNames[$indexStdName]); ?></p>
                                                                                </div>
                                                                                <?php


                                                                                // ลูปสร้างปุ่มรีดิโอ
                                                                                for ($i = 1; $i <= 5; $i++) {
                                                                                    echo '<div class="col-1">';
                                                                                    echo '<div class="form-check form-check-inline">';
                                                                                    echo '<input class="form-check-input" type="radio" name="projectpoint' . $index . '_' . $stdNames[$indexStdName] . '" value="' . $i . '"';

                                                                                    // เช็คว่าคะแนนในปุ่มรีดิโอตรงกับคะแนนที่ถูกเลือกหรือไม่
                                                                                    if ($i == $selected_score) {
                                                                                        echo ' checked';
                                                                                    }

                                                                                    echo '>';
                                                                                    echo '<label class="form-check-label" for="projectpoint' . $index . '_' . $stdNames[$indexStdName] . '">' . $i . '</label>';
                                                                                    echo '</div>';
                                                                                    echo '</div>';
                                                                                }
                                                                                $indexStdName++;
                                                                                ?>

                                                                            </div>
                                                                        </div>

                                                                <?php
                                                                    }
                                                                }
                                                                ?>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }

                                ?>


                            </div>
                        </div>
                        <hr>
                    <?php }
                    ?>









                    <?php
                    $teacherIdAll = array();
                    if (!empty($project['referee_id'])) { ?>
                        <?php
                        $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_id', $project['referee_id']);
                        $stmt->execute();
                        $teacherRow = $stmt->fetch();
                        ?>

                        <div class="card shadow-sm">
                            <h4 class="card-header">รายการการประเมินโครงงาน : <?php echo $teacherRow['position'] . ' ' . $teacherRow['firstname'] . ' ' . $teacherRow['lastname'] ?></h4>
                            <div class="card-body">
                                <div class="col-md-5">
                                    <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
                                </div>

                                <!-- -->
                                <?php
                                $teacher_id = $project['referee_id'];
                                $teacherCheck = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id AND (teacher_id1 = :teacher_id OR teacher_id2 = :teacher_id)");
                                $teacherCheck->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                $teacherCheck->bindParam(':teacher_id', $teacher_id, PDO::PARAM_STR);
                                $teacherCheck->execute();
                                $teacherCheckResult = $teacherCheck->fetchAll();

                                if (empty($teacherCheckResult)) {
                                    $stmt = $conn->prepare("SELECT * FROM `topicsection` WHERE (topic_section_level = 0 or topic_section_level = 2) AND topic_section_status = 1");
                                    // ให้ level เป็น 1 ถ้าไม่ใช่อาจารย์
                                } else {
                                    $stmt = $conn->prepare("SELECT * FROM `topicsection` WHERE  (topic_section_level = 1 or topic_section_level = 2) AND topic_section_status = 1");
                                    // ให้ level เป็น 0 ถ้าเป็นอาจารย์
                                }

                                // $stmt = $conn->prepare("SELECT * FROM `topicSection` WHERE topic_section_level != :topic_section_level AND topic_section_level != 0 ");
                                // $stmt->bindParam(':topic_section_level', $topicSectionLevel, PDO::PARAM_INT);
                                $stmt->execute();
                                $datas = $stmt->fetchAll();

                                $indextopic_section = 0;
                                foreach ($datas as $data) {
                                    $indextopic_section++;
                                    $sql = "SELECT * FROM `topic` WHERE topic_section_id = :topic_section_id AND topic_status = 1 ORDER BY topic_name";
                                    $projectpoint = $conn->prepare($sql);
                                    $projectpoint->bindParam(':topic_section_id', $data['topic_section_id'], PDO::PARAM_STR);
                                    $projectpoint->execute();
                                    $topic_datas = $projectpoint->fetchAll();
                                ?>

                                    <?php if ($data['topic_section_format'] == 1) { ?>
                                        <div class="row g-3 mb-4 d-flex align-items-center text-center">
                                            <div class="accordion" id="Assessment">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $indextopic_section; ?>referee_id">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id">
                                                            <?php echo $data['topic_section']; ?>
                                                            <?php
                                                            $j = assessmentCheck($conn, $project['project_id'], $data['topic_section_id'], $teacher_id);
                                                            if ($j == 1) : $fileStatusHave = false; ?>
                                                                <div class="col-auto">
                                                                    <div class="text-end ms-3">
                                                                        <i class="bi bi-circle-fill text-success"></i>
                                                                        <i>ประเมินเสร็จสิ้น</i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </button>
                                                    </h2>
                                                    <div id="panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $indextopic_section; ?>referee_id">
                                                        <div class="accordion-body">
                                                            <form method="post" action="./assessment.php">
                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                <div class="col-md-6 text-start">
                                                                    <h5></h5>
                                                                </div>

                                                                <div class="col-1 mb-2">
                                                                    <div>น้อยมาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>น้อย</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>ปานกลาง</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มากที่สุด</div>
                                                                </div>
                                                            </div>
                                                                <?php
                                                                $index = 0;
                                                                foreach ($topic_datas as $topic_data) {
                                                                    $index++;

                                                                    $sql = "SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id";
                                                                    $assessmentpoint = $conn->prepare($sql);
                                                                    $assessmentpoint->bindParam(':topic_id', $topic_data['topic_id'], PDO::PARAM_STR);
                                                                    $assessmentpoint->bindParam(':referee_id', $teacher_id, PDO::PARAM_STR);
                                                                    $assessmentpoint->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR); // ควรเพิ่ม PDO::PARAM_STR ที่ parameter นี้
                                                                    $assessmentpoint->execute();
                                                                    $assessment_score = $assessmentpoint->fetch(PDO::FETCH_ASSOC); // ใช้ fetch แทน fetchAll เนื่องจากเราต้องการแถวเดียว

                                                                    // ตรวจสอบว่าคีย์ 'score' มีอยู่ใน $assessment_score หรือไม่
                                                                    if (isset($assessment_score['score'])) {
                                                                        $selected_score = $assessment_score['score'];
                                                                    } else {
                                                                        $selected_score = ''; // หรือค่าเริ่มต้นอื่นๆ ที่คุณต้องการให้มีค่า
                                                                    }
                                                                ?>
                                                                    <div class="mb-3">
                                                                        <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                            <div class="col-md-6 text-start">
                                                                                <h5><?php echo $topic_data['topic_name']; ?> </h5>
                                                                            </div>
                                                                            <?php

                                                                            // ลูปสร้างปุ่มรีดิโอ
                                                                            for ($i = 1; $i <= 5; $i++) {
                                                                                echo '<div class="col-1">';
                                                                                echo '<div class="form-check form-check-inline">';
                                                                                echo '<input class="form-check-input" type="radio" name="projectpoint' . $index . '" value="' . $i . '"';

                                                                                // เช็คว่าคะแนนในปุ่มรีดิโอตรงกับคะแนนที่ถูกเลือกหรือไม่
                                                                                if ($i == $selected_score) {
                                                                                    echo ' checked';
                                                                                }

                                                                                echo '>';
                                                                                echo '<label class="form-check-label" for="projectpoint' . $i . '">' . $i . '</label>';
                                                                                echo '</div>';
                                                                                echo '</div>';
                                                                            }
                                                                            ?>

                                                                        </div>
                                                                    </div>

                                                                <?php
                                                                }
                                                                ?>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <?php if ($data['topic_section_format'] == 0) { ?>
                                        <div class="row g-3 mb-4 d-flex align-items-center text-center">
                                            <div class="accordion" id="Assessment">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $indextopic_section; ?>referee_id">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id">
                                                            <?php echo $data['topic_section']; ?> <?php
                                                                                                    $j = assessmentCheck($conn, $project['project_id'], $data['topic_section_id'], $teacher_id);
                                                                                                    if ($j == 1) : $fileStatusHave = false; ?>
                                                                <div class="col-auto">
                                                                    <div class="text-end ms-3">
                                                                        <i class="bi bi-circle-fill text-success"></i>
                                                                        <i>ประเมินเสร็จสิ้น</i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </button>
                                                    </h2>
                                                    <div id="panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $indextopic_section; ?>referee_id">
                                                        <div class="accordion-body">
                                                            <form method="post" action="./assessment2.php">
                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                <div class="col-md-6 text-start">
                                                                    <h5></h5>
                                                                </div>

                                                                <div class="col-1 mb-2">
                                                                    <div>น้อยมาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>น้อย</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>ปานกลาง</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มากที่สุด</div>
                                                                </div>
                                                            </div>
                                                                <?php
                                                                $index = 0;
                                                                foreach ($topic_datas as $topic_data) {
                                                                    $index++;
                                                                    $stdName = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id ");
                                                                    $stdName->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                                                    $stdName->execute();
                                                                    $stdName = $stdName->fetch();

                                                                    $stdNames = array(); // Initialize an empty array to store student data

                                                                    for ($i = 0; $i < 3; $i++) {
                                                                        $std = 'student_id' . ($i + 1); // Corrected the identifier
                                                                        if (isset($stdName[$std])) {
                                                                            $stdNames[$i] = $stdName[$std];
                                                                        }
                                                                    }
                                                                    $indexStdName = 0;
                                                                    foreach ($stdNames as $stdName) {
                                                                        $sql = "SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id AND student_id = :student_id";
                                                                        $assessmentpoint = $conn->prepare($sql);
                                                                        $assessmentpoint->bindParam(':topic_id', $topic_data['topic_id'], PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':referee_id', $teacher_id, PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':student_id', $stdNames[$indexStdName], PDO::PARAM_STR); // ควรเพิ่ม PDO::PARAM_STR ที่ parameter นี้
                                                                        $assessmentpoint->execute();
                                                                        $assessment_score = $assessmentpoint->fetch(PDO::FETCH_ASSOC); // ใช้ fetch แทน fetchAll เนื่องจากเราต้องการแถวเดียว

                                                                        // ตรวจสอบว่าคีย์ 'score' มีอยู่ใน $assessment_score หรือไม่
                                                                        if (isset($assessment_score['score'])) {
                                                                            $selected_score = $assessment_score['score'];
                                                                        } else {
                                                                            $selected_score = ''; // หรือค่าเริ่มต้นอื่นๆ ที่คุณต้องการให้มีค่า
                                                                        }
                                                                ?>
                                                                        <div class="mb-3">
                                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                                <div class="col-md-6 text-start">
                                                                                    <h5><?php echo $topic_data['topic_name']; ?> </h5>
                                                                                    <p><?php echo giveStudentdentNameById($conn, $stdNames[$indexStdName]); ?></p>
                                                                                </div>
                                                                                <?php


                                                                                // ลูปสร้างปุ่มรีดิโอ
                                                                                for ($i = 1; $i <= 5; $i++) {
                                                                                    echo '<div class="col-1">';
                                                                                    echo '<div class="form-check form-check-inline">';
                                                                                    echo '<input class="form-check-input" type="radio" name="projectpoint' . $index . '_' . $stdNames[$indexStdName] . '" value="' . $i . '"';

                                                                                    // เช็คว่าคะแนนในปุ่มรีดิโอตรงกับคะแนนที่ถูกเลือกหรือไม่
                                                                                    if ($i == $selected_score) {
                                                                                        echo ' checked';
                                                                                    }

                                                                                    echo '>';
                                                                                    echo '<label class="form-check-label" for="projectpoint' . $index . '_' . $stdNames[$indexStdName] . '">' . $i . '</label>';
                                                                                    echo '</div>';
                                                                                    echo '</div>';
                                                                                }
                                                                                $indexStdName++;
                                                                                ?>

                                                                            </div>
                                                                        </div>

                                                                <?php
                                                                    }
                                                                }
                                                                ?>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }

                                ?>


                            </div>
                        </div>
                        <hr>
                    <?php }
                    ?>




                    <?php
                    $teacherIdAll = array();
                    if (!empty($project['referee_id1'])) { ?>
                        <?php
                        $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_id', $project['referee_id1']);
                        $stmt->execute();
                        $teacherRow = $stmt->fetch();
                        ?>

                        <div class="card shadow-sm">
                            <h4 class="card-header">รายการการประเมินโครงงาน : <?php echo $teacherRow['position'] . ' ' . $teacherRow['firstname'] . ' ' . $teacherRow['lastname'] ?></h4>
                            <div class="card-body">
                                <div class="col-md-5">
                                    <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
                                </div>

                                <!-- -->
                                <?php
                                $teacher_id = $project['referee_id1'];
                                $teacherCheck = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id AND (teacher_id1 = :teacher_id OR teacher_id2 = :teacher_id)");
                                $teacherCheck->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                $teacherCheck->bindParam(':teacher_id', $teacher_id, PDO::PARAM_STR);
                                $teacherCheck->execute();
                                $teacherCheckResult = $teacherCheck->fetchAll();

                                if (empty($teacherCheckResult)) {
                                    $stmt = $conn->prepare("SELECT * FROM `topicsection` WHERE (topic_section_level = 0 or topic_section_level = 2) AND topic_section_status = 1");
                                    // ให้ level เป็น 1 ถ้าไม่ใช่อาจารย์
                                } else {
                                    $stmt = $conn->prepare("SELECT * FROM `topicsection` WHERE  (topic_section_level = 1 or topic_section_level = 2) AND topic_section_status = 1");
                                    // ให้ level เป็น 0 ถ้าเป็นอาจารย์
                                }

                                // $stmt = $conn->prepare("SELECT * FROM `topicSection` WHERE topic_section_level != :topic_section_level AND topic_section_level != 0 ");
                                // $stmt->bindParam(':topic_section_level', $topicSectionLevel, PDO::PARAM_INT);
                                $stmt->execute();
                                $datas = $stmt->fetchAll();

                                $indextopic_section = 0;
                                foreach ($datas as $data) {
                                    $indextopic_section++;
                                    $sql = "SELECT * FROM `topic` WHERE topic_section_id = :topic_section_id AND topic_status = 1 ORDER BY topic_name";
                                    $projectpoint = $conn->prepare($sql);
                                    $projectpoint->bindParam(':topic_section_id', $data['topic_section_id'], PDO::PARAM_STR);
                                    $projectpoint->execute();
                                    $topic_datas = $projectpoint->fetchAll();
                                ?>

                                    <?php if ($data['topic_section_format'] == 1) { ?>
                                        <div class="row g-3 mb-4 d-flex align-items-center text-center">
                                            <div class="accordion" id="Assessment">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $indextopic_section; ?>referee_id1">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id1" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id1">
                                                            <?php echo $data['topic_section']; ?>
                                                            <?php
                                                            $j = assessmentCheck($conn, $project['project_id'], $data['topic_section_id'], $teacher_id);
                                                            if ($j == 1) : $fileStatusHave = false; ?>
                                                                <div class="col-auto">
                                                                    <div class="text-end ms-3">
                                                                        <i class="bi bi-circle-fill text-success"></i>
                                                                        <i>ประเมินเสร็จสิ้น</i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </button>
                                                    </h2>
                                                    <div id="panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id1" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $indextopic_section; ?>referee_id1">
                                                        <div class="accordion-body">
                                                            <form method="post" action="./assessment.php">
                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                <div class="col-md-6 text-start">
                                                                    <h5></h5>
                                                                </div>

                                                                <div class="col-1 mb-2">
                                                                    <div>น้อยมาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>น้อย</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>ปานกลาง</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มากที่สุด</div>
                                                                </div>
                                                            </div>
                                                                <?php
                                                                $index = 0;
                                                                foreach ($topic_datas as $topic_data) {
                                                                    $index++;

                                                                    $sql = "SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id";
                                                                    $assessmentpoint = $conn->prepare($sql);
                                                                    $assessmentpoint->bindParam(':topic_id', $topic_data['topic_id'], PDO::PARAM_STR);
                                                                    $assessmentpoint->bindParam(':referee_id', $teacher_id, PDO::PARAM_STR);
                                                                    $assessmentpoint->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR); // ควรเพิ่ม PDO::PARAM_STR ที่ parameter นี้
                                                                    $assessmentpoint->execute();
                                                                    $assessment_score = $assessmentpoint->fetch(PDO::FETCH_ASSOC); // ใช้ fetch แทน fetchAll เนื่องจากเราต้องการแถวเดียว

                                                                    // ตรวจสอบว่าคีย์ 'score' มีอยู่ใน $assessment_score หรือไม่
                                                                    if (isset($assessment_score['score'])) {
                                                                        $selected_score = $assessment_score['score'];
                                                                    } else {
                                                                        $selected_score = ''; // หรือค่าเริ่มต้นอื่นๆ ที่คุณต้องการให้มีค่า
                                                                    }
                                                                ?>
                                                                    <div class="mb-3">
                                                                        <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                            <div class="col-md-6 text-start">
                                                                                <h5><?php echo $topic_data['topic_name']; ?> </h5>
                                                                            </div>
                                                                            <?php

                                                                            // ลูปสร้างปุ่มรีดิโอ
                                                                            for ($i = 1; $i <= 5; $i++) {
                                                                                echo '<div class="col-1">';
                                                                                echo '<div class="form-check form-check-inline">';
                                                                                echo '<input class="form-check-input" type="radio" name="projectpoint' . $index . '" value="' . $i . '"';

                                                                                // เช็คว่าคะแนนในปุ่มรีดิโอตรงกับคะแนนที่ถูกเลือกหรือไม่
                                                                                if ($i == $selected_score) {
                                                                                    echo ' checked';
                                                                                }

                                                                                echo '>';
                                                                                echo '<label class="form-check-label" for="projectpoint' . $i . '">' . $i . '</label>';
                                                                                echo '</div>';
                                                                                echo '</div>';
                                                                            }
                                                                            ?>

                                                                        </div>
                                                                    </div>

                                                                <?php
                                                                }
                                                                ?>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <?php if ($data['topic_section_format'] == 0) { ?>
                                        <div class="row g-3 mb-4 d-flex align-items-center text-center">
                                            <div class="accordion" id="Assessment">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $indextopic_section; ?>referee_id1">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id1" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id1">
                                                            <?php echo $data['topic_section']; ?> <?php
                                                                                                    $j = assessmentCheck($conn, $project['project_id'], $data['topic_section_id'], $teacher_id);
                                                                                                    if ($j == 1) : $fileStatusHave = false; ?>
                                                                <div class="col-auto">
                                                                    <div class="text-end ms-3">
                                                                        <i class="bi bi-circle-fill text-success"></i>
                                                                        <i>ประเมินเสร็จสิ้น</i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </button>
                                                    </h2>
                                                    <div id="panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id1" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $indextopic_section; ?>referee_id1">
                                                        <div class="accordion-body">
                                                            <form method="post" action="./assessment2.php">
                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                <div class="col-md-6 text-start">
                                                                    <h5></h5>
                                                                </div>

                                                                <div class="col-1 mb-2">
                                                                    <div>น้อยมาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>น้อย</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>ปานกลาง</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มากที่สุด</div>
                                                                </div>
                                                            </div>
                                                                <?php
                                                                $index = 0;
                                                                foreach ($topic_datas as $topic_data) {
                                                                    $index++;
                                                                    $stdName = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id ");
                                                                    $stdName->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                                                    $stdName->execute();
                                                                    $stdName = $stdName->fetch();

                                                                    $stdNames = array(); // Initialize an empty array to store student data

                                                                    for ($i = 0; $i < 3; $i++) {
                                                                        $std = 'student_id' . ($i + 1); // Corrected the identifier
                                                                        if (isset($stdName[$std])) {
                                                                            $stdNames[$i] = $stdName[$std];
                                                                        }
                                                                    }
                                                                    $indexStdName = 0;
                                                                    foreach ($stdNames as $stdName) {
                                                                        $sql = "SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id AND student_id = :student_id";
                                                                        $assessmentpoint = $conn->prepare($sql);
                                                                        $assessmentpoint->bindParam(':topic_id', $topic_data['topic_id'], PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':referee_id', $teacher_id, PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':student_id', $stdNames[$indexStdName], PDO::PARAM_STR); // ควรเพิ่ม PDO::PARAM_STR ที่ parameter นี้
                                                                        $assessmentpoint->execute();
                                                                        $assessment_score = $assessmentpoint->fetch(PDO::FETCH_ASSOC); // ใช้ fetch แทน fetchAll เนื่องจากเราต้องการแถวเดียว

                                                                        // ตรวจสอบว่าคีย์ 'score' มีอยู่ใน $assessment_score หรือไม่
                                                                        if (isset($assessment_score['score'])) {
                                                                            $selected_score = $assessment_score['score'];
                                                                        } else {
                                                                            $selected_score = ''; // หรือค่าเริ่มต้นอื่นๆ ที่คุณต้องการให้มีค่า
                                                                        }
                                                                ?>
                                                                        <div class="mb-3">
                                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                                <div class="col-md-6 text-start">
                                                                                    <h5><?php echo $topic_data['topic_name']; ?> </h5>
                                                                                    <p><?php echo giveStudentdentNameById($conn, $stdNames[$indexStdName]); ?></p>
                                                                                </div>
                                                                                <?php


                                                                                // ลูปสร้างปุ่มรีดิโอ
                                                                                for ($i = 1; $i <= 5; $i++) {
                                                                                    echo '<div class="col-1">';
                                                                                    echo '<div class="form-check form-check-inline">';
                                                                                    echo '<input class="form-check-input" type="radio" name="projectpoint' . $index . '_' . $stdNames[$indexStdName] . '" value="' . $i . '"';

                                                                                    // เช็คว่าคะแนนในปุ่มรีดิโอตรงกับคะแนนที่ถูกเลือกหรือไม่
                                                                                    if ($i == $selected_score) {
                                                                                        echo ' checked';
                                                                                    }

                                                                                    echo '>';
                                                                                    echo '<label class="form-check-label" for="projectpoint' . $index . '_' . $stdNames[$indexStdName] . '">' . $i . '</label>';
                                                                                    echo '</div>';
                                                                                    echo '</div>';
                                                                                }
                                                                                $indexStdName++;
                                                                                ?>

                                                                            </div>
                                                                        </div>

                                                                <?php
                                                                    }
                                                                }
                                                                ?>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }

                                ?>


                            </div>
                        </div>
                        <hr>
                    <?php }
                    ?>




                    <?php
                    $teacherIdAll = array();
                    if (!empty($project['referee_id2'])) { ?>
                        <?php
                        $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':teacher_id', $project['referee_id2']);
                        $stmt->execute();
                        $teacherRow = $stmt->fetch();
                        ?>

                        <div class="card shadow-sm">
                            <h4 class="card-header">รายการการประเมินโครงงาน : <?php echo $teacherRow['position'] . ' ' . $teacherRow['firstname'] . ' ' . $teacherRow['lastname'] ?></h4>
                            <div class="card-body">
                                <div class="col-md-5">
                                    <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
                                </div>

                                <!-- -->
                                <?php
                                $teacher_id = $project['referee_id2'];
                                $teacherCheck = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id AND (teacher_id1 = :teacher_id OR teacher_id2 = :teacher_id)");
                                $teacherCheck->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                $teacherCheck->bindParam(':teacher_id', $teacher_id, PDO::PARAM_STR);
                                $teacherCheck->execute();
                                $teacherCheckResult = $teacherCheck->fetchAll();

                                if (empty($teacherCheckResult)) {
                                    $stmt = $conn->prepare("SELECT * FROM `topicsection` WHERE (topic_section_level = 0 or topic_section_level = 2) AND topic_section_status = 1");
                                    // ให้ level เป็น 1 ถ้าไม่ใช่อาจารย์
                                } else {
                                    $stmt = $conn->prepare("SELECT * FROM `topicsection` WHERE  (topic_section_level = 1 or topic_section_level = 2) AND topic_section_status = 1");
                                    // ให้ level เป็น 0 ถ้าเป็นอาจารย์
                                }

                                // $stmt = $conn->prepare("SELECT * FROM `topicSection` WHERE topic_section_level != :topic_section_level AND topic_section_level != 0 ");
                                // $stmt->bindParam(':topic_section_level', $topicSectionLevel, PDO::PARAM_INT);
                                $stmt->execute();
                                $datas = $stmt->fetchAll();

                                $indextopic_section = 0;
                                foreach ($datas as $data) {
                                    $indextopic_section++;
                                    $sql = "SELECT * FROM `topic` WHERE topic_section_id = :topic_section_id AND topic_status = 1 ORDER BY topic_name";
                                    $projectpoint = $conn->prepare($sql);
                                    $projectpoint->bindParam(':topic_section_id', $data['topic_section_id'], PDO::PARAM_STR);
                                    $projectpoint->execute();
                                    $topic_datas = $projectpoint->fetchAll();
                                ?>

                                    <?php if ($data['topic_section_format'] == 1) { ?>
                                        <div class="row g-3 mb-4 d-flex align-items-center text-center">
                                            <div class="accordion" id="Assessment">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $indextopic_section; ?>referee_id2">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id2" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id2">
                                                            <?php echo $data['topic_section']; ?>
                                                            <?php
                                                            $j = assessmentCheck($conn, $project['project_id'], $data['topic_section_id'], $teacher_id);
                                                            if ($j == 1) : $fileStatusHave = false; ?>
                                                                <div class="col-auto">
                                                                    <div class="text-end ms-3">
                                                                        <i class="bi bi-circle-fill text-success"></i>
                                                                        <i>ประเมินเสร็จสิ้น</i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </button>
                                                    </h2>
                                                    <div id="panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id2" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $indextopic_section; ?>referee_id2">
                                                        <div class="accordion-body">
                                                            <form method="post" action="./assessment.php">
                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                <div class="col-md-6 text-start">
                                                                    <h5></h5>
                                                                </div>

                                                                <div class="col-1 mb-2">
                                                                    <div>น้อยมาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>น้อย</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>ปานกลาง</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มากที่สุด</div>
                                                                </div>
                                                            </div>
                                                                <?php
                                                                $index = 0;
                                                                foreach ($topic_datas as $topic_data) {
                                                                    $index++;

                                                                    $sql = "SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id";
                                                                    $assessmentpoint = $conn->prepare($sql);
                                                                    $assessmentpoint->bindParam(':topic_id', $topic_data['topic_id'], PDO::PARAM_STR);
                                                                    $assessmentpoint->bindParam(':referee_id', $teacher_id, PDO::PARAM_STR);
                                                                    $assessmentpoint->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR); // ควรเพิ่ม PDO::PARAM_STR ที่ parameter นี้
                                                                    $assessmentpoint->execute();
                                                                    $assessment_score = $assessmentpoint->fetch(PDO::FETCH_ASSOC); // ใช้ fetch แทน fetchAll เนื่องจากเราต้องการแถวเดียว

                                                                    // ตรวจสอบว่าคีย์ 'score' มีอยู่ใน $assessment_score หรือไม่
                                                                    if (isset($assessment_score['score'])) {
                                                                        $selected_score = $assessment_score['score'];
                                                                    } else {
                                                                        $selected_score = ''; // หรือค่าเริ่มต้นอื่นๆ ที่คุณต้องการให้มีค่า
                                                                    }
                                                                ?>
                                                                    <div class="mb-3">
                                                                        <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                            <div class="col-md-6 text-start">
                                                                                <h5><?php echo $topic_data['topic_name']; ?> </h5>
                                                                            </div>
                                                                            <?php

                                                                            // ลูปสร้างปุ่มรีดิโอ
                                                                            for ($i = 1; $i <= 5; $i++) {
                                                                                echo '<div class="col-1">';
                                                                                echo '<div class="form-check form-check-inline">';
                                                                                echo '<input class="form-check-input" type="radio" name="projectpoint' . $index . '" value="' . $i . '"';

                                                                                // เช็คว่าคะแนนในปุ่มรีดิโอตรงกับคะแนนที่ถูกเลือกหรือไม่
                                                                                if ($i == $selected_score) {
                                                                                    echo ' checked';
                                                                                }

                                                                                echo '>';
                                                                                echo '<label class="form-check-label" for="projectpoint' . $i . '">' . $i . '</label>';
                                                                                echo '</div>';
                                                                                echo '</div>';
                                                                            }
                                                                            ?>

                                                                        </div>
                                                                    </div>

                                                                <?php
                                                                }
                                                                ?>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <?php if ($data['topic_section_format'] == 0) { ?>
                                        <div class="row g-3 mb-4 d-flex align-items-center text-center">
                                            <div class="accordion" id="Assessment">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $indextopic_section; ?>referee_id2">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id2" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id2">
                                                            <?php echo $data['topic_section']; ?> <?php
                                                                                                    $j = assessmentCheck($conn, $project['project_id'], $data['topic_section_id'], $teacher_id);
                                                                                                    if ($j == 1) : $fileStatusHave = false; ?>
                                                                <div class="col-auto">
                                                                    <div class="text-end ms-3">
                                                                        <i class="bi bi-circle-fill text-success"></i>
                                                                        <i>ประเมินเสร็จสิ้น</i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </button>
                                                    </h2>
                                                    <div id="panelsStayOpen-collapse<?php echo $indextopic_section; ?>referee_id2" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $indextopic_section; ?>referee_id2">
                                                        <div class="accordion-body">
                                                            <form method="post" action="./assessment2.php">
                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                <div class="col-md-6 text-start">
                                                                    <h5></h5>
                                                                </div>

                                                                <div class="col-1 mb-2">
                                                                    <div>น้อยมาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>น้อย</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>ปานกลาง</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มาก</div>
                                                                </div>
                                                                <div class="col-1 mb-2">
                                                                    <div>มากที่สุด</div>
                                                                </div>
                                                            </div>
                                                                <?php
                                                                $index = 0;
                                                                foreach ($topic_datas as $topic_data) {
                                                                    $index++;
                                                                    $stdName = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id ");
                                                                    $stdName->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                                                    $stdName->execute();
                                                                    $stdName = $stdName->fetch();

                                                                    $stdNames = array(); // Initialize an empty array to store student data

                                                                    for ($i = 0; $i < 3; $i++) {
                                                                        $std = 'student_id' . ($i + 1); // Corrected the identifier
                                                                        if (isset($stdName[$std])) {
                                                                            $stdNames[$i] = $stdName[$std];
                                                                        }
                                                                    }
                                                                    $indexStdName = 0;
                                                                    foreach ($stdNames as $stdName) {
                                                                        $sql = "SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id AND student_id = :student_id";
                                                                        $assessmentpoint = $conn->prepare($sql);
                                                                        $assessmentpoint->bindParam(':topic_id', $topic_data['topic_id'], PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':referee_id', $teacher_id, PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':project_id', $project['project_id'], PDO::PARAM_STR);
                                                                        $assessmentpoint->bindParam(':student_id', $stdNames[$indexStdName], PDO::PARAM_STR); // ควรเพิ่ม PDO::PARAM_STR ที่ parameter นี้
                                                                        $assessmentpoint->execute();
                                                                        $assessment_score = $assessmentpoint->fetch(PDO::FETCH_ASSOC); // ใช้ fetch แทน fetchAll เนื่องจากเราต้องการแถวเดียว

                                                                        // ตรวจสอบว่าคีย์ 'score' มีอยู่ใน $assessment_score หรือไม่
                                                                        if (isset($assessment_score['score'])) {
                                                                            $selected_score = $assessment_score['score'];
                                                                        } else {
                                                                            $selected_score = ''; // หรือค่าเริ่มต้นอื่นๆ ที่คุณต้องการให้มีค่า
                                                                        }
                                                                ?>
                                                                        <div class="mb-3">
                                                                            <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                                                <div class="col-md-6 text-start">
                                                                                    <h5><?php echo $topic_data['topic_name']; ?> </h5>
                                                                                    <p><?php echo giveStudentdentNameById($conn, $stdNames[$indexStdName]); ?></p>
                                                                                </div>
                                                                                <?php


                                                                                // ลูปสร้างปุ่มรีดิโอ
                                                                                for ($i = 1; $i <= 5; $i++) {
                                                                                    echo '<div class="col-1">';
                                                                                    echo '<div class="form-check form-check-inline">';
                                                                                    echo '<input class="form-check-input" type="radio" name="projectpoint' . $index . '_' . $stdNames[$indexStdName] . '" value="' . $i . '"';

                                                                                    // เช็คว่าคะแนนในปุ่มรีดิโอตรงกับคะแนนที่ถูกเลือกหรือไม่
                                                                                    if ($i == $selected_score) {
                                                                                        echo ' checked';
                                                                                    }

                                                                                    echo '>';
                                                                                    echo '<label class="form-check-label" for="projectpoint' . $index . '_' . $stdNames[$indexStdName] . '">' . $i . '</label>';
                                                                                    echo '</div>';
                                                                                    echo '</div>';
                                                                                }
                                                                                $indexStdName++;
                                                                                ?>

                                                                            </div>
                                                                        </div>

                                                                <?php
                                                                    }
                                                                }
                                                                ?>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }

                                ?>


                            </div>
                        </div>
                    <?php }
                    ?>


                <?php
                }
                ?>





<?php  }else{ ?>
    <p style="text-align: center; color: red; font-size: 24px;">***ไม่พบข้อมูลโครงงาน***</p>
<?php } ?>
            </main>

        </div>
    </div>
</body>

</html>