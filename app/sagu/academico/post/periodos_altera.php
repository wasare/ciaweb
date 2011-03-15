<?php

require("../../common.php");
require("../../lib/InvData.php");

$id                   = $_POST['id'];
$ref_anterior         = $_POST['ref_anterior'];
$descricao            = $_POST['descricao'];
$dt_inicial           = $_POST['dt_inicial'];
$dt_final             = $_POST['dt_final'];
$nota_maxima          = $_POST['nota_maxima'];
$media_final          = $_POST['media_final'];
$dt_inicio_aula       = $_POST['dt_inicio_aula'];

CheckFormParameters(array("id",
                          "ref_anterior",
                          "ref_cobranca",
                          "dt_inicial",
                          "dt_final",
                          "nota_maxima",
                          "media_final",
                          "dt_inicio_aula"));

$conn = new Connection;

$conn->Open();
$conn->Begin();


$dt_inicial = InvData($dt_inicial);
$dt_final = InvData($dt_final);
$dt_inicio_aula = InvData($dt_inicio_aula);


$sql = " update periodos set " .
       "    id = '$id'," .
       "    ref_anterior = '$ref_anterior'," .
       "    descricao = '$descricao'," .
       "    dt_inicial = '$dt_inicial'," .
       "    dt_final = '$dt_final'," .
       "    nota_maxima = '$nota_maxima', " .
       "    media_final = '$media_final', " .
       "    dt_inicio_aula = '$dt_inicio_aula'" .
       " where id = '$id'";

$ok = $conn->Execute($sql);  // tire o @ para visualizar mensagens de error do sistema DB

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Não foi possível alterar o registro!");
SuccessPage("Alteração de Período",
            "location='../consulta_periodos.php'",
            "Período alterado com sucesso.");
?>
<html>
<head>
  <?=$DOC_TYPE?>
</head>
<body></body>
</html>

