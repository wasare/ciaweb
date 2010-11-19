<?php

require("../../common.php");

$id = $_POST['id'];
$nome = $_POST['nome'];
$texto = $_POST['texto'];
$ref_setor = $_POST['ref_setor'];

CheckFormParameters(array("id","nome","texto","ref_setor"));

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = " update carimbos set " .
       "      nome = '$nome'," .
       "      texto = '$texto'," .
       "      ref_setor = '$ref_setor'" .
       " where id = '$id'";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Nгo foi possнvel alterar o registro!");

SuccessPage("Alteraзгo do Carimbo",
            "location='../carimbos.phtml'",
            "Carimbo alterado com sucesso.");
?>