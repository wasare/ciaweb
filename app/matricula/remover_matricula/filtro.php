<?php

header("Cache-Control: no-cache");

//INCLUSAO DE BIBLIOTECAS
require_once("../../../app/setup.php");


//Criando a classe de conex�o
$Conexao = NewADOConnection("postgres");
	
//Setando como conex�o persistente
$Conexao->PConnect("host=$host dbname=$database port=$port user=$user password=$password");

//EXECUTANDO SQL COM ADODB
$Result1 = $Conexao->Execute("SELECT descricao, id FROM periodos ORDER BY 1 DESC;");

//Se Result1 falhar	
if (!$Result1){
	print $Conexao->ErrorMsg();			
    die();
}	

$sa_periodo_id = $_SESSION['sa_periodo_id'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SA</title>
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

  alert(code + ' n�o � um c�digo v�lido!');

  field.focus();

  return true;
}


function submit_opt(arq){

	document.form1.action = arq; 

}

-->
</script>
<script src="../../../lib/functions.js" type="text/javascript"></script>
<link href="../../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
</head>
<body>
<form id="form1" name="form1" method="post" action="lista_cursos.php">
  <div align="center">
    <h1>Excluir Matr&iacute;cula</h1>
    <div class="panel">
      Per&iacute;odo:<br />
      <span id="sprytextPeriodo">
      <input name="periodo1" type="text" id="periodo2" size="10" value="<?=$sa_periodo_id?>" 
onchange="ChangeCode('periodo1','periodo')" />
      <?php  print $Result1->GetMenu('periodo',null,true,false,0,'onchange="ChangeOp()"'); ?>
      <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span></span> 
      <br />
      C&oacute;digo do Aluno:<br />
      <span id="sprytextPessoa">
      <input type="text" name="codigo_pessoa" id="codigo_pessoa" size="10" />
      <input type="text" name="nome_pessoa" id="nome_pessoa" size="35" />
      <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span></span>
      <a href="javascript:abre_consulta_rapida('../../consultas_rapidas/pessoas/index.php')">
      <img src="../../../public/images/icons/lupa.png" alt="Pesquisar usu&aacute;rio" width="20" height="20" />
      </a>
      <br /><br />
      <div align="center">
        <input type="submit" name="avancar" id="avancar" value="Avan&ccedil;ar" />
        <input type="button" value="  Voltar  " onclick="javascript:history.back(-1)" name="Button" />
      </div>
    </div>
  </div>
</form>
<p>&nbsp;</p>
<script type="text/javascript">
<!--
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextPessoa");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextPeriodo");
//-->
</script>
</body>
</html>
