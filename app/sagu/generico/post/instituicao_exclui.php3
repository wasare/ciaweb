<?php 

require("../../common.php"); 

$id = $_GET['id'];

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = "delete from instituicoes where id='$id';";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Não foi possível de excluir o registro!");

SuccessPage("Registro excluído com sucesso",
            "location='../consulta_inclui_instituicoes.phtml'");
?>
<HTML><HEAD>
</HEAD>
<BODY></BODY>
</HTML>