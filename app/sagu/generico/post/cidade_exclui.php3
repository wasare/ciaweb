<? 

require("../../common.php"); 

$id = $_GET['id'];

?>

<html>
<head>
<?php 

CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = " delete from cidade" .
       " where id = '$id'";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Nao foi possivel excluir o registro!");
SuccessPage("Exclusão de Cidades",
            "location='../consulta_cidades.phtml'");

?>
