<?php

require_once("../../tcpdf.php");
require_once "../../connect.php";

$selected = json_decode($_POST['select']); // แปลง JSON เป็น object

//select groups
function giveGroupById($conn, $group_id)
{
	$sql = "SELECT * FROM `groups` WHERE group_id = :group_id";
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(':group_id', $group_id);
	$stmt->execute();
	return $stmt->fetch();
}


// create new PDF document
$pdf = new TCPDF('p', 'mm', 'A4', 'utf-8');
$pdf->AddPage();

define('PROMPT_REGULAR', TCPDF_FONTS::addTTFfont('../.././fonts/THSarabunNew.ttf', 'TrueTypeUnicode'));
define('PROMPT_BOLD', TCPDF_FONTS::addTTFfont('../.././fonts/THSarabunNew Bold.ttf', 'TrueTypeUnicode'));
$pdf->setFont(PROMPT_BOLD, '', 18, '', true);


// $pdf->Cell(190,10,iconv('utf-8','cp874','การรายงานสรุปให้เป็น PDF'),0,1,'C');
$pdf->Cell(190, 10, "รายงานสรุปเกรดวิชาโครงงานของนักศึกษาแต่ละภาคการศึกษา", 1, 1, 'C');
$pdf->Cell(190, 10, $selected, 1, 1, 'C');

$pdf->setFont(PROMPT_REGULAR, '', 18, '', true);
$pdf->Cell(15, 10, "ลำดับที่", 1, 0, 'C');
$pdf->Cell(30, 10, "รหัสนักศึกษา", 1, 0, 'C');
$pdf->Cell(25, 10, "ชื่อ", 1, 0, 'C');
$pdf->Cell(30, 10, "นามสกุล", 1, 0, 'C');
$pdf->Cell(23, 10, "ปีการศึกษา", 1, 0, 'C');
$pdf->Cell(25, 10, "ภาคการศึกษา", 1, 0, 'C');
$pdf->Cell(25, 10, "ชื่อกลุ่มเรียน", 1, 0, 'C');
$pdf->Cell(17, 10, "เกรด", 1, 1, 'C');

$pdf->setFont(PROMPT_REGULAR, '', 16, '', true);

if (isset($_POST['data'])) {
	$datas = json_decode($_POST['data']);
	$index = 0;

	// สร้างตัวแปรเพื่อเก็บจำนวนของแต่ละเกรด
	$countA = 0;
	$countBPlus = 0;
	$countB = 0;
	$countCPlus = 0;
	$countC = 0;
	$countDPlus = 0;
	$countD = 0;
	$countF = 0;
	$countW = 0;
	$countI = 0;

	// สร้างตัวแปรเพื่อเก็บจำนวนของแต่ละเกรดเฉลี่ย
	$totalScore = 0;
	$totalStudents = 0;

	$group_id = '';
	$grade = '';

	foreach ($datas as $data) {

		if (isset($data->group_id)) {
			$group_id = ($data->group_id) ? giveGroupById($conn, $data->group_id) : null;
		} else {
			$group_id = '';
		}
		if (isset($data->grade)) {
			$grade = $data->grade;
		} else {
			$grade = '';
		}
		$index++;
		$pdf->Cell(15, 10, iconv('utf-8', 'cp874', $index), 1, 0, 'C');
		$pdf->Cell(30, 10, iconv('utf-8', 'cp874', $data->student_id), 1, 0, 'C');
		$pdf->Cell(25, 10, $data->firstname, 1, 0, 'C');
		$pdf->Cell(30, 10, $data->lastname, 1, 0, 'C');
		$pdf->Cell(23, 10, iconv('utf-8', 'cp874', $data->year), 1, 0, 'C');
		$pdf->Cell(25, 10, iconv('utf-8', 'cp874', $data->term), 1, 0, 'C');
		$pdf->Cell(25, 10, $group_id ? $group_id['group_name'] : '', 1, 0, 'C');
		$pdf->Cell(17, 10, iconv('utf-8', 'cp874', $grade), 1, 1, 'C');
		// ตรวจสอบค่าเกรดและเพิ่มตัวแปรนับที่เหมาะสม
		if ($grade === 'A') {
			$countA++;
		} elseif ($grade === 'B+') {
			$countBPlus++;
		} elseif ($grade === 'B') {
			$countB++;
		} elseif ($grade === 'C+') {
			$countCPlus++;
		} elseif ($grade === 'C') {
			$countC++;
		} elseif ($grade === 'D+') {
			$countDPlus++;
		} elseif ($grade === 'D') {
			$countD++;
		} elseif ($grade === 'F') {
			$countF++;
		} elseif ($grade === 'W') {
			$countW++;
		} elseif ($grade === 'I') {
			$countI++;
		}
	}
	//คำนวณเกรด	
	foreach ($datas as $data) {
		if (isset($data->grade)) {
			$grade = $data->grade;
		} else {
			$grade = '';
		}
		if ($grade === 'A') {
			$totalScore += 4.0;
		} elseif ($grade === 'B+') {
			$totalScore += 3.5;
		} elseif ($grade === 'B') {
			$totalScore += 3.0;
		} elseif ($grade === 'C+') {
			$totalScore += 2.5;
		} elseif ($grade === 'C') {
			$totalScore += 2.0;
		} elseif ($grade === 'D+') {
			$totalScore += 1.5;
		} elseif ($grade === 'D') {
			$totalScore += 1.0;
		} elseif ($grade === 'F') {
			// ถ้าเกรดเป็น F จะไม่นับคะแนน
		} elseif ($grade === 'W') {
			continue;
		} elseif ($grade === 'I') {
			continue;
		} elseif ($grade === '') {
			continue;
		}
		$totalStudents++;
	}
	if($totalScore){
	$averageGrade = $totalScore / $totalStudents;
	$averageGrade = number_format($averageGrade, 2); // แสดงทศนิยม 2 ตำแหน่ง
	}else $averageGrade = 0;

}

$pdf->Cell(100, 10, "รวมเกรด A", 1, 0, 'C');
$pdf->Cell(90, 10, $countA . ' คน', 1, 1, 'C');
$pdf->Cell(100, 10, "รวมเกรด B+", 1, 0, 'C');
$pdf->Cell(90, 10, $countBPlus . ' คน', 1, 1, 'C');
$pdf->Cell(100, 10, "รวมเกรด B", 1, 0, 'C');
$pdf->Cell(90, 10, $countB . ' คน', 1, 1, 'C');
$pdf->Cell(100, 10, "รวมเกรด C+", 1, 0, 'C');
$pdf->Cell(90, 10, $countCPlus . ' คน', 1, 1, 'C');
$pdf->Cell(100, 10, "รวมเกรด C", 1, 0, 'C');
$pdf->Cell(90, 10, $countC . ' คน', 1, 1, 'C');
$pdf->Cell(100, 10, "รวมเกรด D+", 1, 0, 'C');
$pdf->Cell(90, 10, $countDPlus . ' คน', 1, 1, 'C');
$pdf->Cell(100, 10, "รวมเกรด D", 1, 0, 'C');
$pdf->Cell(90, 10, $countD . ' คน', 1, 1, 'C');
$pdf->Cell(100, 10, "รวมเกรด F", 1, 0, 'C');
$pdf->Cell(90, 10, $countF . ' คน', 1, 1, 'C');
$pdf->Cell(100, 10, "รวมเกรด W", 1, 0, 'C');
$pdf->Cell(90, 10, $countW . ' คน', 1, 1, 'C');
$pdf->Cell(100, 10, "รวมเกรด I", 1, 0, 'C');
$pdf->Cell(90, 10, $countI . ' คน', 1, 1, 'C');
$pdf->setFont(PROMPT_BOLD, '', 18, '', true);
$pdf->Cell(100, 10, "เกรดเฉลี่ยรวม(ไม่คิดนักศึกษาที่มีเกรด I และ W)", 1, 0, 'C');
$pdf->Cell(90, 10, $averageGrade, 1, 1, 'C');

$pdf->Output();
