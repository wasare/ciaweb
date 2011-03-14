<?php

require("../common.php");
require("../lib/InvData.php3");

$ref_disciplina_ofer = $_GET['ref_disciplina_ofer'];

function ListaAlunos($ref_disciplina_ofer)
{
	$conn = new Connection;

	$conn->Open();

	$sql = " select A.ref_pessoa, " .
          "        pessoa_nome(A.ref_pessoa),    " .
          "        A.ref_disciplina_ofer,   " .
          "        A.ref_disciplina,  " .
          "        descricao_disciplina(A.ref_disciplina),  " .
          "        get_disciplina_de_disciplina_of(A.ref_disciplina_ofer), " .
          "        descricao_disciplina(get_disciplina_de_disciplina_of(A.ref_disciplina_ofer)), " .
          "        A.ref_curso,   " .
          "        curso_desc(A.ref_curso), " .
          "        B.fone_particular, " .
          "        B.fone_profissional, " .
          "        B.fone_celular, " .
          "        B.fone_recado, " .
          "        pessoa_nome(get_ref_professor(A.ref_disciplina_ofer)), " .
          "        B.email " .
          " from matricula A, pessoas B " .
          " where A.ref_pessoa = B.id and " .
          "       A.ref_disciplina_ofer = '$ref_disciplina_ofer' and " .
          "       A.dt_cancelamento is null " .
          " order by A.ref_curso, " .
	      "	         pessoa_nome(A.ref_pessoa);" ;

	$query = $conn->CreateQuery($sql);

	echo("<center><table width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");

	$i=1;

	// cores fundo
	$bg0 = "#000000";
	$bg1 = "#EEEEFF";
	$bg2 = "#FFFFEE";

	// cores fonte
	$fg0 = "#FFFFFF";
	$fg1 = "#000099";
	$fg2 = "#000099";

	$aux_curso=0;

	while( $query->MoveNext() )
	{
		list ($ref_pessoa,
		$pessoa_nome,
		$ref_disciplina_ofer,
		$ref_disciplina_curriculo,
		$descricao_disciplina_curriculo,
		$ref_disciplina,
		$descricao_disciplina,
		$ref_curso,
		$curso,
		$fone_res,
		$fone_prof,
		$fone_cel,
		$recado,
		$nome_prof,
		$email) = $query->GetRowValues();

		if ($i == 1)
		{
			echo ("<td bgcolor=\"#000099\" colspan=\"7\" height=\"28\" align=\"center\"><font size=\"3\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#FFFFFF\"><b>Lista de Alunos Matriculados</b></font></td>");
			echo ("<tr>");
			echo ("<td bgcolor=\"#000099\" colspan=\"7\" height=\"28\"> <font size=\"3\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#FFFFFF\"><b>Disciplina Ofertada: " . $ref_disciplina . " - " . $descricao_disciplina . " </b></font></td>");
			echo ("</tr>");
			echo ("<tr>");
			echo ("<td bgcolor=\"#000099\" colspan=\"7\" height=\"28\"> <font size=\"3\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#FFFFFF\"><b>Professor : " . $nome_prof . " </b></font></td>");
			echo ("</tr>");

			echo ("<tr bgcolor=\"#000000\">\n");
			echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Num</b></font></td>");
			echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Código</b></font></td>");
			echo ("<td width=\"45%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Nome/e-mail</b></font></td>");
			echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Telefone<br>Residencial</b></font></td>");
			echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Telefone<br>Profissional</b></font></td>");
			echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Telefone<br>Celular</b></font></td>");
			echo ("<td width=\"10%\" align=\"center\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Disciplina<br>Currículo</b></font></td>");
			echo ("  </tr>");

		}
			
		if ($ref_curso!=$aux_curso)
		{
			echo("<td bgcolor=\"#FFFFFF\" colspan=\"7\"></td>");
			echo ("<tr>");
			echo ("<td bgcolor=\"#000000\" colspan=\"7\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#FFFFFF\"><b>Curso: " . $curso . "</b></font></td>");
			echo ("</tr>");
			$aux_curso=$ref_curso;
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
			
		echo("<tr bgcolor=\"$bg\">\n");
		echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">$i</td>");
		echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">$ref_pessoa</td>");
		echo ("<td width=\"45%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">$pessoa_nome</td>");
		echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">$fone_res</td>");
		echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">$fone_prof</td>");
		echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">$fone_cel</td>");
		echo ("<td width=\"10%\" align=\"center\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">$ref_disciplina_curriculo</td>");
		echo("  </tr>\n");
			
		echo("<tr bgcolor=\"$bg\">\n");
		echo ("<td width=\"15%\" colspan=\"2\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">&nbsp;</td>");
		echo ("<td width=\"85%\" colspan=\"5\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">$email</td>");
		echo("  </tr>\n");

		$i++;

	}

	echo("<tr><td colspan=\"7\"><hr></td></tr>");
	echo("</table></center>");

	$query->Close();

	$conn->Close();
}
?>
<html>
<head>
<title>Número de Alunos por Oferta</title>
</head>
<body marginwidth="20" marginheight="20">
<form method="post" action=""><?php ListaAlunos($ref_disciplina_ofer);?>
<div align="center"><input type="button" name="Button"
	value="  Voltar  " onclick="javascript:history.go(-1)"></div>
</form>
</body>
</html>
