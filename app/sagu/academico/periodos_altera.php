<?php 

require("../common.php");
require("../lib/SQLCombo.php");
require("../lib/InvData.php"); 

$op1_options = SQLArray($sql_periodos_academico);

$id = $_GET['id'];

?>
<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="JavaScript">
function _init()
{
  document.myform.ref_anterior.focus();
}

function ChangeOption(opt,fld)
{
  var i = opt.selectedIndex;

  if ( i != -1 )
    fld.value = opt.options[i].value;
  else
    fld.value = '';
}

function ChangeOp1()
{
  ChangeOption(document.myform.op1,document.myform.ref_anterior);
}

function ChangeOp5()
{
  ChangeOption(document.myform.op5,document.myform.ref_cobranca);
}

function ChangeOp6()
{
  ChangeOption(document.myform.op6,document.myform.ref_local);
}

function ChangeCode(fld_name,op_name)
{
  var field = eval('document.myform.' + fld_name);
  var combo = eval('document.myform.' + op_name);
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
</script>
<?php 

CheckFormParameters(array("id"));


$conn = new Connection;

$conn->Open();

$sql = " select id," .
       "        ref_anterior," .
       "        descricao," .
       "        dt_inicial," .
       "        dt_final," .
       "        fl_livro_matricula," .
       "        media," .
       "        media_final," .
       "        dt_inicio_aula" .
       " from periodos where id = '$id'";

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro n&atilde;o encontrado!");

//esta variavel nas estava recebendo valor do select => $ref_status_vest,

list ($id,
      $ref_anterior,
      $descricao,
      $dt_inicial,
      $dt_final,      
      $fl_livro_matricula,
      $media,
      $media_final,
      $dt_inicio_aula) = $query->GetRowValues();

$query->Close();

$conn->Close();

$dt_inicial = InvData($dt_inicial);
$dt_final = InvData($dt_final);
$dt_inicio_aula = InvData($dt_inicio_aula);

?>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20" onload="_init()">
<form method="post" action="post/periodos_altera.php" name="myform">
  <table width="90%" align="center">
    <tr>
      <td bgcolor="#000099" colspan="2" height="35" align="center"> <font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>&nbsp;Altera&ccedil;&atilde;o de per&iacute;odo letivo</b></font></td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo&nbsp;</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" color="#FF0033">
        <input type="hidden" name="id" value="<? echo($id); ?>">
        <?php echo($id); ?></font></td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Descri&ccedil;&atilde;o&nbsp;&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td>
        <input name="descricao" type=text value="<?echo($descricao);?>" size="40">
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Per&iacute;odo Anterior&nbsp;</font></td>
      <td>
        <input name="ref_anterior" onChange="ChangeCode('ref_anterior','op1')" type=text value="<?echo($ref_anterior);?>" size="12">
        <?PHP ComboArray("op1",$op1_options,"$ref_anterior","ChangeOp1()") ?> </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data Inicial&nbsp;<span class="required">*</span>&nbsp;<br>&nbsp;(dd/mm/aaaa)</font></td>
      <td>
        <input name="dt_inicial" type=text value="<?echo($dt_inicial);?>" size="12" maxlength="10" />
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data Final&nbsp;<span class="required">*</span>&nbsp;<br>&nbsp;(dd/mm/aaaa)</font></td>
      <td>
        <input name="dt_final" type=text value="<?echo($dt_final);?>" size="12" maxlength="10">
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;M&eacute;dia aprova&ccedil;&atilde;o sem reavalia&ccedil;&atilde;o&nbsp;<span class="required">*</span>&nbsp;</font>
      </td>
      <td>
        <input name="media" type=text size="5" maxlength="5" value="<?echo($media);?>">
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;M&eacute;dia final aprova&ccedil;&atilde;o&nbsp;<span class="required">*</span>&nbsp;</font>
      </td>
      <td>
        <input name="media_final" type=text size="5" maxlength="5" value="<? echo($media_final); ?>">
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data in&iacute;cio aulas (Caderno de Chamadas)&nbsp;<span class="required">*</span>&nbsp;<br>&nbsp;(dd-mm-aaaa)</font></td>
      <td>
        <input name="dt_inicio_aula" type=text value="<?echo($dt_inicio_aula);?>" size="10" maxlength="10">
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <hr size="1">
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="submit" name="Submit" value=" Salvar ">
        <input type="button" name="Button" value=" Voltar " onclick="javascript:history.go(-1)">
      </td>
    </tr>
  </table>
</form>
</body>
</html>
