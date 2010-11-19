<?php 

require_once("../../app/setup.php");

$Conexao = NewADOConnection("postgres");
$Conexao->PConnect("host=$host dbname=$database port=$port user=$user password=$password");

$Result1 = $Conexao->Execute("SELECT descricao, id FROM periodos ORDER BY 1 DESC;");

if (!$Result1){
    print $Conexao->ErrorMsg();
    die();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="javascript">
</script>
<link href="../../public/styles/formularios.css" rel="stylesheet"	type="text/css" />
<title>SA</title>
    <script src="../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
    <link href="../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
    <script language="javascript" src="../../lib/prototype.js"></script>
    <script src="../../lib/functions.js" type="text/javascript"></script>
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

            alert(code + ' n�o � um c�digo v�lido!');

            field.focus();

            return true;
        }

       
        function setPeriodo() {
        	periodo = $F('periodo1');
        	var url = 'set_periodo.php';
        	var parametros = 'p=' + periodo;
        	var myAjax = new Ajax.Request( url, { method: 'post', parameters: parametros, onSuccess: function(transport) { return true; } });
	}

    -->
    </script>
</head>
<body>
<div align="center" style="height: 600px;">
<h1>Cola&ccedil;&atilde;o de grau</h1>
<div class="panel">
<form method="post" name="form1" action="lista_alunos.php">
<table>
<tr>
<td>Curso:</td>
<td>
<span id="sprytextfield2">
   <input name="codigo_curso" type="text" id="codigo_curso" size="10" />
   <input name="descricao_curso" disabled="disabled" id="descricao_curso" value="" size="40" />
   <a href="javascript:abre_consulta_rapida('../consultas_rapidas/cursos/index.php')"><img src="../../public/images/icons/lupa.png" alt="Pesquisar usu&aacute;rio" width="20" height="20" /></a>
   <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>
</span>
</td>
</tr>
<tr>
<td>Per&iacute;odo de in&iacute;cio:</td>
<td>
<span id="sprytextfield1">
   <input name="periodo1" type="text" id="periodo1" size="10" onchange="ChangeCode('periodo1','periodo'); setPeriodo();" />
   <?php  print $Result1->GetMenu('periodo',null,true,false,0,'onchange="ChangeOp()"; setPeriodo();'); ?>
   <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>
</span>
</td>
</tr>
</table>
<p align="center">
<input type="submit" value="Avan&ccedil;ar" />
</p>
</form>
<script type="text/javascript">
    <!--
    var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
    var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
    //-->
</script>
</div>
</div>
</body>
</html>
