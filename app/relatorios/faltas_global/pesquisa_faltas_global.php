<?php

require_once("../../../app/setup.php");
require_once("../../../core/reports/carimbo.php");
require_once("../../../core/search.php");

$conn = new connection_factory($param_conn);

$Result1 = $conn->Execute("SELECT descricao, id FROM periodos ORDER BY 1 DESC;");

$carimbo = new carimbo($param_conn);
$busca   = new search('search','codigo_curso','searchlist', 'form1', '../curso_lista.php');


$sa_periodo_id = $_SESSION['sa_periodo_id'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Relat&oacute;rio de Alunos Aprovados/Reprovados</title>
	<link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="../../../lib/prototype.js"></script>
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
	
	function setPeriodo() {
		periodo = $F('periodo1');
	        var url = 'set_periodo.php';
	        var parametros = 'p=' + periodo;
		var myAjax = new Ajax.Request( url, { method: 'post', parameters: parametros, onSuccess: function(transport) { return true; } });
	}

	
	function submit_opt(arq){
		document.form1.action = arq; 
	}
	-->
	</script>
	<script src="../../../lib/functions.js" type="text/javascript"></script>
	<script src="../../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
	<link href="../../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<h2>Relatório de Faltas Global por Per&iacute;odo / Curso</h2>
	<form method="post" name="form1" id="form1" target="_blank">
		<input type="image" name="input" 
			src="../../../public/images/icons/print.jpg" 
			alt="Exibir relat&oacute;rio" 
			title="Exibir relat&oacute;rio"
			id="bt_exibir" 
			name="bt_exibir"  
			class="botao" 
			onclick="document.form1.action = 'lista_faltas_global.php'" />
		<input type="image" name="voltar" 
			src="../../../public/images/icons/back.png" 
			alt="Voltar" 
			title="Voltar" 
			id="bt_voltar" 
			name="bt_voltar" 
			class="botao"
			onclick="history.back(-1);return false;" />
		
		<div  style="height: 400px;">
		<div class="panel">
			Per&iacute;odo:<br />
		    <span id="sprytextfield2">
					<input name="periodo1" type="text" id="periodo1" size="10" value="<?=$sa_periodo_id?>" onchange="ChangeCode('periodo1','periodo'); setPeriodo();" />
				<?php  print $Result1->GetMenu('periodo',null,true,false,0,'onchange="ChangeOp();setPeriodo();"'); ?>
				<span class="textfieldRequiredMsg">Valor obrigat&oacute;rio</span>
			</span>
			<br />
			Curso:<br />
			<span id="sprytextfield1">
				<?php 
			        echo $busca->input_text_retorno("5"); 
			        echo $busca->input_text_consulta("40");
			        echo $busca->area_lista();
				?>
				<span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>
			</span>
			<br />
			C&oacute;digo do Aluno:<br />
		    <input name="aluno" type="text" id="aluno" size="10">
			<span class="comentario">Caso n&atilde;o preenchido exibir&aacute; todos os alunos.</span>
			<br />
			Assinatura:<br />
			<?php echo $carimbo->listar();?>
		</div>
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
