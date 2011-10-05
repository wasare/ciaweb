<?php

require_once(dirname(__FILE__) .'/../../../app/setup.php');

define('PDF_TMP_DIR', dirname(__FILE__) .'/boletins/pdf_tmp/');

require_once(dirname(__FILE__) .'/../../../lib/fpdf17/fpdf.php');
require_once(dirname(__FILE__) .'/../../../lib/fpdi/fpdi.php');
require_once(dirname(__FILE__) .'/../../../lib/fpdf17/pdf.ext.php');


function remove_files($dir) {
	
	if(!is_dir($dir)) 
      @mkdir("$dir",0770,true);
	
	if(is_dir($dir))
		$files = glob("" . $dir . "*.pdf");

	foreach($files as $f) {
		echo $image;
		@unlink($f);
	}
}


remove_files(PDF_TMP_DIR);


class  Boletim extends PDF {

  var $NCurso, $IEnome;
	
	function Header() {
    // IMAGEM COM A LOGO
    $this->Image(dirname(__FILE__) .'/../../../public/images/logo_boletim.jpg',170,16,25); // @todo utilizar parâmetros para a logo da instituição
	  // SELECIONA FONT ARIAL BOLD 10
    $this->SetFont('Arial','',13);
	  // PREPARA TITULO DO CABECALHO
    $this->Cell(0,5,'MEC - SETEC',0,1,'L');
		$this->Cell(0,5,utf8_decode(mb_strtoupper($this->IEnome, 'UTF-8')),0,1,'L'); // @todo utilizar parâmetros para o nome da instituição
    $this->Cell(0,5,utf8_decode('COORDENAÇÃO DE REGISTROS ESCOLARES'),0,1,'L'); // @todo utilizar parâmetros
	  // Quebra de linha
    $this->Ln();
	  // SELECIONA FONT ARIAL  12
    $this->SetFont('Arial','B',11);
	  $this->Cell(0,5,'APROVEITAMENTO MODULAR',0,1,'C');
    // Quebra de linha
	  $this->Ln(5);
	}

	function Footer() {
    //Vai para 1.0 cm da parte inferior
	  $this->SetY(-15);
    //Seleciona a fonte Arial itálico
	  $this->SetFont('Arial','I',8);
	}

	function GeraBoletins($Dados,$FileName,$THeader,$Curso,$IEnome,$Dir,$con)	{
    $this->NPeriodo = $Periodo;
	  $this->NCurso = $Curso;
		$this->IEnome = $IEnome;
		
    // LARGURA DAS COLUNAS
	  $w = array(96,14,18,20,18,12);    
    $numRows = count($Dados); 
		// NUMERO DE LINHAS POR PAGINA^M
		$linhas = 35;
		$Pages = ceil($numRows / $linhas);
		$registro = 0;

    for ( $j = 0; $j < $numRows ; ++$j ) {
				
	    // INICIA UMA NOVA PAGINA
			if($registro != $Dados[$j][1]) {
				if ($j != 0 ) {
        	// Closure line
		    	$this->Cell(array_sum($w),0,'','T');
        }
         		
				$registro = $Dados[$j][1];
				
	      // INICIA UMA NOVA PAGINA
				$this->AddPage();
				$this->SetFont('Arial','B',10);
				$Texto = "Nome: ";
				$this->Write(5,$Texto);
				$this->SetFont('Arial','',12);
				$Texto = $Dados[$j][0];
				$this->Write(5,utf8_decode($Texto));
				$this->SetFont('Arial','B',10);
				$Texto = "               Prontuário: ";
				$this->Write(5,utf8_decode($Texto));
				$this->SetFont('Arial','',11);
				$this->Write(5,$Dados[$j][11]);
				/*
				$this->SetFont('Arial','B',10);
				$Texto = "               Matrícula: ";
				$this->Write(5,utf8_decode($Texto));
				$this->SetFont('Arial','',11);
				$Texto = str_pad($Dados[$j][1], 5, "0", STR_PAD_LEFT);
				$this->Write(5,$Texto);
				*/
				// QUEBRA DE LINHA
				$this->Ln(5);
				$this->SetFont('Arial','B',10);
				$Texto = "Curso: ";
				$this->Write(5,$Texto);
				$this->SetFont('Arial','',11);
				$Texto = " $Curso";
				$this->Write(5,utf8_decode($Texto));
				
				$this->Ln(5);
				$this->SetFont('Arial','B',10);
				$Texto = "Campus: ";
				$this->Write(5,utf8_decode($Texto));
				$this->SetFont('Arial','',11);
				$this->Write(5,$Dados[$j][12]);
				
				// QUEBRA DE LINHA
				$this->Ln(5);
				$this->SetFont('Arial','B',10);
				$Texto = "Período: ";
				$this->Write(5,utf8_decode($Texto));
				$this->SetFont('Arial','',11);
				$Texto = str_pad($Dados[$j][7], 5, "0", STR_PAD_LEFT);
				$this->Write(5,utf8_decode($Texto));
				$this->Ln(5);
				$this->SetFont('Arial','B',10);
				$Texto = "Data de Emissão: ";
				$this->Write(5,utf8_decode($Texto));
				$this->SetFont('Arial','',10);
				$this->Write(5,date('d/m/Y H:i s').'s');
				// QUEBRA DE LINHA
				$this->Ln();
				$this->Ln();
				$this->SetFont('Arial','B',6);
				
		    // ADICIONA O CABECHALHO DA TABELA
				for( $i=0 ; $i < count($THeader) ; ++$i ) {
					$this->Cell($w[$i],6,utf8_decode($THeader[$i]),1,0,'C');
				}
				
				$this->SetFont('Arial','', 9);
				// QUEBRA DE LINHA
				$this->Ln();
      }
			
      //$this->AddPage();
			$nota_final = $Dados[$j][3];
					
      $c_distribuida_sql = 'SELECT COUNT(*) FROM diario_formulas ';
    	$c_distribuida_sql .= " WHERE grupo ILIKE '%-". $Dados[$j][9] . "';";
	    $c_distribuida = $con->get_one($c_distribuida_sql);
				
			$n_distribuida_sql = 'SELECT sum(nota_distribuida) as nota_distribuida FROM diario_formulas ';
			$n_distribuida_sql .= " WHERE grupo ILIKE '%-". $Dados[$j][9] . "';";
			
    	$nota_distribuida = $con->get_one($n_distribuida_sql);
			
			if (is_numeric($nota_final) && is_numeric($nota_distribuida)) {
				$media = @round($nota_final / $nota_distribuida * 100, 2);
			}
			else {
				$media = '-';
			}			
					
			if ( $c_distribuida == 6 ) {
		    $nota_final = str_replace('.', ',', $Dados[$j][3]);
				$nota_distribuida = str_replace('.', ',', $nota_distribuida);
				$media = str_replace('.', ',', $media);
			}
			else {
				$nota_distribuida = '-';
		  	$nota_final = '-';
			}
			
			$this->Cell($w[0],5.7,'  ' . utf8_decode($Dados[$j][9] . ' - ' . $Dados[$j][2]),'LR');
			$this->Cell($w[1],5.7,'     '. utf8_decode($Dados[$j][4]),'LR');
			$this->Cell($w[2],5.7,'      '. $nota_final,'LR');
			$this->Cell($w[3],5.7,'        '. $nota_distribuida,'LR');
			$this->Cell($w[4],5.7,'     '. $media,'LR');
      $this->Cell($w[5],5.7,'     '. $Dados[$j][6],'LR');
			
      // QUEBRA DE LINHA                   
      $this->Ln();
			if ($j == ($numRows - 1)) {
	      // Closure line
        $this->Cell(array_sum($w),0,'','T');
      }    
        
    }
		$this->Output($Dir.$FileName, F);
	}

}// FIM CLASSE BOLETIM

class concat_pdf extends FPDI {

    var $files = array();

    function setFiles($files) {
      $this->files = $files;
    }

    function concat() {
      foreach($this->files as $file) {
        $pagecount = $this->setSourceFile($file);
        for ($i = 1; $i <= $pagecount; $i++) {
          $tplidx = $this->ImportPage($i);
          $s = $this->getTemplatesize($tplidx);
          $this->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
          $this->useTemplate($tplidx);
        }
      }
    }

}

$conn = new connection_factory($param_conn);

$periodo  = $_POST['periodo'];
$curso    = $_POST['codigo_curso'];
$prontuario = (string) $_POST['prontuario'];
$campus_id = (int) $_POST['campus_id'];

$aluno_id = (int) $conn->get_one("SELECT DISTINCT ref_pessoa FROM contratos WHERE ref_campus = $campus_id AND prontuario = '$prontuario';");

if ($aluno_id == 0 && !empty($prontuario))
	exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Aluno não encontrado!");window.close();</script>');


$qryCurso = " SELECT id || ' - ' || descricao FROM cursos WHERE id = ".$curso.";";

$Registro = $Mat;

$NCurso = $conn->get_one($qryCurso);


// RECUPERA ALUNO(S)
$qryAlunos = "
SELECT DISTINCT
    A.ref_pessoa
FROM 
    matricula A
WHERE
	ref_periodo = '$periodo' AND
	ref_curso = $curso ";
	
if ($aluno_id >= 0 && !empty($prontuario)) {
	$qryAlunos .= "AND A.ref_pessoa = ".$aluno_id;
}

$qryAlunos .= " ORDER BY A.ref_pessoa;";

$aAlunos = $conn->get_all($qryAlunos);

if (count($aAlunos) == 0)
	exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Nenhum aluno encontrado!");window.close();</script>');



$qryBoletim = '
SELECT 
    p.nome, m.ref_pessoa as ra_cnec, d.descricao_disciplina, m.nota_final, d.carga_horaria, m.ref_curso, m.num_faltas, s.descricao as periodo,
		m.ref_periodo, m.ref_disciplina_ofer as oferecida, m.ordem_chamada, c.prontuario, get_campus(c.ref_campus)
    FROM
        matricula m, disciplinas d, pessoas p, disciplinas_ofer o, periodos s, pessoa_prontuario_campus ppc, contratos c
    WHERE 
        m.ref_pessoa = p.id AND 
				o.ref_periodo = \'%s\' AND        
				p.ra_cnec = \'%s\' AND 
        m.ref_curso = %s AND         
        m.ref_disciplina_ofer = o.id AND 
        d.id = o.ref_disciplina AND
        s.id = o.ref_periodo AND
				m.ref_contrato = c.id AND
        ppc.prontuario = c.prontuario
    ORDER BY 3;';



foreach ($aAlunos as $aluno) {
	$Boletim = $conn->get_all(sprintf($qryBoletim,$periodo,$aluno['ref_pessoa'],$curso));
    
	//GERA PDF DA LISTA DE PRESENCA DOS CANDIDATOS
  $bo_pdf = new Boletim('P','mm','A4');
    
  $bo_pdf->SetFont('Arial','B',13);
  $bo_pdf->SetMargins(18, 15, 15);
  $bo_pdf->AliasNbPages();
    
  //PREPARA O CABELHO  DA TABELA
  $TableHeader = array('Componente Modular', 'CH', 'Nota', 'Nota Distribuída', 'Média (%)', 'Faltas');
    
  $NArquivo = "Boletim_".$curso."_".$aluno['ref_pessoa'].".pdf"; 
    
  // EXECUTA A GERACAO DO RELATORIO     
  $bo_pdf->GeraBoletins($Boletim,$NArquivo,$TableHeader,$NCurso,$IEnome,PDF_TMP_DIR,$conn);
}

function list_files($dir) {
	if(is_dir($dir)){
		return glob("" . $dir . "*.pdf");
	}
}


$pdf =& new concat_pdf();
$pdf->setFiles(list_files(PDF_TMP_DIR));
$pdf->concat();

$nome_arquivo = "Boletim_".$curso."_".$periodo.".pdf";

$pdf->Output("boletins/$nome_arquivo", 'F');

header("Location: boletins/$nome_arquivo");


?>  
