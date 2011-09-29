<?php

require("../common.php");
require("../lib/SQLCombo.php");

$conn = new connection_factory($param_conn);

// Verifica as permissoes de acesso do usuario quanto ao arquivo
$ACL_FILE = __FILE__;
require_once($BASE_DIR .'core/login/acesso.php');
// ^ Verifica as permissoes de acesso do usuario quanto ao arquivo ^ //

$op_opcoes = SQLArray("select nome_setor, id from setor order by nome_setor");

function Mostra_Carimbos()
{

	$conn = new Connection;

	$conn->Open();

	$sql= " select A.id, " .
        "        A.nome, " .
        "        A.texto, " .
        "        A.ref_setor, " .
        "        B.nome_setor " .
        " from carimbos A, setor B " .
        " where A.ref_setor = B.id " .
        " order by A.texto";

	$query = $conn->CreateQuery($sql);

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

		list ( $id,
		$nome,
		$texto,
		$ref_setor,
		$nome_setor) = $query->GetRowValues();

		if ($i == 1)
		{

			echo("<tr bgcolor=\"#000000\">\n");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Cod.</b></font></td>");
			echo ("<td width=\"30%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Nome</b></font></td>");
			echo ("<td width=\"30%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Fun&ccedil;&atilde;o</b></font></td>");
			echo ("<td width=\"25%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Setor</b></font></td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>&nbsp;</b></font></td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>&nbsp;</b></font></td>");
			echo("  </tr>");
		}

		$href  = "<a href=\"carimbos_altera.php?id=$id\"><img src=\"../images/update.gif\" title='Alterar Carimbo' align='absmiddle' border=0></a>";
		$href1 = "<a href=\"javascript:Confirma_Exclui($id)\"><img src=\"../images/delete.gif\" title='Excluir Carimbo' align='absmiddle' border=0></a>";

		if ( $i % 2)
		{

			echo("<tr bgcolor=\"$bg1\">\n");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">$id</td>");
			echo ("<td width=\"30%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">$nome</td>");
			echo ("<td width=\"30%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">$texto</td>");
			echo ("<td width=\"25%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">$nome_setor</td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">$href</td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">$href1</td>");
			echo("  </tr>");

		}
		else
		{
			echo("<tr bgcolor=\"$bg2\">\n");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$id</td>");
			echo ("<td width=\"30%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$nome</td>");
			echo ("<td width=\"30%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$texto</td>");
			echo ("<td width=\"25%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$nome_setor</td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$href</td>");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$href1</td>");
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

	$sql = "delete from carimbos where id='$arg';";

	$ok = $conn->Execute($sql);

	$conn->Close();

	SaguAssert($ok,"N&atilde;o foi poss&iacute;vel de excluir o registro!");

}
?>
<html>
<head>
<script language="Javascript">
function ChangeOption(opt,fld)
{
  var i = opt.selectedIndex;

  if ( i != -1 )
    fld.value = opt.options[i].value;
  else
    fld.value = '';
}

function ChangeOp()
{
  ChangeOption(document.myform.op,document.myform.ref_setor);
}

function Confirma_Exclui(arg1)
{
  url = 'post/carimbos_exclui.php?id=' + arg1;

  if (confirm("Voce tem certeza que deseja excluir o carimbo: "+arg1))
    location=(url)
  else
    alert("Exclus&atilde;o Cancelada.");
}
</script>
<link href="../estilo.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<form method="post" action="post/carimbos.php" name="myform">
<div class="titulo" align="center"><h2>Carimbos</h2></div>
<table width="90%" align="center">
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="nome" type=text size="35"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Fun&ccedil;&atilde;o&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="texto" type=text size="35"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099">&nbsp;Setor&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td colspan="2"><font color="#000000"> <input type="text"
			name="ref_setor" size="10" value="<?php echo($ref_setor);?>"> 
			<?php ComboArray("op",$op_opcoes,$ref_setor,"ChangeOp()"); ?>
      </font></td>
	</tr>
	<tr>
		<td colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<div align="center"><input type="submit" name="Submit"
			value=" Incluir "> <input type="reset" name="Submit2"
			value=" Limpar "></div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td height="33" colspan="2">
		<div align="center"><?PHP Mostra_Carimbos(); ?></div>
		</td>
	</tr>
</table>
</form>
</body>
</html>
