<?php
require_once('aluno.conf.php');
include_once('includes/topo.htm');
include("includes/menu.html");

$aluno           = $aluno_id;
$periodo         = $_GET["p"];
$curso		 = $_GET["c"];
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
    <strong>Período: </strong><?=$rs_periodo?><br />
</p>

<?php
	//Inicio - Victor Ullisses Pugliese - 10h51min 04/05/2012 - Disciplina info;
    $sql_diario_info = "
        SELECT
            DISTINCT descricao_disciplina (get_disciplina_de_disciplina_of(a.ref_disciplina_ofer)),
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
	$diario_info = $conn->get_all($sql_diario_info);
	//Inicio - Victor Ullisses Pugliese - 10h52min 04/05/2012 -  Media da turma;
	$sql_media_turma = "SELECT AVG(nota_final) FROM matricula WHERE ref_disciplina_ofer = ".$disciplina_ofer;
	$turma = $conn->get_one($sql_media_turma);
	//Inicio - Victor Ullisses Pugliese - 10h52min 04/05/2012 -  Aulas Dadas;
	$sql_carga_horaria = "SELECT get_carga_horaria_realizada(".$disciplina_ofer.");";
	$ch_realizada = $conn->get_one($sql_carga_horaria);
		
	if (count($diario_info) > 0 ) {
		foreach ($diario_info as $disciplina_aluno) {
			if ($count == 0) {
				
				$nao_finalizada = ($disciplina_aluno['fl_finalizada'] == 'f') ? '<strong>*</strong>' : ' ';  
				
				echo '<disc><b>Disciplina: </b>'. $disciplina_aluno['descricao_disciplina'] . $nao_finalizada . '</disc><br />';
				if($disciplina_aluno['fl_finalizada']=="" || $disciplina_aluno['fl_finalizada']=="f")
				{                
					$situacao = "M";
				}
                else
                {
                	if($disciplina_aluno['nota_final'] < 6 || ($disciplina_aluno['num_faltas'] * 100 / $ch_realizada) >= 25)
					{
						$situacao = "R";
					}
					else
					{
						$situacao = "A";
					}
				}
				if($situacao == "M")
					echo "(<strong>*</strong>) Disciplina com lançamentos em aberto, passível de alterações.<p />";
				else
					echo "<p />";
				
				$media_aluno = $disciplina_aluno['nota_final'];
				
				//Inicio - Victor Ullisses Pugliese - 10h54 04/05/2012 - Media da Sala
				echo 
				"<strong>Média da Turma: </strong>". number::numeric2decimal_br($turma,1) ."<br />";
   				//Fim da Media da Sala;
   				//Inicio: Victor Ullisses Pugliese - 12:38 27/04/2012 - CARGA HORARIA;
            		echo "<strong>Aulas Dadas: </strong>". $ch_realizada;
            	//Fim
?>
<!--Inicio - Victor Uliisses Pugliese - 15:28 01/05/2012 - Tabela Media -->
<table style="font-size: 1em" >
	<tr bgcolor="#EEEEEE">
		<th><b>Faltas</b></th>
		<th><b>% Faltas</b></th>
		<th><b>Situação</b></th>
	</tr>
	<tr bgcolor="#9AF8A6">
		<td><center>
			<?php
			//Inicio - Victor Ullisses Pugliese - 10h54min 04/05/2012 - Faltas Alunos;
				$faltas_aluno = $disciplina_aluno['num_faltas'];
				echo $faltas_aluno;				
        	//Fim Faltas Alunos;
			?>		
		</center></td>
		<td><center>
			<?php
			//Inicio - Victor Ullisses Pugliese - 23:27 01/05/2012 
	        	$per_faltas = ($faltas_aluno * 100) / $ch_realizada;
	        	echo number::numeric2decimal_br($per_faltas,1)."%";	        	
	        //Fim Percentagem de Faltas;	
			?>
		</center></td>
		<td><center>
			<?php
			//Inicio -Victor Ullisses Pugliese - 11:24 01/05/2012 - Situacao;				
				echo $situacao;				
				}//fecha if cont == 0;
    		        break;
    			}
    		}
			else
				echo "Não houve retorno na sua busca...";
			//Fim Situacao;
			?>
		</center></td>
	</tr>
</table>
<!-- Fim da Tabela Media -->
<p />
<?php
	$i = $nota_disc_maxima = 0;
    $cont = 1;
	$notas = array();
		if (count($diario_info) > 0 ) {
            foreach ($diario_info as $disciplina_aluno) {
				$notas[$i] = $disciplina_aluno['nota'];
				$i++;
			}
		}
		$nDistribuida_sql = 
			"SELECT 
				df.nota_distribuida 
			FROM diario_formulas as df 
			WHERE df.grupo like '%-$periodo-%-$disciplina_ofer' order by df.prova";
		$n_info = $conn->get_all($nDistribuida_sql);
		
		$detalhes_sql = 
		"SELECT descricao FROM diario_formulas WHERE grupo ILIKE '%-$periodo-%-$disciplina_ofer' order by prova asc";
		$detalhes_info = $conn->get_all($detalhes_sql);
?>
<table style="font-size: 1.0em;">
    <tr bgcolor="#EEEEEE">
    	<th> - </th>
        <th>Nota</th>
        <th>Nota Máxima</th>
    </tr>
    <?php
    //Inicio - Victor Ullisses Pugliese - 11h27min 04/05/2012 - Exibe notas e pesos;
		$i=0;
		//$color =  ($color != '#ffffff') ? '#ffffff' : '#9AF8A6';
        //echo '<tr bgcolor="'. $color .'">';
		
        if (count($n_info) > 0 ) {
            foreach ($n_info as $disciplina_aluno) {            	
            	if($notas[$i] >= 0 && $disciplina_aluno['nota_distribuida'] > 0)
            	{
					echo "<td bgcolor='#EEEEEE'><center><strong>Nota ".$cont."</strong></center></td>";
					echo '<td align="center">'. number::numeric2decimal_br($notas[$i],1) .'</td>';
					$cont++;					
	            }
				$i++;
				
				if($disciplina_aluno['nota_distribuida'] > 0)
				{
					echo '<td align="center">'. number::numeric2decimal_br($disciplina_aluno['nota_distribuida'],1) .'</td>';        
					$nota_disc_maxima += (float) $disciplina_aluno['nota_distribuida'];
					echo "<tr />";
				}
			}
        }
        if($notas[$i]!=-1)
		{
			echo "<td bgcolor='#EEEEEE'><center><strong>Arred./Rec</strong></center></td>";
			echo '<td align="center">'.number::numeric2decimal_br($notas[$i],1).'</td>';
		}
		else
		{
			echo "<td bgcolor='#EEEEEE'><center><strong>Arred./Rec.</strong></center></td>";
			echo '<td align="center"> - </td>';
		}		
		echo '<td align="center"> - </td><tr />';
		echo "<td bgcolor='#EEEEEE'><center><strong>Média Final</strong></center></td>";
		echo '<td align="center">'. number::numeric2decimal_br($media_aluno,1) .'</td>';
        echo '<td align="center">'. number::numeric2decimal_br($nota_disc_maxima,1) .'</td>';
	    //Fim do Exibe notas e pesos;
    ?>
</table>
<br />
<b>Detalhes</b>
<br />
<?php
	$i=0;
	if (count($detalhes_info) > 0 ) {
        foreach ($detalhes_info as $d) {            	
          	if($i < ($cont-1))
				echo "<b>Nota ".($i+1).":</b> ".$d['descricao']."<br />";
				$i++;
		}
	}    
?>
<br />
<div align="left" style="font-size: 0.85em;">
    <h4>Legenda</h4>
	<strong>Arred./Rec.</strong> - Arredondamento / Recuperação<br />
    <strong>A</strong> - Aprovado<br />
    <strong>R</strong> - Reprovado <br />
    <strong>M</strong> - Matriculado <br /><br />
</div>
<br />
Gerado no dia <?php echo date("d/m/Y") ." às ". date("H:i:s"); ?> <br/><br/>
<input type="button" value="Imprimir" onClick="window.print()">&nbsp;&nbsp;&nbsp;<a href="#" onclick="history.back(-1);return false;">Voltar</a>
<br /><br />
<?php include_once('includes/rodape.htm'); ?>      

