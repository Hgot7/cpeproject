<?php
session_start();
require_once "../connect.php";
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
            <navbar_admin-component></navbar_admin-component> <!-- component.js Navber-->
        </div>
    </div>
    <!-- -------------------------------------------------Material------------------------------------------------- -->
    <!-- --------------------------------------------------------Accordion 1---------------------------------------------------------------- -->
    <div class="container-fluid justify-content-around">
        <?php
        $project_id = $_GET['id'];
        $sql = "SELECT * FROM `file` WHERE project_id = :id ORDER BY file_chapter ASC, file_date DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $project_id);
        $stmt->execute();
        $files = $stmt->fetchAll();

        function giveProjectNameTH($conn, $project_id)
        {
            $sql = "SELECT project_nameTH FROM `project` WHERE project_id = :project_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':project_id', $project_id);
            $stmt->execute();
            return $stmt->fetchColumn(); // เปลี่ยนเป็น fetchColumn() เพื่อเรียกคอลัมน์เดียวที่กลับมา
        }

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

        ?>
        <div class="row">

            <sidebar_admin-component></sidebar_admin-component>
            <div class="col">

                <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">
                    <h1 class="h2 text-start" style="font-family: 'IBM Plex Sans Thai', sans-serif;">เอกสารความคืบหน้าโครงงาน</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb fs-5 mt-3 ms-3">
                        <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
                            <li class="breadcrumb-item"><a href="./projectmanage.php">จัดการข้อมูลโครงงาน</a></li>
                            <li class="breadcrumb-item active" aria-current="page">ความคืบหน้าโครงงาน</li>
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
                                <div class="col-3">
                                    <a onclick="return confirm('Are you sure you want to delete all file (ลบไฟล์ทั้งหมดยกเว้นไฟล์เอกสารโปสเตอร์และรูปเล่มฉบับเต็ม โครงงาน<?php echo giveProjectNameTH($conn, $project_id); ?>)?');" href="deleteFileAll_Stdupload.php?id=<?php echo $project_id ?>" class="btn btn-danger float-end">ลบเอกสารความคืบหน้าทั้งหมด</a>
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
                                        <!-- <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label> -->
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>

                                                        <a onclick="return confirm('Are you sure you want to delete File (ไฟล์หน้าปก)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>

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
                                        <!--  <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label> -->
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <a onclick="return confirm('Are you sure you want to delete File (ลายเซ็นกรรมการ)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ลายเซ็นกรรมการ) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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
                                        <!-- <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label> -->
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <a onclick="return confirm('Are you sure you want to delete File (บทคัดย่อ)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (บทคัดย่อ) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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
                                        <!-- <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label> -->
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <a onclick="return confirm('Are you sure you want to delete File (สารบัญ)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (สารบัญ) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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
                                        <!-- <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label> -->
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <a onclick="return confirm('Are you sure you want to delete File (เอกสารโครงงานบทที่ 1)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (เอกสารโครงงานบทที่ 1) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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
                                        <!-- <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label> -->
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <a onclick="return confirm('Are you sure you want to delete File (เอกสารโครงงานบทที่ 2)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (เอกสารโครงงานบทที่ 2) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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
                                        <!-- <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label> -->
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <a onclick="return confirm('Are you sure you want to delete File (เอกสารโครงงานบทที่ 3)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (เอกสารโครงงานบทที่ 3) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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
                                        <!-- <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label> -->
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <a onclick="return confirm('Are you sure you want to delete File (เอกสารโครงงานบทที่ 4)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (เอกสารโครงงานบทที่ 4) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <a onclick="return confirm('Are you sure you want to delete File (เอกสารโครงงานบทที่ 5)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (เอกสารโครงงานบทที่ 5) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <a onclick="return confirm('Are you sure you want to delete File (บรรณานุกรม)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (บรรณานุกรม) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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
                                        <!-- <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label> -->
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <a onclick="return confirm('Are you sure you want to delete File (ภาคผนวก)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ภาคผนวก) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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
                                        <!-- <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label> -->
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <a onclick="return confirm('Are you sure you want to delete File (ประวัติผู้จัดทำปริญญานิพนธ์)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (ประวัติผู้จัดทำปริญญานิพนธ์) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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
                                        <!-- <label for="formFileMultiple" class="text-danger">*เฉพาะไฟล์นามสกุล .pdf เท่านั้น</label> -->
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <!-- <a onclick="return confirm('Are you sure you want to delete File (โปสเตอร์)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a> -->
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (โปสเตอร์) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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
                                                            <?php echo ($i == 1) ? "ไฟล์ที่อัพโหลดล่าสุด" : "ไฟล์ที่อัพโหลดไว้แล้ว"; ?>
                                                        </a>

                                                        <a href="<?php echo '.././student/fileUpload/' . $filePath; ?>" target="_blank">
                                                            <?php echo $filePath; ?>
                                                        </a>
                                                        <!-- <a onclick="return confirm('Are you sure you want to delete File (รูปเล่มปริญญานิพนธ์ฉบับเต็ม)?');" href="deleteFile_Stdupload.php?id=<?php echo $fileId; ?>" class="btn btn-danger ms-2">Delete File</a> -->
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<label for="formFileMultiple" class="text-danger">*ไฟล์ (รูปเล่มปริญญานิพนธ์ฉบับเต็ม) ยังไม่ถูกอัปโหลดจากนักศึกษา*</label>';
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



        <!-- -------------------------------------------------footers------------------------------------------------- -->





</body>

</html>