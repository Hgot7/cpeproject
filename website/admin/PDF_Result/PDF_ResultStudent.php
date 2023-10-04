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
// $pdf = new TCPDF('p', 'mm', 'A4');
$pdf = new TCPDF('p', 'mm', 'A4','utf-8');
$pdf->AddPage();

define('PROMPT_REGULAR', TCPDF_FONTS::addTTFfont('../.././fonts/THSarabunNew.ttf', 'TrueTypeUnicode'));
define('PROMPT_BOLD', TCPDF_FONTS::addTTFfont('../.././fonts/THSarabunNew Bold.ttf', 'TrueTypeUnicode'));
$pdf->setFont(PROMPT_BOLD, '', 18, '', true);


// $pdf->setPrintHeader(false);
// $pdf->setPrintFooter(false);


// $pdf->Cell(190,10,iconv('utf-8','cp874','การรายงานสรุปให้เป็น PDF'),0,1,'C');
$pdf->Cell(190, 10, "รายงานสรุปนักศึกษาที่ลงทะเบียนเรียนในแต่ละภาคการศึกษา", 1, 1, 'C');
$pdf->Cell(190, 10, $selected, 1, 1, 'C');

$pdf->setFont(PROMPT_REGULAR, '', 18, '', true);
$pdf->Cell(15, 10, "ลำดับที่", 1, 0, 'C');
$pdf->Cell(38, 10, "รหัสนักศึกษา", 1, 0, 'C');
$pdf->Cell(29, 10, "ชื่อ", 1, 0, 'C');
$pdf->Cell(34, 10, "นามสกุล", 1, 0, 'C');
$pdf->Cell(24, 10, "ปีการศึกษา", 1, 0, 'C');
$pdf->Cell(25, 10, "ภาคการศึกษา", 1, 0, 'C');
$pdf->Cell(25, 10, "ชื่อกลุ่มเรียน", 1, 1, 'C');

$pdf->setFont(PROMPT_REGULAR, '', 16, '', true);

if (isset($_POST['data'])) {
	$datas = json_decode($_POST['data']);
	$group_id = '';
	$index = 0;
	foreach ($datas as $data) {

		if (isset($data->group_id)) {
			$group_id = ($data->group_id) ? giveGroupById($conn, $data->group_id) : null;
		} else {
			$group_id = '';
		}
		$index++;
		$pdf->Cell(15, 10, iconv('utf-8', 'cp874', $index), 1, 0, 'C');
		$pdf->Cell(38, 10, iconv('utf-8', 'cp874', $data->student_id), 1, 0, 'C');
		$pdf->Cell(29, 10, $data->firstname, 1, 0, 'C');
		$pdf->Cell(34, 10, $data->lastname, 1, 0, 'C');
		$pdf->Cell(24, 10, iconv('utf-8', 'cp874', $data->year), 1, 0, 'C');
		$pdf->Cell(25, 10, iconv('utf-8', 'cp874', $data->term), 1, 0, 'C');
		$pdf->Cell(25, 10, $group_id ? $group_id['group_name'] : '', 1, 1, 'C');
	}
}
$pdf->setFont(PROMPT_BOLD, '', 18, '', true);
$pdf->Cell(190, 10, "รวม Total ".$index.' คน', 1, 0, 'C');
$pdf->Output();

