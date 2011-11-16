<?php

require_once(dirname(__FILE__). '/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

if($_POST) {
    $periodo_id = (string) $_POST['periodo_id'];
    $curso_id   = (int) $_POST['curso_id'];
    $diario_id  = (int) $_POST['diario_id'];
    $professor_id = (int) $_POST['professor_id'];
}else {
    $periodo_id = (string) $_GET['periodo_id'];
    $curso_id   = (int) $_GET['curso_id'];
    $diario_id  = (int) $_GET['diario_id'];
    $professor_id = (int) $_GET['professor_id'];
}

// @todo verificar direitos de acesso
if (empty($periodo_id) OR ($curso_id == 0 AND $professor_id == 0)) {

    $diario_id = ($diario_id == 0) ? (int) $_GET['diario_id'] : $diario_id;

    if ($diario_id == 0) {
        exit('<script language="javascript">
                window.alert("ERRO! Primeiro informe um diário ou um período + um curso e / ou + um professor!");
		window.history.back(-1);
		</script>');
    }

    if (!is_diario($diario_id))
        exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario inexistente ou cancelado!");window.history.back(-1);</script>');

}


if ($diario_id == 0) {
    $qryCurso = 'SELECT DISTINCT id, descricao as nome FROM cursos WHERE id = '. $curso_id.';';
    $qryPeriodo = 'SELECT id, descricao FROM periodos WHERE id = \''. $periodo_id.'\';';
}
else {
    $qryCurso = 'SELECT c.id, c.descricao as nome FROM cursos c, disciplinas_ofer d WHERE d.ref_curso = c.id AND d.id = '. $diario_id .';';
    $qryPeriodo = 'SELECT p.id, p.descricao FROM periodos p, disciplinas_ofer d WHERE d.ref_periodo = p.id AND d.id = '. $diario_id .';';
}


$curso = $conn->get_row($qryCurso);
$periodo = $conn->get_row($qryPeriodo);


if (is_numeric($professor_id) && $professor_id != 0) {

    $sql =  " SELECT DISTINCT A.id as idof, " .
            "        A.ref_campus, " .
            "        get_campus(A.ref_campus), " .
            "        A.ref_curso, " .
            "        curso_desc(A.ref_curso), " .
            "		A.fl_finalizada, A.fl_digitada, ".
            "        descricao_disciplina(A.ref_disciplina) as descricao_extenso, " .
            "        A.ref_disciplina, " .
            "        get_num_matriculados(A.id) || '/' || A.num_alunos as qtde_alunos, " .
            "        A.turma, " .
            "        A.ref_periodo_turma, " .
            "     CASE WHEN professor_disciplina_ofer_todos(A.id) = '' THEN '<font color=\"red\">sem professor</font>' " .
            "			ELSE professor_disciplina_ofer_todos(A.id) " .
            "		END AS \"professor\" " .
            " FROM disciplinas_ofer A FULL OUTER JOIN disciplinas_ofer_prof B ON (A.id = B.ref_disciplina_ofer) " .
            " WHERE A.is_cancelada = '0' ";
} else {

    $sql =  " SELECT DISTINCT A.id as idof, " .
            "        A.ref_campus, " .
            "        get_campus(A.ref_campus), " .
            "        A.ref_curso, " .
            "        curso_desc(A.ref_curso), " .
            "               A.fl_finalizada, A.fl_digitada, ".
            "        descricao_disciplina(A.ref_disciplina) as descricao_extenso, " .
            "        A.ref_disciplina, " .
            "        get_num_matriculados(A.id) || '/' || A.num_alunos as qtde_alunos, " .
            "        A.turma, " .
            "        A.ref_periodo_turma, " .
            "     CASE WHEN professor_disciplina_ofer_todos(A.id) = '' THEN '<font color=\"red\">sem professor</font>' " .
            "                       ELSE professor_disciplina_ofer_todos(A.id) " .
            "               END AS \"professor\" " .
            " FROM disciplinas_ofer A " .
            " WHERE A.is_cancelada = '0' ";
}

if ($diario_id > 0)
    $sql .= " AND A.id = ". $diario_id;
else
    if (!empty($periodo_id)) {
       $sql .= " AND A.ref_periodo = '". $periodo_id ."'";
       $sql .= (is_numeric($curso_id) && $curso_id != 0) ? " AND A.ref_curso = ". $curso_id : " ";
       $sql .= (is_numeric($professor_id) && $professor_id != 0) ? " AND B.ref_professor = ". $professor_id : " ";
    }

//die($sql);
$sql = 'SELECT * from ('. $sql .') AS T1 ORDER BY lower(to_ascii(descricao_extenso,\'LATIN1\'));';


$diarios = $conn->get_all($sql);

if (count($diarios) == 0) {
    exit('<script language="javascript">
                window.alert("Nenhum diário encontrado para o filtro selecionado!");
                window.history.back(-1);
		</script>');
}

?>

<html>
    <head>
        <title><?=$IEnome?> - consulta di&aacute;rios</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
        <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/sorter.css'?>" type="text/css">

        <script type="text/javascript" src="<?=$BASE_URL .'lib/prototype.js'?>"> </script>
        <script type="text/javascript" src="<?=$BASE_URL .'app/web_diario/web_diario.js'?>"> </script>

    <body>

        <br />
        <div align="left">
            <a href="<?=$BASE_URL .'app/web_diario/secretaria/diarios_secretaria.php'?>">
                <input type="image" name="voltar" src="<?=$BASE_URL?>public/images/icons/back.png"
                       alt="Voltar"
                       title="Voltar"
                       id="bt_voltar"
                       name="bt_voltar"
                       class="botao" />&nbsp;Pesquisar outro per&iacute;odo / curso / professor(a)</a>
            <br />
            <br />

            <table cellpadding="0" cellspacing="0" class="papeleta">
                <tr>
                    <th>
                        <div align="center">
                            <font color="#990000" size="4" face="Verdana, Arial, Helvetica, sans-serif">
                                <strong>
                                    <font color="red">Per&iacute;odo: <?=$periodo['descricao']?></font>
                                </strong>
                            </font>
                        </div>
                    </th>
                </tr>
            </table>
            <?php if (is_numeric($curso_id) && $curso_id != 0) : ?>
                    <h4><strong>Curso: </strong><font color="blue"><?=$curso['id'] .' - '. $curso['nome']?></font></h4>
            <?php endif; ?>
            <h5>Clique em "Acessar" para exibir as op&ccedil;&otilde;es do di&aacute;rio:</h5>

            <form id="change_acao" name="change_acao" method="get" action="">

                <table cellspacing="0" cellpadding="0" class="papeleta">
                    <tr bgcolor="#cccccc">
                        <th align="center"><strong>Ordem</strong></th>
                        <th align="center"><b>Di&aacute;rio</b></th>
                        <th align="center"><b>Descri&ccedil;&atilde;o</b></th>
                        <th align="center"><b>Alunos / Vagas</b></th>
                        <th align="center"><b>Turma</b></th>
                        <th align="center"><b>Professor(es)</b></th>
                        <th align="center"><b>Campus</b></th>
                        <th align="center"><b>Situa&ccedil;&atilde;o</b></th>
                        <th align="center"><b>Op&ccedil;&otilde;es</b></th>
                    </tr>

                    <?php

                    $i = 0;

                    $r1 = '#FFFFFF';
                    $r2 = '#FFFFF0';

                    foreach($diarios as $row3) :

                        $descricao_disciplina = $row3["descricao_extenso"];
                        $disciplina_id = $row3["idof"];
                        $diario_id = $row3["idof"];
                        $fl_finalizada = $row3['fl_finalizada'];
                        $fl_digitada = $row3['fl_digitada'];
                        $professor = $row3['professor'];
                        $campus = $row3['get_campus'];
                        $qtde_alunos = $row3['qtde_alunos'];
                        $turma = $row3['turma'];

                        $diarios_pane[] = $diario_id;

                        $fl_encerrado = ($fl_finalizada == 't')  ? 1 : 0;

                        $fl_professor = TRUE;
                        if ( preg_match('/sem professor/i', $professor) )
                            $fl_professor = FALSE;

                        $opcoes_diario = '';
                        $fl_opcoes = 0;

                        if($fl_finalizada == 'f' && $fl_digitada == 'f') {
                            $fl_situacao = '<font color="green"><b>Aberto</b></font>';
                        }
                        else {

                            if($fl_digitada == 't') {
                                $fl_situacao = '<font color="blue"><b>Preenchido</b></font>';
                            }

                            if($fl_finalizada == 't') {
                                $fl_situacao = '<font color="red"><b>Fechado</b></font>';
                                $opcoes_diario .= "<a href=\"#\" onclick=\"reaberto_secretaria('$diario_id','$IEnome');\">abre para lan&ccedil;amentos</a><br />";

                                $fl_encerrado = 1;
                                $fl_opcoes = 1;
                            }
                            else {
                                $opcoes_diario .= "<a href=\"#\" onclick=\"fechado_secretaria('$diario_id','$IEnome');\">fecha para lan&ccedil;amentos</a><br />";
                                $fl_opcoes = 1;
                            }
                        }

                        $opcoes_diario .= empty($opcoes_diario) ? '' : '<br />';

                        if ($fl_professor === TRUE) {
                            $opcoes_diario .= '<strong>Relat&oacute;rios</strong><hr />';
                            $opcoes_diario .= "<a href=\"#\" onclick=\"abrir('$IEnome','../requisita.php?do=papeleta&id=$diario_id');\">papeleta</a><br />";
                            $opcoes_diario .= "<a href=\"#\" onclick=\"abrir('$IEnome','../requisita.php?do=papeleta_completa&id=$diario_id');\">papeleta completa</a><br />";
                            $opcoes_diario .= "<a href=\"#\" onclick=\"abrir('$IEnome','../requisita.php?do=faltas_completo&id=$diario_id');\">faltas detalhado</a><br />";
                            $opcoes_diario .= "<a href=\"#\" onclick=\"abrir('$IEnome','../requisita.php?do=conteudo_aula&id=$diario_id');\">conte&uacute;do de aula</a><br />";
                            $opcoes_diario .= "<a href=\"#\" onclick=\"abrir('$IEnome"."_carometro','../requisita.php?do=carometro&id=$diario_id');\">Car&ocirc;metro</a><br />";
                            $opcoes_diario .= "<a href=\"#\" onclick=\"abrir('$IEnome','../requisita.php?do=diario_classe&id=$diario_id');\">Diário de Classe (A3)</a><br />";
                            //$opcoes_diario .= "<a href=\"#\" onclick=\"abrir('$IEnome','../requisita.php?do=caderno_chamada&id=$diario_id');\">caderno de chamada</a>";
                            $fl_opcoes = 1;
                        }

                        $sem_opcoes = ($fl_opcoes == 0) ? '<font color="red">Nenhuma op&ccedil;&atilde;o dispon&iacute;vel.</font>' : '';

                        $cont = $i + 1;

                        $rcolor = (($i % 2) == 0) ? $r1 : $r2;
                        ?>

                    <tr bgcolor="<?=$rcolor?>">
                        <td align="center"><?=$cont?></td>
                        <td align="center"><?=$diario_id?></td>
                        <td><?=$descricao_disciplina?></td>
                        <td align="center"><?=$qtde_alunos?></td>
                        <td align="center"><?=$turma?></td>
                        <td><?=$professor?></td>
                        <td align="center"><?=$campus?></td>
                        <td align="center"><?=$fl_situacao?></td>
                        <td align="center">
                            <a href="#" id="<?=$diario_id . '_pane'?>" title="clique para visualizar / ocultar">Acessar</a>
                            <!-- panel com as opções do diário // inicio //-->
                            <div id="diario_<?=$diario_id?>_pane" style="display:none; margin: 1.2em; padding: 1em; background-color: <?=$op_color?>" class="opcoes_web_diario">
                                    <?=$sem_opcoes . $opcoes_diario?>
                            </div>
                            <!-- panel com as opções do diário \\ fim \\ -->
                        </td>
                    </tr>

                        <?php

                        $i++;

                    endforeach;
                    ?>
                </table>

                <br />
                &nbsp;&nbsp;&nbsp;
                <input type="button" value="Fechar todos os diários preenchidos" onclick="fecha_todos_secretaria('<?=$diario_id?>','<?=$IEnome?>');" />

            </form>
            <script language="javascript" type="text/javascript">

<?php
foreach($diarios_pane as $diario_id) :
    ?>
        $('<?=$diario_id . '_pane'?>').observe('click', function() { $('diario_<?=$diario_id?>_pane').toggle(); $('diario_<?=$diario_id?>_pane').focus(); });
<?php
endforeach;
?>
            </script>
        </div>
        <br /><br /><br /><br />
    </body>
</head>
</html>

