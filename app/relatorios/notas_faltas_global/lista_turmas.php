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

$sql = "
SELECT DISTINCT turma
FROM contratos
WHERE
    ref_curso = ".$_GET['id_curso']." AND
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

echo $resp;

?>