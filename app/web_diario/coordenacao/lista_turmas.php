<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once('../../../app/setup.php');

/*
 * Estancia a classe de conexao e abre
 */
$conn = new connection_factory($param_conn);

$resp  = '<strong>Selecione a turma:</strong><br />';

$filtro_sql = " ref_curso = ". $_GET['id_curso'] ." AND ";
if (!is_numeric($_GET['turno']) && !empty($_GET['turno'])) {

    $filtro_sql = " c.id IN (SELECT DISTINCT 
                                  m.ref_contrato 
                                FROM disciplinas_ofer o 
                                  LEFT JOIN disciplinas_ofer_compl oc 
                                ON (o.id = oc.ref_disciplina_ofer) 
                                  LEFT JOIN matricula m
                                ON (o.id = m.ref_disciplina_ofer)
                                                          
                                WHERE 
                                    oc.turno = '". $_GET['turno'] ."' AND
                                    o.ref_campus = ". $_GET['campus'] ." AND
                                    o.ref_periodo = '". $_GET['periodo'] ."' AND
                                    o.ref_curso = '". $_GET['curso_id'] ."' AND
                                    o.is_cancelada = '0'
                              ) AND ";
}

$sql = "
SELECT DISTINCT turma
FROM contratos c
WHERE
    $filtro_sql
    c.turma is not null AND
    c.ref_campus = ". $_GET['campus'] ." AND
    c.ref_curso = ". $_GET['curso_id'] ." AND
    c.turma <> '' ORDER BY c.turma DESC; ";

$arr_turmas = $conn->get_all($sql);

$count = 0;
$resp = '';

foreach($arr_turmas as $turma){

    $url = '';
    $url .= $BASE_URL .'app/web_diario/coordenacao/exibe_notas_faltas_global.php?curso_id='. $_GET['curso_id'];
    $url .= '&periodo_id='. $_GET['periodo'];
    $url .= '&campus='. $_GET['campus'];
    $url .= '&turma='. $turma['turma'];
    $url .= '&turno='. $_GET['turno'];
    
    $resp .= '<a href="#" onclick="abrir(\'Sistema Acadêmico\', \''. $url .'\')" title="clique para visualizar">'. $turma['turma'] .'</a> <br />';

    $count++;

}

if ($count == 0)
  echo '<strong>Nenhuma turma encontrada para os critérios informados!</strong>';
else 
  echo '<strong>Clique na turma desejada:</strong><br />'.$resp;

?>
