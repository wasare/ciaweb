<?php 

require("../common.php");

$id = $_POST['id'];
$desc = $_POST['desc'];


$hasmore = false;

function ListaProfessores()
{
	global $id, $desc, $hasmore, $nome;
	global $like;

	$desc = strtoupper($desc);

	$count = 0;

	$like = "";

	if ( $id != "" )
	$like = "$id";

	if ( $desc != "" )
	$like = "$desc%";

	else if ( $like != "" )
	$like = "$like%";

	if ( $like != "" )
	{
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

		$sql = " select A.id, " .
               "        A.ref_professor, " .
               "        B.nome" .
               " from professores A, pessoas B " .
               " where A.ref_professor = B.id ";

		$where = '';

		if ( $id != '' )
		{
			$where .= "and A.ref_professor = '$id'";
		}

		if ( $desc != '' )
		{
			$where .= "and upper(B.nome) like upper('$desc%')";
		}

		$sql .= $where . " order by B.nome";

		$query = $conn->CreateQuery($sql);

		echo("<table width=\"490\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");

		echo("  <tr bgcolor=\"$bg0\">\n");
		echo("    <td width=\"20%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Código</font></b></td>\n");
		echo("    <td width=\"70%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Nome do Aluno</font></b></td>\n");
		echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">&nbsp;</font></b></td>\n");
		echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">&nbsp;</font></b></td>\n");
		echo("  </tr>\n");

		for ( $i=0; $i<25 && $query->MoveNext(); $i++ )
		{
			list ($id_professor,
			$ref_professor,
			$nome ) = $query->GetRowValues();

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

			$href  = "<a href=\"professores_edita.php?id=$id_professor\"><img src=\"../images/update.gif\" title='Alterar Cadastro' align='absmiddle' border=0></a>";
			$href1 = "<a href=\"javascript:Confirma_Exclui('$id_professor','$ref_professor')\"><img src=\"../images/delete.gif\" title='Excluir' align='absmiddle' border=0></a>";

			echo("  <tr bgcolor=\"$bg\">\n");
			echo("    <td width=\"20%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$ref_professor</font></b></td>\n");
			echo("    <td width=\"70%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$nome</font></b></td>\n");
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
<html>
<head>
<title>Cadastrar Professores</title>
<script language="JavaScript">

function _init()
{
    document.selecao.id.focus();
}

function Confirma_Exclui(arg1, arg2)
{
    url = 'post/professor_exclui.php?id=' + arg1;

    if (confirm("Você tem certeza que deseja EXCLUIR o Professor: "+arg2))
        location=(url)
    else
        alert("Exclusão Cancelada.");
}
</script>
<link href="../estilo.css" rel="stylesheet" type="text/css" />
</head>

<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<form method="post" action="consulta_inclui_professores.php"
	name="selecao">
<div align="center">
<div class="titulo"><h2>Professores</h2></div>
<p><input type="button" value=" Incluir "
	onClick="location='professores_inclui.php'" name="incluir"> <input
	type="button" value=" Listar "
	onClick="location='lista_professores.php'" name="button1"></p>
<hr width="500">
<p><font face="Verdana, Arial, Helvetica, sans-serif" size="2"
	color="#0000FF"><b><font color="#FF0000">CUIDADO PARA N&Atilde;O
DUPLICAR CADASTROS !</font></b></font></p>
<table width="500" border="0" cellspacing="0" cellpadding="2">
	<tr bgcolor="#0066CC">
		<td colspan="4" height="28">
		<div align="center"><font size="2" color="#FFFFFF"><b><font
			face="Verdana, Arial, Helvetica, sans-serif">Consulta/Altera&ccedil;&atilde;o
		de Professores</font></b></font></div>
		</td>
	</tr>
	<tr>
		<td width="1">&nbsp;</td>
		<td width="102"><font face="Verdana, Arial, Helvetica, sans-serif"
			size="2">&nbsp;C&oacute;digo:</font></td>
		<td width="311"><font face="Verdana, Arial, Helvetica, sans-serif"
			size="2">&nbsp;Nome:</font></td>
		<td width="74">&nbsp;</td>
	</tr>
	<tr>
		<td width="1">&nbsp;</td>
		<td width="102">
		<div align="left">
			<input type="text" name="id" size="15" value="<?echo($id)?>"></div>
		</td>
		<td width="311"><input type="text" name="desc" size="40"
			value="<?echo($desc)?>"></td>
		<td width="74">
		<div align="right"><input type="submit" name="botao" value="Localizar">
		</div>
		</td>
	</tr>
	<tr>
		<td colspan="4" align="center">
		<hr size="1" width="490">
		</td>
	</tr>
	<tr>
		<td colspan="4"><?PHP ListaProfessores(); ?> <script
			language="javascript">
	     document.selecao.desc.value="<? echo($nome); ?>";
	  </script> <font face="Verdana, Arial, Helvetica, sans-serif"><font
			size="2"><b><font color="#FF0000"> <?PHP  if ( $hasmore )
			echo("<BR><center>Se o aluno não estiver listado, seja mais específico.</center>"); ?>
		</font></b></font></font></td>
	</tr>
	<tr align="center">
		<td colspan="4">
		<hr size="1">
		</td>
	</tr>
	<tr align="center">
		<td colspan="4">
		<div align="left">
		<ul>
			<li><font face="Arial, Helvetica, sans-serif" size="2"
				color="#0000CC"><b><font
				face="Verdana, Arial, Helvetica, sans-serif">Se o professor que
			voc&ecirc; pesquisou estiver presente na lista pesquisada, n&atilde;o
			&eacute; necess&aacute;rio inclu&iacute;-lo novamente.</font></b></font></li>
			<li><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2"
				color="#0000CC">Se voc&ecirc; quiser consultar ou alterar os dados
			de um professor da lista pesquisada, clique na imagem correspondente.</font></b></li>
			<li><font face="Arial, Helvetica, sans-serif" size="2"
				color="#0000CC"><b><font
				face="Verdana, Arial, Helvetica, sans-serif">Se o professor
			n&atilde;o estiver na lista acima, ele n&atilde;o est&aacute;
			cadastrado no sistema. Clique no bot&atilde;o &quot;Incluir&quot;
			para fazer um novo cadastro.</font></b></font></li>
		</ul>
		</div>
		</td>
	</tr>
</table>
</div>
</form>
</body>
</html>
