<?php

require("../../common.php");


$id = $_POST['id'];
$nome = $_POST['nome'];
$ref_pais = $_POST['ref_pais'];


CheckFormParameters(array("id","nome","ref_pais"));

$conn = new Connection;

$conn->Open();

$conn->Begin();

$sql = " update estado set " .
       "    id = '$id'," .
       "    nome = '$nome'," .
       "    ref_pais = '$ref_pais'" .
       " where id = '$id'";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Nao foi possivel de atualizar o registro!");

SuccessPage("Alteração de Estado",
  	        "location='../consulta_inclui_estados.php'",
	        "As informações do estado <b>$nome</b> foram atualizadas com sucesso.");
?>
<html>
<head>
</head>
<body>
</body>
</html>
