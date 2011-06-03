<?php

require_once(dirname(__FILE__). '/../../../setup.php');
require_once($BASE_DIR .'lib/fpdf16/fpdf.php');
require_once($BASE_DIR .'lib/fpdi/fpdi.php');
require_once($BASE_DIR .'core/number.php');

$conn = new connection_factory($param_conn);

//====================================================================
// Set up parameters
//
// We need to set the filename for the template which we want to use
// as the template for our generated PDF.
//
// In addition we supply the information which we want to write to the
// document. This data would usually be sent as GET / POST or be stored
// in session from the order confirmation page.
//====================================================================

// the templates PDF files
$frente_tpl = "Diario_Frente.pdf";
$verso_tpl = "Diario_Verso.pdf";

$diario_id = 95;

$sql3 = "SELECT DISTINCT dia FROM diario_seq_faltas WHERE ref_disciplina_ofer = $diario_id ORDER BY dia;"; 


$sql4 = "SELECT 
         b.nome, b.ra_cnec, c.prontuario, a.ordem_chamada, a.nota_final, a.num_faltas
         FROM matricula a, pessoas b, pessoa_prontuario_campus c

         WHERE
            a.ref_disciplina_ofer = $diario_id AND
            a.ref_pessoa = b.id AND
            a.ref_pessoa = c.ref_pessoa
         ORDER BY lower(to_ascii(nome,'LATIN1'));" ;


$alunos_diario = $conn->get_all($sql4);

/*
echo '<pre>';
print_r($alunos_diario);
echo '</pre>';
*/
$num_chamadas = $conn->get_all($sql3);


if(count($num_chamadas) == 0) {
  echo '<script language="javascript">window.alert("Nenhuma chamada realizada para este diário!"); javascript:window.close(); </script>';
  exit;
}



//====================================================================
// Set up the PDF objects and initialize
//
// This section sets up FPDF and imports our template document. No need
// for changes in this section.
//====================================================================

$pdf = new FPDI('L','mm','A3');
$pdf->AddPage();

// import the template PFD
$pdf->setSourceFile($frente_tpl);

// select the first page
$tplIdx = $pdf->importPage(1);

// use the page we imported
$pdf->useTemplate($tplIdx);



//====================================================================
// Write to the document
//
// The following section writes the actual texts into the document
// template. Expect some trying and failing when placing the texts :)
//====================================================================

// set font, font style, font size.
$pdf->SetFont('Times','',10);

// set initial placement
$pdf->SetXY(20, 50);

// line break
//$pdf->Ln(5);

// go to 25 X (indent)
$pdf->SetX(20);


foreach($alunos_diario as $aluno) {
    $nome = $aluno["nome"];
    $matricula = str_pad($aluno["ra_cnec"], 5, "0", STR_PAD_LEFT);    
    $nota_final = number::numeric2decimal_br($aluno['nota_final'],1);
    $falta_total = $aluno['num_faltas'];
    
    // NOME ALUNO
    $pdf->SetX(20); 
    $pdf->Write(0, utf8_decode($nome));
    
    // PRONTUARIO ALUNO
    $pdf->SetX(102);
    $pdf->Write(0, utf8_decode($matricula));
    
    // CHAMADA PRESENÇA
$pdf->SetX(127.2);
$pdf->Write(0, '.'); //

// CHAMADA FALTA
$pdf->SetX(129.5);
$pdf->Write(0, 'F'); //

// CHAMADA PRESENÇA
$pdf->SetX(133);
$pdf->Write(0, '.'); //

// CHAMADA FALTA
$pdf->SetX(133 + 2.3);
$pdf->Write(0, 'F'); //

// CHAMADA PRESENÇA
$pdf->SetX(135.3 + 3.6);
//$pdf->SetXY(135.3 + 3.6, 49.99);
$pdf->Write(0, '.'); //

// CHAMADA PRESENÇA
$pdf->SetX(138.8 + 3.4);
$pdf->Write(0, '.'); //

// CHAMADA FALTA
$pdf->SetX(142.3 + 2.3);
$pdf->Write(0, 'F'); //

// CHAMADA FALTA
$pdf->SetX(144.6 + 3.1);
$pdf->Write(0, 'F'); //


// CHAMADA FALTA
//$pdf->SetX(147.7 + 3.1);
//$pdf->Write(0, 'F'); //

// CHAMADA PRESENÇA
$pdf->SetX(147.7 + 3.6);
$pdf->Write(0, '.'); //

// CHAMADA FALTA
$pdf->SetX(151.3 + 2.3);
$pdf->Write(0, 'F'); //
    
    
    // NOTA FINAL
    $pdf->SetX(398.5);
    $pdf->Write(0, utf8_decode($nota_final));
    
    // TOTAL DE FALTAS
    $pdf->SetX(404);
    $pdf->Write(0, utf8_decode($falta_total));

    // move to next line
    $pdf->Ln(4);
}
// The following section is basically a repetition of the previous for inserting more text.
// repeat for more text:
/*
// NOME ALUNO
$pdf->SetX(20);
$pdf->Write(0, ucwords(strtolower($address))); // nome

// PRONTUARIO ALUNO
$pdf->SetX(100);
$pdf->Write(0, ucwords(strtolower($country))); //

// DIA CHAMADA
$pdf->SetX(121);
$pdf->Write(0, '28'); //
*/
/*
// CHAMADA PRESENÇA
$pdf->SetX(127.2);
$pdf->Write(0, '.'); //

// CHAMADA FALTA
$pdf->SetX(129.5);
$pdf->Write(0, 'F'); //

// CHAMADA PRESENÇA
$pdf->SetX(133);
$pdf->Write(0, '.'); //

// CHAMADA FALTA
$pdf->SetX(133 + 2.3);
$pdf->Write(0, 'F'); //

// CHAMADA PRESENÇA
$pdf->SetX(135.3 + 3.6);
//$pdf->SetXY(135.3 + 3.6, 49.99);
$pdf->Write(0, '.'); //

// CHAMADA PRESENÇA
$pdf->SetX(138.8 + 3.4);
$pdf->Write(0, '.'); //

// CHAMADA FALTA
$pdf->SetX(142.3 + 2.3);
$pdf->Write(0, 'F'); //

// CHAMADA FALTA
$pdf->SetX(144.6 + 3.1);
$pdf->Write(0, 'F'); //


// CHAMADA FALTA
//$pdf->SetX(147.7 + 3.1);
//$pdf->Write(0, 'F'); //

// CHAMADA PRESENÇA
$pdf->SetX(147.7 + 3.6);
$pdf->Write(0, '.'); //

// CHAMADA FALTA
$pdf->SetX(151.3 + 2.3);
$pdf->Write(0, 'F'); //

*/

/*
$pdf->Ln(4);
$pdf->SetX(20);
$pdf->Write(0, $zipcode . " " . ucwords(strtolower($city)));
$pdf->Ln(4);
$pdf->SetX(20);
$pdf->Write(0,  ucwords(strtolower($country)));
$pdf->Ln(4);
$pdf->SetX(20);
$pdf->Write(0, ucwords(strtolower($address)));
$pdf->Ln(4);
$pdf->SetX(20);
$pdf->Write(0, $zipcode . " " . ucwords(strtolower($city)));
$pdf->Ln(4);
$pdf->SetX(20);
$pdf->Write(0,  ucwords(strtolower($country)));
$pdf->Ln(4);
$pdf->SetX(20);
$pdf->Write(0, ucwords(strtolower($address)));
$pdf->Ln(4);
$pdf->SetX(20);
$pdf->Write(0, $zipcode . " " . ucwords(strtolower($city)));
$pdf->Ln(4);
$pdf->SetX(20);
$pdf->Write(0,  ucwords(strtolower($country)));
$pdf->Ln(4);
*/


// all changes to PDF is now complete.


//====================================================================
// Output document
// This section will give the user a download file dialog with the
// generated document. The filename will be document.pdf
//====================================================================

// MSIE hacks. Need this to be able to download the file over https
// All kudos to http://in2.php.net/manual/en/function.header.php#74736
//header("Content-Transfer-Encoding", "binary");
//header('Cache-Control: maxage=3600'); //Adjust maxage appropriately
//header('Pragma: public');

$pdf->Output("tmp/document.pdf", 'F');

header("Location: tmp/document.pdf");

//$pdf->Output('document.pdf', 'D');


?>
