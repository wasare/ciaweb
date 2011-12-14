<?php

require("../../common.php");
require_once '../../../../core/login/acl.php';

$conn = new connection_factory($param_conn);

$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Você não tem permissão para acessar este formulário!');
}

$id                   = $_POST['id'];
$ref_grupo            = $_POST['ref_grupo'];
$ref_departamento     = $_POST['ref_departamento'];
$abreviatura          = $_POST['abreviatura'];
$descricao_disciplina = $_POST['descricao_disciplina'];
$descricao_extenso    = $_POST['descricao_extenso'];
$num_creditos         = $_POST['num_creditos'];
$carga_horaria        = $_POST['carga_horaria'];


CheckFormParameters(array("id",
                          "ref_grupo",
                          "ref_departamento",
                          "abreviatura",
                          "descricao_disciplina"));

$conn = new Connection;

$conn->Open();

$sql = "update disciplinas set " .
       "    id = '$id'," .
       "    ref_grupo = '$ref_grupo'," .
       "    ref_departamento = '$ref_departamento'," .
       "    abreviatura = '$abreviatura'," .
       "    descricao_disciplina = '$descricao_disciplina'," .
       "    descricao_extenso = '$descricao_extenso'," .
       "    num_creditos = '$num_creditos'," .
       "    carga_horaria = '$carga_horaria'" .
       "  where id = '$id'";

$ok = @$conn->Execute($sql);  // tire o @ para visualizar mensagens de error do sistema DB

$conn->Close();

SaguAssert($ok,"Não foi possível alterar o registro!");
SuccessPage("Alteração de Disciplinas",
            "location='../consulta_disciplinas.php'",
            "Disciplina alterada com sucesso.");
?>
