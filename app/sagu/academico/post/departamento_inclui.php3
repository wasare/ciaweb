<?php 

require("../../common.php"); 

$descricao_depto = $_POST['descricao_depto'];

CheckFormParameters(array("descricao_depto"));

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = " insert into departamentos (descricao)" .
       " values ('$descricao_depto')";



$ok = $conn->Execute($sql);

$err= $conn->GetError();

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Não foi possível inserir o registro!<BR><BR>$err");

SuccessPage("Inclusão de Departamento",
            "location='../departamento_inclui.phtml'",
            "Departamento incluído com sucesso!!!",
            "location='../consulta_inclui_departamentos.phtml'");
?>
<html>
<head>
</HEAD>
<BODY></BODY>
</HTML>
