<? 

require("../../common.php");


$nome       = $_POST['nome'];
$cep        = $_POST['cep'];
$ref_pais   = $_POST['ref_pais'];
$ref_estado = $_POST['ref_estado'];


CheckFormParameters(array("nome",
                          "cep",
                          "ref_pais",
                          "ref_estado"));

$id = GetIdentity('cidade_id_seq');

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = "insert into cidade (" .
       "     id," .
       "     nome," .
       "     cep," .
       "     ref_pais," .
       "     ref_estado" .
       " ) values (" .
       "     '$id'," .
       "     '$nome'," .
       "     '$cep'," .
       "     '$ref_pais'," .
       "     '$ref_estado'" .
       " )";

$ok = $conn->Execute($sql);

$err = $conn->GetError();

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Nao foi possivel inserir o registro!<br><br>$err");

SuccessPage("Inclusão de Cidades",
            "location='../cidades_inclui.php'",
            "O código da Cidade é $id",
            "location='../consulta_cidades.php'");

?>
<html>
    <head>
        <title>Cadastro de Cidade</title>
    </head>
    <body>
    </body>
</html>
