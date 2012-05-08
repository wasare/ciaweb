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
    <strong>Período: </strong><?=$rs_periodo?><br />
</p>

<?php
	//Inicio - Victor Ullisses Pugliese - 10h51min 04/05/2012 - Disciplina info;
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
				echo '<disc><b>Disciplina: </b>'. $disciplina_aluno['descricao_disciplina'] . $nao_finalizada . '</disc><p/>';
				//Inicio - Victor Ullisses Pugliese - 10h54 04/05/2012 - Media da Sala 
				$turma_int = intval($turma) + 0.5;
				echo 
				"<table style='font-size: 12px'>
					<tr><b>Média da Turma: </b></td>". number::numeric2decimal_br($turma,1). "</tr>
	   				 <tr>";
   				//Fim da Media da Sala;
   				//IniciÃ‚Â­o: Victor Ullisses Pugliese - 12:38 27/04/2012 - CARGA HORARIA;
            		echo "<td><b>Aulas Dadas:<b/></td><td>". $ch_realizada . "</td>
            		</tr>
            	</table><p />";
            	//Fim
?>

<!--Inicio - Victor Uliisses Pugliese - 15:28 01/05/2012 - Tabela Media -->
<table style="width: 220px; font-size: 12px" >
	<tr bgcolor="#EEEEEE">
		<td><b>Média</b></td>
		<td><b>Faltas</b></td>
		<td><b>% Faltas</b></td>
		<td><b>Situação</b></td>
	</tr>
	<tr bgcolor="#A7E6FE">
		<td><center>
			<?php	
	        	$nota_int = intval($disciplina_aluno['nota_final']) + 0.5;
	   			if($disciplina_aluno['nota_final'] == intval($disciplina_aluno['nota_final']))
	   				echo $disciplina_aluno['nota_final'];
	   			else if($disciplina_aluno['nota_final'] <= $nota_int)
	   				echo ($nota_int);
	   			else
	   				echo ($nota_int+0.5);
	        //Fim Media Aluno;
			?>
		</center></td>
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
<table style="font-size: 12px">
    <tr bgcolor="#EEEEEE">
    	<th></th>
        <?php 
			for( $i = 1; $i <= $quantidade_notas_diario; $i++ ) :
        ?> 
				<th>Nota <?=$i?></th>
        <?php
		    endfor;
        ?>
        <th>Arredondamento /<br /> Recuperação</th>
        <th>Média</th>
    </tr>
    <?php
    //Inicio - Victor Ullisses Pugliese - 11h27min 04/05/2012 - Exibe notas e pesos;
        $cont = $media_aluno = 0;
        
        $color =  ($color != '#ffffff') ? '#ffffff' : '#cce5ff';
        echo '<tr bgcolor="'. $color .'">'; 
        echo "<td><center> - </center></td>";
        if (count($diario_info) > 0 ) {
            foreach ($diario_info as $disciplina_aluno) {
            	if(($cont % 2 == 0) && $cont<=($quantidade_notas_diario+1)*2)
            	{
            		if($disciplina_aluno['nota'] != -1)
            		{
	            		echo '<td align="center">'. number::numeric2decimal_br($disciplina_aluno['nota'],1) .'</td>';        
	            		$media_aluno += (float) $disciplina_aluno['nota'];
	            	}
	            	else
	            		echo '<td align="center"> 0,0 </td>';
	            }
            	$cont++;            	
        }
        
        echo '<td align="center">'. $media_aluno .'</td>';
        echo '</tr>
        	  <tr>
        	  <td><center><b>Nota<br/>Máxima</b></center></td>';
        	  
       	$cont = $nota_disc_maxima = 0;
       	$nDistribuida_sql = 
       	"SELECT nota_distribuida FROM diario_formulas where grupo like '%-$periodo-%-$disciplina_ofer' order by prova;";
       	$nDist_info = $conn->get_all($nDistribuida_sql);
        if (count($nDist_info) > 0 ) {
            foreach ($nDist_info as $disciplina_aluno) {
             	if($disciplina_aluno['nota_distribuida'] != -1)
	           	{
	           		echo '<td align="center">'. $disciplina_aluno['nota_distribuida'] .'</td>';        
	           		$nota_disc_maxima += (float) $disciplina_aluno['nota_distribuida'];
	           	}
	           	else
	           		echo '<td align="center"> 0,0 </td>';
	         	}	         	
            	$cont++;
            	if ($cont == $quantidade_notas_diario) break;
            }
        }
        
        echo '<td align="center">'. '-' .'</td>';
        echo '<td align="center">'. $nota_disc_maxima .'</td>';
	    //Fim do Exibe notas e pesos;
    ?>
</table>
<br />
<?php
	if($situacao == "M")
		echo "(<strong>*</strong>) Disciplina com lançamentos em aberto, passí­vel de alterações.<br /><br />";
?>
<div align="left" style="font-size: 0.85em;">
    <h4>Legenda</h4>
    <strong>A</strong> - Aprovado<br />
    <strong>R</strong> - Reprovado <br />
    <strong>M</strong> - Matriculado <br /><br />
</div>
<br />
Gerado no dia <?php echo date("d/m/Y") ." às ". date("H:i:s"); ?> <br/><br/>
<input type="button" value="Imprimir" onClick="window.print()">&nbsp;&nbsp;&nbsp;<a href="#" onclick="history.back(-1);return false;">Voltar</a>
<br /><br />
<?php include_once('includes/rodape.htm'); ?>      

