<?php
session_start();
require_once "../connect.php";
function calculateGrade($score, $conn)
{
    $score = round($score);
    $stmt = $conn->prepare("SELECT grade 
        FROM `evaluationcriteria` 
        WHERE :score >= evaluationcriteria_start AND :score <= evaluationcriteria_end");
    $stmt->bindParam(':score', $score, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchColumn();

    if (empty($data)) {
        return "F";
    } else {
        return $data;
    }
}

if (isset($_POST['topic_datas'])) {
    $topicDataJSON = $_POST['topic_datas'];
    $topic_datas = json_decode($topicDataJSON, true);
    $project_id = $_POST['project_id'];
    $oldScore = array();
    try {
        // เชื่อมต่อฐานข้อมูล PDO
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // เปิดโหมด error ของ PDO เพื่อรับข้อผิดพลาดแบบ exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ดึงข้อมูลรหัสนักศึกษาจากฐานข้อมูล
        $stmt = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id ");
        $stmt->bindParam(':project_id', $project_id, PDO::PARAM_STR);
        $stmt->execute();
        $projectData = $stmt->fetch();

        $studentIds = array();
        for ($i = 1; $i <= 3; $i++) {
            $studentIdKey = 'student_id' . $i;
            if (isset($projectData[$studentIdKey])) {
                $studentIds[] = $projectData[$studentIdKey];
            }
        }

        $i = 0; // กำหนดค่าเริ่มต้นให้ $i เป็น 0

        // ตรวจสอบว่ามีข้อมูลที่จำเป็นครบถ้วนหรือไม่
        foreach ($topic_datas as $index =>  $topic_data) {
            foreach ($studentIds as $studentId) {
                $projectpoint_key = 'projectpoint' . ($index + 1) . '_' . $studentId;
                // ตรวจสอบว่า $projectpoint_key มีอยู่ใน $_POST
                if (isset($_POST[$projectpoint_key])) {
                    $i++;
                }
            }
        }

        if (count($topic_datas) * count($studentIds) != $i) {
            $_SESSION['error'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
            header("location: ./pointTest.php?id=$project_id");
            exit; // ออกจากการทำงานหลังจากส่ง header
        }

        // วนลูปผ่านรายการที่ได้รับและทำการประมวลผล
        foreach ($topic_datas as $index =>  $topic_data) {
            foreach ($studentIds as $i => $studentId) {
                $projectpoint_key = 'projectpoint' . ($index + 1) . '_' . $studentId;

                // ตรวจสอบว่า $projectpoint_key มีอยู่ใน $_POST
                if (isset($_POST[$projectpoint_key])) {
                    $projectpoint = $_POST[$projectpoint_key];
                    $topic_id = $topic_data['topic_id'];
                    $topic_section_id = $topic_data['topic_section_id'];

                    // ตรวจสอบว่ามีการประเมินในฐานข้อมูลหรือไม่
                    $stmt = $conn->prepare("SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id AND student_id = :student_id");
                    $stmt->bindParam(':topic_id', $topic_id);
                    $stmt->bindParam(':referee_id', $_SESSION['teacher_id']);
                    $stmt->bindParam(':project_id', $project_id);
                    $stmt->bindParam(':student_id', $studentId);
                    $stmt->execute();
                    $datas = $stmt->fetch();

                    if (!empty($datas)) {
                        // หากมีการประเมินอยู่แล้วให้เก็บคะแนนเก่าในอาร์เรย์ $oldScore
                        $oldScore[$studentId] = isset($oldScore[$studentId]) ? (float)$oldScore[$studentId] : 0;
                        $oldScore[$studentId] += (float)$datas['score'];

                        // ลบคะแนนเก่าของนักศึกษาจากคะแนนรวม
                        // ลบค่าเก่าของนักศึกษาจากคะแนนรวม
// $sql = "UPDATE test SET score = (score - :oldScore) WHERE student_id = :student_id";
// $stmt = $conn->prepare($sql);
// $stmt->bindParam(':oldScore', $oldScore[$i]);
// $stmt->bindParam(':student_id', $studentId);
// $stmt->execute();

                        // ถ้ามีการประเมินอยู่แล้วให้อัปเดตคะแนน
                        $stmt = $conn->prepare("UPDATE `assessment`
                            SET score = :score 
                            WHERE topic_id = :topic_id 
                            AND referee_id = :referee_id 
                            AND project_id = :project_id
                            AND student_id = :student_id");
                        $stmt->bindParam(':topic_id', $topic_id);
                        $stmt->bindParam(':referee_id', $_SESSION['teacher_id']);
                        $stmt->bindParam(':project_id', $project_id);
                        $stmt->bindParam(':student_id', $studentId);
                        $stmt->bindParam(':score', $projectpoint, PDO::PARAM_INT);
                        $stmt->execute();
                        $newInsert = 0;
                    } else {
                        // ถ้ายังไม่มีการประเมิน ให้เพิ่มข้อมูลการประเมิน
                        $stmt = $conn->prepare("INSERT INTO `assessment` (topic_id, referee_id, project_id, student_id, score) VALUES (:topic_id, :referee_id, :project_id, :student_id, :score)");
                        $stmt->bindParam(':topic_id', $topic_id);
                        $stmt->bindParam(':referee_id', $_SESSION['teacher_id']);
                        $stmt->bindParam(':project_id', $project_id);
                        $stmt->bindParam(':student_id', $studentId);
                        $stmt->bindParam(':score', $projectpoint, PDO::PARAM_INT);
                        $stmt->execute();
                        $newInsert = 1;
                    }
                }
            }
        }

        // คำนวณและบันทึกผลรวมค่าเก่าของนักศึกษา
        // คำนวณและบันทึกผลรวมค่าเก่าของนักศึกษา
foreach ($studentIds as $i => $studentId) {
    $stmt = $conn->prepare("SELECT a.* 
        FROM `assessment` a
        INNER JOIN `topic` t ON a.topic_id = t.topic_id
        WHERE t.topic_section_id = :topic_section_id
        AND a.referee_id = :referee_id
        AND a.project_id = :project_id
        AND a.student_id = :student_id;
    ");
    $stmt->bindParam(':topic_section_id', $topic_section_id);
    $stmt->bindParam(':referee_id', $_SESSION['teacher_id']);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->bindParam(':student_id', $studentId);
    $stmt->execute();
    $assessmentdatas = $stmt->fetchAll();

    // วนลูปทำการประมวลผลคะแนน 
    $score = 0;
    foreach ($assessmentdatas as $data) {
        $score += $data['score'];
    }

    $stmt = $conn->prepare("SELECT * FROM `topicsection` WHERE topic_section_id = :topic_section_id");
    $stmt->bindParam(':topic_section_id', $topic_section_id);
    $stmt->execute();
    $topicSectiondatas = $stmt->fetch();

    $stmt = $conn->prepare("SELECT * FROM `project` WHERE project_id = :project_id");
    $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();
    $projectdatas = $stmt->fetch();

    if ($topicSectiondatas['topic_section_level'] == 0) {
        $score /= 3;
        $oldScore[$studentId] /= 3; // แก้ไขบรรทัดนี้
    } elseif ($topicSectiondatas['topic_section_level'] == 1) {
        if (isset($projectdatas['teacher_id2'])) {
            $score /= 2;
            $oldScore[$studentId] /= 2; // แก้ไขบรรทัดนี้
        } elseif (empty($projectdatas['teacher_id2'])) {
            $score /= 1;
            $oldScore[$studentId] /= 1; // แก้ไขบรรทัดนี้
        }
    } elseif ($topicSectiondatas['topic_section_level'] == 2) {
        if (isset($projectdatas['teacher_id2'])) {
            $score /= 5;
            $oldScore[$studentId] /= 5; // แก้ไขบรรทัดนี้
        } elseif (empty($projectdatas['teacher_id2'])) {
            $score /= 4;
            $oldScore[$studentId] /= 4; // แก้ไขบรรทัดนี้
        }
    }

    $weight = count($topic_datas) * 5;
    $weight = intval($topicSectiondatas['topic_section_weight']) / $weight;
    $score *= $weight;
    $oldScore[$studentId] *= $weight; // แก้ไขบรรทัดนี้

    if ($newInsert == 0) {
        // ลบค่าเก่าของนักศึกษาจากคะแนนรวม
        $sql = "UPDATE `student` SET score = (score - :oldScore) WHERE student_id = :student_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':oldScore', $oldScore[$studentId]); // แก้ไขบรรทัดนี้
        $stmt->bindParam(':student_id', $studentId);
        $stmt->execute();
    }

    // อ่านคะแนนที่บันทึกอยู่ในฐานข้อมูล
    $stmt = $conn->prepare("SELECT `score` FROM student WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $studentId);
    $stmt->execute();
    $scoreResult = $stmt->fetchColumn();

    // หากไม่มีคะแนนในฐานข้อมูลให้ให้ค่าเป็น 0
    $studentscore = ($scoreResult !== false) ? (float)$scoreResult : 0;

    // เพิ่มคะแนนเข้าไปใน $studentscore
    $studentscore += $score;


    $teacherProject = array();
        if(empty($projectdatas['teacher_id2'])){
            $teacherProject[0] = $projectdatas['teacher_id1'];
            $teacherProject[1] = $projectdatas['referee_id'];
            $teacherProject[2] = $projectdatas['referee_id1'];
            $teacherProject[3] = $projectdatas['referee_id2'];
        }elseif(isset($projectdatas['teacher_id2'])){
            $teacherProject[0] = $projectdatas['teacher_id1'];
            $teacherProject[1] = $projectdatas['teacher_id2'];
            $teacherProject[2] = $projectdatas['referee_id'];
            $teacherProject[3] = $projectdatas['referee_id1'];
            $teacherProject[4] = $projectdatas['referee_id2'];
        }
        $testTeacher = 0;
        foreach ($teacherProject as $index => $teacherId) {
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

            $stmt = $conn->prepare("SELECT COUNT(DISTINCT t.topic_section_id) AS unique_topic_section_count
            FROM `assessment` a
            INNER JOIN `topic` t ON a.topic_id = t.topic_id
            WHERE  a.project_id = :project_id
            AND referee_id = :referee_id");
            $stmt->bindParam(':project_id', $project_id);
            $stmt->bindParam(':referee_id', $teacherId);
            $stmt->execute();
            $row = $stmt->fetch();
       
            if($row == $topicSectionData){
                $testTeacher++;
            }
        }




    // ตรวจสอบว่ามีข้อมูลของนักเรียนนี้อยู่ในฐานข้อมูลหรือไม่
   
        if (count($teacherProject) == $testTeacher) {
            $grade = calculateGrade($studentscore, $conn);
        }
        if(empty($grade)){$grade = '';}
        // ถ้ามีข้อมูลอยู่แล้ว ให้อัปเดตคะแนน
        $stmt = $conn->prepare("UPDATE `student` SET score = :score  ,grade =  :grade WHERE student_id = :student_id");
        $stmt->bindParam(':score', $studentscore);
        $stmt->bindParam(':grade', $grade);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->execute();
   
}


        // หลังจากปรับปรุงข้อมูลแล้วและจัดการกับข้อผิดพลาด ให้ใส่ข้อความ "บันทึกข้อมูลเรียบร้อยแล้ว" ลงในตัวแปร session
        $_SESSION['success'] = "ประเมินสำเร็จ";
    } catch (PDOException $e) {
        // ถ้าเกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล
        $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }

    // หลังจากปรับปรุงข้อมูลแล้วและจัดการกับข้อผิดพลาด ให้ใช้ header ไปยังหน้า pointTest.php
    header("location: ./pointTest.php?id=$project_id");
    exit; // ออกจากการทำงานหลังจากส่ง header
}
?>
