<?php

require("../../common.php");

unset($msg);

$sala = $_POST['sala'];
$campus = $_POST['campus'];
$capacidade = $_POST['capacidade'];

if ( ! is_numeric($capacidade) )
{
	$msg = "A capacidade da sala deve ser um valor numéricos!";
}

if ( isset($msg) )
{
	echo("<script=JavaScript>");
	echo("  alert (\"$msg\");");
	echo("  history.go(-1);");
	echo("</script>");
}

CheckFormParameters( array( "sala",
                              "campus",
                              "capacidade") );

?>
<html>
<head>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<form method="post" action="sala_inclui.php" name="myform">
<table width="90%" align="center" border="0" cellpadding="2"
	cellspacing="2" height="100">
	<tr bgcolor="#000099">
		<td height="35" colspan="2">
		<div align="center"><font size="3"
			face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Confirme
		a Opera&ccedil;&atilde;o</font></b></font><font size="4"
			face="Verdana, Arial, Helvetica, sans-serif"><b></b></font></div>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="35" valign="bottom">
		<div align="right"><font color="#FF0000"
			face="Verdana, Arial, Helvetica, sans-serif" size="3"><b>Inclus&atilde;o
		de Sala</b></font></div>
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td colspan="2"><font color="#FF0000" size="2"
			face="Verdana, Arial, Helvetica, sans-serif">Verifique se os dados
		est&atilde;o corretos.</font></td>
	</tr>
	<tr>
		<td colspan="2">
		<p>&nbsp;</p>
		</td>
	</tr>
	<tr>
		<td bgcolor="#EFEFFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Sala</font></td>
		<td bgcolor="#FFFFEF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"><b>&nbsp;
		<script language="PHP">echo($sala);</script> </b></font></td>
	</tr>
	<tr>
		<td bgcolor="#EFEFFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Campus</font></td>
		<td bgcolor="#FFFFEF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"><b>&nbsp;
		<script language="PHP">echo($campus);</script> </b></font></td>
	</tr>
	<tr>
		<td bgcolor="#EFEFFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Capacidade</font></td>
		<td bgcolor="#FFFFEF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"><b>&nbsp;
		<script language="PHP">echo($capacidade);</script> </b></font></td>
	</tr>
	<tr>
		<td colspan="2"><input type="hidden" name="sala"
			value="<?echo($sala);?>"> <input type="hidden" name="ref_campus"
			value="<?echo($campus);?>"> <input type="hidden" name="capacidade"
			value="<?echo($capacidade);?>"></td>
	</tr>
</table>
<div align="center"><input type="submit" name="Submit" value=" Salvar ">
<input type="button" name="Submit2" value=" Alterar "
	onClick="history.go(-1)"></div>
</form>
</body>
</html>
