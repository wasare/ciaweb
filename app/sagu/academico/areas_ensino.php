<?php

require("../common.php");

function Mostra_Areas_Ensino()
{

	$conn = new Connection;

	$conn->Open();

	$sql= "select id, area from areas_ensino order by area";

	$query = $conn->CreateQuery($sql);

	$n = $query->GetColumnCount();

	echo("<center><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");

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

		if ($i == 1)
		{

			echo("<tr bgcolor=\"#000000\">\n");
			echo ("<td width=\"20%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Código</b></font></td>");
			echo ("<td width=\"70%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Descrição da &Aacute;rea de Ensino</b></font></td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>&nbsp;</b></font></td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>&nbsp;</b></font></td>");
			echo("  </tr>");
		}

		$registro = $query->GetValue(1);
		$href  = "<a href=\"areas_ensino_altera.php?id=$registro\"><img src=\"../images/update.gif\" title='Alterar &Aacute;rea' align='absmiddle' border=0></a>";
		$href1 = "<a href=\"javascript:Confirma_Exclui($registro)\"><img src=\"../images/delete.gif\" title='Excluir &Aacute;rea' align='absmiddle' border=0></a>";

		if ( $i % 2)
		{

			echo("<tr bgcolor=\"$bg1\">\n");
			echo ("<td width=\"20%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">" . $query->GetValue(1) . "</td>");
			echo ("<td width=\"70%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">" . $query->GetValue(2) . "</td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">" . $href . "</td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">" . $href1 . "</td>");
			echo("  </tr>");

		}
		else
		{
			echo("<tr bgcolor=\"$bg2\">\n");
			echo ("<td width=\"20%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">" . $query->GetValue(1) . "</td>");
			echo ("<td width=\"70%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">" . $query->GetValue(2) . "</td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">" . $href . "</td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">" . $href1 . "</td>");
			echo("  </tr>");
		}

		$i++;

	}

	echo("</table></center>");

	$query->Close();

	$conn->Close();
}


Function Exclui_Area_Ensino($arg)
{
	$conn = new Connection;

	$conn->Open();

	$sql = "delete from areas_ensino where id='$arg';";

	$ok = $conn->Execute($sql);  // tire o @ para visualizar mensagens de error do sistema DB

	$conn->Close();

	SaguAssert($ok,"Não foi possível de excluir o registro!");

}
?>
<html>
<head>
<script language="JavaScript">
function _init()
{
  document.myform.descricao.focus();
}

function Confirma_Exclui(arg1)
{
  url = 'post/areas_ensino_exclui.php?id=' + arg1;

  if (confirm("Você tem certeza que deseja excluir a Área de Ensino: "+arg1))
    location=(url)
  else
    alert("Exclusão Cancelada.");
}
</script>
<link href="../estilo.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<form method="post" action="post/areas_ensino.php" name="myform">
<div class="titulo" align="center"><h2>&Aacute;reas de ensino</h2></div>
<table width="90%" align="center">
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">Descri&ccedil;&atilde;o</font></td>
		<td><input name="area" type=text size="35"></td>
	</tr>
	<tr>
		<td colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td height="33" colspan="2">
		<div align="center"><input type="submit" name="Submit"
			value=" Incluir "> <input type="reset" name="Submit2"
			value=" Limpar "></div>
		</td>
	</tr>
	<tr>
		<td height="13" colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td height="33" colspan="2">
		<div align="center"><?php Mostra_Areas_Ensino(); ?></div>
		</td>
	</tr>
</table>
</form>
</body>
</html>
