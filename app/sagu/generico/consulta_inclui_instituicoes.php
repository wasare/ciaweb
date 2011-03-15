<?php

require("../common.php");

$hasmore = false;

$id   = $_POST['id'];
$nome = $_POST['nome'];


function Lista_Instituicoes() {
	
	global $id, $nome, $hasmore;
	global $like;

	$nome = strtoupper($nome);

	$count = 0;

	$like = "";

	if ( $id != "" )
	$like = "$id";

	if ( $nome != "" )
	$like = "$like $nome";

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
		$sql = "select id, nome" .
         "  from instituicoes";

		$where = '';

		if ( $id != '' )
		{
			$where .= ( $where == '' ) ? ' where ' : ' and ';
			$where .= "id = $id";
		}

		if ( $nome != '' )
		{
			$where .= ( $where == '' ) ? ' where ' : ' and ';
			$where .= "upper(nome) like upper('%$nome%')";
		}

		$sql .= $where . " order by nome";

		//  SaguAssert(0,$sql);

		$query = $conn->CreateQuery($sql);

		echo("<table width=\"500\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");

		echo("  <tr bgcolor=\"$bg0\">\n");
		echo("    <td width=\"20%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">C�digo</font></b></td>\n");
		echo("    <td width=\"70%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Nome da Institui��o</font></b></td>\n");
		echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">&nbsp;</font></b></td>\n");
		echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">&nbsp;</font></b></td>\n");
		echo("  </tr>\n");

		for ( $i=0; $i<25 && $query->MoveNext(); $i++ )
		{

			list ( $id,$nome ) = $query->GetRowValues();

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
			$href  = "<a href=\"instituicao_altera.php?id=$id\"><img src=\"../images/update.gif\" title='Alterar Cadastro' align='absmiddle' border=0></a>";
			$href1 = "<a href=\"javascript:Confirma_Exclui($registro)\"><img src=\"../images/delete.gif\" title='Excluir Institui��o' align='absmiddle' border=0></a>";


			echo("  <tr bgcolor=\"$bg\">\n");
			echo("    <td width=\"20%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$id</font></b></td>\n");
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
<title>Institui��es</title>
<script language="JavaScript">
function _init()
{
  document.myform.id.focus();
}

function Confirma_Exclui(arg1)
{
  url = 'post/instituicao_exclui.php?id=' + arg1;

  if (confirm("Voc� tem certeza que deseja excluir a Institui��o: "+arg1))
    location=(url)
  else
    alert("Exclus�o Cancelada.");
}
</script>
<link href="../estilo.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<form method="post" action="consulta_inclui_instituicoes.php"
	name="myform">
<div align="center">
<div class="titulo" align="center"><h2>Institui&ccedil;&otilde;es</h2></div>
<p><input type="button" value=" Incluir "
	onClick="location='inclui_instituicao.php'" name="button"> 
	<input type="button" value=" Listar "
	onClick="location='post/listar_instituicoes.php'" name="button"></p>
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
		de Institui&ccedil;&otilde;es</font></b></font></div>
		</td>
	</tr>
	<tr>
		<td width="6">&nbsp;</td>
		<td width="50"><font face="Verdana, Arial, Helvetica, sans-serif"
			size="2">C&oacute;digo:</font></td>
		<td width="271"><font face="Verdana, Arial, Helvetica, sans-serif"
			size="2">Nome da Institui&ccedil;&atilde;o:</font></td>
		<td width="40">&nbsp;</td>
	</tr>
	<tr>
		<td width="6">&nbsp;</td>
		<td width="50"><input type="text" name="id" size="8"
			value="<?echo($id)?>"></td>
		<td width="271"><input type="text" name="nome" size="40"
			value="<?echo($nome)?>"></td>
		<td width="40"><input type="submit" name="botao" value="Localizar"></td>
	</tr>
	<tr>
		<td colspan="4" align="center">
		<hr size="1" width="500">
		</td>
	</tr>
	<tr>
		<td colspan="4"><?php Lista_Instituicoes(); ?> <font
			face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font
			color="#FF0000"> </font><font
			face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font
			color="#FF0000"><?php  if ( $hasmore )
			echo("<BR>Se o nome da Institui��o n�o estiver listado, seja mais espec�fico."); ?></font></b></font></font><font
			color="#FF0000"> </font></b></font></font></td>
	</tr>
	<tr align="center">
		<td colspan="4">
		<hr size="1" width="500">
		</td>
	</tr>
</table>
</div>
</form>
</body>
</html>
