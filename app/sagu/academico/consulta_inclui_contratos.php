<?php

require("../common.php");
require("../lib/InvData.php");


$id    = $_POST['id'];
$aluno = $_POST['aluno'];

?>
<html>
<head>
<title>Contratos</title>
<script language="JavaScript">
function _init()
{
  document.myform.id.focus();
}
</script>

<?php

$hasmore = false;

function ListaContratos()
{
	global $hasmore;

	//global $like;

	$aluno = $_POST['aluno'];
	$id = $_POST['id'];

	$count = 0;

	if ( !empty($id)  OR !empty($aluno) )
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

		// note the parantheses in the where clause !!!
		$sql = " select distinct c.id, " .
         "        p.nome  || ' (' || p.id || ')' as nome , " .
  	 "	  c.ref_curso, " .
  	 "        curso_desc(c.ref_curso), " .
	 "	  c.dt_ativacao,".
         "        c.dt_desativacao " .
	 " from contratos c, pessoas p";


		$where = '';

		if ( !empty($aluno) )
		{
			if ( empty($where) )
			$where .= ' where ';
			else
			$where .= ' and ';
			//$where .= ( $where == '' ) ? ' where ' : ' and ';
			//$where .= "upper(nome) like upper('$desc%') and tipo_pessoa='f'";
			$where .= "  lower(to_ascii(p.nome,'LATIN1')) ";
			$where .= " SIMILAR TO lower(to_ascii('$aluno%','LATIN1')) ";
			//$where .= " AND p.id = c.ref_pessoa";
		}

		if ( !empty($id) )
		{
			if ( empty($where) )
			$where .= ' where ';
			else
			$where .= ' and ';
			$where .= " p.id = '$id' AND c.ref_pessoa = '$id' ";
		}


		$sql .= $where . " AND p.id = c.ref_pessoa ORDER BY nome, c.dt_ativacao";


		// " where ref_pessoa = $id " .
		// " order by dt_ativacao";

		//echo $sql;
		$query = $conn->CreateQuery($sql);

		echo("<table width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");

		echo("  <tr bgcolor=\"$bg0\">\n");
		echo(" <td width=\"10%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Contrato</font></b></td>\n");
		echo("    <td width=\"40%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"
color=\"$fg0\">Aluno</font></b></td>\n");
		echo("    <td width=\"30%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Curso</font></b></td>\n");
		echo("    <td width=\"10%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Ativação</font></b></td>\n");
		echo("    <td width=\"8%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">Desativação</font></b></td>\n");
		echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">&nbsp;</font></b></td>\n");
		echo("  </tr>\n");

		for ( $i=0; $i<25 && $query->MoveNext(); $i++ )
		{

			list ( $ref_contrato,
			$ref_nome,
			$ref_curso,
			$curso,
			$dt_ativacao,
			$dt_desativacao ) = $query->GetRowValues();

			$dt_ativacao = InvData($dt_ativacao);
			$dt_desativacao = InvData($dt_desativacao);

			if ($dt_desativacao == '')
			{
				$dt_desativacao = '&nbsp;';
			}

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

			$href  = "<a href=\"alterar_contrato.php?id=$ref_contrato\"><img src=\"../images/update.gif\" title='Alterar Cadastro' align='absmiddle' border=0></a>";

			echo("  <tr bgcolor=\"$bg\">\n");
			echo("    <td width=\"10%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$ref_contrato</font></b></td>\n");
			echo("    <td width=\"35%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$ref_nome</font></b></td>\n");
			echo("    <td width=\"45%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$ref_curso - $curso</font></b></td>\n");
			echo("    <td width=\"15%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$dt_ativacao</font></b></td>\n");
			echo("    <td width=\"10%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$dt_desativacao</font></b></td>\n");
			echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$href</font></b></td>\n");
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
<link href="../estilo.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<form method="post" action="consulta_inclui_contratos.php"
	name="myform">
<div align="center">
<div class="titulo"><h2>Contratos</h2></div>
<p><input type="button" value=" Incluir "
	onClick="location='novo_contrato.php'" name="button"></p>
</div>
<hr align="center" width="500">
<p align="center"><font face="Verdana, Arial, Helvetica, sans-serif"
	size="2" color="#0000FF"><b><font color="#FF0000">CUIDADO PARA
N&Atilde;O DUPLICAR CADASTROS !</font></b></font></p>
<div align="center">
<table width="90%" border="0" cellspacing="0" cellpadding="2"
	height="100">
	<tr bgcolor="#0066CC">
		<td colspan="5" height="28">
		<div align="center"><font size="2" color="#FFFFFF"><b><font
			face="Verdana, Arial, Helvetica, sans-serif">Consulta/Altera&ccedil;&atilde;o
		de Contratos</font></b></font></div>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td colspan="3"><font face="Verdana, Arial, Helvetica, sans-serif"
			size="2">Código:</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <font
			face="Verdana, Arial, Helvetica, sans-serif" size="2">Aluno:</font></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td colspan="2"><input type="text" name="id" size="8"
			value="<?echo($_POST['id'])?>"> &nbsp;&nbsp; <input type="text"
			name="aluno" id="aluno" size="40"
			value="<?php echo $_POST['aluno']; ?>"> &nbsp;&nbsp; <input
			type="submit" name="botao" value="Localizar"></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="5" align="center">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td colspan="5" align="center"><?php ListaContratos(); ?> <font
			face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font
			color="#FF0000"></font><font
			face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font
			color="#FF0000"> <?if ( $hasmore )
			echo("<BR>Se o Contrato não estiver listado, seja mais específico."); ?></font></b></font></font><font
			color="#FF0000"></font></b></font></font></td>
	</tr>
	<tr align="center">
		<td colspan="5">
		<hr size="1">
		</td>
	</tr>
</table>
</div>
</form>
</body>
</html>
