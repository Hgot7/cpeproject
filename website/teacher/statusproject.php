<?php

session_start();
require_once "../connect.php";
$project_id = $_GET["id"];
// $decoded_url = urldecode($_GET['id']);

// if( $_SESSION['student_id'] != $_GET['id']){
//   http_response_code(404);
//   exit('Page not found');
// }

if (!isset($_SESSION['teacher_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบนักศึกษา';
    header('Location: ../index.php');
    exit();
}

$sql = "SELECT * FROM `project` WHERE project_id = :project_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':project_id', $project_id);
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
        return $result['firstname'] ." ". $result['lastname']; // ส่งกลับข้อมูลไฟล์
    }

    return null; // หรือส่งค่าอื่นที่เหมาะสมตามสถานการณ์
}

function assessmentCheck($conn, $project_id, $data)
{
    $stmt = $conn->prepare("SELECT a.* 
        FROM `assessment` a
        INNER JOIN `topic` t ON a.topic_id = t.topic_id
        WHERE t.topic_section_id = :topic_section_id
        AND a.referee_id = :referee_id
        AND a.project_id = :project_id");
        
    $stmt->bindParam(':topic_section_id', $data);
    $stmt->bindParam(':referee_id', $_SESSION['teacher_id']);
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

    <title>ข้อมูลสถานะโครงงาน</title>

</head>

<body>
    <!-- -------------------------------------------------Header------------------------------------------------- -->
    <div class="HeaderBg shadow">
        <div class="container">
        <navbar_teacher-component></navbar_teacher-component> <!-- component.js Navber-->
        </div>
    </div>
    <div class="container-fluid justify-content-around">
        <div class="row">
        <?php include("sidebarTeacherComponent.php"); ?>
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">

            <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลสถานะโครงงาน</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb fs-5 mt-3 ms-3">
            <li class="breadcrumb-item"><a href="./Stdpage.php">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="./Teacheryourproject.php">โครงงานที่รับเป็นที่ปรึกษา</a></li>
            <li class="breadcrumb-item active" aria-current="page">สถานะโครงงาน</li>
          </ol>
        </nav>

        
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

                            <div class="col-12">
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
                                    <span><?php if(!empty($data)){echo $data['position'] . ' ' . $data['firstname'] . ' ' . $data['lastname'];}else{echo '-';} ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เบอร์โทรติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data)){echo $data['phone'];}else{echo '-';} ?></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">อีเมลติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data)){echo $data['email'];}else{echo '-';} ?></span>
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
                                    <span><?php if(!empty($data['phone'])){echo $data['phone'];} else{echo '-';} ?></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">อีเมลติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['email'])){echo $data['email'];} else{echo '-';} ?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เกรดการศึกษา</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['grade'])){echo $data['grade'];} else{echo '-';} ?></span>
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
                                    <span><?php if(!empty($data)){echo $data['firstname'] . ' ' . $data['lastname'];} else{echo '-';}?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เบอร์โทรติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['phone'])){echo $data['phone'];} else{echo '-';}?></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">อีเมลติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['email'])){echo $data['email'];} else{echo '-';}?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เกรดการศึกษา</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['grade'])){echo $data['grade'];} else{echo '-';} ?></span>
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
                                    <span><?php if(!empty($data)){echo $data['firstname'] . ' ' . $data['lastname'];} else{echo '-';}?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เบอร์โทรติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['phone'])){echo $data['phone'];} else{echo '-';}?></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">อีเมลติดต่อ</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['email'])){echo $data['email'];} else{echo '-';}?></span>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="username" class="form-label">เกรดการศึกษา</label>
                                <div class="input-group has-validation">
                                    <span><?php if(!empty($data['grade'])){echo $data['grade'];} else{echo '-';} ?></span>
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
                                            if(isset($studentGrade)){
                                            ?>
                                              <i class="bi bi-circle-fill text-success h4"></i> <i>สอบเสร็จสิ้น</i><?php }elseif(empty($fileChapter)) {?>
                                                <i class="bi bi-circle-fill text-warning"></i> <i>ส่งเอกสารความคืบหน้า 0 จาก 14</i>
                                                                    <?php
                                                                    }else{?>
                                                                        <i class="bi bi-circle-fill text-warning"></i> <i>ส่งเอกสารความคืบหน้า <?php echo $fileChapter ?> จาก 14</i> 
                                                                    <?php }?>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>
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
                                    <?php if(!empty($fileStatus1)){?>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                            <i class="bi bi-circle-fill text-success h4"></i> 
                                            </div>
                                            <i class="ms-2 fs-5">เอกสารผ่านการอนุมัติ</i>
                                        </div>
                                        <?php }elseif($fileStatus0){?>
                                            <div class="d-flex align-items-center">
                                            <div class="flex-grow">
                                                <i class="bi bi-circle-fill text-warning h4"></i>
                                            </div>
                                            <i class="ms-2 fs-5">รอการอนุมัติเอกสาร</i>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </div><hr>

                            </div>
                        </div>
                    </div>
                </div>



            </main>

        </div>
    </div>
</body>

</html>