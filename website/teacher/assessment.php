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
    $oldScore = 0;
    try {
        // เชื่อมต่อฐานข้อมูล PDO
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // เปิดโหมด error ของ PDO เพื่อรับข้อผิดพลาดแบบ exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $i = 0;
        foreach ($topic_datas as $index => $topic_data) {
            $projectpoint_key = 'projectpoint' . ($index + 1);
            if (isset($_POST[$projectpoint_key])) {
                $i++;
            }
        }
        // ตรวจสอบว่ามีข้อมูลที่จำเป็นครบถ้วนหรือไม่
        if (count($topic_datas) != $i) {
            $_SESSION['error'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
            header("location: ./pointTest.php?id=$project_id");
            exit; // ออกจากการทำงานหลังจากส่ง header
        }

        // วนลูปผ่านรายการที่ได้รับและทำการประมวลผล
        foreach ($topic_datas as $index => $topic_data) {
            $projectpoint_key = 'projectpoint' . ($index + 1);

            // ตรวจสอบว่า $projectpoint_key มีอยู่ใน $_POST
            if (isset($_POST[$projectpoint_key])) {
                $projectpoint = $_POST[$projectpoint_key];
                $topic_id = $topic_data['topic_id'];
                $topic_section_id = $topic_data['topic_section_id'];

                // ตรวจสอบว่ามีการประเมินในฐานข้อมูลหรือไม่
                $stmt = $conn->prepare("SELECT * FROM `assessment` WHERE topic_id = :topic_id AND referee_id = :referee_id AND project_id = :project_id");
                $stmt->bindParam(':topic_id', $topic_id);
                $stmt->bindParam(':referee_id', $_SESSION['teacher_id']);
                $stmt->bindParam(':project_id', $project_id);
                $stmt->execute();
                $datas = $stmt->fetch();

                if (!empty($datas)) {
                    $oldScore += (float)$datas['score'];

                    // ถ้ามีการประเมินอยู่แล้วให้อัปเดตคะแนน
                    $stmt = $conn->prepare("UPDATE `assessment` 
                      SET score = :score 
                      WHERE topic_id = :topic_id 
                      AND referee_id = :referee_id 
                      AND project_id = :project_id");

                    $stmt->bindParam(':score', $projectpoint, PDO::PARAM_INT);
                    $stmt->bindParam(':topic_id', $topic_id);
                    $stmt->bindParam(':referee_id', $_SESSION['teacher_id']);
                    $stmt->bindParam(':project_id', $project_id);
                    $stmt->execute();

                    $newInsert = 0;
                } else {
                    // ถ้ายังไม่มีการประเมิน ให้เพิ่มข้อมูลการประเมิน
                    $stmt = $conn->prepare("INSERT INTO `assessment` (topic_id, referee_id, project_id, score) VALUES (:topic_id, :referee_id, :project_id, :score)");
                    $stmt->bindParam(':topic_id', $topic_id);
                    $stmt->bindParam(':referee_id', $_SESSION['teacher_id']);
                    $stmt->bindParam(':project_id', $project_id);
                    $stmt->bindParam(':score', $projectpoint, PDO::PARAM_INT);
                    $stmt->execute();

                    $newInsert = 1;
                }
            }
        }
        // if ($newInsert == 1) {
        $stmt = $conn->prepare("SELECT a.* 
        FROM `assessment` a
        INNER JOIN `topic` t ON a.topic_id = t.topic_id
        WHERE t.topic_section_id = :topic_section_id
        AND a.referee_id = :referee_id
        AND a.project_id = :project_id;
        ");
        // $stmt->bindParam(':topic_id', $topic_id);
        $stmt->bindParam(':topic_section_id', $topic_section_id);
        $stmt->bindParam(':referee_id', $_SESSION['teacher_id']);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        $assessmentdatas = $stmt->fetchAll();
        // วนลูปทำการประมวลผลคะแนน 
        $score = 0;
        foreach ($assessmentdatas as $data) {
            $score = $score + $data['score'];
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
            $score = $score / 3;
            $oldScore = $oldScore / 3;
        } elseif ($topicSectiondatas['topic_section_level'] == 1) {
            if (isset($projectdatas['teacher_id2'])) {
                $score = $score / 2;
                $oldScore = $oldScore / 2;
            } elseif (empty($projectdatas['teacher_id2'])) {
                $score = $score / 1;
                $oldScore = $oldScore / 1;
            }
        } elseif ($topicSectiondatas['topic_section_level'] == 2) {
            if (isset($projectdatas['teacher_id2'])) {
                $score = $score / 5;
                $oldScore = $oldScore / 5;
            } elseif (empty($projectdatas['teacher_id2'])) {
                $score = $score / 4;
                $oldScore = $oldScore / 4;
            }
        }

        $weight = count($topic_datas) * 5;
        $weight = intval($topicSectiondatas['topic_section_weight']) / $weight;
        $score = $score * $weight;
        $oldScore = $oldScore * $weight;
        $STDcount = 0;
        for ($i = 0; $i < 3; $i++) {
            $student_idkey = 'student_id' . ($i + 1);
            if (isset($projectdatas[$student_idkey])) {
                $STDcount++;
            };
        }


        if ($newInsert == 0) {
            $sql = "UPDATE `student` SET score = (score - :oldScore) WHERE student_id = :student_id";
            $stmt = $conn->prepare($sql);

            // ทำการผูกพารามิเตอร์ที่ใช้ใน SQL นอกลูป
            $stmt->bindParam(':oldScore', $oldScore);

            for ($i = 1; $i <= $STDcount; $i++) {
                $student_idkey = 'student_id' . $i;
                $studentId = $projectdatas[$student_idkey]; // Assuming $student_idkey contains the key for the student_id

                // ทำการผูกพารามิเตอร์ในลูป
                $stmt->bindParam(':student_id', $studentId);
                $stmt->execute();
            }
        }


        $stmt = $conn->prepare("SELECT score FROM `student` WHERE student_id = :student_id");

        for ($i = 0; $i < $STDcount; $i++) {
            $student_idkey = 'student_id' . ($i + 1);
            $stmt->bindParam(':student_id', $projectdatas[$student_idkey]);
            $stmt->execute();
            $scoreResult = $stmt->fetchColumn();

            // หากไม่มีคะแนนในฐานข้อมูลให้ให้ค่าเป็น 0
            $studentscore[$i] = $scoreResult !== false ? (float)$scoreResult : 0;

            // เพิ่มคะแนนเข้าไปใน $studentscore[$i]
            $studentscore[$i] += $score;
        }


        $teacherProject = array();
        if (empty($projectdatas['teacher_id2'])) {
            $teacherProject[0] = $projectdatas['teacher_id1'];
            $teacherProject[1] = $projectdatas['referee_id'];
            $teacherProject[2] = $projectdatas['referee_id1'];
            $teacherProject[3] = $projectdatas['referee_id2'];
        } elseif (isset($projectdatas['teacher_id2'])) {
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

            if ($row == $topicSectionData) {
                $testTeacher++;
            }
        }




        for ($i = 1; $i <= $STDcount; $i++) {
            $student_idkey = 'student_id' . $i;


            if (count($teacherProject) == $testTeacher) {
                $grade = calculateGrade($studentscore[$i - 1], $conn);
            }
            if (empty($grade)) {
                $grade = null;
            }
            // ถ้ามีข้อมูลอยู่แล้ว ให้อัปเดตคะแนน
            $stmt = $conn->prepare("UPDATE `student` SET score = :score  ,grade =  :grade WHERE student_id = :student_id");
            $stmt->bindParam(':score', $studentscore[$i - 1]);
            $stmt->bindParam(':student_id', $projectdatas[$student_idkey]);
            $stmt->bindParam(':grade', $grade);
            $stmt->execute();
        }




        // }



        // หลังจากปรับปรุงข้อมูลและจัดการกับข้อผิดพลาด ให้ใส่ข้อความ "บันทึกข้อมูลเรียบร้อยแล้ว" ลงในตัวแปร session
        $_SESSION['success'] = "ประเมินสำเร็จ";
    } catch (PDOException $e) {
        // ถ้าเกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล
        $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }

    // หลังจากปรับปรุงข้อมูลและจัดการกับข้อผิดพลาด ให้ใช้ header ไปยังหน้า pointTest.php
    header("location: ./pointTest.php?id=$project_id");
    exit; // ออกจากการทำงานหลังจากส่ง header
}
