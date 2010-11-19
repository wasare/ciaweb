<?php 

require("../../common.php");
require("../../lib/GetPais.php3"); 

$id = $_POST['id'];
$nome = $_POST['nome'];
$ref_pais = $_POST['ref_pais'];


$pais = GetPais($ref_pais, true);


CheckFormParameters(array(
    			    "id",
 		            "nome",
      		    "ref_pais"));

$conn = new Connection;

$conn->Open();
$conn->Begin();
    
$sql = " insert into estado ( id, nome, ref_pais)" .
       " values ( '$id', '$nome', $ref_pais )";

$ok = $conn->Execute($sql);

SaguAssert($ok,"Nao foi possivel inserir o registro!");

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Não foi possível inserir o registro!");

SuccessPage("Inclusão de Estados",
            "location='../estados_inclui.phtml'",
            "O código do Estado é $id",
            "location='../consulta_inclui_estados.phtml'");
?>

<html>
<head>
</head>
<body>
</body>
</html>
