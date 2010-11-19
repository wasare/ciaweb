<?php
// ATUALIZA DIARIOS ANTIGOS PARA REGISTRAR A NOTA EXTRA

//require_once('../webdiario/webdiario.conf.php');


/*
ENCONTRAR DIARIOS SEM A NOTA EXTRA
==================================

SELECT * FROM (SELECT d_ref_disciplina_ofer, ra_cnec, rel_diario_formulas_grupo,
MAX(ref_diario_avaliacao) AS avaliacao_maior,
COUNT(ref_diario_avaliacao) AS registros
FROM diario_notas GROUP BY d_ref_disciplina_ofer,ra_cnec,rel_diario_formulas_grupo) AS T1 WHERE
avaliacao_maior = 6;

*/
/*
$Geral = file(dirname(__FILE__).'/notas_extra.csv');

$sqlNotaExtra = 'BEGIN;';

foreach ( $Geral as $Reg ) 
{

	$campo = explode('|', $Reg);
    
    $ofer = trim($campo[0]);
	$ref_pessoa = trim($campo[1]);
    $grupo = trim($campo[2]);
    
	$itens = explode('-', $grupo);

	$periodo = trim($itens[1]);
    $disc = trim($itens[2]);

    $curso = getCurso($periodo,$disc,$ofer);

    $sqlNotaExtra .= ' INSERT INTO diario_notas(ra_cnec, ';
    $sqlNotaExtra .= ' ref_diario_avaliacao,nota,peso,id_ref_pessoas,';
	$sqlNotaExtra .= ' id_ref_periodos,id_ref_curso,d_ref_disciplina_ofer,';
	$sqlNotaExtra .= ' rel_diario_formulas_grupo)';
	$sqlNotaExtra .= " VALUES($ref_pessoa,'7','-1','0',$ref_pessoa,'$periodo',$curso,";
	$sqlNotaExtra .= " $ofer,'$grupo'); <br />";
}

$sqlNotaExtra .= 'COMMIT;';

echo "<br /> $sqlNotaExtra</br />";


//^ ATUALIZA DIARIOS ANTIGOS PARA REGISTRAR A NOTA EXTRA ^ //
*/


$geral = file(dirname(__FILE__).'/egressos.csv');



$sql = 'BEGIN;<br/>';

foreach($geral as $linha){
	
	$campo = explode(';', $linha);
		
	$sql .= "UPDATE contratos 
	SET dt_formatura = '$campo[2]', dt_conclusao = '$campo[2]'
	WHERE id = '$campo[1]';";
	
	$sql .= "<br/>";

}		

$sql .= 'COMMIT;';




echo "<p>".$sql."</p>";


?>
