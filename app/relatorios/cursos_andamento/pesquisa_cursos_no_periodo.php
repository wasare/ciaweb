<?php

require_once("../../../app/setup.php");

$conn = new connection_factory($param_conn);

$Result1   = $conn->Execute("SELECT descricao, id FROM periodos ORDER BY 1 DESC;");

$Result3   = $conn->Execute("SELECT descricao, id FROM tipos_curso ORDER BY 1 DESC;");

$RsCidades = $conn->Execute("SELECT nome_campus, id FROM campus WHERE ref_empresa = 1 ORDER BY 1;");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>SA</title>	
	<script src="pesquisa_cursos_no_periodo.js" type="text/javascript"></script>
	<script src="../../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
	<link href="../../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
	<link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<h2>Cursos em andamento por per&iacute;odo</h2>
	<form method="post" action="lista_cursos_no_periodo.php" name="form1" target="_blank">
		<input type="image" name="input" 
			src="../../../public/images/icons/print.jpg" 
			alt="Exibir relat&oacute;rio" 
			title="Exibir relat&oacute;rio"
			id="bt_exibir" 
				name="bt_exibir"  
			class="botao" 
			onclick="document.form1.action = 'lista_cursos_no_periodo.php'" />
		<input type="image" name="voltar" 
			src="../../../public/images/icons/back.png" 
			alt="Voltar" 
			title="Voltar" 
			id="bt_voltar" 
			name="bt_voltar" 
			class="botao"
			onclick="history.back(-1);return false;" />
		
		<div class="panel">
			Per&iacute;odo:<br />
			<span id="sprytextfield1">
			    <input name="periodo1" type="text" id="periodo1" size="10" onchange="ChangeCode('periodo1','periodo')" />
			    <?php  print $Result1->GetMenu('periodo',null,true,false,0,'onchange="ChangeOp()"'); ?>
				<span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>
			</span>
			<br />
			Tipo de curso:<br />
		    <?php print $Result3->GetMenu('tipo',null,true,false,0); ?>
		    <span class="comentario">Caso n&atilde;o selecionado exibir&aacute; todos.</span>
			<br />
			Campus:<br />
			<?php  print $RsCidades->GetMenu('cidade',null,true,false,0); ?>
			<span class="comentario">Caso n&atilde;o preenchido exibir&aacute; todos.</span>
		</div>
	</form>
	<script type="text/javascript">
	<!--
		var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
	//-->
	</script>
</body>
</html>
