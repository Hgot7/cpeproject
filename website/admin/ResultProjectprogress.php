<?php

session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('Location: ../index.php');
    exit();
}


function giveTeacherById($conn, $teacher_id)
{
    $sql = "SELECT * FROM `teacher` WHERE teacher_id = :teacher_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':teacher_id', $teacher_id);
    $stmt->execute();
    return $stmt->fetch();
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


    <title>ResultTeacher</title>

</head>

<body>
    <!-- -------------------------------------------------Header------------------------------------------------- -->
    <div class="HeaderBg shadow">
        <div class="container">
            <navbar_admin-component></navbar_admin-component>
        </div>
    </div>

    <!-- -------------------------------------------------Material------------------------------------------------- -->
    <div class="container-fluid justify-content-around">
        <div class="row">

            <sidebar_admin-component></sidebar_admin-component> <!-- component.js sidebar_admin-->
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">

                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลรายงานสรุปสถานะโครงงานของนักศึกษา</h1>
                <div class="me-3 mb-3 justify-content-between align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb fs-5 mt-3 ms-3">
                            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
                            <li class="breadcrumb-item"><a href="./Reportpage.php">รายงานสรุป</a></li>
                            <li class="breadcrumb-item active" aria-current="page">รายงานสรุปสถานะโครงงานของนักศึกษา</li>

                        </ol>
                    </nav>
                    <div class="input-group flex-nowrap">
                        <?php
                        $sql = "SELECT * FROM `defaultsystem`";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $defaultsystem = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($defaultsystem as $row) {
                        ?>
                            <span class="input-group-text text-bg-primary fs-5">ปัจจุบัน</span>
                            <span class="input-group-text text-bg-secondary fs-5">ปีการศึกษา</span>
                            <span class="input-group-text fs-5"><?php echo $row['year'] ?></span>
                            <span class="input-group-text text-bg-secondary fs-5">ภาคการศึกษาที่</span>
                            <span class="input-group-text fs-5"><?php echo $row['term'] ?></span>
                        <?php
                        }
                        ?>
                    </div>
                </div>

                <div class="col me-3 mb-4 mb-lg-0">

                    <div class="card shadow-sm">
                        <div class="card-header justify-content-between align-items-center">

                            <form action="./ResultProjectprogress.php" method="POST">
                                <div class="row g-3 mb-2">

                                    <div class="col-md-2">
                                        <label for="filterYear" class="form-label">ฟิลเตอร์ปีการศึกษา</label>
                                        <select class="form-select" name="filteryear">
                                            <?php
                                            if (isset($_POST['resetfilter'])) {
                                                unset($_SESSION['selectedYear']);
                                                unset($_SESSION['selectedTerm']);
                                            }
                                            if (isset($_POST['submitfilter'])) {
                                                $_SESSION['selectedYear'] = isset($_POST['filteryear']) ? $_POST['filteryear'] : null;
                                                $_SESSION['selectedTerm'] = isset($_POST['filterterm']) ? $_POST['filterterm'] : null;

                                                $selectedYear =  $_SESSION['selectedYear'];
                                                $selectedTerm =   $_SESSION['selectedTerm'];
                                            }
                                            $years = $conn->query("SELECT DISTINCT year FROM `project` ORDER BY year DESC");
                                            $years->execute();
                                            ?>
                                            <option value="">เลือกปีการศึกษา</option>
                                            <?php
                                            while ($datayear = $years->fetch(PDO::FETCH_ASSOC)) {
                                                $yearValue = $datayear['year'];
                                                $isYearSelected = ($selectedYear == $yearValue) ? 'selected' : ''; // เพิ่มเงื่อนไขเช็คค่า selected
                                            ?>
                                                <option value="<?php echo $yearValue; ?>" <?php echo $isYearSelected; ?>>
                                                    <?php echo $yearValue; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="filterTerm" class="form-label">ฟิลเตอร์ภาคการศึกษา</label>
                                        <select class="form-select" name="filterterm">
                                            <?php
                                            $terms = $conn->query("SELECT DISTINCT term FROM `project` ORDER BY term DESC");
                                            $terms->execute();
                                            ?>
                                            <option value="">เลือกภาคการศึกษา</option>
                                            <?php
                                            while ($dataterm = $terms->fetch(PDO::FETCH_ASSOC)) {
                                                $termValue = $dataterm['term'];
                                                $isTermSelected = ($selectedTerm == $termValue) ? 'selected' : ''; // เพิ่มเงื่อนไขเช็คค่า selected
                                            ?>
                                                <option value="<?php echo $termValue; ?>" <?php echo $isTermSelected; ?>>
                                                    <?php echo $termValue; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-auto d-flex align-items-end justify-content-start">
                                        <button type="submit" id="submitfilter" name="submitfilter" class="btn btn-success">ฟิลเตอร์</button>
                                    </div>
                                    
                                    <div class="col-auto d-flex align-items-end justify-content-start">
                                        <button type="submit" id="resetfilter" name="resetfilter" class="btn btn-warning">รีเซ็ตฟิลเตอร์</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" scope="col" style="width : 10%;">ลำดับที่</th>
                                            <th class="text-center" scope="col" style="width : 30%;">ชื่อโครงงาน</th>
                                            <th class="text-center" scope="col">อาจารย์ที่ปรึกษาหลัก</th>
                                            <th class="text-center" scope="col">อาจารย์ที่ปรึกษาร่วม</th>
                                            <th class="text-center" scope="col">สถานะ</th>
                                            <th class="text-center" scope="col">ปีการศึกษา</th>
                                            <th class="text-center" scope="col">ภาคการศึกษา</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            $selected = '';
                                            if (isset($_POST['submitfilter'])) {

                                                // $selectedYear = isset($_POST['filteryear']) ? $_POST['filteryear'] : null;
                                                // $selectedTerm = isset($_POST['filterterm']) ? $_POST['filterterm'] : null;


                                                if (empty($selectedYear) && empty($selectedTerm)) {
                                                    $sql = "SELECT * FROM `project` WHERE year = (SELECT year FROM `defaultsystem` WHERE default_system_id = 1 ) 
                                            AND term = (SELECT term FROM `defaultsystem` WHERE default_system_id = 1 ) ORDER BY year DESC, term DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();
                                                    $selected = 'โครงงานทั้งหมด';
                                                } elseif (!empty($selectedYear) && !empty($selectedTerm)) {
                                                    // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                                                    $sql = "SELECT * FROM `project` WHERE term = :term AND year = :year ORDER BY year DESC, term DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':term', $selectedTerm);
                                                    $stmt->bindParam(':year', $selectedYear);
                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();
                                                    $selected = 'โครงงานทั้งหมดในปีการศึกษา ' . $selectedYear . ' ภาคการศึกษาที่ ' . $selectedTerm;
                                                } elseif (!empty($selectedYear) && empty($selectedTerm)) {
                                                    // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                                                    $sql = "SELECT * FROM `project` WHERE year = :year ORDER BY year DESC, term DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':year', $selectedYear);
                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();
                                                    $selected = 'โครงงานทั้งหมดในปีการศึกษา ' . $selectedYear;
                                                } elseif (empty($selectedYear) && !empty($selectedTerm)) {
                                                    // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                                                    $sql = "SELECT * FROM `project` WHERE  term = :term  ORDER BY year DESC, term DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':term', $selectedTerm);
                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();
                                                    $selected = 'โครงงานทั้งหมดในภาคการศึกษาที่ ' . $selectedTerm;
                                                } else {
                                                    // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                                                    $sql = "SELECT * FROM `project` WHERE year = (SELECT year FROM `defaultsystem` WHERE default_system_id = 1 ) 
                                            AND term = (SELECT term FROM `defaultsystem` WHERE default_system_id = 1 ) ORDER BY year DESC, term DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $selected = 'โครงงานทั้งหมด';

                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();
                                                }
                                                $index = 1;
                                                if (empty($datas)) {
                                                    echo "<tr><td colspan='20' class='text-center'>No data available</td></tr>";
                                                } else {
                                                    foreach ($datas as $project) {
                                                        //อาจารย์ที่ปรึกษาหลัก
                                                        $teacher1 = ($project['teacher_id1']) ? giveTeacherById($conn, $project['teacher_id1']) : null;
                                                        //อาจารย์ที่ปรึกษาร่วม
                                                        $teacher2 = ($project['teacher_id2']) ? giveTeacherById($conn, $project['teacher_id2']) : null;
                                        ?>
                                                        <tr>
                                                            <th scope="row"><?php echo $index++; ?></th>
                                                            <td><?php echo $project['project_nameTH']; ?></td>
                                                            <td><?php echo giveTeacherPositionById($teacher1['position']) . $teacher1['firstname']; ?></td>
                                                            <td><?php if (empty($teacher2)) {
                                                                    echo "";
                                                                } else {
                                                                    echo giveTeacherPositionById($teacher2['position']) . $teacher2['firstname'];
                                                                } ?></td>
                                                            <?php
                                                            $sql = "SELECT count(distinct file_chapter) FROM `file` WHERE project_id = :project_id";
                                                            $stmt = $conn->prepare($sql);
                                                            $stmt->bindParam(':project_id', $project['project_id']);
                                                            $stmt->execute();
                                                            $chapter = $stmt->fetchColumn(); // Use fetchColumn to get the count directly

                                                            $sql = "SELECT grade FROM `student` WHERE student_id = :student_id";
                                                            $stmt = $conn->prepare($sql);
                                                            $stmt->bindParam(':student_id', $project['student_id1']);
                                                            $stmt->execute();
                                                            $studentGrade = $stmt->fetchColumn();

                                                            ?>
                                                            <td>
                                                                <?php
                                                                if (isset($studentGrade)) {
                                                                ?>
                                                                    สอบเสร็จสิ้น <?php } elseif (empty($chapter)) { ?>
                                                                    ส่งเอกสารความคืบหน้า 0 จาก 14
                                                                <?php
                                                                                } else { ?>
                                                                    ส่งเอกสารความคืบหน้า <?php echo $chapter ?> จาก 14
                                                                <?php } ?>
                                                            </td>

                                                            <td><?php echo ($project['year']); ?></td>
                                                            <td><?php echo ($project['term']); ?></td>
                                                            <td>
                                                        </tr>
                                                    <?php
                                                    }
                                                }
                                            } elseif (isset($_POST['viewAll'])) {
                                                // ดึงข้อมูลโครงงานทั้งหมด
                                                $sql = "SELECT * FROM `project` ORDER BY year DESC, term DESC";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->execute();
                                                $datas = $stmt->fetchAll();
                                                $index = 1;
                                                $selected = 'โครงงานทั้งหมดในระบบ';
                                                // ครับประกันว่า $datas มีข้อมูล
                                                if (!$datas) {
                                                    echo "<p><td colspan='20' class='text-center'>No data available</td></p>";
                                                } else {
                                                    foreach ($datas as $project) {
                                                        //อาจารย์ที่ปรึกษาหลัก 
                                                        $teacher1 = ($project['teacher_id1']) ? giveTeacherById($conn, $project['teacher_id1']) : null;
                                                        //อาจารย์ที่ปรึกษาร่วม
                                                        $teacher2 = ($project['teacher_id2']) ? giveTeacherById($conn, $project['teacher_id2']) : null;
                                                    ?>

                                                        <tr>
                                                            <th scope="row"><?php echo $index++; ?></th>
                                                            <td><?php echo $project['project_nameTH']; ?></td>
                                                            <td><?php echo giveTeacherPositionById($teacher1['position']) . $teacher1['firstname']; ?></td>
                                                            <td><?php if (empty($teacher2)) {
                                                                    echo "";
                                                                } else {
                                                                    echo giveTeacherPositionById($teacher2['position']) . $teacher2['firstname'];
                                                                } ?></td>
                                                            <?php
                                                            $sql = "SELECT count(distinct file_chapter) FROM `file` WHERE project_id = :project_id";
                                                            $stmt = $conn->prepare($sql);
                                                            $stmt->bindParam(':project_id', $project['project_id']);
                                                            $stmt->execute();
                                                            $chapter = $stmt->fetchColumn(); // Use fetchColumn to get the count directly

                                                            $sql = "SELECT grade FROM `student` WHERE student_id = :student_id";
                                                            $stmt = $conn->prepare($sql);
                                                            $stmt->bindParam(':student_id', $project['student_id1']);
                                                            $stmt->execute();
                                                            $studentGrade = $stmt->fetchColumn();
                                                            ?>
                                                            <td>
                                                                <?php
                                                                if (isset($studentGrade)) {
                                                                ?>
                                                                    สอบเสร็จสิ้น <?php } elseif (empty($chapter)) { ?>
                                                                    ส่งเอกสารความคืบหน้า 0 จาก 14
                                                                <?php
                                                                                } else { ?>
                                                                    ส่งเอกสารความคืบหน้า <?php echo $chapter ?> จาก 14
                                                                <?php } ?>
                                                            </td>

                                                            <td><?php echo ($project['year']); ?></td>
                                                            <td><?php echo ($project['term']); ?></td>
                                                            <td>
                                                        </tr>
                                                    <?php }
                                                }
                                            } else {
                                                $sql = "SELECT * FROM `project` WHERE year = (SELECT year FROM `defaultsystem` WHERE default_system_id = 1 ) 
                                                AND term = (SELECT term FROM `defaultsystem` WHERE default_system_id = 1 ) ORDER BY year DESC, term DESC";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->execute();
                                                $datas = $stmt->fetchAll();
                                                $index = 1;
                                                foreach ($datas as $project) {
                                                    //อาจารย์ที่ปรึกษาหลัก 
                                                    $teacher1 = ($project['teacher_id1']) ? giveTeacherById($conn, $project['teacher_id1']) : null;
                                                    //อาจารย์ที่ปรึกษาร่วม
                                                    $teacher2 = ($project['teacher_id2']) ? giveTeacherById($conn, $project['teacher_id2']) : null;
                                                    ?>
                                                    <tr>
                                                        <th scope="row"><?php echo $index++; ?></th>
                                                        <td><?php echo $project['project_nameTH']; ?></td>
                                                        <td><?php echo giveTeacherPositionById($teacher1['position']) . $teacher1['firstname']; ?></td>
                                                        <td><?php if (empty($teacher2)) {
                                                                echo "";
                                                            } else {
                                                                echo giveTeacherPositionById($teacher2['position']) . $teacher2['firstname'];
                                                            } ?></td>
                                                        <?php
                                                        $sql = "SELECT count(distinct file_chapter) FROM `file` WHERE project_id = :project_id";
                                                        $stmt = $conn->prepare($sql);
                                                        $stmt->bindParam(':project_id', $project['project_id']);
                                                        $stmt->execute();
                                                        $chapter = $stmt->fetchColumn(); // Use fetchColumn to get the count directly

                                                        $sql = "SELECT grade FROM `student` WHERE student_id = :student_id";
                                                        $stmt = $conn->prepare($sql);
                                                        $stmt->bindParam(':student_id', $project['student_id1']);
                                                        $stmt->execute();
                                                        $studentGrade = $stmt->fetchColumn();

                                                        ?>
                                                        <td>
                                                            <?php
                                                            if (isset($studentGrade)) {
                                                            ?>
                                                                สอบเสร็จสิ้น <?php } elseif (empty($chapter)) { ?>
                                                                ส่งเอกสารความคืบหน้า 0 จาก 14
                                                            <?php
                                                                            } else { ?>
                                                                ส่งเอกสารความคืบหน้า <?php echo $chapter ?> จาก 14
                                                            <?php } ?>
                                                        </td>

                                                        <td><?php echo ($project['year']); ?></td>
                                                        <td><?php echo ($project['term']); ?></td>
                                                        <td>
                                                    </tr>
                                        <?php
                                                    $selected = 'โครงงานทั้งหมดในปีการศึกษา ' . $project['year'] . ' ภาคการศึกษาที่ ' . $project['term'];
                                                }
                                            }
                                        } catch (PDOException $e) {
                                            echo "Error: " . $e->getMessage();
                                        }

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <form action="./ResultProjectprogress.php" method="POST">
                                <div class="d-grid gap-2">
                                    <button style="font-family: 'IBM Plex Sans Thai', sans-serif;" name="viewAll" class="btn btn-secondary">View All</button>
                                </div>
                            </form>
                            <?php
                            if (!empty($datas)) { ?>
                                <form action="./PDF_Result/PDF_ResultProjectprogress.php" method="post" target="_blank">
                                    <input type="hidden" name="data" value='<?php echo json_encode($datas); ?>'>
                                    <input type="hidden" name="select" value='<?php echo json_encode($selected); ?>'>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-info text-white mt-2">ดาว์โหลดเป็นไฟล์ PDF</button>
                                    </div>
                                </form>
                            <?php
                            }

                            ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

</body>

</html>