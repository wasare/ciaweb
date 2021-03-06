<?php

require_once("../common.php");

$conn = new connection_factory($param_conn);

// Verifica as permissoes de acesso do usuario quanto ao arquivo
$ACL_FILE = __FILE__;
require_once($BASE_DIR .'core/login/acesso.php');
// ^ Verifica as permissoes de acesso do usuario quanto ao arquivo ^ //

$ref_disciplina = $_POST['ref_disciplina'];
$desc = $_POST['desc'];

$hasmore = false;

function ListaDisciplinas()
{
	global $ref_disciplina, $desc, $hasmore, $limite_list;
	global $like;

	$desc = strtoupper($desc);

	$like = "";

	if ( $ref_disciplina != "" )
	$like = "$ref_disciplina";

	if ( $desc != "" )
	$like = "$like% $des%";

	else if ( $like != "" )
	$like = "$like%";
	 
	if ( $like != "" )
	{
		// $hasmore = true;

		// cores fundo
		$bg0 = "#000000";
		$bg1 = "#EEEEFF";
		$bg2 = "#FFFFEE";

		// cores fonte
		$fg0 = "#FFFFFF";
		$fg1 = "#000099";
		$fg2 = "#000099";

		$conn = new Connection;

		$conn->Open();

		// note the parantheses in the where clause !!!
		$sql = " select id, " .
           "        ref_disciplina, " .
           "        descricao_disciplina(ref_disciplina), " .
           "        ref_disciplina_equivalente, " .
           "        descricao_disciplina(ref_disciplina_equivalente), " .
           "        ref_curso " .
           " from disciplinas_equivalentes";

		$where = '';

		if ( $ref_disciplina != '' )
		{
			$where .= ( $where == '' ) ? ' where ' : ' and ';
			$where .= "ref_disciplina = $ref_disciplina";
		}

		if ( $desc != '' )
		{
			$where .= ( $where == '' ) ? ' where ' : ' and ';
			$where .= "upper(descricao_disciplina(ref_disciplina)) like upper('$desc%')";
		}

		$sql .= $where . " order by descricao_disciplina(ref_disciplina)";

		$query = $conn->CreateQuery($sql);

		echo("<table width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");

		echo("  <tr bgcolor=\"$bg0\">\n");
		echo("    <td width=\"10%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Código</font></b></td>\n");
		echo("    <td width=\"30%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Disciplina</font></b></td>\n");
		echo("    <td width=\"10%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Código</font></b></td>\n");
		echo("    <td width=\"30%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Equivalente</font></b></td>\n");
		echo("    <td width=\"10%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Curso</font></b></td>\n");
		echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">&nbsp;</font></b></td>\n");
		echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">&nbsp;</font></b></td>\n");
		echo("  </tr>\n");

		for ( $i=0; $i<$limite_list && $query->MoveNext(); $i++ )
		{

			list ( $id,
			$ref_disciplina,
			$nome_disciplina,
			$ref_disciplina_equivalente,
			$nome_disciplina_equivalente,
			$ref_curso) = $query->GetRowValues();

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

			$href  = "<a href=\"altera_disciplinas_equivalentes.php?id=$id\"><img src=\"../images/update.gif\" title='Alterar Disciplina Equivalente' align='absmiddle' border=0></a>";
			$href1 = "<a href=\"javascript:Confirma_Exclui('$id','$ref_disciplina')\"><img src=\"../images/delete.gif\" title='Excluir Disciplina Equivalente' align='absmiddle' border=0></a>";


			echo("  <tr bgcolor=\"$bg\">\n");
			echo("    <td width=\"10%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$ref_disciplina</font></b></td>\n");
			echo("    <td width=\"30%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$nome_disciplina</font></b></td>\n");
			echo("    <td width=\"10%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$ref_disciplina_equivalente</font></b></td>\n");
			echo("    <td width=\"30%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$nome_disciplina_equivalente</font></b></td>\n");
			echo("    <td width=\"10%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$ref_curso</font></b></td>\n");
			echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$href</font></b></td>\n");
			echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$href1</font></b></td>\n");
			echo("  </tr>\n");
		}

		echo("</table>");

		$hasmore = $query->MoveNext();

		$query->Close();

		$conn->Close();

	}

	else
	echo("<br><center><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=red><b>Informe um campo para fazer a pesquisa!</b></font></center><br>");
}
?>
<html>
<head>
<title>Disciplinas Equivalentes</title>
<script language="JavaScript">
function _init()
{
  document.myform.id.focus();
}

function Confirma_Exclui(arg1, arg2)
{
  url = 'post/disciplinas_equivalentes_exclui.php?id=' + arg1;

  if (confirm("Você tem certeza que deseja EXCLUIR a Equivalência da Disciplina: "+arg2+" ?"))
    location=(url)
  else
    alert("Exclusão Cancelada.");
}
</script>
<link href="../estilo.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<div class="titulo" align="center">
<h2>Disciplinas equivalentes</h2>
</div>
<form method="post" action="lista_disciplinas_equivalentes.php"
	name="myform1">
<table width="90%" border="0" cellspacing="0" cellpadding="2"
	align="center">
	<div align="center">
	<p><input type="button" value="  Incluir  "
		onClick="location='inclui_disciplinas_equivalentes.php'"
		name="button"></p>
	</div>
	<hr align="center">
	<p align="center"><font face="Verdana, Arial, Helvetica, sans-serif"
		size="2" color="#0000FF"><b><font color="#FF0000">CUIDADO PARA
	N&Atilde;O DUPLICAR CADASTROS !</font></b></font></p>
	<div align="center">
	
	
	<tr bgcolor="#0066CC">
		<td height="28">
		<div align="center"><font size="2" color="#FFFFFF"><b><font
			face="Verdana, Arial, Helvetica, sans-serif">Consulta/Altera&ccedil;&atilde;o
		de Disciplinas Equivalentes por Curso</font></b></font></div>
		</td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="center"><font face="Verdana, Arial, Helvetica, sans-serif"
			size="2">Código do Curso:&nbsp;&nbsp;</font> <input type="text"
			name="ref_curso" size="10"> <input type="submit" value="Consultar"
			name="button"><br>
		</td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td align="center"><font face="Verdana, Arial, Helvetica, sans-serif"
			size="2">(Deixe em branco para listar todos os cursos)</font></td>
	</tr>
</table>
</form>
<form method="post" action="consulta_disciplinas_equivalentes.php"
	name="myform2">
<table width="90%" border="0" cellspacing="0" cellpadding="2"
	align="center">
	<tr bgcolor="#0066CC">
		<td colspan="5" height="28">
		<div align="center"><font size="2" color="#FFFFFF"><b><font
			face="Verdana, Arial, Helvetica, sans-serif">Consulta/Altera&ccedil;&atilde;o
		de Disciplinas Equivalentes por Disciplina</font></b></font></div>
		</td>
	</tr>
	<tr>
		<td><font face="Verdana, Arial, Helvetica, sans-serif" size="2">C&oacute;digo:</font></td>
		<td><input type="text" name="ref_disciplina" size="8"
			value="<?echo($ref_disciplina)?>"></td>
		<td><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Descri&ccedil;&atilde;o:</font></td>
		<td><input type="text" name="desc" size="40" value="<?echo($desc)?>">
		</td>
		<td><input type="submit" name="botao" value="Localizar"></td>
	</tr>
	<tr>
		<td colspan="5" align="center">
		<hr>
		</td>
	</tr>
	<tr>
		<td colspan="5"><?php ListaDisciplinas(); ?> <font
			face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font
			face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font
			color="#FF0000"> <?php
			if ( $hasmore )
			echo("<BR><center>Se a Disciplina não estiver listada, seja mais específico.</center>");
			?> </font></b></font></font></b></font></font></td>
	</tr>
	<tr align="center">
		<td colspan="5">
		<hr>
		</td>
	</tr>
</table>
</form>
</body>
</html>
