<?php

require("../../common.php");
require_once '../../../../core/login/acl.php';

$conn = new connection_factory($param_conn);

$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Você não tem permissão para acessar este formulário!');
}

$id = $_GET['id'];

CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();

$sql = " delete from pre_requisitos " .
       " where id = '$id' ";

$ok = $conn->Execute($sql);  // tire o @ para visualizar mensagens de error do sistema DB

$conn->Close();

SaguAssert($ok,"Nao foi possivel excluir o registro!");

SuccessPage("Exclusão de Pré-Requisito",
            "location='../consulta_inclui_pre_requisito.php'");

?>