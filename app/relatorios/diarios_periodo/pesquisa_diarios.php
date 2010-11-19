<?php

require_once("../../../app/setup.php");
require_once("../../../core/reports/carimbo.php");

$conn = new connection_factory($param_conn);

$carimbo = new carimbo($param_conn);

$Result1 = $conn->Execute("SELECT descricao, id FROM periodos ORDER BY 1 DESC;");

$Result2 = $conn->Execute("SELECT nome_campus, id FROM campus ORDER BY 1 DESC;");
	
$Result3 = $conn->Execute("SELECT descricao, id FROM tipos_curso ORDER BY 1 DESC;");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Lista de di&aacute;rios</title>
    <link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    <script src="../../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
    <script language="javascript">
        <!--
        function ChangeOption(opt,fld){
            var i = opt.selectedIndex;
            if ( i != -1 )
                fld.value = opt.options[i].value;
            else
                fld.value = '';
        }

        function ChangeOp() {
            ChangeOption(document.form1.periodo,document.form1.periodo1);
        }

        function ChangeCode(fld_name,op_name){
            var field = eval('document.form1.' + fld_name);
            var combo = eval('document.form1.' + op_name);
            var code  = field.value;
            var n     = combo.options.length;
            for ( var i=0; i<n; i++ )
            {
                if ( combo.options[i].value == code )
                {
                    combo.selectedIndex = i;
                    return;
                }
            }
            alert(code + ' não é um código válido!');
            field.focus();
            return true;
        }
        -->
    </script>
	<link href="../../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<h2>Relat&oacute;rio de Di&aacute;rios</h2>
	<form method="post" action="lista_diarios.php" name="form1" target="_blank">
		<input type="image" name="input" 
			src="../../../public/images/icons/print.jpg" 
			alt="Exibir relat&oacute;rio" 
			title="Exibir relat&oacute;rio"
			id="bt_exibir" 
				name="bt_exibir"  
			class="botao" 
			onclick="document.form1.action = 'lista_diarios.php'" />
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
                <input name="periodo1" type="text" id="periodo2" size="10" onchange="ChangeCode('periodo1','periodo')" />
                <?php  print $Result1->GetMenu('periodo',null,true,false,0,'onchange="ChangeOp()"'); ?>
				<span class="textfieldRequiredMsg">Valor obrigat&oacute;rio.</span>
			</span>
			<br />
			Tipo de curso:<br />
			<?php print $Result3->GetMenu('tipo',null,true,false,0); ?>
			<span class="comentario">Caso n&atilde;o selecionado exibir&aacute; todos.</span>
			<br />
			Campus:<br />
            <?php print $Result2->GetMenu('campus',null,true,false,0); ?>
			<span class="comentario">Caso n&atilde;o selecionado exibir&aacute; todos.</span>
			<br />
			Assinatura:<br />
			<?php echo $carimbo->listar();?>
		</div>
	</form>
	<script type="text/javascript">
	<!--
		var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
	//-->
	</script>
</body>
</html>
