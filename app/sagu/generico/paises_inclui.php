<?php

require("../common.php");

?>

<html>
<head>

<script language="JavaScript">
function Confirma_Exclui(arg1)
{
  url = 'post/paises_exclui.php?id=' + arg1;

  if (confirm("Você tem certeza que deseja EXCLUIR o País: "+arg1+" ?"))
    location=(url)
  else
    alert("Exclusão Cancelada.");
}
</script>

<?php

function MostraPaises()
{
	$conn = new Connection;

	$conn->Open();

	$sql= "select id, nome from pais order by nome";

	$query = $conn->CreateQuery($sql);

	$n = $query->GetColumnCount();

	echo("<br><center><table width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");

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
		$href  = "<a href=\"altera_pais.php?id=$registro\"><img src=\"../images/update.gif\" title='Alterar Cadastro' align='absmiddle' border=0></a>";
		$href1 = "<a href=\"javascript:Confirma_Exclui('$registro')\"><img src=\"../images/delete.gif\" title='Excluir Pais' align='absmiddle' border=0></a>";
		if ($i == 1)
		{

			echo("<tr bgcolor=\"#000000\">\n");
			echo ("<td width=\"30%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Código</b></font></td>");
			echo ("<td width=\"60%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Descrição</b></font></td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>&nbsp;</b></font></td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>&nbsp;</b></font></td>");
			echo("  </tr>");
		}


		if ( $i % 2)
		{

			echo("<tr bgcolor=\"$bg1\">\n");
			echo ("<td width=\"30%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">" . $query->GetValue(1) . "</td>");
			echo ("<td width=\"60%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">" . $query->GetValue(2) . "</td>");
			echo ("<td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$bg1\">$href</font></b></td>\n");
			echo ("<td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$bg1\">$href1</font></b></td>\n");
			echo("  </tr>");

		}
		else
		{
			echo("<tr bgcolor=\"$bg2\">\n");
			echo ("<td width=\"30%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">" . $query->GetValue(1) . "</td>");
			echo ("<td width=\"60%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">" . $query->GetValue(2) . "</td>");
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
?>
<link href="../estilo.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<form method="post" action="post/confirm_paises_inclui.php"
	name="myform">
	<div class="titulo" align="center"><h2>Pa&iacute;ses</h2></div>
<table border="0" width="90%" align="center" cellspacing="2" height="40"
	align="center">
	<tr bgcolor="#000099">
		<td height="35" colspan="2">
		<div align="center"><font size="3"
			face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Inclus&atilde;o
		de Países</font></b></font></div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">Nome</font></td>
		<td><input name="nome" type=text size="50"></td>
	</tr>
	<tr>
		<td colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2"><input type="submit" name="Submit"
			value=" Prosseguir "> <input type="reset" name="Submit2"
			value="   Limpar   "></td>
	</tr>
	<tr>
		<td colspan="2">
		<div align="center"><script language="PHP">
		    MostraPaises();
   	      </script></div>
		</td>
	</tr>
</table>
</form>
</body>
</html>
