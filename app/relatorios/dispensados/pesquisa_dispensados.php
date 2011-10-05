<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/reports/carimbo.php');

$conn = new connection_factory($param_conn);

$Result1 = $conn->Execute("SELECT descricao, id FROM periodos ORDER BY 1 DESC;");

$Result2 = $conn->Execute("SELECT nome_campus, id FROM campus ORDER BY 1 DESC;");
$arr_campi = $conn->get_all('SELECT id, nome_campus FROM campus ORDER BY nome_campus;');

$Result3 = $conn->Execute("SELECT descricao, id FROM tipos_curso ORDER BY 1 DESC;");

$carimbo = new carimbo($param_conn);	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Lista de di&aacute;rios</title>
    <script src="pesquisa_dispensados.js" type="text/javascript"></script>
    <link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    <script src="../../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
	<link href="../../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h2>Relat&oacute;rio de Alunos Dispensados</h2>
<form method="post" name="form1" id="form1" target="_blank">
	
	<input type="image" name="input" 
		src="../../../public/images/icons/print.jpg" 
		alt="Exibir relat&oacute;rio" 
		title="Exibir relat&oacute;rio"
		id="bt_exibir" 
		name="bt_exibir" 
		class="botao" 
		onclick="document.form1.action = 'lista_dispensados.php'" />
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
        	<input name="periodo1" type="text" id="periodo1" size="10" onchange="ChangeCode('periodo1','periodo'); setPeriodo();" />
            <?php  print $Result1->GetMenu('periodo',null,true,false,0,'onchange="ChangeOp()"; setPeriodo();'); ?>
        	<span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>
        </span>
        <br />
        Campus:<br />
        <select id="campus" name="campus" disabled="disabled">
        <?php
          $ref_campus = 0;
          foreach($arr_campi as $campus): 
            if ($_SESSION['sa_campus'] == $campus['nome_campus']) {
              $selected = ' selected="selected"'; 
              $ref_campus = $campus['id'];
            }
            else
              $selected = '';
        ?>
          <option value="<?=$campus['id']?>" <?=$selected?>>
            <?=$campus['nome_campus']?>
          </option>
        <?php endforeach;?>
        </select> 
        <input type="hidden" name="campus_id" id="campus_id" value="<?=$ref_campus?>" />
        <br />
         <h4>Ou</h4>
                Prontu&aacute;rio do aluno:<br />
                &nbsp;&nbsp;<span class="comentario">Se preenchido, os campos anteriores ser&atilde;o ignorados.</span><br />
                <input name="prontuario" type="text" id="prontuario" size="10" />
                <br />


		<br />Assinatura:<br />
		<?php echo $carimbo->listar();?>
	</div>
	
</form>
<!--
<script type="text/javascript">
    
    var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
    
</script>
//-->
</body>
</html>
