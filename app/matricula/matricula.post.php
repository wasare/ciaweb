<?php

header("Cache-Control: no-cache");

//-- ARQUIVO E BIBLIOTECAS
require_once(dirname(__FILE__) .'/../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

//-- PARAMETROS
$sa_periodo_id  = (string) $_POST['periodo_id'];
$curso_id    = (int) $_POST["curso_id"];
$aluno_id    = (int) $_POST["aluno_id"];
$contrato_id = (int) $_POST['contrato_id'];
$ref_campus  = (int) $_POST["ref_campus"];
$id_diarios  = (array) $_POST["id_diarios"]; //Array com todos os diarios a matricular

// SOMENTE PROCESSA OS DADOS SE EXISTIR PELO MENOS UMA MATRICULA A SER FEITA
if (count($id_diarios) > 0)
{
    $msg = '<h3><font color=\"#006600\">Disciplinas matr&iacute;culadas:</font></h3>'; //-- resposta para o usuario
    $sqlInsereDiario = "BEGIN;"; //-- Variavel com a sql de insercao das matrículas

     //-- Percorre os diarios
	foreach($id_diarios as $diario){

		//-- Verifica se o aluno ja esta matriculado nesta disciplina oferecida
		$sqlMatriculado = "
			SELECT 
				count(ref_disciplina_ofer)
			FROM 
				matricula
			WHERE 
				ref_disciplina_ofer = '$diario' AND
				ref_periodo = '$sa_periodo_id' AND
				ref_pessoa  = '$aluno_id'";
	
		$RsMatriculado = $conn->Execute($sqlMatriculado);
		$Result1 = $RsMatriculado->fields[0];

         	
		if($Result1 == 0){
	
			//-- Informacoes da disciplina
			$sqlDisciplina = "
			SELECT 
				descricao_disciplina(ref_disciplina),
				ref_disciplina,
				ref_campus
			FROM 
				disciplinas_ofer 
			WHERE 
				id = $diario";
		
			$RsDisciplina = $conn->Execute($sqlDisciplina);
		
			$disciplina_descricao = $RsDisciplina->fields[0];
			$disciplina_id = $RsDisciplina->fields[1];
			$ref_campus_ofer = $RsDisciplina->fields[2];
		
			//-- Verifica se tem vaga
			$sqlVerificaVagas = "
				SELECT
					count(*),
					check_matricula_pessoa('$diario','$aluno_id'),
					num_alunos('$diario')
				FROM
					matricula
				WHERE
					ref_disciplina_ofer = '$diario' AND
					dt_cancelamento is null";
	  
			$RsVerificaVagas = $conn->Execute($sqlVerificaVagas);
	
			if ($RsVerificaVagas)
			{
				$num_matriculados = $RsVerificaVagas->fields[0];
				$is_matriculado = $RsVerificaVagas->fields[1];
				$tot_alunos = $RsVerificaVagas->fields[2];
			}
			else
			{
				$num_matriculados = 0;
				$tot_alunos = 0;
			}
		
			//-- Se o total de vagas excedeu nÃ£o matricula
			if (($num_matriculados+1) > $tot_alunos)
			{
				$msg .= "<p>>> <b><font color=\"#FF0000\">Aluno n&atilde;o matr&iacute;culado!</font></b><br>";
				$msg .= "Disciplina <b>$disciplina_descricao</b> ($disciplina_id) excedeu n&uacute;mero m&aacute;ximo de alunos.</p>";
			}
			else
			{
				$alunos_matriculados = $num_matriculados + 1;
				$msg .= "<p>>> <b>Di&aacute;rio: </b>$diario - "; 
				$msg .= "<b>$disciplina_descricao</b> ($disciplina_id) - ";
				$msg .= "<b>Matric./Vagas: </b> ".$alunos_matriculados."/$tot_alunos.</p>";
			
				//-- Informacoes da disciplina substituta --  IMPLEMENTAR
				$ref_curso_subst = 0;
				$ref_disciplina_subst = 0;
		
				$sqlInsereDiario .= "
				INSERT INTO matricula
				(
					ref_contrato,
					ref_pessoa,
					ref_campus,
					ref_curso,
					ref_periodo,
					ref_disciplina,
					ref_curso_subst,
					ref_disciplina_subst,
					ref_disciplina_ofer,
					complemento_disc,
					fl_exibe_displ_hist,
					dt_matricula,
					hora_matricula,
					status_disciplina
				)
				VALUES (
					'$contrato_id',
					'$aluno_id',
					'$ref_campus_ofer',
					'$curso_id',
					'$sa_periodo_id',
					'$disciplina_id',
					'$ref_curso_subst',
					'$ref_disciplina_subst',
					'$diario',
					get_complemento_ofer('$diario'),
					'S',
					date(now()),
					now(),
					'f'
				);";
			
				$diarios_matriculados[] = $diario;
	
			}//fim total de vagas
		}
		else{
			$msg .= "<p>>> <b><font color=\"#FF0000\">Aluno j&aacute; matriculado no di&aacute;rio $diario!</font></b></p>";
		}//fim matriculados
	
	}//fim foreach
    $sqlInsereDiario .= "COMMIT;";
}
// ^ SOMENTE PROCESSA OS DADOS SE EXISTIR PELO MENOS UMA MATRICULA A SER FEITA ^ //

// SOMENTE EFETUA A MATRICULA SE EXISTIR PELO MENOS UM DISCIPLINA A SER MATRICULADA
if ( is_numeric(count($diarios_matriculados)) AND count($diarios_matriculados) > 0)
{
    //echo $sqlInsereDiario; die;
	//-- Inserindo a matricula
	$RsInsereDiario = $conn->Execute($sqlInsereDiario);

	
	if (!$RsInsereDiario)
	{
		$title = "<h3><font color=\"#FF0000\">Erro: matr&iacute;cula n&atilde;o efetuada!</font></h3>";
		$msg = ">> Di&aacute;rio: $diario<br>";
	    $msg .= "<p><b>Informa&ccedil;&otilde;es sobre o erro:</b><br>$conn->ErrorMsg()</p>";
	}
	else
	{
		// atualizar o contrato somente quando pelo menos uma matricula for efetivada
		//-- Atualizando o contrato para o periodo corrente
		$sqlAtualizaContrato = "
			UPDATE contratos SET
  				cod_status = null,
  				ref_last_periodo = '$sa_periodo_id'
			WHERE
  				id = '$contrato_id';";

		if($conn->Execute($sqlAtualizaContrato) === false)
		{
    		$msg .= ">> <font color=\"#FF0000\">Erro ao atualizar contrato: $conn->ErrorMsg()</font><br>";
		}
		else
		{
    		$msg .= ">> Contrato atualizado para o per&iacute;odo<br>";
		}
	}

	// ATUALIZA NOTAS E FALTAS CASO O DIARIO TEM SIDO INICIALIZADO 
	foreach($diarios_matriculados as $matriculado) {
		atualiza_diario("$aluno_id","$matriculado");
	}

	// ^ ATUALIZA NOTAS E FALTAS CASO O DIARIO TEM SIDO INICIALIZADO ^ //
} //^ SOMENTE EFETUA A MATRICULA SE EXISTIR PELO MENOS UMA DISCIPLINA A SER MATRICULADA ^//
else
{
  	$msg .= '<h4><font color="#FF0000">Selecione pelo menos uma disciplina para efetuar a matr&iacute;cula!</font></h4>';
}

$cabecalho = ">> <strong>Aluno</strong>: $aluno_id <br />";
$cabecalho .= ">> <strong>Curso</strong>: $curso_id  - <strong>Per&iacute;odo</strong>: $sa_periodo_id <br />";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SA</title>
<link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
<script language="JavaScript">
<!--
//JavaScript Document

//Oculta 
function Oculta(id)
{
	document.getElementById(id).style.display = "none";
}
//Exibe
function Exibe(id)
{
	document.getElementById(id).style.display = "inline";
}

function confirma()
{
	if (confirm('Tem certeza que deseja matricular o aluno nas disciplinas selecionadas?'))
	{
		document.form1.submit();
	} else {
		// se não confirmar, coloque o codigo aqui
    }
}

function selecionar_tudo(){
		for (i=0;i<document.form1.elements.length;i++)
  		if(document.form1.elements[i].type == "checkbox")
     		document.form1.elements[i].checked=1
} 

function deselecionar_tudo(){
		for (i=0;i<document.form1.elements.length;i++){
  			if(document.form1.elements[i].type == "checkbox")
    			document.form1.elements[i].checked=0
		}
} 

-->
</script>
</head>
<body>
<div align="center">
  <h1>Processo de Matr&iacute;cula</h1>
  <div class="panel">
	<?=$title?>
       <?=$cabecalho?>
    <?=$msg?>
  </div>
  <a href="matricula_aluno.php">Nova matr&iacute;cula</a> <a href="<?=$BASE_URL .'app/'?>">P&aacute;gina inicial</a> </div>
</body>
</html>
