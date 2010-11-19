<?php

require("../../common.php");
require_once '../../../../core/login/acl.php';

$conn = new connection_factory($param_conn);

$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Vocк nгo tem permissгo para acessar este formulбrio!');
}

require("../../lib/InvData.php3");



$ref_curso           = $_POST['ref_curso'];
$curso_id            = $_POST['curso_id'];
$curso               = $_POST['curso'];
$ref_campus          = $_POST['ref_campus'];
$campus_id           = $_POST['campus_id'];
$ref_disciplina      = $_POST['ref_disciplina'];
$disciplina_id       = $_POST['disciplina_id'];
$ref_disciplina_nome = $_POST['ref_disciplina_nome'];
$semestre_curso      = $_POST['semestre_curso'];
$curriculo_mco       = $_POST['curriculo_mco'];
$dt_inicio_curriculo = $_POST['dt_inicio_curriculo'];
$dt_final_curriculo  = $_POST['dt_final_curriculo'];
$pre_requisito_hora  = $_POST['pre_requisito_hora'];
$ref_area            = $_POST['ref_area'];
$area                = $_POST['area'];
$exibe_historico     = $_POST['exibe_historico'];


CheckFormParameters(array("ref_curso",
                          "curso_id",
                          "ref_campus",
                          "campus_id",
                          "ref_disciplina",
                          "disciplina_id",
                          "semestre_curso",
                          "curriculo_mco",
                          "exibe_historico"));

$dt_inicio_curriculo = InvData($dt_inicio_curriculo);
$dt_final_curriculo = InvData($dt_final_curriculo);

$conn = new Connection;

$conn->Open();

$sql = " update cursos_disciplinas set " .
       " ref_curso = '$ref_curso'," .
       " ref_campus = '$ref_campus'," .
       " ref_disciplina = '$ref_disciplina'," .
       " semestre_curso = '$semestre_curso'," .
       " curriculo_mco = '$curriculo_mco'," .
       " equivalencia_disciplina = '$equivalencia_disciplina'," .
       " cursa_outra_disciplina = '$cursa_outra_disciplina'," .
       " pre_requisito_hora = '$pre_requisito_hora'," .
       " ref_area = '$ref_area'," .
       " esconde_historico = '$esconde_historico'," ;

if ($dt_inicio_curriculo == ''){
	$sql=$sql . "dt_inicio_curriculo = null," ;
}else{
	$sql=$sql . "dt_inicio_curriculo= '$dt_inicio_curriculo'," ;
}

if ($dt_final_curriculo == ''){
	$sql=$sql . "dt_final_curriculo = null," ;
}else{
	$sql=$sql . "dt_final_curriculo= '$dt_final_curriculo'," ;
}

$sql=$sql . " curso_substituido = '$curso_substituido'," .
                   " disciplina_substituida = '$disciplina_substituida',";

if ( ($exibe_historico == 'S') || ($exibe_historico == 'Sim') ){
	$sql=$sql . " exibe_historico = 'S' ";
}else{
	$sql=$sql . " exibe_historico = 'N' ";
}

$sql=$sql . "   where ref_curso='$curso_id' and ref_campus='$campus_id' and ref_disciplina='$disciplina_id'";

$ok = $conn->Execute($sql);  // tire o @ para visualizar mensagens de error do sistema DB

$conn->Close();

SaguAssert($ok,"Nгo foi possнvel alterar o registro!");
SuccessPage("Alteraзгo de Curso/Disciplina",
            "location='../consulta_inclui_cursos_disciplinas.phtml'",
            "Curso/Disciplina alterado com sucesso.");
?>