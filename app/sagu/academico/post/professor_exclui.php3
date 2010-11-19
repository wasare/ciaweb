<?php

require("../../common.php"); 

$id = $_GET['id'];

CheckFormParameters(array("id"));

$conn = new Connection;
$conn->Open();

$sql = " delete from professores where id = '$id'";

$ok = $conn->Execute($sql);
$conn->Close();

SaguAssert($ok,"Não foi possível de excluir o professor!");
SuccessPage("Professor excluído com sucesso",
            "location='../consulta_inclui_professores.phtml'");

?>