<?php

/**
* Seleciona a disciplina para dispensar
* @author Wanderson Santiago dos Reis
* @version 1
* @since 04-02-2009
**/

//Arquivos de configuracao e biblioteca
header("Cache-Control: no-cache");
require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/situacao_academica.php');


$conn = new connection_factory($param_conn);

/**
 * @var string
 */
$sa_periodo_id = $_POST['periodo_id'];
/**
 * @var string
 */
$aluno_id = $_POST['codigo_pessoa'];
/**
 * @var string
 */
$id_contrato = $_POST['id_contrato'];
/**
 * @var string
 */
$first = $_POST['first'];
/**
 * @var integer
 */
$checar_turma = $_POST['checar_turma'];

$_SESSION['sa_periodo_id'] = $sa_periodo_id;

$NOTAS = mediaPeriodo($sa_periodo_id);
$MEDIA_FINAL_APROVACAO = $NOTAS['media_final'];

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
  contratos.id = $id_contrato;";

//Exibindo a descricao do curso caso setado
$curso = $conn->get_row($sqlCurso);


/**
 * @var integer
 */
$curso_id   = $curso['id'];
/**
 * @var string
 */
$curso_nome = $curso['descricao'];
/**
 * @var integer
 */
$ref_campus = $curso['ref_campus'];
/**
 * @var string
 */
$turma = $curso['turma'];


$sqlCampus = "SELECT get_campus($ref_campus)";
/**
 * @var string Descricao no campus
 */
$campus_nome = $conn->get_one($sqlCampus);

$sqlAluno = "SELECT nome FROM pessoas WHERE id = $aluno_id;";
/**
 * @var string Nome do aluno
 */
$aluno_nome = $conn->get_one($sqlAluno);

$disciplinas_liberadas = 0;


// EXIBE AS DISCIPLINAS DISPONÍVEIS
// Primeiro acesso na pagina

if ($first){

    // -- Verifica as disciplinas não cursadas do aluno mas com oferta em qualquer tempo
    // CONSIDERA SOMENTE SOMATORIO FINAL DE NOTA E FALTAS
    //  FIXME:  tratar o campus
$sqlDisciplinas = "
SELECT DISTINCT
        o.id as diario, d.descricao_disciplina || ' (' || o.ref_disciplina || ')' as disciplina, o.ref_curso, o.ref_periodo, o.turma
        FROM
                disciplinas d, disciplinas_ofer o, periodos s
        WHERE
                d.id = o.ref_disciplina AND
                d.id = o.ref_disciplina AND
                o.is_cancelada = '0' AND
                s.id = o.ref_periodo AND
                o.ref_campus = '$ref_campus' AND
                o.id IN
        (
SELECT
 DISTINCT
 id FROM
 (";

//-- seleciona as disciplinas do currículo que não foram cursadas mas ofertadas a qualquer tempo
$sqlDisciplinas .= "
SELECT DISTINCT
        o.ref_disciplina as matriculada, o.ref_disciplina
        FROM
                matricula m, disciplinas d, pessoas p, disciplinas_ofer o
        WHERE
                m.ref_pessoa = p.id AND
                p.id = $aluno_id AND
                m.ref_disciplina_ofer = o.id AND
                d.id = o.ref_disciplina AND
                o.is_cancelada = '0' AND
                d.id IN (
                  select distinct ref_disciplina
                        from cursos_disciplinas
                        where ref_curso = $curso_id
                ) AND
                ( m.nota_final < $MEDIA_FINAL_APROVACAO OR
                m.num_faltas > ( d.carga_horaria * 0.25) )
				) AS T1

FULL OUTER JOIN (";

//-- seleciona todas as ofertas de disciplinas em aberto do curriculo aluno, mas sem matricula feita e sem aprovação

$sqlDisciplinas .= "
SELECT DISTINCT
        o.ref_disciplina, o.id
        FROM
                disciplinas d, cursos_disciplinas c, disciplinas_ofer o, periodos s
        WHERE
                c.ref_disciplina = d.id AND
                d.id = o.ref_disciplina AND
                d.id = o.ref_disciplina AND
                o.is_cancelada = '0' AND
                s.id = o.ref_periodo AND
                d.id IN (
                  select distinct ref_disciplina
                        from cursos_disciplinas
                        where ref_curso = $curso_id
                ) AND
				d.id NOT IN (
                    select distinct o.ref_disciplina from
						disciplinas_ofer o where
						   o.id IN (
									select distinct ref_disciplina_ofer
						                 from matricula m
									where
										m.ref_pessoa = $aluno_id AND
                                        m.ref_contrato = $id_contrato )
				)

) AS T2 USING (ref_disciplina)
WHERE matriculada is null
) ORDER BY 2, 4 DESC, 3; ";

// -- o.fl_finalizada = 'f' AND -- somente em diario aberto/finalizado
//
//echo  '<pre>'. $sqlDisciplinas .'</pre>';

    $RsDisciplinas = $conn->Execute($sqlDisciplinas);

    $DisciplinasNaoCursadas = '';
    while(!$RsDisciplinas->EOF){

        $ref_disciplina_ofer  = $RsDisciplinas->fields[0];
        $descricao_disciplina = $RsDisciplinas->fields[1];
        $ref_curso            = $RsDisciplinas->fields[2];
        $ref_periodo          = $RsDisciplinas->fields[3];
        $turma_ofer           = $RsDisciplinas->fields[4];

        $DisciplinasNaoCursadas .= "<input type=\"radio\" name=\"id_diario\" ".
                   "id=\"id_diarios\" value=\"$ref_disciplina_ofer\" onclick=\"Exibe('dispensar')\" />";
        $DisciplinasNaoCursadas .= '&nbsp;&nbsp;';
			$DisciplinasNaoCursadas .= "<strong>$ref_disciplina_ofer - $descricao_disciplina</strong> - $ref_curso - $turma_ofer($ref_periodo) <br />";

			$RsDisciplinas->MoveNext();

        $code++;

    }
}

$count = count($code); //soma quantos diarios

// se existir diarios
if ( $count == 0 ) {

    $DisciplinasNaoCursadas = '
   <div align="center">
       <b><font color="#CC0000">Nenhuma disciplina dispon&iacute;vel</font></b>
   </div>';
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SA</title>
<link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../../lib/prototype.js"></script>
<script language="JavaScript" src="dispensa.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body onload="Oculta('dispensar')">
<div align="center" style="height:600px;">
  <h1>Processo de Dispensa de Disciplina</h1>
  <h4>Sele&ccedil;&atilde;o da disciplina: Etapa 2/3</h4>
  <!--<strong>Identifica&ccedil;&atilde;o do aluno</strong>-->
  <div class="panel"> <strong>Aluno: </strong>
    <?=$aluno_id?>
    -
    <?=$aluno_nome?>
    <br />
    <strong>Curso: </strong>
    <?=$curso_id?>
    -
    <?=$curso_nome?>
    <strong>Turma: </strong>
    <?=$turma?>
    <br />
    <strong>Contrato: </strong>
    <?=$id_contrato?>
    <strong>Cidade: </strong>
    <?=$campus_nome?>
  </div>
  <form name="form1" method="post" action="dispensa_disciplina_tipo.php">
  <div class="panel"> <strong>Disciplinas dispon&iacute;veis para dispensa</strong> <br />( Di&aacute;rio - Disciplina / Curso  / Turma (Per&iacute;odo de oferta)) <br />
    <br />
    <?=$DisciplinasNaoCursadas?>
  </div>
    <!--<input type="hidden" name="periodo_id" value="<\?=$periodo_id?>">-->
    <input type="hidden" name="curso_id" value="<?=$curso_id?>">
    <input type="hidden" name="aluno_id" value="<?=$aluno_id?>">
    <input type="hidden" name="id_contrato" value="<?=$id_contrato?>">
    <input type="hidden" name="ref_campus" value="<?=$ref_campus?>">
    <p>
      <input type="button" value="  Voltar  " onclick="javascript:history.back(-1)" name="Button" />
      <input type="button" name="dispensar" id="dispensar" onclick="envia()" value=">> Prosseguir" />
    </p>
  </form>
</div>
</body>
</html>

