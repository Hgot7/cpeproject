<?php

session_start();
require_once "../connect.php";
$project_id = $_GET["id"];
if (!isset($_SESSION['teacher_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ';
    header('Location: ../index.php');
    exit();
}

function giveProjectNameTH($conn, $project_id)
{
    $sql = "SELECT project_nameTH FROM `project` WHERE project_id = :project_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();
    return $stmt->fetchColumn(); // เปลี่ยนเป็น fetchColumn() เพื่อเรียกคอลัมน์เดียวที่กลับมา
}




function giveFileById($conn, $project_id, $file_chapter)
{
    $sql = "SELECT file_path FROM `file` WHERE (project_id = :project_id and (file_chapter = :file_chapter and file_status = 1))";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->bindParam(':file_chapter', $file_chapter);
    $stmt->execute();

    $result = $stmt->fetch(); // รับผลลัพธ์จากคำสั่ง SQL

    if ($result !== false) { // ตรวจสอบว่ามีข้อมูลหรือไม่
        return $result['file_path']; // ส่งกลับข้อมูลไฟล์
    }

    return null; // หรือส่งค่าอื่นที่เหมาะสมตามสถานการณ์
}

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


    <title>Teacher</title>

</head>

<body>
    <!-- -------------------------------------------------Header------------------------------------------------- -->
    <div class="HeaderBg shadow">
        <div class="container">
            <navbar_teacher-component></navbar_teacher-component>
        </div>
    </div>

    <!-- -------------------------------------------------Material------------------------------------------------- -->
    <div class="container-fluid justify-content-around">
        <div class="row">
            <?php include("sidebarTeacherComponent.php"); ?>

            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-3 py-3">
                <h1 class="fs-2 pb-2" style="font-family: 'IBM Plex Sans Thai', sans-serif;">รายการประเมินโครงงาน</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb fs-5 mt-2 ms-3">
                        <li class="breadcrumb-item"><a href="./Teacherpage.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="./viewpointTest.php">ให้คะแนนสอบวิชาโครงงาน</a></li>
                        <li class="breadcrumb-item active" aria-current="page">รายการประเมินโครงงาน</li>
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
                <div class="card shadow-sm">
                    <h4 class="card-header">โครงงาน : <?php echo giveProjectNameTH($conn, $project_id); ?></h4>
                    <div class="card-body">
                        <div class="col-md-5">
                            <div class="list-group" style="position: absolute; width: 400px;" id="show-list"></div>
                        </div>



                        <div class="row g-3 mb-4 d-flex align-items-center text-center">
                            <div class="accordion" id="ProjectDocument">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="panelsStayOpen-headingDocument">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseDocument" aria-expanded="false" aria-controls="panelsStayOpen-collapseDocument">
                                        รายละเอียดโครงงาน : <?php echo giveProjectNameTH($conn, $project_id); ?>
                                        </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapseDocument" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingDocument">
                                        <div class="accordion-body">
                                        <?php
                                         $sql = "SELECT * FROM `project` WHERE project_id = :project_id";
                                         $stmt = $conn->prepare($sql);
                                         $stmt->bindParam(':project_id', $project_id);
                                         $stmt->execute();
                                         $result = $stmt->fetch();

                                         $studentAll = array();
                                         if(isset($result['student_id3'])){
                                            $studentAll[0] = $result['student_id1'];
                                            $studentAll[1] = $result['student_id2'];
                                            $studentAll[2] = $result['student_id3'];
                                         }elseif(empty($result['student_id3'])){          
                                            $studentAll[0] = $result['student_id1'];
                                            $studentAll[1] = $result['student_id2'];
                                         }
                                         foreach ($studentAll as $i => $student) {
                                        ?>
                                        <div class="mb-3">
                                                <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                    <div class="col-md-6 text-start">
                                                    <strong> </h4><?php echo "สมาชิกโครงงาน " .($i+1)?></h4></strong>
                                                    </div>
                                                    <div class="col-md-6 text-start">
                                                    <?php echo giveStudentdentNameById($conn, $student); ?>
                                                    </div>
                                                    <div class="col-md-6 text-start">
                                                    <strong><?php echo "คะแนน"; ?></strong>
                                                    </div>
                                                    <?php 
                                                    $sql = "SELECT score FROM `student` WHERE student_id = :student_id";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->bindParam(':student_id', $student);
                                                    $stmt->execute();
                                                    $showScore = $stmt->fetch();
                                                    ?>
                                                    <div class="col-md-6 text-start">
                                                        <?php echo round($showScore['score']); ?>
                                                    </div>

                                                </div>
                                            </div>
                                            <?php } ?>

                                            <?php
                                            //เอกสารโปสเตอร์
                                            $fullDocument1 = giveFileById($conn, $project_id, 13);
                                            //เอกสารรูปเล่มฉบับเต็ม
                                            $fullDocument2 = giveFileById($conn, $project_id, 14);
                                            ?>
                                            <div class="mb-3">
                                                <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                    <div class="col-md-6 text-start">
                                                    <strong></h4><?php echo "เอกสารโปสเตอร์" ?></h4></strong>
                                                    </div>
                                                    <div class="col-md-6 text-start">
                                                        </h5><a href="<?php echo '.././student/fileUpload/' . $fullDocument1; ?>" target="_blank"><?php echo $fullDocument1; ?></a></h5>
                                                    </div>


                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <div class="row g-3 mb-4 d-flex align-items-center text-center border-bottom">
                                                    <div class="col-md-6 text-start">
                                                    <strong></h4><?php echo "เอกสารรูปเล่มฉบับเต็ม" ?></h4></strong>
                                                    </div>
                                                    <div class="col-md-6 text-start">
                                                   </h5><a href="<?php echo '.././student/fileUpload/' . $fullDocument2; ?>" target="_blank"><?php echo $fullDocument2; ?></a></h5>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




                        <!-- การเสนอผลงานภาคบรรยาย -->
                        <?php
$sql = "SELECT * FROM `project`
        WHERE project_id = :project_id 
        AND year = (SELECT year FROM `defaultsystem` WHERE default_system_id = :id) 
        AND term = (SELECT term FROM `defaultsystem` WHERE default_system_id = :id)";

$stmt = $conn->prepare($sql);
$defaultSystemId = 1;
$stmt->bindParam(':id', $defaultSystemId, PDO::PARAM_INT);
$stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
$stmt->execute();
$testCheck = $stmt->fetchAll();

$teacherCheck = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id AND (teacher_id1 = :teacher_id OR teacher_id2 = :teacher_id)");
$teacherCheck->bindParam(':project_id', $project_id, PDO::PARAM_STR);
$teacherCheck->bindParam(':teacher_id', $_SESSION['teacher_id'], PDO::PARAM_STR);
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
if (!empty($testCheck)) {
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
                        <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $indextopic_section; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $indextopic_section; ?>" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $indextopic_section; ?>">
                                <?php echo $data['topic_section']; ?> 
                                <?php 
                                $j = assessmentCheck($conn, $project_id,$data['topic_section_id']);
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
                        <div id="panelsStayOpen-collapse<?php echo $indextopic_section; ?>" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $indextopic_section; ?>">
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
                                    //หัวคะแนน
                                    $index = 0;
                                    foreach ($topic_datas as $topic_data) {
                                        $index++;

                                        $sql = "SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id";
                                        $assessmentpoint = $conn->prepare($sql);
                                        $assessmentpoint->bindParam(':topic_id', $topic_data['topic_id'], PDO::PARAM_STR);
                                        $assessmentpoint->bindParam(':referee_id', $_SESSION['teacher_id'], PDO::PARAM_STR);
                                        $assessmentpoint->bindParam(':project_id', $project_id, PDO::PARAM_STR); // ควรเพิ่ม PDO::PARAM_STR ที่ parameter นี้
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
                                    <div class="d-grid gap-2">
                                        <?php
                                        $topic_datas = json_encode($topic_datas);
                                        echo '<input type="hidden" name="topic_datas" value="' . htmlspecialchars($topic_datas, ENT_QUOTES, 'UTF-8') . '">';
                                        ?>
                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                        <button style="font-family: 'IBM Plex Sans Thai', sans-serif;" class="btn btn-success">บันทึก</button>
                                    </div>
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
                        <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $indextopic_section; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $indextopic_section; ?>" aria-expanded="false" aria-controls="panelsStayOpen-collapse<?php echo $indextopic_section; ?>">
                                <?php echo $data['topic_section']; ?> <?php 
                                $j = assessmentCheck($conn, $project_id,$data['topic_section_id']);
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
                        <div id="panelsStayOpen-collapse<?php echo $indextopic_section; ?>" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $indextopic_section; ?>">
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
                                        $stdName->bindParam(':project_id', $project_id, PDO::PARAM_STR);
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
                                        $assessmentpoint->bindParam(':referee_id', $_SESSION['teacher_id'], PDO::PARAM_STR);
                                        $assessmentpoint->bindParam(':project_id', $project_id, PDO::PARAM_STR);
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
                                                    echo '<input class="form-check-input" type="radio" name="projectpoint' . $index . '_'.$stdNames[$indexStdName].'" value="' . $i . '"';

                                                    // เช็คว่าคะแนนในปุ่มรีดิโอตรงกับคะแนนที่ถูกเลือกหรือไม่
                                                    if ($i == $selected_score) {
                                                        echo ' checked';
                                                    }

                                                    echo '>';
                                                    echo '<label class="form-check-label" for="projectpoint' . $index . '_'.$stdNames[$indexStdName].'">' . $i . '</label>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                                $indexStdName++;
                                                ?>

                                            </div>
                                        </div>

                                    <?php
                                    }}
                                    ?>
                                    <div class="d-grid gap-2">
                                        <?php
                                        $topic_datas = json_encode($topic_datas);
                                        echo '<input type="hidden" name="topic_datas" value="' . htmlspecialchars($topic_datas, ENT_QUOTES, 'UTF-8') . '">';
                                        ?>
                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                        <button style="font-family: 'IBM Plex Sans Thai', sans-serif;" class="btn btn-success">บันทึก</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }}
} else {
    echo '<p style="text-align:center; color:red; font-size:36px;"><b>***หมดเวลาการประเมินโครงงาน***</b></p>';
}
?>



            </main>
        </div>
    </div>
</body>

</html>