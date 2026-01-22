<?php
require_once('aluno.conf.php');
include_once('includes/topo.htm');
include("includes/menu.html");

$aluno   = $aluno_id;
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
    <strong>Perí­odo: </strong><?=$rs_periodo?><br />
</p>
<span style="color: red; font-style:italic; font-family:arial,times;">Clique no
nome da disciplina para detalhar os lançamentos e visualizar mais informações</span>
<table style="font-size:100%; wordwrap: auto;">
    <tr bgcolor=#EEEEEE>
        <th>Disciplina</th>
        <th>Média</th>
        <th>Nota<br />Máxima</th>
        <th>Faltas</th>
  		<!-- Inicio: Victor Ullisses Pugliese - 12:38 27/04/2012 - % FALTAS; -->
        <th>% Faltas</th>
        <th>Situação</th>
    </tr>
    <?php
    $count = 0;
	$m = 0;
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
				$color =  ($color != '#ffffff') ? '#ffffff' : '#9AF8A6';

                echo '<tr bgcolor="'. $color .'">';
				if($disciplina_aluno['total_distribuido'] > 0)
				{
					echo '<td><a href="lista_notas_detalhe.php?c='. $curso .'&p='. $periodo .'&d='. $disciplina_aluno['ref_disciplina_ofer'] .'" alt="Clique para detalhar a disciplina" title="Clique para detalhar a disciplina">'. $disciplina_aluno['descricao_disciplina'] .'</a>'. $nao_finalizada .'</td>';
				}
				else
				{
					echo '<td>'. $disciplina_aluno['descricao_disciplina'] . $nao_finalizada .'</td>';
				}
				if($disciplina_aluno['total_distribuido'] > 0)
				{
					echo '<td align="center">'. number::numeric2decimal_br($disciplina_aluno['nota_final'],1) .'</td>';
				}
				else
				{
					echo '<td align="center"> - </td>';
				}
				if($disciplina_aluno['total_distribuido'] > 0)
				{
					echo '<td align="center">'. number::numeric2decimal_br($disciplina_aluno['total_distribuido'],1) .'</td>';
				}
				else
				{
					echo '<td align="center"> - </td>';
				}
				echo '<td align="center">'. $disciplina_aluno['num_faltas'] .'</td>';

				//Inici­o: Victor Ullisses Pugliese - 12:38 27/04/2012 - CARGA HORARIA;
                $sql_carga_horaria = "SELECT get_carga_horaria_realizada(".$disciplina_aluno['ref_disciplina_ofer'].");";
        		$ch_realizada = $conn->get_one($sql_carga_horaria);

                echo '<td align="center">'. number::numeric2decimal_br(@($disciplina_aluno['num_faltas'] * 100 / $ch_realizada),1) .'</td>';
                //Fim

                if($disciplina_aluno['fl_finalizada']=="" || $disciplina_aluno['fl_finalizada']=="f")
				{
					echo '<td align="center"> M </td>';
					$situacao = "M";
					$m++;
				}
                else
                {
                	if($disciplina_aluno['nota_final'] < 6 || (($disciplina_aluno['num_faltas'] * 100 / $ch_realizada) > 25))
					{
						echo '<td align="center"> R </td>';
						$situacao = "R";
					}
					else
					{
						echo '<td align="center"> A </td>';
						$situacao = "A";
					}
				}
				echo '</tr>';

				$count++;
        }
    }
    ?>
</table>
<br />
<?php
	if($m>0)
		echo "(<strong>*</strong>) Disciplina com lançamentos em aberto, passí­vel de alterações.<br /><br />";
?>
<div class="aviso_notas">As informações acima possuem somente um cárater informacional, ou seja não tem valor oficial. Solicite junto a secretaria escolar um boletim ou histórico para obter um documento válido. </div>
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
Existem disciplinas matriculadas não exibidas. <br />
Estas disciplinas somente estaram disponí­veis quando o professor(a) iniciar o
lançaamento das notas. <br />
Qualquer dúvida entre em contato com seu professor(a) ou com a
coordenação do curso. <br />
</strong>
</font>
<?php endif; ?>
<br />
Gerado no dia <?php echo date("d/m/Y") ." às ". date("H:i:s"); ?> <br/><br/>
<input type="button" value="Imprimir" onClick="window.print()">&nbsp;&nbsp;&nbsp;<a href="lista_cursos.php">Voltar</a>
<br /><br />
<?php include_once('includes/rodape.htm'); ?>
