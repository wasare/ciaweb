<?php

require_once("../../../app/setup.php");
require_once("../../../core/reports/carimbo.php");
require_once("../../../core/search.php");

$conn = new connection_factory($param_conn);

$carimbo = new carimbo($param_conn);

$busca = new search('search','codigo_curso','searchlist', 'form1', '../curso_lista.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript">
	<!--
		//Configuracao do caminho das imagens do tigra calendar
		var caminho_img_tigra = '../../../lib/tigra_calendar/img/';
	-->
	</script>
    <script src="../../../lib/prototype.js" type="text/javascript"></script>
    <script src="../../../lib/tigra_calendar/calendar_br.js" language="JavaScript"></script>
    <link href="../../../lib/tigra_calendar/calendar.css" rel="stylesheet" type="text/css" />
    <link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    <title>SA</title>
</head>

<body>
<h2>Egressos</h2>
<div style="height: 400px;">
	<form method="post" action="lista_egressos.php" name="form1" id="form1" target="_blank">
	
	<input type="image" name="input" 
		src="../../../public/images/icons/print.jpg" 
		alt="Exibir relat&oacute;rio" 
		title="Exibir relat&oacute;rio"
		id="bt_exibir"
		name="bt_exibir"
		class="botao" />
	<input type="image" name="voltar" 
		src="../../../public/images/icons/back.png" 
		alt="Voltar" 
		title="Voltar"
	        id="bt_voltar"
		name="bt_voltar"
		onclick="history.back(-1);return false;" 
		class="botao" />
			
	<div class="panel">
		Cola&ccedil;&atilde;o de grau:<br />
		De <input type="text" name="data_inicio" id="data_inicio" value="<?php echo date("d/m/Y");?>" size="10" />
		<script language="JavaScript">
			new tcal ({ 'formname': 'form1', 'controlname': 'data_inicio' });
		</script>
		&nbsp;&nbsp;&nbsp;&agrave;&nbsp;&nbsp;&nbsp; 
		<input type="text" name="data_fim" id="data_fim" value="<?php echo date("d/m/Y");?>" size="10" />
		<script language="JavaScript">
		        new tcal ({ 'formname': 'form1', 'controlname': 'data_fim' });
		</script>
		<br />
		Curso:<br />
		<?php 
		echo $busca->input_text_retorno("5"); 
		echo $busca->input_text_consulta("40");
		echo $busca->area_lista();
		?>
		<br />
		Assinatura:<br />
		<?php echo $carimbo->listar();?>
	</div>
	
	</form>
</div>
</body>
</html>