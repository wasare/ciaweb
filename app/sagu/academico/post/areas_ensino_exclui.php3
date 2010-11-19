<? 

require("../../common.php"); 

$id = $_GET['id'];

$conn = new Connection;

$conn->Open();

$sql = "delete from areas_ensino where id='$id';";

$ok = $conn->Execute($sql);  // tire o @ para visualizar mensagens de error do sistema DB

$conn->Close();

SaguAssert($ok,"Não foi possível de excluir o registro!");

SuccessPage("Registro excluído com sucesso",
            "location='../areas_ensino.phtml'");

?>