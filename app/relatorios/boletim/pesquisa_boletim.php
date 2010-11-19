<?php

require_once("../../../app/setup.php");
require_once("../../../core/search.php");

$conn = new connection_factory($param_conn);

$Result1 = $conn->Execute("SELECT descricao, id FROM periodos ORDER BY 1 DESC;");

$busca   = new search('search','codigo_curso','searchlist', 'form1', '../curso_lista.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Lista alunos matriculados</title>
	<link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
	<script src="../../../lib/SpryAssets/SpryValidationCheckbox.js" type="text/javascript"></script>
	<link href="../../../lib/SpryAssets/SpryValidationCheckbox.css" rel="stylesheet" type="text/css" />
	<link href="../../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
	<script src="../../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
	<script language="javascript" src="../../../lib/prototype.js"></script>
	<script src="pesquisa_boletim.js" type="text/javascript" language="javascript"></script>
</head>

<body>
	<h2>Boletim Escolar</h2>
	
	<form name="form1" id="form1" action="boletim.php" method="post" target="_blank">
		<input type="image" name="input" 
			src="../../../public/images/icons/pdf_icon.jpg" 
			alt="Exibir relat&oacute;rio PDF" 
			title="Exibir relat&oacute;rio PDF"
			id="bt_pdf" 
			name="bt_pdf"  
			class="botao" />
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
		        <input name="periodo" type="text" id="periodo" size="10" onchange="ChangeCode('periodo','periodo1'); setPeriodo();" />
		        <?php  print $Result1->GetMenu('periodo1',null,true,false,0,'onchange="ChangeOp();setPeriodo();"'); ?>
		        <span class="textfieldRequiredMsg">Valor obrigat&oacute;rio.</span>
		    </span>
		    <br />
		    Curso:<br />
		    <span id="sprytextfield2">
		        <?php 
		            echo $busca->input_text_retorno("5"); 
		            echo $busca->input_text_consulta("40");
		            echo $busca->area_lista();
		        ?>
				<span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>
		    </span>
		    <br />
		    C&oacute;digo do Aluno:<br />
		    <input name="aluno_id" type="text" id="aluno_id" size="10" />
		    <span class="comentario">Caso n&atilde;o preenchido exibir&aacute; todos.</span>
		</div>
	</form>
	<script type="text/javascript">
	<!--
		var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
		var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
	//-->
    </script>
</body>
</html>
