<?php

require("../common.php");
require("../lib/SQLCombo.php");

$op_campus = SQLArray("select nome_campus, id from campus order by 1");
$op_cursos = SQLArray("select id || ' - ' || descricao,id from cursos order by descricao");


function Coordenadores(){
	

	$conn = new Connection;

	$conn->Open();

	$sql= " select A.ref_curso, " .
        "        B.abreviatura, " .
        "        A.ref_campus, " .
        "        C.nome_campus, " .
        "        A.ref_professor, " .
        "        D.nome, " .
        "        D.email " .
        " from coordenador A, " .
        "      cursos B, " .
        "      campus C, " .
        "      pessoas D " .
        " where A.ref_curso = B.id and " .
        "       A.ref_campus = C.id and " .
        "       A.ref_professor = D.id " .
        " order by 2,1";

	$query = $conn->CreateQuery($sql);

	$n = $query->GetColumnCount();

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
		list ($ref_curso,
		$curso,
		$ref_campus,
		$campus,
		$ref_professor,
		$professor,
		$email) = $query->GetRowValues();

		$href1 = "<a href=\"javascript:Confirma_Exclui('$ref_campus','$ref_curso','$ref_professor')\"><img src=\"../images/delete.gif\" title='Excluir Coordenador' align='absmiddle' border=0></a>";

		if ($i == 1)
		{
			echo("<tr bgcolor=\"#000000\">\n");
			echo ("<td width=\"3%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>&nbsp;</b></font></td>");
			echo ("<td width=\"36%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Curso</b></font></td>");
			echo ("<td width=\"20%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Campus</b></font></td>");
			echo ("<td width=\"36%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Professor</b></font></td>");
			echo ("<td width=\"15%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Email</b></font></td>");
			echo("  </tr>");
		}

		if ( $i % 2)
		{
			echo("<tr bgcolor=\"$bg1\">\n");
			echo ("<td width=\"3%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">$href1</td>");
			echo ("<td width=\"36%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\"><b>$ref_curso - $curso</b></font></td>");
			echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\"><b>$ref_campus - $campus</b></font></td>");
			echo ("<td width=\"36%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\"><b>$ref_professor - $professor</b></font></td>");
			echo ("<td width=\"15%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\"><b>$email</b></font></td>");
			echo("  </tr>");
		}
		else
		{
			echo("<tr bgcolor=\"$bg2\">\n");
			echo ("<td width=\"3%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$href1</td>");
			echo ("<td width=\"36%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\"><b>$ref_curso - $curso</b></font></td>");
			echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\"><b>$ref_campus - $campus</b></font></td>");
			echo ("<td width=\"36%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\"><b>$ref_professor - $professor</b></font></td>");
			echo ("<td width=\"15%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\"><b>$email</b></font></td>");
			echo("  </tr>");
		}

		$i++;

	}

	echo("</table></center>");

	$query->Close();

	$conn->Close();
}

?>
<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="JavaScript">

function Confirma_Exclui(ref_campus,ref_curso,ref_professor)
{
  url = 'post/coordenador_exclui.php?ref_campus=' + ref_campus + '&ref_curso=' + ref_curso + '&ref_professor='+ ref_professor;

  if (confirm("Tem certeza que deseja excluir este coordenador do curso "+ ref_curso +" ?"))
    location=(url)
  else
    alert("Exclus&atilde;o Cancelada.");
}

function setResult(arg1,arg2)
{
    document.myform.professor.value = arg2;
    document.myform.ref_professor.value = arg1;
}

function _busca()
{
  tipo_busca = 2;

  var url = "../generico/post/lista_professores.php" +
            "?pnome=" + escape(document.myform.professor.value);

  var wnd = window.open(url,'busca','toolbar=no,width=530,height=350,scrollbars=yes');
}

function _init()
{
  document.myform.ref_campus.focus();
}

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
  ChangeOption(document.myform.op,document.myform.ref_campus);
}

function ChangeOp2()
{
  ChangeOption(document.myform.op2,document.myform.ref_cursos);
}

</script>
<link href="../estilo.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
	<div align="center">
	<div class="titulo"><h2>Coordenadores</h2></div>
	</div>
<form method="post" action="post/coordenadores.php" name="myform">
<table width="90%" align="center">
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Campus&nbsp;<span class="required">*</span> </font></td>
		<td><input name="ref_campus" type=text size="7"> <script
			language="PHP">ComboArray("op",$op_campus,"0","ChangeOp()");</script>
		</font></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cursos&nbsp;<span class="required">*</span> </font></td>
		<td><input name="ref_cursos" type=text size="7"> <script
			language="PHP">ComboArray("op2",$op_cursos,"0","ChangeOp2()");</script>
		</font></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="00009C">&nbsp;Professor&nbsp;<span class="required">*</span> </font></td>
		<td>
		<table width="65%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="0%"><font color="#000000"> <input name="ref_professor"
					type=text size="10" value="<?=$ref_professor?>"> </font></td>
				<td width="100%"><font color="#000000"> <input type="text"
					name="professor" size="35" value="<?=$professor?>"> </font></td>
				<td width="0%">
				<div><font color="#000000"> <input type="button" value="..."
					onClick="_busca()" name="button2"> </font></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td height="33" colspan="2">
		<div align="center"><input type="submit" name="Submit"
			value="  Incluir  "></div>
		</td>
	</tr>
	<tr>
		<td height="13" colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td height="33" colspan="2">
		<div align="center"><?PHP Coordenadores(); ?></div>
		</td>
	</tr>
</table>
</form>
</body>
</html>
