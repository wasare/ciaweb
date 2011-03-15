<?php

require("../../common.php");
require("../../lib/InvData.php");


$id                = $_POST['id'];
$ref_professor     = $_POST['ref_professor'];
$professor         = $_POST['professor'];
$ref_departamento  = $_POST['ref_departamento'];
$nome_departamento = $_POST['nome_departamento'];
$dt_ingresso       = $_POST['dt_ingresso'];


CheckFormParameters(array("id",
                          "ref_professor",
                          "ref_departamento",
                          "dt_ingresso"));

$dt_ingresso = InvData($dt_ingresso);

$conn = new Connection;

$conn->Open();

$sql = " UPDATE professores SET " .
       "        ref_professor = '$ref_professor'," .
       "        ref_departamento = '$ref_departamento'," .
       "        dt_ingresso = '$dt_ingresso' " .
       " WHERE id = '$id'";


$ok = $conn->Execute($sql);

SaguAssert($ok,"Nao foi possivel inserir o registro!");

$conn->Close();

SuccessPage("Alteração de Professores",
            "location='../consulta_inclui_professores.php'",
            "Professor alterado com sucesso!!!.") 

?>
<html>
<head>
</head>
<body>
</body>
</html>
