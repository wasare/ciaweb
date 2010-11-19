<?php

header("Cache-Control: no-cache");

//-- ARQUIVO CONFIGURACAO E BIBLIOTECAS
require_once('../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);//,TRUE,TRUE);

// PROCESSA A DISPENSA SE NAO HOUVER ERROS
if ( $_POST['second'] != 1 )
	die;

$flag_processa = 1;

require_once('dispensa_valida.php');

//-- PARAMETROS
$dispensa_tipo  = $_POST['dispensa_tipo'];
$ref_liberacao_ed_fisica  = $_POST['ref_liberacao_ed_fisica'];
$processo  = $_POST['processo'];
$diario_id  = $_POST['diario_id'];
$ref_instituicao  = $_POST['ref_instituicao'];
$obs_aproveitamento  = $_POST['obs_aproveitamento'];
$obs_final  = $_POST['obs_final'];
$nota_final  = $_POST['nota_final'];


$periodo_id  = $_POST['periodo_id'];
$curso_id    = $_POST['curso_id'];
$aluno_id    = $_POST['aluno_id'];
$id_contrato = $_POST['id_contrato'];
$ref_campus  = $_POST['ref_campus'];


// PARAMETROS SQL

// APROVEITAMENTO DE ESTUDOS
if ($dispensa_tipo == 2)
{

 	$insert_sql = ',ref_instituicao,obs_aproveitamento,nota_final';
 	$values_sql = ",$ref_instituicao,'$obs_aproveitamento', $nota_final";

}
// CERTIFICACAO DE EXPERIENCIAS
if ($dispensa_tipo == 3)
{
	$insert_sql = ',nota_final';
    $values_sql = ",$nota_final";

}

// EDUCACAO FISICA
if ($dispensa_tipo == 4)
{
	$insert_sql = ',obs_final, ref_liberacao_ed_fisica';
    $values_sql = ",'$obs_final',$ref_liberacao_ed_fisica";
}

$insert_sql .= ',ref_motivo_matricula, processo';
$values_sql .= ",$dispensa_tipo,'$processo'";


$msg = '<h3><font color=\"#006600\">Dispensa de Disciplina:</font></h3>'; //-- Variavel com a resposta para o usuario

$sqlInsereDispensa = ""; //-- Variavel com a sql de insercao da dispensa


	//-- Verifica se o aluno ja esta matriculado nesta disciplina oferecida
	$sqlDispensado = "
  	SELECT 
    	count(ref_disciplina_ofer)
  	FROM 
    	matricula
  	WHERE 
    	ref_disciplina_ofer = '$diario_id' AND
    	ref_periodo = '$periodo_id' AND
    	ref_pessoa  = '$aluno_id'";
	
	$Result1 = $conn->get_one($sqlDispensado);
         	
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
	  		id = $diario_id";
		
		$disciplina = $conn->get_row($sqlDisciplina);
		
		$disciplina_descricao = $disciplina['descricao_disciplina'];
		$disciplina_id = $disciplina['ref_disciplina'];
		$ref_campus_ofer = $disciplina['ref_campus'];
		
		
		//-- Verifica se tem vaga
    	$sqlVerificaVagas = "
		SELECT
    	  count(*) as total_matriculas,
	      check_matricula_pessoa('$diario_id','$aluno_id'),
    	  num_alunos('$diario_id') as numero_vagas
	    FROM
    	  matricula
	    WHERE
    	  ref_disciplina_ofer = '$diario_id' AND
	      dt_cancelamento is null";
	  
		$verifica_vagas = $conn->get_row($sqlVerificaVagas);
	
	    if ($verifica_vagas['total_matriculas'] > 0)
    	{
        	$num_matriculados = $verifica_vagas['total_matriculas'];
	        $is_matriculado = $verifica_vagas['check_matricula_pessoa'];
    	    $numero_vagas = (int) $verifica_vagas['numero_vagas'];
    	}
	    else
    	{
        	$num_matriculados = 0;
	        $numero_vagas = (int) $verifica_vagas['numero_vagas'];
    	}
	
		//-- Se o total de vagas excedeu não matricula
		if (($num_matriculados+1) > $numero_vagas || $numero_vagas == 0)
    	{
	       $msg .= "<p>>> <b><font color=\"#FF0000\">Aluno n&atilde;o dispensado!</font></b><br>";
		   $msg .= "Disciplina <b>$disciplina_descricao</b> ($disciplina_id) excedeu n&uacute;mero m&aacute;ximo de alunos.</p>";
    	}
	    else
    	{
			$alunos_matriculados = $num_matriculados + 1;
			$msg .= "<p>>> <b>Di&aacute;rio: </b>$diario_id - "; 
			$msg .= "<b>$disciplina_descricao</b> ($disciplina_id) - ";
			$msg .= "<b>Matric./Vagas: </b> ". $alunos_matriculados .'/'. $numero_vagas .'</p>';
			
			//-- Informacoes da disciplina substituta --  IMPLEMENTAR
			$ref_curso_subst = 0;
			$ref_disciplina_subst = 0;
		
			$sqlInsereDispensa .= "
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
			   $insert_sql
    	    )
        	VALUES (
	           '$id_contrato',
    	       '$aluno_id',
        	   '$ref_campus_ofer',
	           '$curso_id',
    	       '$periodo_id',
        	   '$disciplina_id',
			   '$ref_curso_subst',
	           '$ref_disciplina_subst',
    	       '$diario_id',
        	   get_complemento_ofer('$diario_id'),
	           'S',
    	       date(now()),
        	    now(),
	           'f'
			   $values_sql
    	    );";
           
            // Registra a dispensa no banco
            $RsInsereDiario = $conn->Execute($sqlInsereDispensa);			

		}//fim total de vagas
	}
	else{
	       $msg .= "<p>>> <b><font color=\"#FF0000\">Aluno j&aacute; matriculado/dispensado no di&aacute;rio $diario_id!</font></b></p>";
	}//fim matriculados
	

//echo '<pre>'. $sqlInsereDispensa .'</pre>'; die;

//-- Inserindo a matricula
//$RsInsereDiario = $conn->Execute($sqlInsereDispensa);
			
if ($RsInsereDiario == FALSE)
{
	$title = "<h3><font color=\"#FF0000\">Erro ao efetuar a dispensa!</font></h3>";
	$msg .= ">> Di&aacute;rio: $diario_id<br>";
    
	//$msg .= "<p><b>Informa&ccedil;&otilde;es adicionais:</b>".$Conexao->ErrorMsg."</p>";
}
else
{

	// ATUALIZA NOTAS E FALTAS NO DIARIO
    atualiza_dispensa($aluno_id,$diario_id,$dispensa_tipo);
    if(is_numeric($nota_final) AND $nota_final >= 50 )
		$msg .= lanca_nota($aluno_id,$nota_final,$diario_id);

	// ^ ATUALIZA NOTAS E FALTAS NO DIARIO ^ //
}

$cabecalho = ">> <strong>Aluno</strong>: $aluno_id <br />";
$cabecalho .= ">> <strong>Curso</strong>: $curso_id  - <strong>Per&iacute;odo</strong>: $periodo_id <br />";


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SA</title>
<link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="matricula.js"></script>
</head>
<body>
<div align="center">
  <h1>Processo de Dispensa de Disciplina</h1>
  <div class="panel">
	<?=$title?>
       <?=$cabecalho?>
    <?=$msg?>
  </div>
  <a href="dispensa_aluno.php">Nova Dispensa</a> <a href="<?=$BASE_URL .'app/index.php'?>">P&aacute;gina inicial</a> </div>
</body>
</html>
