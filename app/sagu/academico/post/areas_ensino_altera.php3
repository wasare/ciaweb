<?php

require("../../common.php"); 

$id = $_POST['id'];
$area = $_POST['area'];

CheckFormParameters(array("id", "area"));

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = " update areas_ensino set " .
       "    id = '$id'," .
       "    area = '$area'" .
       " where id = '$id'";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Não foi possível alterar o registro!");
SuccessPage("Alteração de Área de Ensino",
            "location='../areas_ensino.phtml'",
            "Área de Ensino alterada com sucesso.");

?>