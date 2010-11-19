<?php

require("../../common.php");

$id = $_POST['id'];
$desc = $_POST['desc'];

?>
<html>
<head>
<title>Disciplinas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">
function _init()
{
  document.myform.id.focus();
}

function _select(id,nome)
{
  if ( window.callSetResult )
    window.callSetResult(id,nome);
  else
    window.opener.setResult(id,nome);

  window.close();
}
</script>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<form method="post" action="lista_disciplinas_todas.php3" name="myform">
<table width="100%" border="0" cellspacing="2" cellpadding="0"
	align="center">
	<tr bgcolor="#0066CC">
		<td colspan="4" height="28" align="center"><font size="2"
			face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><b>Consulta
		de Disciplinas</b></font></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><font size="2" face="Arial, Helvetica, sans-serif">C&oacute;digo:</font></td>
		<td colspan="2"><font size="2" face="Arial, Helvetica, sans-serif">Descri&ccedil;&atilde;o:</font></td>
		<td width="50">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="text" name="id" size="8" value="<?echo($id);?>"></td>
		<td><input type="text" name="desc" value="<?echo($desc);?>" size="40">
		<input type="hidden" name="periodo" value="<?echo($periodo);?>"></td>
		<td width="50"><input type="submit" name="Submit" value="Localizar"></td>
	</tr>
	<tr>
		<td colspan="4">
		<hr size="1" width="500">
		</td>
	</tr>
	<?php
	if ( $id != '' || $desc != '' )
	{

		$conn = new Connection;

		$conn->Open();

		// note the parantheses in the where clause !!!
		$sql = "select id, descricao_disciplina" .
         "  from disciplinas";

		$where = '';

		if ( $id != '' )
		{
			$where .= ( $where == '' ) ? ' where ' : ' and ';
			$where .= "id = $id";
		}

		if ( $desc != '' )
		{
			$where .= ( $where == '' ) ? ' where ' : ' and ';
			$where .= "upper(descricao_disciplina) like upper('$desc%')";
		}

		$sql .= $where . " order by id";

		$query = $conn->CreateQuery($sql);

		for ( $i=0; $i<25 && $query->MoveNext(); $i++ )
		{
			list ( $id, $nome ) = $query->GetRowValues();

			$href = "<a href=\"javascript:_select($id,'$nome')\"><img src=\"../../images/select.gif\" title='Selecionar' border=0></a>";

			if ( $i % 2 == 0)
			{
				?>
	<tr bgcolor="#EEEEFF">
		<td width="20"><font face="Arial, Helvetica, sans-serif" size="2"> <?php echo($href);?>
		</font></td>
		<td width="50"><font face="Arial, Helvetica, sans-serif" size="2"> <?php echo($id);?>
		</font></td>
		<td colspan="2" width=300><font face="Arial, Helvetica, sans-serif"
			size="2"> <?php echo($nome);?> </font></td>
	</tr>
	<?php     } // if
	else
	{?>
	<tr bgcolor="#FFFFEE">
		<td width="20"><font face="Arial, Helvetica, sans-serif" size="2"> <?php echo($href);?>
		</font></td>
		<td><font face="Arial, Helvetica, sans-serif" size="2"> <?php echo($id);?>
		</font></td>
		<td colspan="2"><font face="Arial, Helvetica, sans-serif" size="2"> <?php echo($nome);?>
		</font></td>
	</tr>
	<?php     } // else
		} // for

		$hasmore = $query->MoveNext();

		$query->Close();
		$conn->Close();
	} // if
	?>
	<tr>
		<td colspan="4" align="center"><?php 
		if ( $hasmore )
		echo("<br>Se o Curso não estiver listado, seja mais específico.<br>");
		?>
		<hr size="1" width="100%">
		<input type="button" name="Button" value=" Voltar "
			onClick="javascript:window.close()"></td>
	</tr>
</table>
</form>
</body>
</html>