<?php

require("../common.php");

?>
<html>
<head>
 <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="JavaScript">
function buscaCidade()
{
  var url;
   url = '../generico/post/lista_cidades.php' + 
         '?cnome=' + escape(document.myform.cnome.value);

   window.open(url,"popWindow","toolbar=no,width=600,height=368,top=5,left=5,directories=no,menubar=no,scrollbars=yes");
}


function setResult(id,nome,cep)
{
   document.myform.ref_cidade.value = id;
   document.myform.cnome.value = nome;
   document.myform.cep.value = cep;
}

function Confirma_Exclui(arg1)
{
  url = 'post/configuracao_empresa_exclui.php?id=' + arg1;

  if (confirm("Tem certeza que deseja EXCLUIR o Empresa: "+arg1+" ?"))
    location=(url)
  else
    alert("Procedimento cancelado.");
}

</script>

<?php

function MostraConfiguracao()
{
	$conn = new Connection;

	$conn->Open();

	$sql= "select id, razao_social from configuracao_empresa";

	$query = $conn->CreateQuery($sql);

	$n = $query->GetColumnCount();

	echo("<center><table width=\"90%%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");
	echo("<tr bgcolor=\"#000000\">\n");
	echo ("<td width=\"20%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>C&oacute;digo</b></font></td>");
	echo ("<td width=\"70%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Descri&ccedil;&atilde;o</b></font></td>");
	echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\">&nbsp;</font></td>");
	echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\">&nbsp;</font></td>");
	echo("  </tr>");

	$i=1;

	// cores fundo
	$bg0 = "#000000";
	$bg1 = "#EEEEFF";
	$bg2 = "#FFFFEE";

	// cores fonte
	$fg0 = "#FFFFFF";
	$fg1 = "#000099";
	$fg2 = "#000099";

	while( $query->MoveNext() )
	{

		$registro = $query->GetValue(1);
		$href  = "<a href=\"altera_configuracao_empresa.php?id=$registro\"><img src=\"../images/update.gif\" title='Alterar Cadastro' align='absmiddle' border=0></a>";
		$href1 = "<a href=\"javascript:Confirma_Exclui('$registro')\"><img src=\"../images/delete.gif\" title='Excluir Cadastro' align='absmiddle' border=0></a>";

		if ( $i % 2)
		{

			echo("<tr bgcolor=\"$bg1\">\n");
			echo ("<td width=\"20%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">" . $query->GetValue(1) . "</td>");
			echo ("<td width=\"70%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">" . $query->GetValue(2) . "</td>");
			echo ("<td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$bg1\">$href</font></b></td>\n");
			echo ("<td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$bg1\">$href1</font></b></td>\n");


			echo("  </tr>");

		}
		else
		{
			echo("<tr bgcolor=\"$bg2\">\n");
			echo ("<td width=\"20%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">" . $query->GetValue(1) . "</td>");
			echo ("<td width=\"70%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">" . $query->GetValue(2) . "</td>");
			echo ("<td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$bg1\">$href</font></b></td>\n");
			echo ("<td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$bg1\">$href1</font></b></td>\n");

			echo("  </tr>");
		}

		$i++;

	}

	echo("</table></center>");
	$query->Close();
	$conn->Close();
}

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
?>
<link href="../estilo.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<form method="post" action="post/confirm_configuracao_inclui.php"
	name="myform">
	<div class="titulo" align="center"><h2>Empresas</h2></div>
<table width="90%" align="center">
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Raz&atilde;o
		Social&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="razao_social" type=text size="50" maxlength="199"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Sigla&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="sigla" type=text size="10" maxlength="29"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Rua&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="rua" type=text size="50" maxlength="50"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Complemento&nbsp;</font></td>
		<td><input name="complemento" type=text size="50" maxlength="50"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Bairro&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="bairro" type=text></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cidade&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="9%">
				<div align="left"><input type="text" name="ref_cidade" size="5"
					maxlength="10"></div>
				</td>
				<td width="100%">
				<div align="left"><font color="#000000"> <input type="text"
					name="cnome" size="35"> </font></div>
				</td>
				<td width="0"><font color="#000000"><font color="#000000"> <font
					color="#000000"> <input type="button" value="..."
					onClick="buscaCidade(1)" name="button"> </font></font></font></td>
			</tr>
		</table>

		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cep&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="cep" type=text size="9" maxlength="9"></td>
	</tr>
	<tr>
		<td colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="Submit"
			value=" Prosseguir "> <input type="reset" name="Submit2"
			value="   Limpar   "> </td>
	</tr>
	<tr>
		<td colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<div align="center"><script language="PHP">
            MostraConfiguracao();
          </script></div>
		</td>
	</tr>
</table>
</form>
</body>
</html>
