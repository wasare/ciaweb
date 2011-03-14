<?php
/*
 * Arquivo com as configuracoes iniciais
*/
require_once("../../../app/setup.php");
require_once("../../../core/reports/header.php");

$header  = new header($param_conn);

/**
 * Parametros do formulario
 */
$periodo = $_POST['periodo'];
$campus  = $_POST['campus'];
$curso   = $_POST['curso'];
$turma   = $_POST['turma'];

if(empty($periodo) or empty($campus) or empty($curso) or empty($turma) or
        !isset($periodo) or !isset($campus) or !isset($curso) or !isset($turma) ) {
    echo '<script language="javascript">window.alert("Nenhum diario a ser exibido!");</script>';
    echo '<meta http-equiv="refresh" content="0;url=index.php">';
    exit;
}

/*
 * Estancia a classe de conexao e abre
*/
$conn = new connection_factory($param_conn);


/**
 * Busca a descricao do periodo
 */
$sql_periodo = '
SELECT DISTINCT descricao
FROM periodos WHERE id = \''. $periodo.'\';';

$desc_periodo = $conn->get_one($sql_periodo);


/**
 * Busca a descricao do curso
 */
$sql_curso = "
SELECT DISTINCT
    a.ref_curso || ' - ' || c.descricao AS curso, b.nome_campus
FROM
    disciplinas_ofer a, cursos c, campus b
WHERE
    a.ref_periodo = '".$periodo."' AND
    c.id = a.ref_curso AND
    a.ref_curso = ".$curso." AND
    a.ref_campus = b.id AND
    b.id = $campus; ";

$desc_curso = $conn->get_row($sql_curso);


/**
 * Conteudo da legenda
 */
$sql_legenda = "
SELECT DISTINCT
    o.id AS diario,
    d.id,
    d.descricao_disciplina,
    d.descricao_extenso,
    d.carga_horaria,
    professor_disciplina_ofer_todos(o.id) AS prof,
    o.fl_digitada,
    o.fl_finalizada
FROM
    disciplinas d, disciplinas_ofer o, disciplinas_ofer_prof dp
WHERE
    d.id = o.ref_disciplina AND
    dp.ref_disciplina_ofer = o.id AND
    dp.ref_professor IS NOT NULL AND
    o.id IN (
			SELECT DISTINCT
        ref_disciplina_ofer
    FROM
        matricula a, pessoas b
    WHERE
        (a.dt_cancelamento is null) AND
        a.ref_disciplina_ofer IN (
            SELECT
                id from disciplinas_ofer
            WHERE
                is_cancelada = '0' AND
                ref_curso = $curso AND
                ref_periodo = '$periodo'
        ) AND
        a.ref_pessoa = b.id AND
        a.ref_pessoa IN(
            SELECT DISTINCT ref_pessoa
            FROM contratos
            WHERE
                ref_curso = $curso AND
                turma = '$turma'
        ) AND
        a.ref_motivo_matricula = '0'

        ) AND
    is_cancelada = '0'
ORDER BY diario;";

$arr_legenda = $conn->get_all($sql_legenda);

/**
 * Consulta principal
 */
$sql_rel = "
SELECT * FROM (
    SELECT DISTINCT
        b.nome, b.id as matricula, a.nota_final, a.num_faltas, ref_disciplina_ofer
    FROM
        matricula a, pessoas b
    WHERE
        (a.dt_cancelamento is null) AND
        a.ref_disciplina_ofer IN (
            SELECT
                id from disciplinas_ofer
            WHERE
                fl_finalizada = 't' AND
                is_cancelada = '0' AND
                ref_curso = $curso AND
                ref_periodo = '$periodo'
        ) AND
        a.ref_pessoa = b.id AND
        a.ref_pessoa IN(
            SELECT DISTINCT ref_pessoa
            FROM contratos
            WHERE
                ref_curso = $curso AND
                turma = '$turma'
        ) AND
        a.ref_motivo_matricula = '0'
) AS T1
ORDER BY lower(to_ascii(nome,'LATIN1')), ref_disciplina_ofer";


$arr_rel = $conn->get_all($sql_rel);

$arr_diarios  = array();
$arr_alunosid = array();

//Criar um vetor somente com os diarios
foreach($arr_rel as $rel) {
    $arr_diarios[]  = $rel['ref_disciplina_ofer'];
    $arr_alunosid[] = $rel['matricula'];
}

//Remove os valores duplicados
$arr_diarios = array_unique($arr_diarios);
sort($arr_diarios);

//Remove os valores duplicados
$arr_alunosid = array_unique($arr_alunosid);

//Totalizando
$num_diarios = count($arr_diarios);
$num_alunos  = count($arr_alunosid);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SA</title>
        <link href="<?=$BASE_URL?>public/styles/relatorio.css" rel="stylesheet" type="text/css">
        <link href="<?=$BASE_URL?>public/styles/print.css" rel="stylesheet" type="text/css" media="print" />
    </head>
    <body>
        <?php echo $header->get_empresa($PATH_IMAGES, $IEnome); ?>
        <h2>Resumo de notas e faltas do curso no per&iacute;odo</h2>
        <p>
            <b style="color:red;">Atenção:</b><br />
            <span style="color: teal; font-size: 0.8em;font-style: italic;">
            - Este relatório exibe somente os diários concluídos ou finalizados.<br />
            - Lista todos os alunos com matrícula no período
            selecionado e que tenham vínculo no curso/turma selecionados,
            independente da situação atual do aluno.
            </span>
        </p>
        <strong>Per&iacute;odo:</strong> <?php echo $desc_periodo; ?><br />
        <strong>Curso:</strong> <?php echo $desc_curso[0]; ?><br />
        <strong>Turma:</strong> <?php echo $turma; ?><br />
        <strong>Campus:</strong> <?php echo $desc_curso[1]; ?><br />
        <br />
        <b>LEGENDA</b>
        <table cellpadding="0" cellspacing="0" class="relato">
            <tr>
                <th align="center"><strong>C&oacute;d. Di&aacute;rio</strong></th>
                <th align="center"><strong>Descri&ccedil;&atilde;o</strong></th>
                <th align="center"><strong>Professor(a)</strong></th>
                <th align="center"><strong>CH Prevista</strong></th>
                <th align="center"><strong>CH Realizada</strong></th>
                <th align="center"><strong>N Distribuida</strong></th>
                <th align="center"><strong>Situa&ccedil;&atilde;o</strong></th>
            </tr>
            <?php foreach($arr_legenda as $legenda) : ?>
            <tr>
                <td align="center"><?=$legenda['diario']?></td>
                <td><?=$legenda['id']?> - <?=$legenda['descricao_extenso']?></td>
                <td><?=$legenda['prof']?></td>
                <td align="center"><?=$legenda['carga_horaria']?></td>
                <td align="center">
                        <?php
                        //Carga horaria realizada
                        $sql_realizada = "
                            SELECT SUM(CAST(flag AS INTEGER)) AS carga
                            FROM  diario_seq_faltas
                            WHERE  ref_disciplina_ofer = ".$legenda['diario']." ;";

                        $carga_realizada = $conn->get_one($sql_realizada);

                        if ( $carga_realizada == "") {
                            $carga_realizada = 0;
                        }
                        echo $carga_realizada;
                        ?>
                </td>
                <td align="center">
                        <?php
                        //Nota distribuida
                        $sql_distribuida = "
                            SELECT SUM(nota_distribuida) AS nota
                            FROM  diario_formulas
                            WHERE  grupo ILIKE '%-".$legenda['diario']."' ;";

                        $nota_distribuida = $conn->get_one($sql_distribuida);

                        if ( $nota_distribuida == "") {
                            $nota_distribuida = 0;
                        }
                        echo $nota_distribuida;
                        ?>
                </td>
                <td>
                        <?php
                        //Situacao do diario
                        if($legenda['fl_digitada'] == 't') {
                            echo '<font color="red"><b>Finalizado</b></font>';
                        }
                        elseif($legenda['fl_digitada'] == 'f' AND $legenda['fl_finalizada'] == 't') {
                            echo '<font color="blue"><b>Conclu&iacute;do</b></font>';
                        }
                        elseif($legenda['fl_digitada'] == 'f' AND $legenda['fl_finalizada'] == 'f') {
                            echo '<font color="green"><b>Aberto</b></font>';
                        }
                        ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br /><br />

        <table cellpadding="0" cellspacing="0" class="relato">
            <tr>
                <th rowspan="2">
                    <strong>Aluno</strong>
                </th>
                <?php foreach($arr_diarios as $diario) : ?>
                <th colspan="2"><strong><?=$diario?></strong></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php for($i = 0; $i < $num_diarios; $i++): ?>
                <th><strong>N</strong></th>
                <th><strong>F</strong></th>
                <?php endfor; ?>
            </tr>

            <?php foreach($arr_alunosid as $alunoid) : ?>

            <tr valign="top">
                <td width="300">
                        <?php echo $conn->get_one('SELECT nome FROM pessoas WHERE id = '. $alunoid) .' ('. $alunoid .')' ; ?>
                </td>
                    <?php foreach($arr_diarios as $diario): ?>
                <td>
                    <?php
                    foreach($arr_rel as $rel) {
                        if($alunoid === $rel['matricula'] AND $diario === $rel['ref_disciplina_ofer'])
                            echo number_format($rel['nota_final'],2,',','.');
                    }
                    ?>&nbsp;
                </td>
                <td>
                    <?php
                    foreach($arr_rel as $rel) {
                        if($alunoid === $rel['matricula'] AND $diario === $rel['ref_disciplina_ofer'])
                            echo $rel['num_faltas'];
                    }
                    ?>&nbsp;
                </td>
                <?php endforeach; ?>
            </tr>

            <?php endforeach; ?>

        </table>
        <br />
        <div class="nao_imprime">
            <input type="button" value="Imprimir" onClick="window.print()" />
            &nbsp;&nbsp;&nbsp;
            <a href="#" onclick="javascript:window.close();">Fechar</a>
        </div>
        <br />
    </body>
</html>
