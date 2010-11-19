<?php


require_once("../setup.php");

$conn = new connection_factory($param_conn);

$sqlDispensas = 'SELECT id, ref_pessoa AS "Matricula", pessoa_nome(ref_pessoa) AS "Nome", ref_curso || \' - \' ||  get_curso_abrv(ref_curso) AS "Curso", descricao_disciplina(get_disciplina_de_disciplina_of(ref_disciplina_ofer)) || \' (\' || ref_disciplina_ofer || \')\' || \'   \' || ref_periodo  AS "Disciplina", nota_final AS "Nota", ref_disciplina_ofer FROM matricula where ref_motivo_matricula <> 0 AND nota_final < 50 ORDER BY 2,1;';

$dispensas = $conn->get_all($sqlDispensas);

echo 'Matricula &nbsp;/&nbsp; Nome &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ Curso&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/  Disciplina &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / Di&aacute;rio&nbsp;&nbsp;&nbsp; / Per&iacute;odo&nbsp;&nbsp;&nbsp; / Nota&nbsp;&nbsp;&nbsp;  <br />  ';

foreach($dispensas as $d)
{
    $ref_pessoa = $d['Matricula'];
    $nome = $d['Nome'];
    $curso = $d['Curso'];
    $disciplina = $d['Disciplina'];
    $nota = $d['Nota'];
    $registro_id = $d['id'];
    $diario_id = $d['ref_disciplina_ofer'];
    echo "$ref_pessoa&nbsp;&nbsp;&nbsp;$nome&nbsp;&nbsp;&nbsp;$curso&nbsp;&nbsp;&nbsp;$disciplina&nbsp;&nbsp;&nbsp;$nota";
    echo '&nbsp;&nbsp;&nbsp;<a href="altera_dispensa.php?id='. $registro_id .'">alterar nota</a><br />';
}

?>
