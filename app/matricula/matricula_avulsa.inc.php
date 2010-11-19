<?php

/**
* Seleciona as disciplinas para matricular
* @author Santiago Silva Pereira, Wanderson S. Reis
* @version 1
* @since 04-02-2009
**/
require_once(dirname(__FILE__) .'/../setup.php');
require_once($BASE_DIR .'core/situacao_academica.php');

$conn = new connection_factory($param_conn);

$sa_periodo_id = (string) $_POST['periodo_id'];
$aluno_id = (int) $_POST['codigo_pessoa'];
$contrato_id = (int) $_POST['contrato_id'];
$first = $_POST['first'];
$checar_turma = $_POST['checar_turma'];

$_SESSION['sa_periodo_id'] = $sa_periodo_id;
$_SESSION['sa_aluno_id'] = $aluno_id;

$periodo_id = $sa_periodo_id;


//Limpa a matriz de sessao com os diarios
$_SESSION['DiarioMatricular'] = '';
unset($_SESSION['DiarioMatricular']);


$sqlCurso = "
SELECT 
  cursos.id,
  cursos.descricao,
  contratos.ref_campus,
  contratos.turma
FROM
  contratos, cursos
WHERE
  cursos.id = contratos.ref_curso AND
  contratos.id = $contrato_id;";

//Exibindo a descricao do curso caso setado
$RsCurso = $conn->Execute($sqlCurso);

$curso_id   = $RsCurso->fields[0];
$curso_nome = $RsCurso->fields[1];
$ref_campus = $RsCurso->fields[2];
$turma = $RsCurso->fields[3];


$sqlCampus = "SELECT get_campus($ref_campus)";
$RsCampus = $conn->Execute($sqlCampus);
$ref_campus = $RsCurso->fields[2];

$campus_nome = $RsCampus->fields[0];


$sqlAluno = "SELECT nome FROM pessoas WHERE id = $aluno_id;";
$RsAluno = $conn->Execute($sqlAluno);
$ref_campus = $RsCurso->fields[2];

$aluno_nome = $RsAluno->fields[0];


$disciplinas_liberadas = 0;


//EXIBE AS DISCIPLINAS MATRICULADAS
//Primeiro acesso na pagina

if ($first){

    $sqlDisciplinas = "
    SELECT
        A.ref_curso,
        A.ref_disciplina_ofer,
        B.ref_disciplina,
        descricao_disciplina(B.ref_disciplina),
        A.ref_curso_subst,
        B.ref_curso,
        A.ref_disciplina_subst,
        descricao_disciplina(A.ref_disciplina_subst),
        professor_disciplina_ofer_todos(B.id),
        get_dia_semana_abrv(dia_disciplina_ofer_todos(B.id)),
        turno_disciplina_ofer_todos(B.id),
        A.status_disciplina,
        B.is_cancelada
    FROM
        matricula A, disciplinas_ofer B
    WHERE
        A.ref_periodo = '$sa_periodo_id' AND
        A.ref_pessoa  = $aluno_id AND
        A.ref_curso   = '$curso_id' AND
        A.ref_contrato = '$contrato_id' AND
        B.id = A.ref_disciplina_ofer AND
        A.dt_cancelamento IS NULL
    ORDER BY A.id";

    $RsDisciplinas = $conn->Execute($sqlDisciplinas);

    while(!$RsDisciplinas->EOF){

        $ref_curso            = $RsDisciplinas->fields[0];
        $ref_disciplina_ofer  = $RsDisciplinas->fields[1];
        $ref_disciplina       = $RsDisciplinas->fields[2];
        $nome1                = $RsDisciplinas->fields[3];
        $ref_curso_subst      = $RsDisciplinas->fields[4];
        $ref_curso_ofer       = $RsDisciplinas->fields[5];
        $ref_disciplina_subst = $RsDisciplinas->fields[6];
        $nome2                = $RsDisciplinas->fields[7];
        $prof                 = $RsDisciplinas->fields[8];
        $dia_semana           = $RsDisciplinas->fields[9];
        $turno                = $RsDisciplinas->fields[10];
        $status_disciplina    = $RsDisciplinas->fields[11];
        $is_cancelada         = $RsDisciplinas->fields[12];

        $code1[] = $ref_disciplina;
        $code2[] = $ref_disciplina_subst == 0 ? '' : $ref_disciplina_subst;
        $desc2[] = $nome2;

        $disc_cancelada = ($is_cancelada == 1) ? '&nbsp;<strong>*</strong>&nbsp;' : '&nbsp;&nbsp;&nbsp;&nbsp;';

        if ( !$ref_disciplina_subst ){

            $desc1[] = $disc_cancelada . $ref_disciplina_ofer.' - '.$nome1;
            $ofer1[]   = $ref_disciplina_ofer;
            $ofer2[]   = '';
            $curso1[]  = $ref_curso_ofer;
            $curso2[]  = '';
            $prof1[]   = $prof;
            $prof2[]   = '';
            $day1[]    = $dia_semana;
            $day2[]    = '';
            $turno1[]  = $turno;
            $turno2[]  = '';
            $status1[] = $status_disciplina;
            $status2[] = '';

        }
        else {

            $desc1[] = $disc_cancelada . $ref_disciplina_ofer.' - '.$nome1;

            $ofer1[]   = '';
            $ofer2[]   = $ref_disciplina_ofer;
            $curso1[]  = '';
            $curso2[]  = $ref_curso_ofer;
            $prof1[]   = '';
            $prof2[]   = $prof;
            $day1[]    = '';
            $day2[]    = $dia_semana;
            $turno1[]  = '';
            $turno2[]  = $turno;
            $status1[] = $status_disciplina;
            $status2[] = $status_disciplina;
        }

        $RsDisciplinas->MoveNext();

    }
}

$count = count($code1); //soma quantos diarios

//se existir diarios
if ( $count != 0 ) {

    //Percorre os diarios
    for ( $i=0; $i<$count; $i++ ) {

        if ( $code1[$i] == '' )
            continue;

        $DisciplinasMatriculadas .= "<strong>".$desc1[$i]."</strong> (".$code1[$i].") - ".$prof1[$i]."<br />";
    }
    $autorizado = 'true';
}
else 
{
    $DisciplinasMatriculadas = '
   <div align="center">
       <b><font color="#CC0000">Nenhuma disciplina matriculada</font></b>
   </div>';
}

?>
