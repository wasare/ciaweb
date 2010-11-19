<?php 

require("../../common.php"); 

$id = $_GET['id'];

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = "delete from departamentos where id='$id';";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Não foi possível de excluir o registro!");

SuccessPage("Departamento excluído com sucesso",
            "location='../consulta_inclui_departamentos.phtml'");
?>