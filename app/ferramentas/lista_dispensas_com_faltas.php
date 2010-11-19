<?php


require_once("../setup.php");
//require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

$sqlDispensas = 'SELECT id, ref_pessoa AS "Matricula", pessoa_nome(ref_pessoa) AS "Nome", ref_curso || \' - \' ||  get_curso_abrv(ref_curso) AS "Curso", descricao_disciplina(get_disciplina_de_disciplina_of(ref_disciplina_ofer)) || \' (\' || ref_disciplina_ofer || \')\' || \'   \' || ref_periodo  AS "Disciplina", num_faltas AS "Faltas", ref_disciplina_ofer, ref_motivo_matricula FROM matricula where ref_motivo_matricula <> 0 AND num_faltas > 0 ORDER BY 2,1;';

//$sqlDispensas = 'SELECT id, ref_pessoa AS "Matricula", pessoa_nome(ref_pessoa) AS "Nome", ref_curso || \' - \' ||  get_curso_abrv(ref_curso) AS "Curso", descricao_disciplina(get_disciplina_de_disciplina_of(ref_disciplina_ofer)) AS "Disciplina", nota_final AS "Nota" FROM matricula WHERE id IN (150199,150162,150185,150194,150196,150197,151415,151416,150200,150202,150203,150204,150205,147727,147994,147995,150208,148506,148508,147778,147779,147774,147775,147776,148560,148561,148562,150212,148494,148288,148305,148105,151417,147724,147725,147726,150050,150068,147728,147731,147729,147730,147732,155298,148294,148297,148301) ORDER BY 2,1;';

$dispensas = $conn->get_all($sqlDispensas);

echo 'Matricula &nbsp;/&nbsp; Nome &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ Curso&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/  Disciplina &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / Di&aacute;rio&nbsp;&nbsp;&nbsp; / Per&iacute;odo&nbsp;&nbsp;&nbsp; / Faltas&nbsp;&nbsp;&nbsp;  <br />  ';

$diarios = array();

foreach($dispensas as $d)
{
    $ref_pessoa = $d['Matricula'];
    $pessoas[] = $d['Matricula']; 
    $nome = $d['Nome'];
    $curso = $d['Curso'];
    $disciplina = $d['Disciplina'];
    $faltas = $d['Faltas'];
    $registro_id = $d['id'];
    echo "$ref_pessoa&nbsp;&nbsp;&nbsp;$nome&nbsp;&nbsp;&nbsp;$curso&nbsp;&nbsp;&nbsp;$disciplina&nbsp;&nbsp;&nbsp;$faltas";
    echo '<br />';//echo '&nbsp;&nbsp;&nbsp;<a href="altera_dispensa.php?id='. $registro_id .'">alterar nota</a><br />';
    $diarios[] = array($d['Matricula'], $d['ref_disciplina_ofer'], $d['ref_motivo_matricula']);
    
}

echo '<br />';
/*
foreach($diarios as $d) {
     atualiza_dispensa($d[0],$d[1],$d[2]);
     echo $d[0] .';'. $d[1] .';'. $d[2] .'<br />';
}
*/
?>
