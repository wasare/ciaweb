<?php

require("../../common.php");
require("../../lib/InvData.php3");
require("../../lib/GetDescDisciplina.php3");


$ref_campus            = $_POST['ref_campus'];
$nome_campus           = $_POST['nome_campus'];
$ref_curso             = $_POST['ref_curso'];
$nome_curso            = $_POST['nome_curso'];
$ref_periodo           = $_POST['ref_periodo'];
$turma                 = $_POST['turma'];
$ref_periodo_turma     = $_POST['ref_periodo_turma'];
$ref_disciplina        = $_POST['ref_disciplina'];
$ref_disciplina_nome   = $_POST['ref_disciplina_nome'];
$num_creditos_desconto = $_POST['num_creditos_desconto'];
$desconto              = $_POST['desconto'];
$ref_professor         = (int) $_POST['ref_professor'];
$ref_professor_nome    = $_POST['ref_professor_nome'];
$dia_semana            = $_POST['dia_semana'];
$ref_regime            = $_POST['ref_regime'];
$num_sala              = $_POST['num_sala'];
$num_alunos            = $_POST['num_alunos'];
$conteudo              = $_POST['conteudo'];
$observacao            = $_POST['observacao'];
$dt_exame              = $_POST['dt_exame'];


CheckFormParameters(array("ref_campus",
        "ref_curso",
        "ref_periodo",
        "ref_disciplina"));

$nome_disciplina = GetDescDisciplina($ref_disciplina, true);

$conn = new Connection;
$conn->Open();
$conn->Begin();

$sql = "select nextval('seq_disciplinas_ofer')";  // ID disciplina_ofer

$query = $conn->CreateQuery($sql);

$success = false;

if ( $query->MoveNext() ) {
    $id_disciplina_ofer = $query->GetValue(1);

    $success = true;
}

$query->Close();

SaguAssert($success,"Não foi possível obter o número da disciplina cadastrada!");

$sql = "
    INSERT INTO disciplinas_ofer (
        id,
        ref_campus,
        ref_curso,
        ref_periodo,
        ref_disciplina,
        num_alunos,
        fixar_num_sala,
        is_cancelada,
        turma,
        ref_periodo_turma,
        conteudo
    ) VALUES (
        $id_disciplina_ofer,
        '$ref_campus',
        '$ref_curso',
        '$ref_periodo',
        '$ref_disciplina',
        '$num_alunos',
        '$fixar_num_sala',
        '0',
        '$turma',
        '$ref_periodo_turma',
        '$conteudo'
    )" ;

$ok1 = $conn->Execute($sql);

$sql = "select nextval('seq_disciplinas_ofer_compl_id')";  // ID disciplina_ofer_compl

$query = $conn->CreateQuery($sql);

$success = false;

if ( $query->MoveNext() ) {
    $id_disciplina_ofer_compl = $query->GetValue(1);

    $success = true;
}

$query->Close();

$dt_exame = InvData($dt_exame);

$sql = "
    INSERT INTO disciplinas_ofer_compl (
        id,
        ref_disciplina_ofer,
        dia_semana,
        desconto,
        num_creditos_desconto,
        num_sala,
        observacao,
        ref_horario,
        ref_regime,
        dt_exame
    ) VALUES (
        '$id_disciplina_ofer_compl',
        '$id_disciplina_ofer',
        '$dia_semana',
        '$desconto',
        '$num_creditos_desconto',
        '$num_sala',
        '$observacao',
        '$ref_horario',
        '$ref_regime',";

if ( $dt_exame == '')
    $sql = $sql . "  null)";
else
    $sql = $sql . "  '$dt_exame')";

$ok2 = $conn->Execute($sql);

if (is_numeric($ref_professor) && $ref_professor > 0) { 
    $sql = " insert into disciplinas_ofer_prof (" .          // Disciplina_ofer_prof
        "         ref_disciplina_ofer," .
        "         ref_disciplina_compl," .
        "         ref_professor) " .
        " values ('$id_disciplina_ofer'," .
        "         '$id_disciplina_ofer_compl'," .
        "         '$ref_professor')" ;

    $ok3 = $conn->Execute($sql);

}
else
    $ok3 = TRUE;

$conn->Finish();
$conn->Close();

SaguAssert($ok1,"Não foi possível inserir o registro na tabela de disciplinas_ofer!");
SaguAssert($ok2,"Não foi possível inserir o registro na tabela de disciplinas_ofer_compl!");
SaguAssert($ok3,"Não foi possível inserir o registro na tabela de disciplinas_ofer_prof!");


function Lista_Complemento($id, $ref_campus) {
    $conn = new Connection;
    $conn->Open();
    $conn->Begin();

    $sql = " select A.id, " .
          "        A.ref_disciplina_ofer, " .
          "        get_dia_semana(A.dia_semana), " .
          "        B.ref_professor, " .
          "        pessoa_nome(B.ref_professor), " .
          "        get_turno(A.turno), " .
          "        A.desconto, " .
          "        A.num_creditos_desconto, " .
          "        A.num_sala, " .
          "        A.observacao " .
          " from disciplinas_ofer_compl A FULL OUTER JOIN disciplinas_ofer_prof B " .
          " USING(ref_disciplina_ofer) " .
          " WHERE  A.ref_disciplina_ofer='$id' " .
          " order by A.dia_semana; "; 

    $query = $conn->CreateQuery($sql);

    echo("<center><table width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");

    $i=1;
    $j=0;

    // cores fundo
    $bg0 = "#000000";
    $bg1 = "#EEEEFF";
    $bg2 = "#FFFFEE";

    // cores fonte
    $fg0 = "#FFFFFF";
    $fg1 = "#000099";
    $fg2 = "#000099";

    while( $query->MoveNext() ) {
        list ($id,
                $ref_disciplina_ofer,
                $dia_semana,
                $ref_professor,
                $professor,
                $turno,
                $desconto,
                $num_creditos_desconto,
                $num_sala,
                $observacao) = $query->GetRowValues();

        $professor = empty($professor) ? "<strong>sem professor</strong>" : $professor;

                if ($i == 1)
                {
                        echo("<tr>");
                        echo ("<td bgcolor=\"#000099\" colspan=\"12\"><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#FFFFFF\"><center><b>Informações Complementares</b></center></font></td>");
                        echo("</tr>");
                        echo ("<tr bgcolor=\"#000000\">\n");
                        echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Ofer</b></font></td>");
                        echo ("<td width=\"2%\">&nbsp;</td>");
                        echo ("<td width=\"25%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Professor</b></font></td>");
                        echo ("<td width=\"8%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Op&ccedil;&otilde;es</font></td>");
                        echo ("<td width=\"13%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Dia</b></font></td>");
                        echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Turno</b></font></td>");
                        echo ("<td width=\"10%\" align=\"center\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Desconto</b></font></td>");
                        echo ("<td width=\"10%\" align=\"center\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Sala</b></font></td>");
                        echo ("<td width=\"5%\" align=\"center\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Créditos</b></font></td>");
                        echo ("<td width=\"5%\" align=\"center\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Obs.</b></font></td>");
                        echo ("  </tr>");
                }
                 
                $href1  = "<a href=\"../disciplina_ofer_compl_edita.phtml?id=$id&ref_campus=$ref_campus&ref_professor=$ref_professor\">$ref_disciplina_ofer</a>";
                $href3  = "<a href=\"../disciplina_ofer_prof.phtml?ref_disciplina_ofer=$ref_disciplina_ofer&id_disciplina_ofer_compl=$id\"><img src=\"../../images/add.gif\" title='Adiciona mais um professor a disciplina' align='absmiddle' border=0></a>";

                $href4 = "";
                
                if ($aux_id == $id)
                {
                        $href2  = "<center><img src=\"../../images/etc.gif\" title='Disciplina ministrada por mais de um Professor' align='absmiddle' border=0></center>";
                }
                else
                {
                        $href2 = "&nbsp;";
                }

                if ( $i % 2) { $bg = $bg1; } else { $bg = $bg2; }

                echo("<tr bgcolor=\"$bg\">\n");
                echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\">$href1</font></td>");
                echo ("<td width=\"2%\">$href2</td>");
                echo ("<td width=\"25%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$professor&nbsp;</td>");
                echo ("<td width=\"8%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>$href3&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$href4</b></font></td>");
                echo ("<td width=\"13%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$dia_semana&nbsp;</td>");
                echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$turno&nbsp;</td>");
                echo ("<td width=\"10%\" align=\"center\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$desconto&nbsp;</td>");
                echo ("<td width=\"10%\" align=\"center\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$num_sala&nbsp;</td>");
                echo ("<td width=\"5%\" align=\"center\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$num_creditos_desconto&nbsp;</td>");
                echo ("<td width=\"5%\" align=\"center\"><Font face=\"Verdana\" size=\"2\" color=\"$fg2\">$observacao&nbsp;</td>");
                echo("  </tr>");

                $i++;
                $aux_id = $id;

    }

    echo("</table></center>");

    $query->Close();

    $conn->Finish();
    $conn->Close();
}
?>

<html>
    <head>

        <script language="JavaScript">
            function Inclui_Complemento(id, ref_campus, ref_curso, ref_periodo, ref_disciplina, num_alunos, fixar_num_sala, conteudo, turma, ref_periodo_turma)
            {
                location="../disciplina_ofer_compl.phtml?id_disciplina_ofer=" + id +
                    "&ref_campus=" + ref_campus +
                    "&ref_curso=" + ref_curso +
                    "&ref_periodo=" + ref_periodo +
                    "&ref_disciplina=" + ref_disciplina +
                    "&turma=" + turma +
                    "&ref_periodo_turma=" + ref_periodo_turma +
                    "&num_alunos=" + num_alunos +
                    "&fixar_num_sala=" + fixar_num_sala +
                    "&conteudo=" + conteudo;
            }
        </script>
    </head>
    <body bgcolor="#FFFFFF">
        <form method="post" action="" name="myform">
            <table width="90%" align="center">
                <tr bgcolor="#000099">
                    <td height="40" colspan="2" align="center"><font
                            face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF" size="3"><b>&nbsp;Disciplinas
		Oferecidas</b></font></td>
                </tr>
                </tr>
                <tr align="center">
                    <td colspan="2" height="40"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FF0000"><b><font
                                    size="3">Disciplina Inclu&iacute;da com sucesso</font></b></font></td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo</font></td>
                    <td bgcolor="#FFFFFF" width="70%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#FF0000"><b><? echo($id_disciplina_ofer);?></b></font>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Campus</font></td>
                    <td bgcolor="#FFFFFF" width="70%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"><b><?echo($ref_campus);?>
		- <?echo($nome_campus);?></b></font></td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Curso</font></td>
                    <td bgcolor="#FFFFFF" width="70%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"><b><?echo($ref_curso);?>
		- <?echo($nome_curso);?></b></font></td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Turma</font></td>
                    <td bgcolor="#FFFFFF" width="70%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"><b><?echo($turma);?><i>(<?echo($ref_periodo_turma);?>)</i></b></font>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Per&iacute;odo</font></td>
                    <td bgcolor="#FFFFFF" width="70%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"><b><?echo($ref_periodo);?></b></font>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Disciplina</font></td>
                    <td bgcolor="#FFFFFF" width="70%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"><b><?echo($ref_disciplina);?>
		- <?echo($nome_disciplina);?></b></font></td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nº
		Alunos</font></td>
                    <td bgcolor="#FFFFFF" width="70%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"><b><?echo($num_alunos);?></b></font>
                    </td>
                </tr>
<?php
if ($fixar_num_sala == '1') {
    $fixar_num_sala = 'Sim';
}
else {
    $fixar_num_sala = 'Não';
}
                echo($fixar_num_sala);
                ?></b></font>
              </td>
            </tr>-->
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Conteúdo</font></td>
                    <td bgcolor="#FFFFFF" width="70%"><font
                            face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"><b><?echo($conteudo);?></b></font>
                    </td>
                </tr>
                <tr align="center">
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
<?php Lista_Complemento($id_disciplina_ofer, $ref_campus);?>
            <table cols=2 width="90%" align="center">
                <tr align="center">
                    <td colspan="2">
                        <hr size="1">
                        <font color="#000000" size="2"
                              face="Verdana, Arial, Helvetica, sans-serif"> <input type="button"
                                                                             name="sair" value=" Continuar "
                                                                             onclick="location='../disciplina_ofer.phtml'"> </font></td>
                </tr>
            </table>
        </form>
    </body>
</html>
