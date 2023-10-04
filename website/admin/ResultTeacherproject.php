<?php

session_start();
require_once "../connect.php";

if (!isset($_SESSION['admin_login'])) {
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


    <title>หน้ารายงานสรุปจำนวนหัวข้อโครงงานที่อาจารย์แต่ละคนรับเป็นที่ปรึกษา</title>

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


                <div class="col me-3 mb-4 mb-lg-0 d-flex justify-content-between align-items-center">
                    <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลรายงานสรุปจำนวนหัวข้อโครงงานที่อาจารย์แต่ละคนรับเป็นที่ปรึกษา</h1>
                </div>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb fs-5 mt-3 ms-3">
                        <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="./Reportpage.php">รายงานสรุป</a></li>
                        <li class="breadcrumb-item active" aria-current="page">รายงานสรุปจำนวนหัวข้อโครงงานที่อาจารย์แต่ละคนรับเป็นที่ปรึกษา</li>

                    </ol>
                </nav>




                <div class="col me-3 mb-4 mb-lg-0">
                    <div class="card shadow-sm">
                        <div class="card-header justify-content-between align-items-center">
                            <form action="./ResultTeacherproject.php" method="POST">
                                <div class="row g-3 mb-2">

                                    <div class="col-md-2">
                                        <label for="filterYear" class="form-label">ฟิลเตอร์ปีการศึกษา</label>
                                        <select class="form-select" name="filteryear">
                                            <?php
                                            $years = $conn->query("SELECT DISTINCT year FROM `project` ORDER BY year DESC");
                                            $years->execute();
                                            ?>
                                            <option value="">เลือกปีการศึกษา</option>
                                            <?php
                                            while ($datayear = $years->fetch(PDO::FETCH_ASSOC)) { ?>
                                                <option value="<?php echo $datayear['year']; ?>">
                                                    <?php echo $datayear['year']; ?>
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
                                            while ($dataterm = $terms->fetch(PDO::FETCH_ASSOC)) { ?>
                                                <option value="<?php echo $dataterm['term']; ?>">
                                                    <?php echo $dataterm['term']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="filterteacher" class="form-label">ฟิลเตอร์อาจารย์</label>
                                        <select class="form-select" name="filterteacher">
                                            <option value="">เลือกอาจารย์</option>
                                            <?php
                                            $teachers = $conn->query("SELECT * FROM `teacher`");
                                            $teachers->execute();
                                            while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                                                // ถ้า teacher_id เป็น 1 ให้ข้ามการแสดง option นี้ไป
                                                if ($teacher['teacher_id'] == 1) {
                                                    continue;
                                                }
                                                echo '<option value="' . $teacher['teacher_id'] . '">';
                                                echo $teacher['position'] . ' ' . $teacher['firstname'];
                                                echo '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-auto d-flex align-items-end justify-content-start">
                                        <button type="submit" id="submitfilter" name="submitfilter" class="btn btn-success">ฟิลเตอร์</button>
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
                                            <th class="text-center" scope="col">รหัสกลุ่มโครงงาน</th>
                                            <th scope="col" style="width : 30%;">ชื่อโครงงาน</th>
                                            <th class="text-center" scope="col">ปีการศึกษา</th>
                                            <th class="text-center" scope="col">ภาคการศึกษา</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            $selected = '';
                                            if (isset($_POST['submitfilter'])) {

                                                $selectedYear = isset($_POST['filteryear']) ? $_POST['filteryear'] : null;
                                                $selectedTerm = isset($_POST['filterterm']) ? $_POST['filterterm'] : null;
                                                $selectedTeacher = isset($_POST['filterteacher']) ? $_POST['filterteacher'] : null;

                                                if (empty($selectedYear) && empty($selectedTerm) && empty($selectedTeacher)) {
                                                    $sql = "SELECT * FROM `project` ORDER BY year DESC, term DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();
                                                    $selected = 'โครงงานทั้งหมด';
                                                } elseif (!empty($selectedYear) && !empty($selectedTerm) && empty($selectedTeacher)) {
                                                    // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                                                    $sql = "SELECT * FROM `project` WHERE term = :term AND year = :year ORDER BY year DESC, term DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':term', $selectedTerm);
                                                    $stmt->bindParam(':year', $selectedYear);
                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();
                                                    $selected = 'โครงงานทั้งหมดในปีการศึกษา ' . $selectedYear . ' ภาคการศึกษาที่ ' . $selectedTerm;
                                                } elseif (!empty($selectedYear) && empty($selectedTerm) && empty($selectedTeacher)) {
                                                    // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                                                    $sql = "SELECT * FROM `project` WHERE year = :year ORDER BY year DESC, term DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':year', $selectedYear);
                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();
                                                    $selected = 'โครงงานทั้งหมดในปีการศึกษา ' . $selectedYear;
                                                } elseif (empty($selectedYear) && !empty($selectedTerm) && empty($selectedTeacher)) {
                                                    // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                                                    $sql = "SELECT * FROM `project` WHERE  term = :term  ORDER BY year DESC, term DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':term', $selectedTerm);
                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();
                                                    $selected = 'โครงงานทั้งหมดในภาคการศึกษาที่ ' . $selectedTerm;
                                                } elseif (empty($selectedYear) && empty($selectedTerm) && !empty($selectedTeacher)) {
                                                    // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                                                    $sql = "SELECT * FROM `project` WHERE (teacher_id1 = :teacher) OR (teacher_id2 = :teacher)  ORDER BY year DESC, term DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':teacher', $selectedTeacher);
                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();

                                                    $teachers = "SELECT * FROM `teacher` WHERE teacher_id = :id";
                                                    $teachers = $conn->prepare($teachers);
                                                    $teachers->bindParam(':id', $selectedTeacher);
                                                    $teachers->execute();
                                                    while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($teacher['teacher_id'] == 1) {
                                                            continue;
                                                        }
                                                        $selected = $teacher['position'] . ' ' . $teacher['firstname'];
                                                    }
                                                } elseif (empty($selectedYear) && !empty($selectedTerm) && !empty($selectedTeacher)) {
                                                    // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                                                    $sql = "SELECT * FROM `project` WHERE (term = :term AND (teacher_id1 = :teacher OR teacher_id2 = :teacher)) ORDER BY year DESC, term DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':term', $selectedTerm);
                                                    $stmt->bindParam(':teacher', $selectedTeacher);
                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();

                                                    $teachers = "SELECT * FROM `teacher` WHERE teacher_id = :id";
                                                    $teachers = $conn->prepare($teachers);
                                                    $teachers->bindParam(':id', $selectedTeacher);
                                                    $teachers->execute();
                                                    while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($teacher['teacher_id'] == 1) {
                                                            continue;
                                                        }
                                                        $selected = $teacher['position'] . ' ' . $teacher['firstname'];
                                                    }

                                                    $selected = $selected . ' โครงงานทั้งหมดในภาคการศึกษาที่ ' . $selectedTerm;
                                                } elseif (!empty($selectedYear) && empty($selectedTerm) && !empty($selectedTeacher)) {
                                                    // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                                                    $sql = "SELECT * FROM `project` WHERE (year = :year AND (teacher_id1 = :teacher OR teacher_id2 = :teacher)) ORDER BY year DESC, term DESC";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':year', $selectedYear);
                                                    $stmt->bindParam(':teacher', $selectedTeacher);
                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();
                                                    $teachers = "SELECT * FROM `teacher` WHERE teacher_id = :id";
                                                    $teachers = $conn->prepare($teachers);
                                                    $teachers->bindParam(':id', $selectedTeacher);
                                                    $teachers->execute();
                                                    while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($teacher['teacher_id'] == 1) {
                                                            continue;
                                                        }
                                                        $selected = $teacher['position'] . ' ' . $teacher['firstname'];
                                                    }

                                                    $selected = $selected . ' โครงงานทั้งหมดในปีการศึกษา ' . $selectedYear;
                                                } elseif (!empty($selectedYear) && !empty($selectedTerm) && !empty($selectedTeacher)) {
                                                    // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                                                    $sql = "SELECT * FROM `project` WHERE (year =:year) AND (term = :term) AND ((teacher_id1 = :teacher) OR (teacher_id2 = :teacher)) ORDER BY year DESC, term DESC";

                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':year', $selectedYear);
                                                    $stmt->bindParam(':term', $selectedTerm);
                                                    $stmt->bindParam(':teacher', $selectedTeacher);
                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();

                                                    $teachers = "SELECT * FROM `teacher` WHERE teacher_id = :id";
                                                    $teachers = $conn->prepare($teachers);
                                                    $teachers->bindParam(':id', $selectedTeacher);
                                                    $teachers->execute();
                                                    while ($teacher = $teachers->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($teacher['teacher_id'] == 1) {
                                                            continue;
                                                        }
                                                        $selected = $teacher['position'] . ' ' . $teacher['firstname'];
                                                    }
                                                    $selected = $selected . ' โครงงานทั้งหมดในปีการศึกษา ' . $selectedYear . ' ภาคการศึกษาที่ ' . $selectedTerm;
                                                } else {
                                                    // ถ้ามีการเลือกเงื่อนไขในการค้นหาให้ดำเนินการตามปกติ
                                                    $sql = "SELECT project.* FROM `project` WHERE (year LIKE :year) OR (term LIKE :term)";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':year', $selectedYear);
                                                    $stmt->bindParam(':term', $selectedTerm);

                                                    $stmt->execute();
                                                    $datas = $stmt->fetchAll();
                                                }
                                                $index = 1;
                                                if (empty($datas)) {
                                                    echo "<tr><td colspan='20' class='text-center'>No data available</td></tr>";
                                                } else {
                                                    foreach ($datas as $data) {
                                        ?>
                                                        <tr>
                                                            <th scope="row"><?php echo $index++; ?></th>
                                                            <td><?php echo $data['project_id']; ?></td>
                                                            <td><?php echo ($data['project_nameTH']); ?></td>
                                                            <td><?php echo ($data['year']); ?></td>
                                                            <td><?php echo ($data['term']); ?></td>
                                                            <td>
                                                        </tr>
                                                    <?php }
                                                }
                                            } elseif (isset($_POST['viewAll'])) {
                                                $sql = "SELECT * FROM `project` ORDER BY year DESC, term DESC";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->execute();
                                                $datas = $stmt->fetchAll();
                                                $selected = 'โครงงานทั้งหมด';

                                                if (empty($datas)) {
                                                    echo "<tr><td colspan='20' class='text-center'>No data available</td></tr>";
                                                } else {
                                                    $index = 1;
                                                    foreach ($datas as $data) {
                                                    ?>
                                                        <tr>
                                                            <th scope="row"><?php echo $index++; ?></th>
                                                            <td><?php echo $data['project_id']; ?></td>
                                                            <td><?php echo ($data['project_nameTH']); ?></td>
                                                            <td><?php echo ($data['year']); ?></td>
                                                            <td><?php echo ($data['term']); ?></td>
                                                            <td>
                                                        </tr>
                                                    <?php
                                                    }
                                                }
                                            } else {
                                                $sql = "SELECT * FROM `project` ORDER BY year DESC, term DESC";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->execute();
                                                $datas = $stmt->fetchAll();
                                                $selected = 'โครงงานทั้งหมด';

                                                if (empty($datas)) {
                                                    echo "<tr><td colspan='20' class='text-center'>No data available</td></tr>";
                                                } else {
                                                    $index = 1;
                                                    foreach ($datas as $data) {
                                                    ?>
                                                        <tr>
                                                            <th scope="row"><?php echo $index++; ?></th>
                                                            <td><?php echo $data['project_id']; ?></td>
                                                            <td><?php echo ($data['project_nameTH']); ?></td>
                                                            <td><?php echo ($data['year']); ?></td>
                                                            <td><?php echo ($data['term']); ?></td>
                                                            <td>
                                                        </tr>
                                        <?php
                                                    }
                                                }
                                            }
                                        } catch (PDOException $e) {
                                            echo "Error: " . $e->getMessage();
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <form action="./ResultTeacherproject.php" method="POST">
                                <div class="d-grid gap-2">
                                    <button style="font-family: 'IBM Plex Sans Thai', sans-serif;" class="btn btn-secondary">View All</button>
                                </div>
                            </form>
                            <?php
                            if (!empty($datas) && !empty($selectedTeacher)) {  ?>
                                <form action="./PDF_Result/PDF_ResultTeacherproject.php" method="post" target="_blank">
                                    <input type="hidden" name="data" value='<?php echo json_encode($datas); ?>'>
                                    <input type="hidden" name="select" value='<?php echo json_encode($selected); ?>'>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-info text-white mt-2">ดาว์โหลดเป็นไฟล์ PDF</button>
                                    </div>
                                </form> <?php } ?>

                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

</body>

</html>