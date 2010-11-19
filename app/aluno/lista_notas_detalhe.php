<?php

require_once('aluno.conf.php');
include_once('includes/topo.htm');

$aluno           = $user;
$periodo		 = $_GET["p"];
$curso			 = $_GET["c"];
$disciplina_ofer = $_GET['d'];

$rs_pessoa   = $conn->get_one("SELECT nome FROM pessoas WHERE id = $aluno");
$rs_curso    = $conn->get_one("SELECT descricao FROM cursos WHERE id = $curso");
$rs_periodo  = $conn->get_one("SELECT descricao FROM periodos WHERE id = '$periodo'");


$sql_quantidade_notas = "SELECT quantidade_notas_diario FROM tipos_curso WHERE id = get_tipo_curso($curso);";
$quantidade_notas_diario = $conn->get_one($sql_quantidade_notas);


?>
<p>
    <strong>Aluno: </strong><?=$aluno?> - <?=$rs_pessoa?><br />
    <strong>Curso: </strong><?=$curso?> - <?=$rs_curso?><br />
    <strong>Per&iacute;odo: </strong><?=$rs_periodo?>
</p>
<h3> Detalhes da disciplina </h3>
<table>
    <tr bgcolor="#545443">
        <th><font color="#ffffff">Disciplina</font></th>
        <?php 
			for( $i = 1; $i <= $quantidade_notas_diario; $i++ ) :
        ?> 
				<th><font color="#ffffff">Nota <?=$i?></font></th>
        <?php
		    endfor;
        ?>
        <th><font color="yellow">Reavalia&ccedil;&atilde;o</font></th>
        <th><font color="#ffffff">Nota Total</font></th>
        <th><font color="#ffffff">Nota distribuida</font></th>
        <th><font color="#ffffff">M&eacute;dia da turma</font></th> 
        <th><font color="#ffffff">Faltas</font></th>
        <th><font color="#ffffff">% faltas</font></th>
        <th><font color="#ffffff">Aulas dadas</font></th>
        <th><font color="#ffffff">Situa&ccedil;&atilde;o</font></th>
    </tr>
    <?php
    $count = 0;

        // Exibe as principais informacoes do aluno a.ref_disciplina_ofer IN ($str_in) AND
        $sql_diario_info = "
        SELECT
            descricao_disciplina (get_disciplina_de_disciplina_of(a.ref_disciplina_ofer)),
            a.ref_disciplina_ofer, b.nome, b.ra_cnec, a.ordem_chamada,
            a.nota_final, c.ref_diario_avaliacao, c.nota, a.num_faltas,
            nota_distribuida(a.ref_disciplina_ofer) as \"total_distribuido\", fl_finalizada
        FROM
            matricula a, pessoas b, diario_notas c, disciplinas_ofer d
        WHERE
            (a.dt_cancelamento is null) AND
            a.ref_disciplina_ofer =  ". $disciplina_ofer ." AND
            d.id =  ". $disciplina_ofer ." AND
            b.id = $aluno AND
            a.ref_pessoa = b.id AND
            b.ra_cnec = c.ra_cnec AND
            c.d_ref_disciplina_ofer = a.ref_disciplina_ofer AND
            a.ref_motivo_matricula = 0
        ORDER BY descricao_disciplina, ref_diario_avaliacao;";

       
        $sql_carga_horaria = "SELECT get_carga_horaria_realizada($disciplina_ofer);";
        $ch_realizada = $conn->get_one($sql_carga_horaria);

        $sql_media_disciplina = "SELECT AVG(nota_final) 
										FROM matricula 
										WHERE ref_disciplina_ofer = $disciplina_ofer AND 
											  (dt_cancelamento is null) AND
											  ref_motivo_matricula = 0;";
        $media_disciplina = $conn->get_one($sql_media_disciplina);
       
		$diario_info = $conn->get_all($sql_diario_info);

        $color =  ($color != '#ffffff') ? '#ffffff' : '#cce5ff';
        echo '<tr bgcolor="'. $color .'">'; 
        if (count($diario_info) > 0 ) {
            foreach ($diario_info as $disciplina_aluno) {

                if ($count == 0) {
                    $nao_finalizada = ($disciplina_aluno['fl_finalizada'] == 'f') ? '<strong>*</strong>' : ' ';  
					echo '<td>'. $disciplina_aluno['descricao_disciplina'] . $nao_finalizada .'</td>';
				}
                $count++;

                if ($disciplina_aluno['nota'] == '-1')
                    echo '<td align="center"> - </td>';
                else
                    echo '<td align="center">'. number::numeric2decimal_br($disciplina_aluno['nota'],1) .'</td>';
                   // if ($disciplina_aluno['ref_diario_avaliacao'] <= $quantidade_notas_diario)
				   //		echo '<td align="center">'. number::numeric2decimal_br($disciplina_aluno['nota'],1) .'</td>';
                
                if ($disciplina_aluno['ref_diario_avaliacao'] != 7)
					continue;

				$situacao = '';

                if(verificaAprovacao($aluno, $curso, $disciplina_ofer))
                    $situacao = 'A';
                else
                    $situacao = '<span style="color: red; font-weight: bold;">R</span>';

                if(!verificaPeriodo($periodo) && $disciplina_aluno['fl_finalizada'] == 'f')
                    $situacao = 'M';

                 			
				echo '<td align="center">'. number::numeric2decimal_br($disciplina_aluno['nota_final'],1) .'</td>';
				echo '<td align="center">'. $disciplina_aluno['total_distribuido'] .'</td>';
                echo '<td align="center">'. number::numeric2decimal_br($media_disciplina,1) .'</td>';
				echo '<td align="center">'. $disciplina_aluno['num_faltas'] .'</td>';
                echo '<td align="center">'. number::numeric2decimal_br(@($disciplina_aluno['num_faltas'] * 100 / $ch_realizada),1) .'</td>';
                echo '<td align="center">'. $ch_realizada .'</td>';
			    echo '<td align="center">'. $situacao .'</td>';
        }
        echo '</tr>';
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
<br />
<input type="button" value="Imprimir" onClick="window.print()">&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript:history.back();">Voltar</a>
<br /><br />
<?php include_once('includes/rodape.htm'); ?>      

