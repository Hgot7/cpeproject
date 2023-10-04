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
		case "รองศาสตราจารย์":
			return $Position = "รศ.";
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
		case "ดร.":
			return $Position = "ดร.";
			break;
		default:
			return $Position = $Position;
	}
}



$selected = json_decode($_POST['select']); // แปลง JSON เป็น object

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
$pdf->Cell(190, 10, "รายงานสรุปสถานะโครงงานของนักศึกษาจะแสดงจำนวนครั้งที่ส่งความคืบหน้าของโครงงานและสอบเสร็จสิ้น", 1, 1, 'C');
$pdf->Cell(190, 10, $selected, 1, 1, 'C');

$pdf->setFont(PROMPT_REGULAR, '', 18, '', true);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(15, 10, "ลำดับที่", 1, 0, 'C');
$pdf->Cell(49, 10, "ชื่อโครงงาน", 1, 0, 'C');
$pdf->MultiCell(40, 10, "อาจารย์ที่ปรึกษาหลัก", 1, 'C', 1, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(40, 10, "อาจารย์ที่ปรึกษาร่วม", 1, 'C', 1, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->Cell(46, 10, "สถานะโครงงาน", 1, 1, 'C');
// $pdf->Cell(79, 40, mb_substr($data['project_nameTH'], 0, 20), 1, 0, 'C');
// $pdf->Cell(26, 40, iconv('utf-8', 'cp874', mb_substr($data['project_nameTH'], 0, 20)), 1, 0, 'C');
// $pdf->Cell(79, 40, $data['project_nameTH'], 1, 0, 'C');
$pdf->setFont(PROMPT_REGULAR, '', 16, '', true);

if (isset($_POST['data'])) {
	$datas = json_decode($_POST['data']);
	$teacher2 = '';
	$index = 0;
	foreach ($datas as $project) {
		$index++;
		//อาจารย์ที่ปรึกษาหลัก
		$teacher1 = ($project->teacher_id1) ? giveTeacherById($conn, $project->teacher_id1) : null;
		//อาจารย์ที่ปรึกษาร่วม
		if (isset($project->teacher_id2)) {
			// ถ้า $project['teacher_id2'] ถูกกำหนด
			$teacher2 = giveTeacherById($conn, $project->teacher_id2);
		} else {
			// ถ้า $project['teacher_id2'] ไม่ถูกกำหนด
			$teacher2 = '';
		}

		$sql = "SELECT count(distinct file_chapter) FROM `file` WHERE project_id = :project_id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':project_id', $project->project_id);
		$stmt->execute();
		$chapter = $stmt->fetchColumn(); // Use fetchColumn to get the count directly

		$sql = "SELECT grade FROM `student` WHERE student_id = :student_id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':student_id', $project->student_id1);
		$stmt->execute();
		$studentGrade = $stmt->fetchColumn();


		$pdf->SetFillColor(255, 255, 255);
		if (mb_strlen($project->project_nameTH, 'utf-8') > 20) {               //เกินความยาว

			$pdf->Cell(15, 40, iconv('utf-8', 'cp874', $index), 1, 0, 'C');
			$pdf->MultiCell(49, 40, $project->project_nameTH, 1, 'C', 1, 0, '', '', true, 0, false, true, 40, 'M');
			$pdf->Cell(40, 40, giveTeacherPositionById($teacher1['position']) . $teacher1['firstname'], 1, 0, 'C');
			if (empty($teacher2)) {
				$pdf->Cell(40, 40, ' ', 1, 0, 'C');
			} else $pdf->Cell(40, 40, giveTeacherPositionById($teacher2['position']) . $teacher2['firstname'], 1, 0, 'C');
			
			if (isset($studentGrade)) {
				$pdf->MultiCell(46, 40, 'สอบเสร็จสิ้น', 1, 'C', 1, 1, '', '', true, 0, false, true, 40, 'M');
			} elseif (empty($chapter)) {
				$pdf->MultiCell(46, 40, 'ส่งเอกสารความคืบหน้า 0' . ' จาก 14', 1, 'C', 1, 1, '', '', true, 0, false, true, 40, 'M');
			} else {
				$pdf->MultiCell(46, 40, 'ส่งเอกสารความคืบหน้า ' . $chapter . ' จาก 14', 1, 'C', 1, 1, '', '', true, 0, false, true, 40, 'M');
			}
		} else {

			$pdf->Cell(15, 20, iconv('utf-8', 'cp874', $index), 1, 0, 'C');
			$pdf->Cell(49, 20, $project->project_nameTH, 1, 0, 'C');
			$pdf->Cell(40, 20, giveTeacherPositionById($teacher1['position']) . $teacher1['firstname'], 1, 0, 'C');
			if (empty($teacher2)) {
				$pdf->Cell(40, 20, ' ', 1, 0, 'C');
			} else $pdf->Cell(40, 20, giveTeacherPositionById($teacher2['position']) . $teacher2['firstname'], 1, 0, 'C');

			if (isset($studentGrade)) {
				$pdf->MultiCell(46, 20, 'สอบเสร็จสิ้น', 1, 'C', 1, 1, '', '', true, 0, false, true, 20, 'M');
			} elseif (empty($chapter)) {
				$pdf->MultiCell(46, 20, 'ส่งเอกสารความคืบหน้า 0' . ' จาก 14', 1, 'C', 1, 1, '', '', true, 0, false, true, 20, 'M');
			} else $pdf->MultiCell(46, 20, 'ส่งเอกสารความคืบหน้า ' . $chapter . ' จาก 14', 1, 'C', 1, 1, '', '', true, 0, false, true, 20, 'M');			

			
		}

		// $pdf->Cell(79, 40, $projectName, 1, 0, 'C');
	}
}
$pdf->setFont(PROMPT_BOLD, '', 18, '', true);
$pdf->Cell(190, 10, "รวม Total " . $index . ' โครงงาน', 1, 0, 'C');

$pdf->Output();
