<?php require_once("../../common.php"); ?>

<html>
<head>
<title>Localizar Cidade</title>
<script language="JavaScript">
function _init(){
   document.selecao.cnome.focus();
}
function addcidade(){
   window.open("../cidades_inclui.phtml","popWindowCity","toolbar=no,width=600,height=368,top=5,left=5,directories=no,menubar=no,scrollbars=yes");
}
</script>
<?php

$cnome = $_POST['cnome'];

$hasmore = false;

function ListaCidades()
{
	global $cnome, $hasmore, $limite_list;
	global $like;

	//$cnome = strtoupper($cnome);

	$like = "";

	if ( $cnome != "" )
	$like = "$cnome$like%";

	else if ( $like != "" )
	$like = "$like%";

	//$like = "$like%";

	if ( $like != "" )
	{
		//$hasmore = true;

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
           "        A.nome, " .
           "        A.cep, " .
           "        A.ref_pais, " .
           "        A.ref_estado, " .
           "        B.nome " .
           " from cidade A, pais B ";


		$where = '';

		if ( $cnome != '' )
		{

			$where .= " WHERE lower(to_ascii(A.nome,'LATIN1')) ";
			$where .= " SIMILAR TO lower(to_ascii('$like','LATIN1')) ";
			$where .= " AND A.ref_pais = B.id ";

		}

		$sql .= $where . " order by A.nome";

		//echo $sql .'<br />';

		$query = $conn->CreateQuery($sql);

		echo("<table width=\"490\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");

		echo("  <tr bgcolor=\"$bg0\">\n");
		echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"$fg0\">&nbsp;</font></b></td>\n");
		echo("    <td width=\"10%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"$fg0\">Cód.</font></b></td>\n");
		echo("    <td width=\"46%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"$fg0\">Descrição</font></b></td>\n");
		echo("    <td width=\"15%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"$fg0\">CEP</font></b></td>\n");
		echo("    <td width=\"18%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"$fg0\">País</font></b></td>\n");
		echo("    <td width=\"6%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"$fg0\">UF</font></b></td>\n");
		echo("  </tr>\n");

		for ( $i=1; $i <= $limite_list; $i++ )
		{
			if ( !$query->MoveNext() )
			{
				$hasmore = false;
				break;
			}

			list ( $id,
			$nome,
			$cep,
			$ref_pais,
			$ref_estado,
			$pais_desc ) = $query->GetRowValues();

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

			$href = "<a href=\"javascript:_select($id,'$nome','$cep',$ref_pais,'$ref_estado','$pais_desc')\"><img src=\"../../images/select.gif\" title='Selecionar' border=0></a>";

			echo("  <tr bgcolor=\"$bg\">\n");

			echo("    <td width=\"5%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"$fg\">$href</font></b></td>\n");
			echo("    <td width=\"10%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"$fg\">$id</font></b></td>\n");
			echo("    <td width=\"46%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"$fg\">$nome</font></b></td>\n");

			echo("    <td width=\"15%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"$fg\">&nbsp;$cep</font></b></td>\n");
			echo("    <td width=\"18%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"$fg\">$ref_pais - $pais_desc</font></b></td>\n");
			echo("    <td width=\"6%\"><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\" color=\"$fg\">$ref_estado</font></b></td>\n");
			echo("  </tr>\n");
		}

		echo("</table>");

		$query->Close();

		$conn->Close();
	}

	else
	echo("<br><center><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=red><b>Escolha um campo pelo menos!</b></font></center><br>");
}


?>
<script language="JavaScript">
            function _select(id,nome,cep,ref_pais,ref_estado,pais_desc)
            {
                window.opener.setResult(id,nome,cep,ref_pais,ref_estado,pais_desc);
                window.close();
            }
        </script>
</head>
<body bgcolor="#FFFFFF" onload="_init()">
<form method="post" action="lista_cidades.php3" name="selecao">
<div align="center">
<table width="490" border="0" cellspacing="0" cellpadding="2">
	<tr bgcolor="#0066CC">
		<td colspan="2">
		<div align="center"><font size="2" color="#FFFFFF"><b><font
			face="Verdana, Arial, Helvetica, sans-serif">Localiza&ccedil;&atilde;o
		de Cidade</font></b></font></div>
		</td>
	</tr>
	<tr>
		<td width="260"><font face="Verdana, Arial, Helvetica, sans-serif"
			size="2">Nome da Cidade:</font></td>
		<td width="222">&nbsp;</td>
	</tr>
	<tr>
		<td width="260"><input type="text" name="cnome" size="40"
			maxlength="45" value="<?echo($cnome)?>"></td>
		<td width="222"><input type="submit" name="botao" value="Localizar"></td>
	</tr>
	<tr>
		<td colspan="2">
		<hr>
		</td>
	</tr>
	<tr>
		<td colspan="2"><?php ListaCidades();?> <font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FF0000">
			<?php
			if ( $hasmore )
			echo("<center>Se a cidade não estiver listada, seja mais específico.</center>");
			?></b></font></td>
	</tr>
	<tr>
		<td colspan="2">
		<hr>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<div align="center"><input type="button" value=" Voltar  "
			onClick="javascript:window.close()" name="button"> <input
			type="button" value=" Incluir " onClick="addcidade()" name="button2">
		</div>
		</td>
	</tr>
</table>
</div>
</form>
</body>
</html>
