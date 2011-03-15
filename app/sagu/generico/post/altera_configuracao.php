<?php 

require("../../common.php"); 

$id           = $_POST['id'];
$razao_social = $_POST['razao_social'];
$sigla        = $_POST['sigla'];
$rua          = $_POST['rua'];
$complemento  = $_POST['complemento'];
$bairro       = $_POST['bairro'];
$cep          = $_POST['cep'];
$ref_cidade   = $_POST['ref_cidade'];


CheckFormParameters(array("id","razao_social","sigla","rua","bairro","cep","ref_cidade"));

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = " update configuracao_empresa set " .
       "    id = '$id'," .
       "    razao_social = '$razao_social', " .
       "    sigla = '$sigla', " .
       "    rua = '$rua', " .
       "    complemento = '$complemento', " .
       "    bairro = '$bairro', " .
       "    cep = '$cep', " .
       "    ref_cidade = '$ref_cidade' " .
       " where id = '$id'";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"N&atilde;o foi poss&iacute;vel de atualizar o registro!");

SuccessPage("Altera&ccedil;&atilde;o da Empresa",
	        "location='../configuracao_empresa.php'",
	        "As informa&ccedil;&otilde;es da empresa <b>$nome</b> foram atualizadas com sucesso.");
?>
<html>
<head>
</head>
<body>
</body>
</html>
