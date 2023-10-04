<?php

require_once("../../tcpdf.php");
require_once "../../connect.php";


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
$pdf->Cell(190, 10, "รายงานสรุปจำนวนหัวข้อโครงงานที่อาจารย์แต่ละคนรับเป็นที่ปรึกษาในแต่ละภาคการศึกษา", 1, 1, 'C');

$pdf->Cell(190, 10, $selected, 1, 1, 'C');

$pdf->setFont(PROMPT_REGULAR, '', 18, '', true);
$pdf->Cell(15, 10, "ลำดับที่", 1, 0, 'C');
$pdf->Cell(32, 10, "รหัสกลุ่มโครงงาน", 1, 0, 'C');
$pdf->Cell(91, 10, "ชื่อโครงงาน", 1, 0, 'C');
$pdf->Cell(24, 10, "ปีการศึกษา", 1, 0, 'C');
$pdf->Cell(28, 10, "ภาคการศึกษา", 1, 1, 'C');
// $pdf->Cell(79, 40, mb_substr($data->project_nameTH, 0, 20), 1, 0, 'C');
// $pdf->Cell(26, 40, iconv('utf-8', 'cp874', mb_substr($data->project_nameTH, 0, 20)), 1, 0, 'C');
// $pdf->Cell(79, 40, $data->project_nameTH, 1, 0, 'C');
$pdf->setFont(PROMPT_REGULAR, '', 16, '', true);

if (isset($_POST['data'])) {
	$datas = json_decode($_POST['data']);

	$index = 0;
	foreach ($datas as $data) {
		$index++;
		// Limit the length of $data->project_nameTH to 20 characters and add ellipsis if needed
		// $projectName =  mb_substr($data->project_nameTH, 0, 20, 'utf-8');
		$pdf->SetFillColor(255, 255, 255);
		if (mb_strlen($data->project_nameTH, 'utf-8') > 54) {

			$pdf->Cell(15, 40, iconv('utf-8', 'cp874', $index), 1, 0, 'C');
			$pdf->Cell(32, 40, iconv('utf-8', 'cp874', $data->project_id), 1, 0, 'C');
			$pdf->MultiCell(91, 40, $data->project_nameTH, 1, 'C', 1, 0, '', '', true, 0, false, true, 40, 'M');
			$pdf->Cell(24, 40, $data->year, 1, 0, 'C');
			$pdf->Cell(28, 40, iconv('utf-8', 'cp874', $data->term), 1, 1, 'C');
		} else {
			$pdf->Cell(15, 10, iconv('utf-8', 'cp874', $index), 1, 0, 'C');
			$pdf->Cell(32, 10, iconv('utf-8', 'cp874', $data->project_id), 1, 0, 'C');
			$pdf->Cell(91, 10, $data->project_nameTH, 1, 0, 'C');
			$pdf->Cell(24, 10, $data->year, 1, 0, 'C');
			$pdf->Cell(28, 10, iconv('utf-8', 'cp874', $data->term), 1, 1, 'C');
		}
		// $pdf->Cell(79, 40, $projectName, 1, 0, 'C');
	}
}
$pdf->setFont(PROMPT_BOLD, '', 18, '', true);
$pdf->Cell(190, 10, "รวม Total ".$index.' โครงงาน', 1, 0, 'C');

$pdf->Output();
