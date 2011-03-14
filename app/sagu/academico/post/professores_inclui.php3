<?php 

require("../../common.php");
require("../../lib/InvData.php3"); 


$ref_professor     = $_POST['ref_professor'];
$professor         = $_POST['professor'];
$ref_departamento  = $_POST['ref_departamento'];
$nome_departamento = $_POST['nome_departamento'];
$dt_ingresso       = $_POST['dt_ingresso'];


CheckFormParameters(array("ref_professor",
                          "ref_departamento",
                          "dt_ingresso"));

$dt_ingresso = InvData($dt_ingresso);

$conn = new Connection;

$conn->Open();

$sql = " insert into professores ( " .
       "        ref_professor," .
       "        ref_departamento," .
       "        dt_ingresso)" . 
       " values ( " .
       "        '$ref_professor'," .
       "        '$ref_departamento', " .
       "        '$dt_ingresso')";


$ok = $conn->Execute($sql);

SaguAssert($ok,"Nao foi possivel inserir o registro!");

$conn->Close();

SuccessPage("Inclusão de Professores",
            "location='../professores_inclui.phtml'",
            "Professor incluído com sucesso!!!.", 
            "location='../consulta_inclui_professores.phtml'");

?>
<html>
<head>
</head>
<body>
</body>
</html>
