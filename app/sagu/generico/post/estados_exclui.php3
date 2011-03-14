<?php 

require("../../common.php");

$id = $_GET['id'];

CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = "delete from estado" .
       "  where id = '$id'";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Nao foi possivel excluir o registro!");
SuccessPage("Exclusão de Estado",
            "location='../consulta_inclui_estados.phtml'");
?>
<html>
<head>
