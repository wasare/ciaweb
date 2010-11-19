<?php

require_once('aluno.conf.php');
include_once('includes/topo.htm');

$aluno   = $user;
$periodo = $_GET["p"];
$curso   = $_GET["c"];

$rs_pessoa   = $conn->get_one("SELECT nome FROM pessoas WHERE id = $aluno");
$rs_curso    = $conn->get_one("SELECT descricao FROM cursos WHERE id = $curso");
$rs_periodo  = $conn->get_one("SELECT descricao FROM periodos WHERE id = '$periodo'");

$sql_diarios_matriculados = "
SELECT ref_disciplina_ofer 
FROM
    matricula m LEFT OUTER JOIN disciplinas_ofer o ON (m.ref_disciplina_ofer = o.id)
WHERE
    (m.dt_cancelamento is null) AND
    m.ref_pessoa = $aluno AND
    m.ref_contrato IN (
        SELECT id FROM contratos
	WHERE 
            ref_pessoa = $aluno AND
            ref_curso = $curso
	) AND
	m.ref_motivo_matricula = 0 AND 
	o.is_cancelada = '0' AND
	o.ref_periodo = '$periodo' ";

$rs_diarios = $conn->get_col($sql_diarios_matriculados);
$rs_diarios_matriculados = count($rs_diarios);

?>
<p>
    <strong>Aluno: </strong><?=$aluno?> - <?=$rs_pessoa?><br />
    <strong>Curso: </strong><?=$curso?> - <?=$rs_curso?><br />
    <strong>Per&iacute;odo: </strong><?=$rs_periodo?>
</p>
<span style="color: red; font-style:italic; font-family:arial,times;">Clique no nome da disciplina para detalhar os lan&ccedil;amentos e visualizar mais informa&ccedil;&otilde;es</span>
<table>
    <tr bgcolor="#545443">
        <th><font color="#ffffff">Disciplina</font></th>
        <th><font color="#ffffff">Nota Total</font></th>
        <th><font color="#ffffff">Nota distribuida</font></th>
        <th><font color="#ffffff">Faltas</font></th>
        <th><font color="#ffffff">Situa&ccedil;&atilde;o</font></th>
    </tr>
    <?php
    $count = 0;

        // Exibe as principais informacoes do aluno a.ref_disciplina_ofer IN ($str_in) AND
        $sql_diarios_info = "
        SELECT
            descricao_disciplina(get_disciplina_de_disciplina_of(m.ref_disciplina_ofer)),
            m.ref_disciplina_ofer, m.nota_final, m.num_faltas, m.ref_contrato,
            nota_distribuida(m.ref_disciplina_ofer) as \"total_distribuido\", d.fl_finalizada
        FROM
            matricula m, disciplinas_ofer d
        WHERE
            (m.dt_cancelamento is null) AND
            m.ref_disciplina_ofer IN  ( ". $sql_diarios_matriculados ." ) AND
            m.ref_pessoa = $aluno AND
            m.ref_motivo_matricula = 0 AND
            m.ref_disciplina_ofer = d.id 
        ORDER BY descricao_disciplina;";
        
        //die($sql_diarios_info);
        $diarios_info = $conn->get_all($sql_diarios_info);

        if (count($diarios_info) > 0 ) {
            foreach ($diarios_info as $disciplina_aluno) {
				$nao_finalizada = ($disciplina_aluno['fl_finalizada'] == 'f') ? '<strong>*</strong>' : ' ';
				$color =  ($color != '#ffffff') ? '#ffffff' : '#cce5ff';

                $situacao = '';

                if(verificaAprovacao($aluno, $curso, $disciplina_aluno['ref_disciplina_ofer']))
                    $situacao = 'A';
                else
                    $situacao = '<span style="color: red; font-weight: bold;">R</span>';

    			if(!verificaPeriodo($periodo) && $disciplina_aluno['fl_finalizada'] == 'f')
        			$situacao = 'M';

				echo '<tr bgcolor="'. $color .'">';
				echo '<td><a href="lista_notas_detalhe.php?c='. $curso .'&p='. $periodo .'&d='. $disciplina_aluno['ref_disciplina_ofer'] .'" alt="Clique para detalhar a disciplina" title="Clique para detalhar a disciplina">'. $disciplina_aluno['descricao_disciplina'] .'</a>'. $nao_finalizada .'</td>';
				echo '<td align="center">'. number::numeric2decimal_br($disciplina_aluno['nota_final'],1) .'</td>';
				echo '<td align="center">'. $disciplina_aluno['total_distribuido'] .'</td>';
				echo '<td align="center">'. $disciplina_aluno['num_faltas'] .'</td>';
                echo '<td align="center">'. $situacao .'</td>';
				echo '</tr>';

				$count++;
        }
    }
    ?>
</table>
<br />
(<strong>*</strong>) Disciplina com lan&ccedil;amentos n&atilde;o finalizados, pass&iacute;vel de altera&ccedil;&otilde;es.
<br /><br />
<div align="left" style="font-size: 0.85em;">
    <h4>Legenda</h4>
    <strong>A</strong> - Aprovado<br />
    <strong>R</strong> - Reprovado <br />
    <strong>M</strong> - Matriculado <br /><br />
</div>
<?php if ($rs_diarios_matriculados > $count) : ?>
<br />
<font color="red">
<strong>
Existem disciplinas matriculadas n&atilde;o exibidas. <br />
Estas disciplinas somente estaram dispon&iacute;veis quando o professor(a) iniciar o lan&ccedil;amento das notas. <br />
Qualquer d&uacute;vida entre em contato com seu professor(a) ou com a coordena&ccedil;&atilde;o do curso. <br />
</strong>
</font>
<?php endif; ?>
<br />
<input type="button" value="Imprimir" onClick="window.print()">&nbsp;&nbsp;&nbsp;<a href="lista_cursos.php">Voltar</a>
<br /><br />
<?php include_once('includes/rodape.htm'); ?>      

