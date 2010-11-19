<?php

require_once('declaracao_matricula.inc.php');
require_once('../../../lib/fpdf16/fpdf.php');

class PDF extends FPDF
{
}

$pdf=new PDF();

$pdf->AddPage();

$pdf->SetMargins(20,20,20);

$pdf->SetTitle('Declaração de matrícula');

$pdf->SetFont('Times','B',14);
//$pdf->Write(5,'DECLARAÇÃO');
$pdf->Ln(70);
$pdf->Cell(0,5,'DECLARAÇÃO',0,1,'C',0,0,0);
$pdf->Ln(15);
$pdf->SetFont('Times','',13);
$pdf->MultiCell(0,5,$corpo,0,'J');
$pdf->Ln(10);
$pdf->Cell(0,5,$data_declaracao,0,1,'J',0,0,0);
$pdf->Ln(15);
$pdf->Cell(44);
$pdf->Cell(70,5,$carimbo_nome,0,1,'C',0,0,0);
$pdf->SetFont('Times','',10);
$pdf->Cell(44);
$pdf->MultiCell(70,5,$carimbo_dados,0,'C');
//$pdf->SetFont('Times','',12);
$pdf->Ln(15);
$pdf->SetFont('Times','',9);
$pdf->MultiCell(0,5,$decretos,0,'J');
$pdf->Ln(2);
//$pdf->SetDrawColor(0,0,0);
//$pdf->SetLineWidth(300);
//$pdf->Line(0,0,0,100);
$pdf->Cell(0,0,'',1,0);
$pdf->Ln(2);
$pdf->SetFont('Times','',10);
$pdf->MultiCell(0,5,$empresa,0,'C');

$pdf->Output();

?>
