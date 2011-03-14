<?php

require_once("aprovados_reprovados.php");
 
?>
<html>
<head>
	<title>Lista de Alunos</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="<?=$BASE_URL?>public/styles/style.css" rel="stylesheet" type="text/css">
    <link href="<?=$BASE_URL?>public/styles/print.css" rel="stylesheet" type="text/css" media="print" />
</head>
<body marginwidth="20" marginheight="20">
<div style="width: 760px;" align="center">
     <?php echo $header->get_empresa($PATH_IMAGES, $IEnome); ?>

	<h2>RELAT&Oacute;RIO DE SITUA&Ccedil;&Atilde;O DE APROVA&Ccedil;&Atilde;O DE ALUNO(S)</h2>
    <?=$info?>
	<?php		
  		rs2html($Result1, 'width="90%" cellspacing="0" border="0" class="tabela_relatorio" cellpadding="0"'); 
	?>
	<br /><br />
	<div class="carimbo_box">
           	_______________________________<br>
			<span class="carimbo_nome">
		   		<?php echo $carimbo->get_nome($_POST['carimbo']);?>
			</span><br />
			<span class="carimbo_funcao">
		   		<?php echo $carimbo->get_funcao($_POST['carimbo']);?>
			</span>
		</div>
</div>
<br />
<div class="nao_imprime">
  <input type="button" value="Imprimir" onClick="window.print()" />
  &nbsp;&nbsp;&nbsp;
  <a href="#" onclick="javascript:window.close();">Fechar</a>
</div>
<br />
</body>
</html>
