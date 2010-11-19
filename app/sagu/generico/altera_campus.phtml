<?php

require("../common.php");

$id = $_GET['id'];

function SQL_Combo($nome,$sql,$default,$onchange)
{
	$conn = new Connection;

	$conn->Open();

	$query = $conn->CreateQuery($sql);

	if ( $onchange != "" )
	echo("<select name=\"$nome\" onchange=\"$onchange\">");
	else
	echo("<select name=\"$nome\">");

	echo("  <option value=\"0\" selected>----- clique aqui -----</option>\n");

	for ( $i=1; $query->MoveNext(); $i++ )
	{
		list ( $text, $value ) = $query->GetRowValues();

		if ( $value == $default )
		echo("  <option value=\"$value\" selected>$text</option>\n");
		else
		echo("  <option value=\"$value\">$text</option>\n");
	}

	echo("</select>");

	$query->Close();

	$conn->Close();
}

CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();

$sql = " select " .
       "    id," .
       "    ref_empresa," .
       "    nome_campus, " .
       "    cidade_campus, " .
       "    ref_campus_sede " .
       " from campus where id = '$id'";

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro n&atilde;o encontrado!");

list ( $id,
$ref_empresa,
$nome_campus,
$cidade_campus,
$ref_campus_sede) = $query->GetRowValues();

$query->Close();
$conn->Close();

?>
<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="JavaScript">
function ChangeOption(opt,fld)
{
  var i = opt.selectedIndex;

  if ( i != -1 )
    fld.value = opt.options[i].value;
  else
    fld.value = '';
}

function ChangeOp()
{
  ChangeOption(document.myform.op,document.myform.ref_empresa);
}

function ChangeOpCampus()
{
  ChangeOption(document.myform.opcampus,document.myform.ref_campus_sede);
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

  alert(code + ' n&atilde;o &eacute; um c&oacute;digo v&aacute;lido!');

  field.focus();

  return true;
}
</script>

</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<form method="post" action="post/altera_campus.php" name="myform">
<table width="90%" align="center">
	<tr bgcolor="#000099"align "center">
		<td height="35" colspan="2" align="center"><font size="3"
			face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Altera&ccedil;&atilde;o
		de Campus</font></b></font></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo&nbsp;</font></td>
		<td><font face="Verdana, Arial, Helvetica, sans-serif" size="2"
			color="#0000FF"><? echo($id); ?> <input type="hidden" name="id"
			value="<? echo($id); ?>"> </font></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Empresa&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="ref_empresa"
			onChange="ChangeCode('ref_empresa','op')" type=text
			value="<?=$ref_empresa?>" size="12"> <?=SQL_Combo("op","select substr(razao_social, 0, 120),id from configuracao_empresa order by razao_social","$ref_empresa","ChangeOp()"); ?>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome
		do Campus&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="nome_campus" type=text size="50"
			value="<? echo($nome_campus); ?>"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome
		da Cidade&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="cidade_campus" type=text size="50"
			value="<? echo($cidade_campus); ?>"></td>
	</tr>
        <tr>
        <td bgcolor="#CCCCFF"><font
            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Campus Sede&nbsp;<span class="required">*</span>&nbsp;</font></td>
        <td><input name="ref_campus_sede" type=text size="10" maxlength="10" value="<?=$ref_campus_sede?>" 
            onChange="ChangeCode('ref_campus_sede','opcampus')"> <font color="#000000">  
            <?=SQL_Combo("opcampus","select substr(nome_campus, 0, 120), id, cidade_campus from campus order by cidade_campus","$ref_campus_sede","ChangeOpCampus()");?></font></td>
    </tr> 
	<tr>
		<td colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2"><input type="submit" name="Submit"
			value=" Salvar "> <input type="button" name="Submit2"
			value=" Voltar " onclick="javascript:history.go(-1)"></td>
	</tr>
</table>
</form>
</body>
</html>
