<?

require("../../common.php");

$pnome = $_POST['pnome'];
$snome = $_POST['snome'];
?>
<html>
<head>
<title>Localização de Pessoas por Nome</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php

$hasmore = false;

function ListaPessoas(){
	
	global $pnome, $snome, $hasmore, $limite_list;
	global $like;

	$pnome = strtoupper($pnome);
	$snome = strtoupper($snome);

	$count = 0;

	$like = "";

	if ( $pnome != "" )
	$like = "$pnome";

	if ( $snome != "" )
	$like = "$like% $snome%";

	else if ( $like != "" )
	$like = "$like%";

	if ( $like != "" )
	{
		$hasmore = true;

		// cores fundo
		$bg0 = "#000000";
		$bg1 = "#EEEEFF";
		$bg2 = "#FFFFEE";

		// cores fonte
		$fg0 = "#FFFFFF";
		$fg1 = "#000099";
		$fg2 = "#000099";

		$pessoa = strtoupper($pessoa);

		$conn = new Connection;

		$conn->Open();

		$sql = "SELECT" .
           "    a.id," .
           "    a.nome" .
           " FROM" .
           "    pessoas a". 
           " WHERE" .
           "    a.tipo_pessoa = 'f' and " .
           "    a.nome ilike '$like' " .
           " ORDER BY" .
           "    a.nome";

		$query = $conn->CreateQuery($sql);

		echo "    <table width=\"490\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n";

		echo "      <tr bgcolor=\"$bg0\">\n";
		echo "        <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">&nbsp;</font></b></td>\n";
		echo "        <td width=\"25%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Código</font></b></td>\n";
		echo "        <td width=\"7%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Nome</font></b></td>\n";
		echo "      </tr>\n";

		for ( $i=1; $i <= $limite_list; $i++ )
		{
			if ( !$query->MoveNext() )
			{
				$hasmore = false;
				break;
			}

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

			$href = "<a href=\"javascript:_select($id,'$nome')\"><img src=\"../../images/select.gif\" title='Selecionar' border=0></a>";
			echo "      <tr bgcolor=\"$bg\">\n";
			echo "        <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$href</font></b></td>\n";
			echo "        <td width=\"25%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$id</font></b></td>\n";
			echo "        <td width=\"70%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$nome</font></b></td>\n";
			echo "      </tr>\n";
		}

		echo "    </table>\n";

		$query->Close();

		$conn->Close();

		$count = $i;
	}
	else
	{
		echo("<br><center><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=red><b>Informe um campo para fazer a pesquisa!</b></font></center><br>");
	}

	return $count;
}

?>
<script language="JavaScript">

function _init()
{
  document.selecao.pnome.focus();
}

function _select(id,nome)
{
  window.opener.setResult(id,nome);
  window.close();
}
</script>
</head>
<body bgcolor="#FFFFFF" onload="_init()">
<form method="post" action="lista_pessoas.php3" name="selecao">
<div align="center">
<table width="490" border="0" cellspacing="0" cellpadding="2">
	<tr bgcolor="#0066CC">
		<td colspan="6" height="28" align="center"><b><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">Localiza&ccedil;&atilde;o
		de Pessoas</font></b></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Nome:</font></td>
		<td><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Sobrenome:</font></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="text" name="pnome" size="20" maxlength="45"
			value="<?echo($pnome)?>"></td>
		<td><input type="text" name="snome" size="20" maxlength="45"
			value="<?echo($snome)?>"></td>
		</td>
		<td><input type="submit" name="botao" value="Localizar"></td>
	</tr>
</table>
<hr size="1" width="490">
<?
ListaPessoas();
if ( $hasmore )
{
	echo "<font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"#FF0000\">";
	echo "<br><center><b>Se a Pessoa não estiver listada, seja mais específico.</b></center>";
	echo "</font>";
}
?>
<hr size="1" width="490">
<input type="button" value=" Voltar "
	onclick="javascript:window.close()"></div>
</form>
</body>
</html>
