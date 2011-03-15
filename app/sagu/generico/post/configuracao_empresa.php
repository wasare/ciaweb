<?php 

require("../../common.php"); 

$razao_social = $_POST['razao_social'];
$sigla        = $_POST['sigla'];
$rua          = $_POST['rua'];
$complemento  = $_POST['complemento'];
$bairro       = $_POST['bairro'];
$cep          = $_POST['cep'];
$ref_cidade   = $_POST['ref_cidade'];

CheckFormParameters(array("razao_social",
                          "sigla",
                          "rua",
                          "bairro",
                          "cep",
                          "ref_cidade"));

$id_config_empresa = GetIdentity("seq_configuracao_empresa");

$conn = new Connection;
$conn->Open();
$conn->Begin();

$sql = "insert into configuracao_empresa" .
       "  (" .
       "    id," .
       "    razao_social," .
       "    sigla," .
       "    logotipo," .
       "    rua," .
       "    complemento," .
       "    bairro," .
       "    cep," .
       "    ref_cidade" .
       "  )" .
       "  values" .
       "  (" .
       "    '$id_config_empresa'," .
       "    '$razao_social'," .
       "    '$sigla'," .
       "    '$logotipo'," .
       "    '$rua'," .
       "    '$complemento'," .
       "    '$bairro'," .
       "    '$cep'," .
       "    '$ref_cidade'" .
       "  )";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Nao foi possivel inserir o registro!");
SuccessPage("Inclusão de Empresa",
            "location='../configuracao_empresa.php'",
            "O código da empresa é <b>$id_config_empresa</b>.");
?>
<html>
<head>
