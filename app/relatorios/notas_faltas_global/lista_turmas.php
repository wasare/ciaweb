<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once("../../../app/setup.php");

/*
 * Estancia a classe de conexao e abre
 */
$conn = new connection_factory($param_conn);

$resp  = '<strong>Selecione a turma:</strong><br />';

$filtro_sql = " ref_curso = ". $_GET['id_curso'] ." AND ";
if (!is_numeric($_GET['turno']) && !empty($_GET['turno'])) {

  $filtro_sql = " ref_curso IN (SELECT DISTINCT 
                                  o.ref_curso 
                                FROM disciplinas_ofer o LEFT JOIN disciplinas_ofer_compl oc 
                                ON (o.id = oc.ref_disciplina_ofer)
                                WHERE 
                                    oc.turno = '". $_GET['turno'] ."' AND
                                    o.ref_campus = ". $_GET['campus'] ." AND
                                    o.ref_periodo = '". $_GET['periodo'] ."' AND
                                    o.ref_curso = '". $_GET['id_curso'] ."' AND
                                    o.is_cancelada = '0'
                              ) AND ";

}

$sql = "
SELECT DISTINCT turma
FROM contratos
WHERE
    $filtro_sql
    turma is not null AND turma <> ''; ";

$arr_turmas = $conn->get_all($sql);

$count = 0;

foreach($arr_turmas as $turma){

    if($count === 0){
        $checked = 'checked="checked"';
    }else{
        $checked = "";
    }

    $count++;

    $resp .= '<input type="radio" name="turma" id="turma" value="'.$turma['turma'].'" '.$checked.'  />';
    $resp .= $turma['turma'];
    $resp .= '<br />';
}

if ($count == 0)
  echo '<strong>Nenhuma Turma dispon&icaute;vel para os crit&eacute;rios informados!</strong>';
else 
  echo $resp;

?>
