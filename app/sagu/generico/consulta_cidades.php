<?php

require("../common.php");


$id   = $_POST['id'];
$desc = $_POST['desc'];


?>

<html>
<head>
<title>Cidades</title>
<script language="JavaScript">
            function _init()
            {
                document.myform.id.focus();
            }
        </script>
<?php

$hasmore = false;

function ListaCidades()
{
	global $id, $desc, $hasmore;
	global $like;

	// $desc = strtoupper($desc);

	$count = 0;

	$like = "";

	if ( $id != "" )
	$like = "$id";

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
		$sql = "select id, nome, ref_estado" .
                 "  from cidade";

		$where = '';

		if ( $id != '' )
		{
			$where .= ( $where == '' ) ? ' where ' : ' and ';
			$where .= "upper(id) = upper('$id')";
		}

		if ( $desc != '' )
		{
			//$where .= ( $where == '' ) ? ' where ' : ' and ';
			//$where .= "upper(nome) like upper('$desc%')";
			$where .= " WHERE lower(to_ascii(nome,'LATIN1')) ";
			$where .= " SIMILAR TO lower(to_ascii('$desc%','LATIN1')) ";
		}

		$sql .= $where . " order by nome";

		$query = $conn->CreateQuery($sql);

		echo("<table width=\"490\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");

		echo("  <tr bgcolor=\"$bg0\">\n");
		echo("    <td width=\"20%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Código</font></b></td>\n");
		echo("    <td width=\"65%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Nome</font></b></td>\n");
		echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">UF</font></b></td>\n");
		echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">&nbsp;</font></b></td>\n");
		echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">&nbsp;</font></b></td>\n");
		echo("  </tr>\n");

		for ( $i=0; $i<25 && $query->MoveNext(); $i++ )
		{

			list ( $id,$nome,$uf ) = $query->GetRowValues();

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

			if ( empty($campo) )
			$campo = '';

			$registro = $query->GetValue(1);
			$href  = "<a href=\"altera_cidade.php?id=$id\"><img src=\"../images/update.gif\" title='Alterar Cadastro' align='absmiddle' border=0></a>";
			$href1 = "<a href=\"javascript:Confirma_Exclui('$registro')\"><img src=\"../images/delete.gif\" title='Excluir Cidade' align='absmiddle' border=0></a>";


			echo("  <tr bgcolor=\"$bg\">\n");
			echo("    <td width=\"20%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$id</font></b></td>\n");
			echo("    <td width=\"65%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$nome</font></b></td>\n");
			echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$uf</font></b></td>\n");
			echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$href</font></b></td>\n");
			echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$href1</font></b></td>\n");
			echo("  </tr>\n");
		}

		echo("</table>");

		$hasmore = $query->MoveNext();

		$query->Close();

		$conn->Close();

		$count = $i;
	}

	else
	echo("<br><center><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=red><b>Informe um campo para fazer a pesquisa!</b></font></center><br>");

	return $count;
}
?>
<script language="JavaScript">
            function Confirma_Exclui(arg1)
            {
                url = 'post/cidade_exclui.php?id=' + arg1;

                if (confirm("Você tem certeza que deseja EXCLUIR a Cidade: "+arg1+" ?"))
                    location=(url)
                else
                    alert("Exclusão Cancelada.");
            }
</script>
<link href="../estilo.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<form method="post" action="consulta_cidades.php" name="myform">
<div align="center">
<div class="titulo"><h2>Cidades</h2></div>
<p><input type="button" value=" Incluir "
	onClick="location='cidades_inclui.php'" name="button"></p>
</div>
<hr align="center" width="500">
<p align="center"><font face="Verdana, Arial, Helvetica, sans-serif"
	size="2" color="#0000FF"><b><font color="#FF0000">CUIDADO PARA
N&Atilde;O DUPLICAR CADASTROS !</font></b></font></p>
<div align="center">
<table width="500" border="0" cellspacing="0" cellpadding="2"
	height="100">
	<tr bgcolor="#0066CC">
		<td colspan="4" height="28">
		<div align="center"><font size="2" color="#FFFFFF"><b><font
			face="Verdana, Arial, Helvetica, sans-serif">Consulta/Altera&ccedil;&atilde;o
		de Cidades</font></b></font></div>
		</td>
	</tr>
	<tr>
		<td width="6">&nbsp;</td>
		<td width="50"><font face="Verdana, Arial, Helvetica, sans-serif"
			size="2">C&oacute;digo:</font></td>
		<td width="271"><font face="Verdana, Arial, Helvetica, sans-serif"
			size="2">Descri&ccedil;&atilde;o:</font></td>
		<td width="40">&nbsp;</td>
	</tr>
	<tr>
		<td width="6">&nbsp;</td>
		<td width="50"><input type="text" name="id" size="8"
			value="<?echo($id)?>"></td>
		<td width="271"><input type="text" name="desc" size="40"
			value="<?echo($desc)?>"></td>
		<td width="40"><input type="submit" name="botao" value="Localizar"></td>
	</tr>
	<tr>
		<td colspan="4" align="center">
		<hr size="1" width="490">
		</td>
	</tr>
	<tr>
		<td colspan="4"><?php ListaCidades(); ?> <font
			face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font
			color="#FF0000"> </font><font
			face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font
			color="#FF0000"><?php  if ( $hasmore )
			echo("<BR><center>Se a Cidade não estiver listada, seja mais específico.</center>"); ?></font></b></font></font><font
			color="#FF0000"> </font></b></font></font></td>
	</tr>
	<tr align="center">
		<td colspan="4">
		<hr size="1" width="490">
		</td>
	</tr>
</table>
</div>
</form>
</body>
</html>
