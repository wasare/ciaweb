<?php

require_once("../common.php");

function ListaCursos()
{

	$conn = new Connection;

	$conn->open();

	$sql = " SELECT c.id, descricao, t.nome as turno, case when sequencia = 0 then null else sequencia end FROM cursos c, turno t WHERE t.id = c.turno ORDER BY 2, c.id, 4;";
	 
	$query = $conn->CreateQuery($sql);

	$n = $query->GetColumnCount();

	echo("<center><table width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");
	echo ("<tr>");
	echo ("<td bgcolor=\"#000099\" colspan=\"4\" height=\"28\" align=\"center\"><font size=\"3\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#FFFFFF\"><b>Listagem de Cursos Cadastrados</b></font></td>");
	echo ("</tr>");
	 
	echo ("<tr bgcolor=\"#000000\">\n");
	echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Cód</b></font></td>");
	echo ("<td width=\"75%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Descrição</b></font></td>");
	echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Turno</b></font></td>");
	echo ("</tr>");

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
		list ( $id,
		$descricao,
		$turno,
		$sequencia) = $query->GetRowValues();

		$href  = "<a href=\"curso_altera.php?id=$id\">$id</a>";

		if ( $i % 2 )
		{
			$bg = $bg1;
			$fg = $fg1;
		}
		else
		{
			$bg = $bg2;
			$fg = $fg2;
		}
		 
		echo ("<tr bgcolor=\"$bg\">\n");
		echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$href</td>\n");
		echo ("<td width=\"75%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$descricao</td>\n");
		echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">&nbsp;$turno</td>\n");
		echo ("</tr>\n");
		 
		$i++;

	}

	echo("<tr><td colspan=\"4\" align=\"center\"><hr></td></tr>");

	echo("</table></center>");

	$query->Close();

	$conn->Close();
}
?>
<html>
<head>
<title></title>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<form name="Form1">
<p><?php ListaCursos(); ?></p>
<div align="center"><input type="button" name="Button"
	value="  Voltar  " onClick="location='consulta_cursos.php'"></div>
</form>
</body>
</html>
