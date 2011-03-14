<?php 

require_once('../../common.php'); 


$ref_empresa   = $_POST['ref_empresa'];
$nome_campus   = $_POST['nome_campus'];
$cidade_campus = $_POST['cidade_campus'];
$ref_campus_sede = $_POST['ref_campus_sede'];


CheckFormParameters(array("ref_empresa",
                          "nome_campus",
                          "cidade_campus",
                          "ref_campus_sede"));

$id_campus = GetIdentity('seq_campus');

$conn = new Connection;
$conn->Open();
$conn->Begin();

$sql = "insert into campus" .
       "  (" .
       "    id," .
       "    ref_empresa," .
       "    nome_campus," .
       "    cidade_campus," .
       "    ref_campus_sede" .
       "  )" .
       "  values" .
       "  (" .
       "    $id_campus," .
       "    '$ref_empresa'," .
       "    '$nome_campus'," .
       "    '$cidade_campus'," .
       "     $ref_campus_sede" .
       "  )";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Nao foi possivel inserir o registro!");
SuccessPage("Inclusão de Campus",
            "location='../campus_inclui.phtml'",
            "O código do campus é <b>$id_campus</b>.");
?>
<html>
<head>
