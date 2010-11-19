<?php 

require("../../common.php"); 

$id = $_POST['id'];
$nome = $_POST['nome'];
 

CheckFormParameters(array("id","nome"));

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = " update pais set " .
       "    id = '$id'," .
       "    nome = '$nome'" .
       " where id = '$id'";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Nao foi possivel de atualizar o registro!");

SuccessPage("Alteração de Países",
	        "location='../paises_inclui.phtml'",
	        "As informações do país <b>$nome</b> foram atualizadas com sucesso.");
?>
<html>
<head>
</head>
<body>
</body>
</html>
