<?php

require("../../common.php");
require_once '../../../../core/login/acl.php';

$conn = new connection_factory($param_conn);

$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Você não tem permissão para acessar este formulário!');
}

require("../../lib/InvData.php");


$ref_curso = $_POST['ref_curso'];
$ref_disciplina = $_POST['ref_disciplina'];
$ref_disciplina_pre = $_POST['ref_disciplina_pre'];
$ref_area = $_POST['ref_area'];
$horas_area = $_POST['horas_area'];
$tipo = $_POST['tipo'];
$id = $_POST['id'];

CheckFormParameters(array("id",
                          "ref_curso",
                          "ref_disciplina",
                          "tipo"));

$conn = new Connection;
$conn->Open();

$tipo = substr($tipo, 0, 1);

$sql = " update pre_requisitos set " .
       "    ref_curso = '$ref_curso', " .
       "    ref_disciplina = '$ref_disciplina', " .
       "    ref_disciplina_pre = '$ref_disciplina_pre'," .
       "    ref_area = '$ref_area'," .
       "    horas_area = '$horas_area',".
       "    tipo = '$tipo'".
       " where id = '$id'" ;


$ok = $conn->Execute($sql);  // tire o @ para visualizar mensagens de error do sistema DB

$conn->Close();

SaguAssert($ok,"Nao foi possivel de atualizar o registro!");

SuccessPage("Alteração do Pré-Requisito",
            "location='../consulta_inclui_pre_requisito.php'");

?>