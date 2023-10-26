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


function giveStatusById($conn, $project_id, $teacherId)
{

    $teacherCheckQuery = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id AND (teacher_id1 = :teacher_id OR teacher_id2 = :teacher_id)");
    $teacherCheckQuery->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $teacherCheckQuery->bindParam(':teacher_id', $teacherId, PDO::PARAM_STR);
    $teacherCheckQuery->execute();
    $teacherCheckResult = $teacherCheckQuery->fetchAll();

    $isTeacher = !empty($teacherCheckResult);

    $level = $isTeacher ? 1 : 0;

    $topicSectionQuery = $conn->prepare("SELECT COUNT(DISTINCT topic_section_id) AS unique_topic_section_count FROM `topicsection` WHERE (topic_section_level = :level or topic_section_level = 2) AND topic_section_status = 1");
    $topicSectionQuery->bindParam(':level', $level, PDO::PARAM_INT);
    $topicSectionQuery->execute();
    $topicSectionData = $topicSectionQuery->fetch();

    $assessmentQuery = $conn->prepare("SELECT COUNT(DISTINCT t.topic_section_id) AS unique_topic_section_count
        FROM `assessment` a
        INNER JOIN `topic` t ON a.topic_id = t.topic_id
        WHERE a.project_id = :project_id
        AND a.referee_id = :referee_id");
    $assessmentQuery->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $assessmentQuery->bindParam(':referee_id', $teacherId, PDO::PARAM_STR);
    $assessmentQuery->execute();
    $assessmentData = $assessmentQuery->fetch();

    return ($assessmentData['unique_topic_section_count'] == $topicSectionData['unique_topic_section_count']) ? 1 : 0;
}

function giveStatusNameById($conn, $project_id, $teacherId)
{

    $teacherCheckQuery = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id AND (teacher_id1 = :teacher_id OR teacher_id2 = :teacher_id)");
    $teacherCheckQuery->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $teacherCheckQuery->bindParam(':teacher_id', $teacherId, PDO::PARAM_STR);
    $teacherCheckQuery->execute();
    $teacherCheckResult = $teacherCheckQuery->fetchAll();

    $isTeacher = !empty($teacherCheckResult);

    $level = $isTeacher ? 1 : 0;

    $topicSectionQuery = $conn->prepare("SELECT DISTINCT topic_section_id FROM `topicsection` WHERE (topic_section_level = :level OR topic_section_level = 2) AND topic_section_status = 1");
    $topicSectionQuery->bindParam(':level', $level, PDO::PARAM_INT);
    $topicSectionQuery->execute();
    $topicSectionData = $topicSectionQuery->fetchAll(PDO::FETCH_COLUMN);

    $assessmentQuery = $conn->prepare("SELECT DISTINCT t.topic_section_id 
    FROM `assessment` a
    INNER JOIN `topic` t ON a.topic_id = t.topic_id
    WHERE a.project_id = :project_id
    AND a.referee_id = :referee_id");
    $assessmentQuery->bindParam(':project_id', $project_id, PDO::PARAM_STR);
    $assessmentQuery->bindParam(':referee_id', $teacherId, PDO::PARAM_STR);
    $assessmentQuery->execute();
    $assessmentData = $assessmentQuery->fetchAll(PDO::FETCH_COLUMN);

    $results = array_diff($topicSectionData, $assessmentData);


    $result = array(); // สร้างอาร์เรย์เพื่อเก็บข้อมูล topic_section

    foreach ($results as $data) {
        $topicSectionQuery = $conn->prepare("SELECT topic_section FROM `topicsection` WHERE topic_section_id = :topic_section_id");
        $topicSectionQuery->bindParam(':topic_section_id', $data, PDO::PARAM_INT);
        $topicSectionQuery->execute();
        $topicSectionData = $topicSectionQuery->fetch(PDO::FETCH_COLUMN);

        if ($topicSectionData !== false) { // ตรวจสอบว่าพบข้อมูลหรือไม่
            $result[] = $topicSectionData; // เพิ่มข้อมูลลงในอาร์เรย์ $result
        }
    }



    $result = implode(", ", $result);

    return $result;
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


    <title>หน้ารายงานสรุปอาจารย์ที่ไม่ประเมินผลการสอบแต่ละภาคการศึกษา</title>

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

                <h1 class="h2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">ข้อมูลรายงานสรุปอาจารย์ที่ไม่ประเมินผลการสอบแต่ละภาคการศึกษา</h1>
                <div class="me-3 mb-3 justify-content-between align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb fs-5 mt-3 ms-3">
                            <li class="breadcrumb-item"><a href="./adminpage.php">หน้าหลัก</a></li>
                            <li class="breadcrumb-item"><a href="./Reportpage.php">รายงานสรุป</a></li>
                            <li class="breadcrumb-item active" aria-current="page">รายงานสรุปอาจารย์ที่ไม่ประเมินผลการสอบแต่ละภาคการศึกษา</li>
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
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <th class="text-center" scope="col" style="width : 10%;">ลำดับที่</th>
                                        <th class="text-center" scope="col">รหัสโครงงาน</th>
                                        <th class="text-center" scope="col">ชื่อโครงงาน</th>
                                        <th class="text-center" scope="col">ชื่ออาจารย์ที่ไม่ประเมิน</th>
                                        <th class="text-center" scope="col">ส่วนที่ไม่ประเมิน</th>
                                        <th class="text-center" scope="col">ปีการศึกษา</th>
                                        <th class="text-center" scope="col">ภาคการศึกษา</th>
                                        </tr>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {

                                            $sql = "SELECT * FROM `project` WHERE year = (SELECT year 
                                            FROM `defaultsystem` WHERE default_system_id = 1) 
                                            and term = (SELECT term FROM `defaultsystem` WHERE default_system_id = 1)";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->execute();
                                            $projects = $stmt->fetchAll();


                                            if (empty($projects)) {
                                                echo "<tr><td colspan='20' class='text-center'>No data available</td></tr>";
                                            } else {
                                                $index = 1;
                                                foreach ($projects as $project) {

                                                    $sql = "SELECT grade FROM `student` WHERE student_id = :student_id";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':student_id', $project['student_id1']);
                                                    $stmt->execute();
                                                    $studentGrade = $stmt->fetchColumn();
                                                    if (isset($studentGrade)) { continue; }

                                                    $teacherIdAll = array();
                                                    $teacherIdAll[0] = $project['teacher_id1'];

                                                    if (!empty($project['teacher_id2'])) {
                                                        $teacherIdAll[1] = $project['teacher_id2'];
                                                        $teacherIdAll[2] = $project['referee_id'];
                                                        $teacherIdAll[3] = $project['referee_id1'];
                                                        $teacherIdAll[4] = $project['referee_id2'];
                                                    } else {
                                                        $teacherIdAll[1] = $project['referee_id'];
                                                        $teacherIdAll[2] = $project['referee_id1'];
                                                        $teacherIdAll[3] = $project['referee_id2'];
                                                    }

                                                    $teacherNo = array();
                                                    $i = 0;
                                                    foreach ($teacherIdAll as $teacher_id) {
                                                        $j = giveStatusById($conn, $project['project_id'], $teacher_id);
                                                        if ($j  == 0) {
                                                            $teacherNo[$i] = $teacher_id;
                                                            $i++;
                                                        }
                                                    }

                                                    foreach ($teacherNo as $data) {

                                                        $teacherName = ($data) ? giveTeacherById($conn, $data) : null;
                                        ?>
                                                        <tr>
                                                            <th scope="row"><?php echo $index++; ?></th>
                                                            <td><?php echo $project['project_id']; ?></td>
                                                            <td><?php echo $project['project_nameTH']; ?></td>
                                                            <td><?php echo giveTeacherPositionById($teacherName['position']) . $teacherName['firstname']; ?></td>
                                                            <td> <?php echo giveStatusNameById($conn, $project['project_id'], $data) ?> </td>
                                                            <td><?php echo ($project['year']); ?></td>
                                                            <td><?php echo ($project['term']); ?></td>
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
                            <?php
                            if (!empty($projects)) { ?>
                                <form action="./PDF_Result/PDF_ResultTeacherAssessment.php" method="post" target="_blank">
                                    <input type="hidden" name="data" value='<?php echo json_encode($projects); ?>'>
                                    <input type="hidden" name="select" value='<?php echo json_encode($selected); ?>'>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-info text-white mt-2">ดาว์โหลดเป็นไฟล์ PDF</button>
                                    </div>
                                </form>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

</body>

</html>