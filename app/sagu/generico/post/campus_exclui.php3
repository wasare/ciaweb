<?php 

require("../../common.php");

$id = $_GET['id'];

CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = " delete from campus " .
       " where id = '$id'";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Nao foi possivel excluir o registro!");
SuccessPage("Exclusão de Campus",
            "location='../campus_inclui.phtml'");
?>
<html>
<head>
</head>
<body>
</body>
</html>
