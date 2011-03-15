<?php require_once("../common.php"); ?>
<HTML>
<HEAD>
<?php

$nome = $_GET['nome'];

CheckFormParameters(array("nome"));


$conn = new Connection;
$conn->Open();
$conn->Begin();

$sql = " DROP USER $nome;";
$mensagem = "Exclusão de Usuário...";

$ok = $conn->Execute($sql);  

// Exclui usuário na tabela SAGU_USUARIOS no banco de dados sagu.
$sql2 = " DELETE FROM usuario " .
        " WHERE nome = '$nome';";

$ok2 = $conn->Execute($sql2);  

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Erro ao excluir usuário no banco de dados!");

SaguAssert($ok2,"Erro ao excluir o usuário!");

SuccessPage("$mensagem",
            "location='../consulta_inclui_usuarios.php'",
            "O usuário <b>$nome</b> foi excluído com sucesso!!!");

?>
</HEAD>
<BODY>
</BODY>
</HTML>
