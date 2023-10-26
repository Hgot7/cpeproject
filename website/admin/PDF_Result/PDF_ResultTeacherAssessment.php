<?php

require_once("../../tcpdf.php");
require_once "../../connect.php";


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


// create new PDF document
// $pdf = new TCPDF('p', 'mm', 'A4');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->AddPage();

define('PROMPT_REGULAR', TCPDF_FONTS::addTTFfont('../.././fonts/THSarabunNew.ttf', 'TrueTypeUnicode'));
define('PROMPT_BOLD', TCPDF_FONTS::addTTFfont('../.././fonts/THSarabunNew Bold.ttf', 'TrueTypeUnicode'));
$pdf->setFont(PROMPT_BOLD, '', 18, '', true);


// $pdf->setPrintHeader(false);
// $pdf->setPrintFooter(false);


// $pdf->Cell(190,40,iconv('utf-8','cp874','การรายงานสรุปให้เป็น PDF'),0,1,'C');

$sql = "SELECT * FROM `defaultsystem`";
$stmt = $conn->prepare($sql);
$stmt->execute();
$defaultsystem = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($defaultsystem as $row) {
    $pdf->Cell(190, 10, "รายงานสรุปอาจารย์ที่ไม่ประเมินผลการสอบในปีการศึกษาที่ " . $row['year'] . " ภาคการศึกษาที่ " . $row['term'], 1, 1, 'C');
}

$pdf->setFont(PROMPT_REGULAR, '', 18, '', true);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(15, 20, "ลำดับที่", 1, 0, 'C');
$pdf->Cell(25, 20, "รหัสโครงงาน", 1, 0, 'C');
$pdf->MultiCell(45, 20, "ชื่อโครงงาน", 1, 'C', 1, 0, '', '', true, 0, false, true, 20, 'M');
$pdf->MultiCell(55, 20, "ชื่ออาจารย์ที่ยังประเมินไม่ครบ", 1, 'C', 1, 0, '', '', true, 0, false, true, 20, 'M');
$pdf->Cell(50, 20, "ส่วนที่ยังไม่ได้ประเมิน", 1, 1, 'C');
// $pdf->Cell(79, 40, mb_substr($data['project_nameTH'], 0, 20), 1, 0, 'C');
// $pdf->Cell(26, 40, iconv('utf-8', 'cp874', mb_substr($data['project_nameTH'], 0, 20)), 1, 0, 'C');
// $pdf->Cell(79, 40, $data['project_nameTH'], 1, 0, 'C');
$pdf->setFont(PROMPT_REGULAR, '', 16, '', true);

if (isset($_POST['data'])) {
    $datas = json_decode($_POST['data']);

    $index = 0;
    foreach ($datas as $project) {
        $sql = "SELECT grade FROM `student` WHERE student_id = :student_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':student_id', $project->student_id1);
        $stmt->execute();
        $studentGrade = $stmt->fetchColumn();
        if (isset($studentGrade)) { continue; }
        $teacherIdAll = array();
        $teacherIdAll[0] = $project->teacher_id1;

        if (!empty($project->teacher_id2)) {
            $teacherIdAll[1] = $project->teacher_id2;
            $teacherIdAll[2] = $project->referee_id;
            $teacherIdAll[3] = $project->referee_id1;
            $teacherIdAll[4] = $project->referee_id2;
        } else{
            $teacherIdAll[1] = $project->referee_id;
            $teacherIdAll[2] = $project->referee_id1;
            $teacherIdAll[3] = $project->referee_id2;
        }

        $teacherNo = array();
        $i = 0;
        foreach ($teacherIdAll as $teacher_id) {
            $j = giveStatusById($conn, $project->project_id, $teacher_id);
            if ($j  == 0) {
                $teacherNo[$i] = $teacher_id;
                $i++;
            }
        }

        foreach ($teacherNo as $data) {
            $index++;
            $teacherName = ($data) ? giveTeacherById($conn, $data) : null;
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(15, 40, iconv('utf-8', 'cp874', $index), 1, 0, 'C');
            $pdf->MultiCell(25, 40, $project->project_id, 1, 'C', 1, 0, '', '', true, 0, false, true, 40, 'M');
            $pdf->MultiCell(45, 40, $project->project_nameTH, 1, 'C', 1, 0, '', '', true, 0, false, true, 40, 'M');
            $pdf->MultiCell(55, 40, giveTeacherPositionById($teacherName['position']) . $teacherName['firstname'], 1, 'C', 1, 0, '', '', true, 0, false, true, 40, 'M');
            $pdf->MultiCell(50, 40, giveStatusNameById($conn, $project->project_id, $data), 1, 'C', 1, 1, '', '', true, 0, false, true, 40, 'M');
        }

        // $pdf->Cell(79, 40, $projectName, 1, 0, 'C');
    }
}
$pdf->setFont(PROMPT_BOLD, '', 18, '', true);
$pdf->Cell(190, 10, "รวม Total " . $index . ' ท่าน', 1, 0, 'C');

$pdf->Output();
