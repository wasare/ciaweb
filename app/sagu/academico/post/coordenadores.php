<?php

require("../../common.php"); 

$ref_campus    = $_POST['ref_campus'];
$ref_cursos    = $_POST['ref_cursos'];
$ref_professor = $_POST['ref_professor'];
$professor     = $_POST['professor'];


CheckFormParameters(array("ref_campus",
                          "ref_cursos",
                          "ref_professor"));

$conn = new Connection;

$conn->Open();

$sql = " insert into coordenador ( " .
       "        ref_professor," .
       "        ref_campus," .
       "        ref_curso)" . 
       " values ( " .
       "        '$ref_professor'," .
       "        '$ref_campus', " .
       "        '$ref_cursos')";


$ok = $conn->Execute($sql);

saguassert($ok,"Nao foi possivel inserir o registro!");

$conn->Close();

SuccessPage("Inclusão de Coordenadores",
            "location='../coordenadores.php'",
            "Coordenador incluído com sucesso!!!.");

?>
<html>
<head>
</head>
<body>
</body>
</html>
