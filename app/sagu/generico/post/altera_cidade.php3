<?php 

require("../../common.php");


$id         = $_POST['id'];
$nome       = $_POST['nome'];
$cep        = $_POST['cep'];
$ref_pais   = $_POST['ref_pais']; 
$ref_estado = $_POST['ref_estado'];


CheckFormParameters(array("id","nome","cep"));

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = "update cidade set " .
       "    nome = '$nome'," .
       "    cep = '$cep'," .
       "    ref_pais = '$ref_pais'," .
       "    ref_estado = '$ref_estado' " .
       "  where id = '$id'";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Nao foi possivel de atualizar o registro!");

SuccessPage("Alteração de Cidade",
            "location='../consulta_cidades.phtml'",
	        "As informações da Cidade <b>$nome</b> foram atualizadas com sucesso.");
?>
<html>
<head>
