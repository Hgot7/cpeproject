<?php
session_start();
require_once "../connect.php";
if (!isset($_SESSION['teacher_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('Location: ../index.php');
    exit();
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
    <title>
        เอกสารความคืบหน้าโครงงาน
    </title>


</head>

<body>
    <!-- -------------------------------------------------Header------------------------------------------------- -->
    <div class="HeaderBg shadow">
        <div class="container">
            <navbar_teacher-component></navbar_teacher-component> <!-- component.js Navber-->
        </div>
    </div>
    <!-- -------------------------------------------------Material------------------------------------------------- -->
    <!-- --------------------------------------------------------Accordion 1---------------------------------------------------------------- -->
    <div class="container-fluid justify-content-around">
        <?php

        $project_id = $_GET['id'];
        $sql = "SELECT * FROM `file` WHERE project_id = :id ORDER BY file_chapter ASC, file_date DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $project_id);
        $stmt->execute();
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);


        function findFilesByChapter($files, $targetChapter)
        {
            $matchingIndices = array();

            foreach ($files as $index => $file) {
                if ($file['file_chapter'] == $targetChapter) {
                    $matchingIndices[] = $index;
                }
            }

            return empty($matchingIndices) ? null : $matchingIndices;
        }

        function getStudentById(PDO $conn, $student_id)
        {
            $sql = "SELECT * FROM `student` WHERE student_id = :student_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        }

        function getTeacherById(PDO $conn, $teacher_id)
        {
            $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        }

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


        function giveProjectNameTH($conn, $project_id)
        {
            $sql = "SELECT project_nameTH FROM `project` WHERE project_id = :project_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':project_id', $project_id);
            $stmt->execute();
            return $stmt->fetchColumn(); // เปลี่ยนเป็น fetchColumn() เพื่อเรียกคอลัมน์เดียวที่กลับมา
        }
        ?>

        <div class="row">
            <?php include("sidebarTeacherComponent.php"); ?>
            <div class="row">
                <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">
                    <h1 class="h2 text-start" style="font-family: 'IBM Plex Sans Thai', sans-serif;">เอกสารความคืบหน้าโครงงาน</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb fs-5 mt-3 ms-3">
                            <li class="breadcrumb-item"><a href="./Teacherpage.php">หน้าหลัก</a></li>
                            <li class="breadcrumb-item"><a href="./Teacheryourproject.php">โครงงานที่รับเป็นที่ปรึกษา</a></li>
                            <li class="breadcrumb-item active" aria-current="page">เอกสารความคืบหน้าโครงงาน</li>
                        </ol>
                    </nav>
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
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center justify-content-center">
                                <div class="col">
                                    <a class="fs-3 text-decoration-none" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ชื่อโครงงาน : <?php echo giveProjectNameTH($conn, $project_id); ?></a>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- --------------------------------------------------------หน้าปก upload---------------------------------------------------------------- -->
                    <div class="accordion" id="ProjectDocuments">

                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 1);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>
                            <h2 class="accordion-header" id="panelsStayOpen-headingCover">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseCover" aria-expanded="false" aria-controls="panelsStayOpen-collapseCover">
                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">หน้าปก</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 1) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseCover" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingCover">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">
                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 1);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 1; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                            echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                                                                                                            echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        }; ?></a>
                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>

                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์หน้าปก) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>



                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- --------------------------------------------------------ลายเซ็นกรรมการ upload---------------------------------------------------------------- -->


                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 2);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>
                            <h2 class="accordion-header" id="panelsStayOpen-headingSignature">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseSignature" aria-expanded="false" aria-controls="panelsStayOpen-collapseSignature">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">ลายเซ็นกรรมการ</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 2) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>


                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseSignature" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingSignature">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">
                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 2);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 2; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                            echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                                                                                                            echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        }; ?></a>
                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์ลายเซ็นกรรมการ) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- --------------------------------------------------------บทคัดย่อ upload---------------------------------------------------------------- -->


                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 3);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>
                            <h2 class="accordion-header" id="panelsStayOpen-headingAbstract">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseAbstract" aria-expanded="false" aria-controls="panelsStayOpen-collapseAbstract">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">บทคัดย่อ</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 3) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>


                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseAbstract" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingAbstract">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">

                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 3);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 3; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                            echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                                                                                                            echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        }; ?></a>
                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์บทคัดย่อ) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- --------------------------------------------------------สารบัญ upload---------------------------------------------------------------- -->


                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 4);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>
                            <h2 class="accordion-header" id="panelsStayOpen-headingContents">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseContents" aria-expanded="false" aria-controls="panelsStayOpen-collapseContents">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">สารบัญ</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 4) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>


                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseContents" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingContents">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">

                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 4);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 4; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                            echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                                                                                                            echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        }; ?></a>
                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์สารบัญ) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- --------------------------------------------------------Accordion 1---------------------------------------------------------------- -->
                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 5);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>

                            <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="false" aria-controls="panelsStayOpen-collapseOne">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">เอกสารโครงงานบทที่ 1</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 5) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingOne">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">

                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 5);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 5; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                            echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                                                                                                            echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        }; ?></a>
                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์เอกสารโครงงานบทที่ 1) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- --------------------------------------------------------Accordion 2---------------------------------------------------------------- -->
                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 6);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>
                            <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">เอกสารโครงงานบทที่ 2</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 6) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">

                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 6);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 6; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                            echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                                                                                                            echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        }; ?></a>
                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์เอกสารโครงงานบทที่ 2) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- --------------------------------------------------------Accordion 3--------------------------------------------------------- -->
                        <div class="accordion-item">

                            <?php
                            $matchingIndices = findFilesByChapter($files, 7);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>
                            <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">เอกสารโครงงานบทที่ 3</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 7) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">

                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 7);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 7; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                            echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                                                                                                            echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        }; ?></a>
                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์เอกสารโครงงานบทที่ 3) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- --------------------------------------------------------Accordion 4--------------------------------------------------------- -->
                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 8);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>


                            <h2 class="accordion-header" id="panelsStayOpen-headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">เอกสารโครงงานบทที่ 4</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 8) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFour">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">

                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 8);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 8; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                            echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                                                                                                            echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        }; ?></a>
                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์เอกสารโครงงานบทที่ 4) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- --------------------------------------------------------Accordion 5--------------------------------------------------------- -->
                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 9);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>
                            <h2 class="accordion-header" id="panelsStayOpen-headingFive">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFive" aria-expanded="false" aria-controls="panelsStayOpen-collapseFive">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">เอกสารโครงงานบทที่ 5</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 9) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseFive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFive">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">

                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 9);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 9; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                            echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                                                                                                            echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                        }; ?></a>
                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์เอกสารโครงงานบทที่ 5) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- --------------------------------------------------------บรรณานุกรม upload--------------------------------------------------------- -->

                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 10);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>
                            <h2 class="accordion-header" id="panelsStayOpen-headingBibliography">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseBibliography" aria-expanded="false" aria-controls="panelsStayOpen-collapseBibliography">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">บรรณานุกรม</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 10) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseBibliography" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingBibliography">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">

                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 10);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 10; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                                echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                                                                                echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                            }; ?></a>

                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์บรรณานุกรม) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- --------------------------------------------------------ภาคผนวก upload--------------------------------------------------------- -->

                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 11);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>

                            <h2 class="accordion-header" id="panelsStayOpen-headingAppendix">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseAppendix" aria-expanded="false" aria-controls="panelsStayOpen-collapseAppendix">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">ภาคผนวก</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 11) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseAppendix" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingAppendix">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">

                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 11);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 11; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                                echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                                                                                echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                            }; ?></a>

                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์ภาคผนวก) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- --------------------------------------------------------ประวัติผู้จัดทำปริญญานิพนธ์ Author's History upload--------------------------------------------------------- -->

                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 12);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>
                            <h2 class="accordion-header" id="panelsStayOpen-headingHistory">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseHistory" aria-expanded="false" aria-controls="panelsStayOpen-collapseHistory">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">ประวัติผู้จัดทำปริญญานิพนธ์</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 12) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseHistory" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingHistory">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">

                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 12);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 12; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                                echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                                                                                echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                            }; ?></a>

                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์ประวัติผู้จัดทำปริญญานิพนธ์) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- --------------------------------------------------------โปสเตอร์ upload--------------------------------------------------------- -->

                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 13);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>
                            <h2 class="accordion-header" id="panelsStayOpen-headingPoster">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapsePoster" aria-expanded="false" aria-controls="panelsStayOpen-collapsePoster">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">โปสเตอร์</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 13) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapsePoster" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingPoster">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">

                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 13);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 13; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                                echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                                                                                echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                            }; ?></a>

                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์โปสเตอร์) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- --------------------------------------------------------รูปเล่มปริญญานิพนธ์ฉบับเต็ม--------------------------------------------------------- -->

                        <div class="accordion-item">
                            <?php
                            $matchingIndices = findFilesByChapter($files, 14);
                            $fileStatusHave = false;
                            $fileChapter = false;
                            if (!empty($matchingIndices)) {
                                $i = 0;
                                foreach ($matchingIndices as $fileIndex) {
                                    $i++;
                                    $currentFile = $files[$fileIndex];
                                    $filePath = $currentFile['file_path'];
                                    $fileChapter = $currentFile['file_chapter'];
                                    $fileId = $currentFile['file_id'];
                                    $fileStatus = $currentFile['file_status'];
                                    $fileStatusHave = 0;
                                    if ($fileStatus == 1) {
                                        $fileStatusHave = true; // มีไฟล์ที่มี file_status = 1
                                        break;
                                    }
                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                            }
                            ?>
                            <h2 class="accordion-header" id="panelsStayOpen-headingFullBook">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseAll" aria-expanded="false" aria-controls="panelsStayOpen-collapseAll">

                                    <div class="row">
                                        <div class="ms-3 col-auto me-auto">รูปเล่มปริญญานิพนธ์ฉบับเต็ม</div>
                                        <div class="ms-3 col-auto me-auto"> </div>
                                        <?php if ($fileStatusHave) : $fileStatusHave = false; ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-success"></i>
                                                    <i>เอกสารผ่านการอนุมัติ</i>
                                                </div>
                                            </div>
                                        <?php elseif ($fileChapter == 14) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <i class="bi bi-circle-fill text-warning"></i>
                                                    <i>รอการอนุมัติเอกสาร</i>
                                                </div>
                                            </div>
                                        <?php elseif (empty($filePath)) : ?>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseAll" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingAll">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="col-12">

                                            <?php
                                            $matchingIndices = findFilesByChapter($files, 14);

                                            if (!empty($matchingIndices)) {
                                                $i = 0;
                                                foreach ($matchingIndices as $fileIndex) {
                                                    $i++;
                                                    $currentFile = $files[$fileIndex];
                                                    $filePath = $currentFile['file_path'];
                                                    $fileId = $currentFile['file_id'];
                                                    $fileStatus = $currentFile['file_status'];

                                                    $sql = "SELECT * FROM `comments` WHERE file_id = :file_id ORDER BY comment_time DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>

                                                    <div class="mt-3">
                                                        <?php if ($fileStatus == 1) : ?>
                                                            <i class="bi bi-check-lg h4 text-danger"></i>
                                                        <?php endif; ?>

                                                        <a>
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัปโหลดล่าสุด" : "ไฟล์ที่อัปโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to approve File (ไฟล์ <?php echo $filePath; ?>)?');" href="approvetTeacher.php?fileId=<?php echo $fileId; ?>&project_id=<?php echo $project_id; ?>&file_chapter=<?php echo 14; ?>" class="btn btn-danger ms-2"><?php if ($fileStatus == 1) {
                                                                                                                                                                                                                                                                                                                                echo "ยกเลิกอนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                                                                                echo "อนุมัติเอกสาร";
                                                                                                                                                                                                                                                                                                                            }; ?></a>
                                                        <button id="toggleAccordion" class="btn btn-info text-white">ความคิดเห็น</button>

                                                        <div id="hiddenContent">
                                                            <div class="card shadow-sm mt-3">
                                                                <h5 class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-md3">
                                                                            <form class="input-group flex-nowrap" method="post" action="commentTeacher.php?fileId=<?php echo $fileId; ?>" enctype="multipart/form-data">
                                                                                <span class="input-group-text">ความคิดเห็น</span>
                                                                                <input class="form-control" type="text" id="comment" name="comment" placeholder="พิมพ์ข้อความของคุณที่นี่...">
                                                                                <input class="form-control" type="hidden" id="project_id" name="project_id" value="<?php echo $project_id; ?>">
                                                                                <button id="buttonsend" onclick="return confirm('Are you sure you want to send the comment (ไฟล์<?php echo $filePath; ?>)?');" type="submit" class="btn btn-primary ms-3">ส่ง</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </h5>
                                                                <div class="card-body">
                                                                    <?php foreach ($comments as $comment) : ?>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3">
                                                                                <i class="bi bi-person-circle h4"></i>
                                                                            </div>
                                                                            <div class="col-9">
                                                                                <div class="row">

                                                                                    <?php
                                                                                    $authorName = "";
                                                                                    $commentTime = $comment['comment_time'];

                                                                                    if (!empty($comment['student_id'])) {
                                                                                        $Student = getStudentById($conn, $comment['student_id']);
                                                                                        $authorName = $Student['firstname'] . " " . $Student['lastname'];
                                                                                    } elseif (!empty($comment['teacher_id'])) {
                                                                                        $teacher = getTeacherById($conn, $comment['teacher_id']);
                                                                                        $authorName =  $Position = giveTeacherPositionById($teacher['position']) . " " . $teacher['firstname'] . " " . $teacher['lastname'];
                                                                                    }
                                                                                    ?>
                                                                                    <p class="text-muted">
                                                                                        <?php echo $authorName; ?>
                                                                                        <i class="float-end"><?php echo $commentTime; ?></i>
                                                                                    </p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-1 message-box ms-3"></div>
                                                                            <div class="col-9">
                                                                                <div class="row">
                                                                                    <div class="mb-3" tabindex="-1">
                                                                                        <p><?php echo $comment['comment']; ?></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ไฟล์รูปเล่มปริญญานิพนธ์ฉบับเต็ม) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
                                            }
                                            ?>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </main>


            </div>
        </div>


        <script>
            // // สร้างคลิกอีเวนต์สำหรับทุกตัวที่มี id="toggleAccordion"
            // let toggleButtons = document.querySelectorAll("#toggleAccordion");

            // // สร้าง event listener สำหรับแต่ละ button
            // toggleButtons.forEach(function(button) {
            //     let isOpen = true;
            //     button.addEventListener("click", function() {
            //         var hiddenContent = button.nextElementSibling; // หา element ถัดไปจาก button

            //         if (isOpen) { // ถ้า isOpen เป็น true
            //             hiddenContent.style.display = "block"; // แสดงเนื้อหา
            //         } else {
            //             hiddenContent.style.display = "none"; // ซ่อนเนื้อหา
            //         }

            //         isOpen = !isOpen; // สลับค่า isOpen ระหว่าง true และ false
            //     });
            // });


            // ===========================================================================================================
            //  check ดูการกดของ element buttonsend เเล้วมีการเก็บ commentStatus ไว้หลัง refresh จะ focus มาที่ element

            document.addEventListener("DOMContentLoaded", function() {

                // หาปุ่มที่ใช้ในการแสดงหรือซ่อนความคิดเห็น
                const toggleButtons = document.querySelectorAll("#toggleAccordion");
                const hiddenContent = document.querySelectorAll("#hiddenContent");
                const buttonsend = document.querySelectorAll("#buttonsend");
                console.log(buttonsend);

                // สร้าง event listener สำหรับแต่ละปุ่ม
                toggleButtons.forEach(function(button, index) {
                    // เพิ่ม event listener สำหรับปุ่มเพื่อแสดงหรือซ่อนความคิดเห็น
                    button.addEventListener("click", function() {
                        // ตรวจสอบว่า index ของปุ่มตรงกับ index ของ buttonsend หรือไม่
                        buttonsend.forEach(function(buttonsend, sendIndex) {
                            // เพิ่ม event listener สำหรับปุ่มส่งความคิดเห็น
                            buttonsend.addEventListener("click", function() {
                                localStorage.setItem("commentStatus" + sendIndex, "open");
                                const parentCollapse = this.closest(".accordion-item").querySelector(".accordion-collapse");
                                if (parentCollapse) {
                                    // บันทึกสถานะ accordion-collapse ที่กดเปิดไว้ใน localStorage
                                    localStorage.setItem("accordionCollapseStatus", parentCollapse.id);
                                }

                                // ตรวจสอบว่า index ของปุ่มส่งที่ถูกคลิกตรงกับ index ของปุ่ม toggle
                                if (sendIndex === index) {
                                    // ถ้าเป็น Index เดียวกัน ให้แสดง hiddenContent และปุ่ม toggle ที่เกี่ยวข้องกัน
                                    hiddenContent.forEach(function(content, contentIndex) {
                                        if (contentIndex === index) {
                                            content.style.display = "block"; // แสดงเนื้อหา
                                        } else {
                                            content.style.display = "none"; // ซ่อนเนื้อหา
                                        }
                                    });
                                    toggleButtons.forEach(function(toggleButton, toggleIndex) {
                                        if (toggleIndex === index) {
                                            toggleButton.textContent = "ซ่อนความคิดเห็น"; // เปลี่ยนข้อความบนปุ่ม
                                        } else {
                                            toggleButton.textContent = "ความคิดเห็น"; // เปลี่ยนข้อความบนปุ่ม
                                        }
                                    });
                                }
                            });
                        });

                        if (index < hiddenContent.length) {
                            const content = hiddenContent[index];
                            if (content.style.display === "none") {
                                // ถ้าเนื้อหาถูกซ่อนหรือไม่แสดง
                                content.style.display = "block"; // แสดงเนื้อหา                 
                                button.textContent = "ซ่อนความคิดเห็น"; // เปลี่ยนข้อความบนปุ่ม
                            } else {
                                content.style.display = "none"; // ซ่อนเนื้อหา
                                localStorage.setItem("commentStatus" + index, "closed");
                                button.textContent = "ความคิดเห็น"; // เปลี่ยนข้อความบนปุ่ม
                            }
                        }
                    });
                });

                // ตรวจสอบสถานะความคิดเห็นเมื่อหน้าเว็บโหลด
                toggleButtons.forEach(function(button, index) {
                    const commentStatus = localStorage.getItem("commentStatus" + index);
                    if (commentStatus === "open") {
                        // ถ้าสถานะความคิดเห็นเป็น "open" ให้แสดงความคิดเห็น
                        hiddenContent.forEach(function(content, contentIndex) {
                            if (contentIndex === index) {
                                content.style.display = "block"; // แสดงเนื้อหา
                                localStorage.setItem("commentStatus" + index, "closed");
                            } else {
                                content.style.display = "none"; // ซ่อนเนื้อหา
                            }
                            button.textContent = "ซ่อนความคิดเห็น"; // เปลี่ยนข้อความบนปุ่ม
                        });

                    } else {
                        localStorage.removeItem("commentStatus" + index);
                    }
                });


                // ===========================================================================================================
                //  check ดูการกดของ element accordionCollapseStatus เเล้วมีการเก็บ commentStatus ไว้หลัง refresh จะ focus มาที่ element

                // ให้ตรวจสอบ localStorage สำหรับสถานะ accordion-collapse
                function checkAccordionStatus() {
                    const collapseStatus = localStorage.getItem("accordionCollapseStatus");
                    if (collapseStatus) {
                        const accordionCollapseElements = document.querySelectorAll(".accordion-collapse");
                        accordionCollapseElements.forEach((collapse) => {
                            if (collapse.id === collapseStatus) {
                                // เปิด accordion-collapse ที่มี id ตรงกับสถานะใน localStorage
                                collapse.classList.add("show");
                                // หาปุ่มที่ใช้ในการเปิด accordion-collapse
                                const button = document.querySelector(`[data-bs-target="#${collapse.id}"]`);
                                if (button) {
                                    // เพิ่มคลาส "accordion-button" ให้กับปุ่ม
                                    button.classList.remove('collapsed');
                                } else {
                                    button.classList.add("accordion-button collapsed");
                                }
                            }
                        });
                    }

                }

                // ตรวจสอบสถานะ accordion-collapse เมื่อหน้าเว็บโหลด
                checkAccordionStatus();

                // ตรวจสอบการคลิกที่ลิงก์ "ลบเอกสาร"
                const approveLinks = document.querySelectorAll(".btn.btn-danger.ms-2");
                approveLinks.forEach((approveLink, index) => {
                    approveLink.addEventListener("click", function(e) {
                        const parentCollapse = this.closest(".accordion-item").querySelector(".accordion-collapse");
                        if (parentCollapse) {
                            // บันทึกตำแหน่ง scroll ใน localStorage
                            localStorage.setItem("scrollPositionY", window.scrollY);
                            // บันทึกสถานะ accordion-collapse ที่กดเปิดไว้ใน localStorage
                            localStorage.setItem("accordionCollapseStatus", parentCollapse.id);

                            // หาปุ่มที่ใช้ในการเปิด accordion-collapse
                            const button = document.querySelector(`[data-bs-target="#${parentCollapse.id}"]`);
                            if (button) {
                                // เพิ่มคลาส "accordion-button" ให้กับปุ่ม
                                button.classList.remove('collapsed');
                            } else {
                                button.classList.add("accordion-button collapsed");
                            }
                        }
                    });
                });
                // logout clear ค่า
                document.querySelector('#logoutLink').addEventListener('click', function() {
                    // ลบค่า scroll ใน Local Storage
                    localStorage.removeItem('accordionCollapseStatus');
                    // ลบค่า commentStatus ออกจาก localStorage
                    for (let index = 0; index < toggleButtons.length; index++) {
                        localStorage.removeItem("commentStatus" + index);
                    }
                });

            });

            // ตรวจสอบว่ามีตำแหน่ง scroll ที่ถูกบันทึกไว้ใน localStorage หรือไม่
            const scrollPositionY = localStorage.getItem("scrollPositionY");
            if (scrollPositionY) {
                // คืนค่าตำแหน่ง scroll ให้กับหน้าเว็บ
                window.scrollTo(0, scrollPositionY);

                // หลังจากคืนค่าตำแหน่ง scroll ให้ลบค่าที่ถูกบันทึกไว้ใน localStorage ออก
                localStorage.removeItem("scrollPositionY");
            }


            // ตรวจสอบสถานะ accordion-collapse เมื่อมีการคลิกที่ปุ่มหรือไม่เมื่อเปลี่ยนที่คลิก
            const accordionButtons = document.querySelectorAll("[data-bs-toggle='collapse']");
            accordionButtons.forEach((button) => {
                button.addEventListener("click", function() {
                    const targetCollapseId = this.getAttribute("data-bs-target").replace("#", "");
                    const isAccordionOpen = this.getAttribute("aria-expanded") === "true";

                    if (isAccordionOpen) {
                        // ตรวจสอบว่ามีค่า accordionCollapseStatus ใน localStorage
                        const accordionCollapseStatus = localStorage.getItem("accordionCollapseStatus");
                        if (accordionCollapseStatus) {
                            // ถ้ามีค่าใน localStorage ให้ลบออก
                            localStorage.removeItem("accordionCollapseStatus");
                        }
                    }
                });
            });
        </script>

</body>

</html>