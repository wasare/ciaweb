<?php

require("../../common.php");
require_once '../../../../core/login/acl.php';

$conn = new connection_factory($param_conn);

$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Você não tem permissão para acessar este formulário!');
}

$id = $_GET['id'];

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = "delete from disciplinas_equivalentes where id='$id';";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Não foi possível de excluir o registro!");

SuccessPage("Disciplina Equivalente excluída com sucesso",
            "location='../consulta_disciplinas_equivalentes.php'");

?>