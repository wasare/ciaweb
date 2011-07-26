<?php

header("Cache-Control: no-cache");

//-- ARQUIVO E BIBLIOTECAS
require_once("../../app/setup.php");
require_once($BASE_DIR .'core/web_diario.php');

$cabecalho = '';

if ($_POST['btnOK'] == 10) 
{
	//-- PARAMETROS
	$aluno_id    = $_POST['aluno_id']; // matricula do aluno
	$diarios  = explode("|", $_POST['diarios']); // diarios a ajustar quando for mais de um separá-los por um |

	/* 
		Exemplos de URLs para efetivação do ajuste de nota e/ou faltas

		ajusta_notas_faltas.php?d=2483|2484|2485|2486|2487|2488&id=2735
		ajusta_notas_faltas.php?d=2483&id=2735
	*/

	// SOMENTE EFETUA AJUSTE SE EXISTIR PELO MENOS UM DIARIO E UM ALUNO
	if (is_numeric(count($diarios)) AND count($diarios) > 0 AND is_numeric($aluno_id))
	{
		$diarios_ajustados = '';
		foreach($diarios as $diario) {
            if (is_diario($diario) && is_inicializado($diario) && !is_fechado($diario)) {
				atualiza_diario("$aluno_id","$diario");
				$diarios_ajustados .=  $diario .'  ';
			}
		}

		// ^ ATUALIZA NOTAS E FALTAS CASO O DIARIO TEM SIDO INICIALIZADO ^ //
	}	//^ SOMENTE EFETUA AJUSTE SE EXISTIR PELO MENOS UM DIARIO E UM ALUNO ^//

	$cabecalho = ">> <strong>Aluno</strong>: $aluno_id <br />";
	$cabecalho .= ">> <strong>Di&aacute;rios</strong>: $diarios_ajustados <br />";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SA</title>
<link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div align="center">
  <h1>Ajuste de Notas e Faltas</h1>
  <div class="panel">
	<?=$title?>

	<?php
		if ($_POST['btnOK'] == 10)
		{	
			echo $cabecalho;
            $_POST = array();
     ?>
			<br /><a href="ajusta_notas_faltas.php">Voltar</a> 
	<?php
		} else {
     ?>
		<form name="form1" method="post" action="">
			<input type="hidden" name="btnOK" id="btnOK" value="10" />
			N&ordm; de matr&iacute;cula do aluno&nbsp;<input type="text" name="aluno_id" id="aluno_id" size="6" value="" />
			<br />
			C&oacute;digo do(s) di&aacute;rio(s):
			<br />
			<textarea name="diarios" cols="40" rows="2"></textarea><br />
            <span class="dica">Para mais de um di&aacute;rio separ&aacute;-los por um "|", exemplo: 254|3654|4578</span>

			<br /><br />
			<input type="submit" name="enviar" id="enviar" value="Verificar e ajustar notas e faltas -->" />
		</form>
	<?php
		}
	?>
  </div>
</body>
</html>
