<?php

require("../../common.php");
require("../../lib/InvData.php");


$id                   = $_POST['id'];
$descricao            = $_POST['descricao'];
$ref_anterior         = $_POST['ref_anterior'];
$ref_cobranca         = $_POST['ref_cobranca'];
$ref_origem           = $_POST['ref_origem'];
$origem               = $_POST['origem'];
$ref_local            = $_POST['ref_local'];
$dt_inicial           = $_POST['dt_inicial'];
$dt_final             = $_POST['dt_final'];
$nota_maxima          = $_POST['nota_maxima'];
$media_final          = $_POST['media_final'];
$dt_inicio_aula       = $_POST['dt_inicio_aula'];
$tx_acresc            = $_POST['tx_acresc'];
$tx_cancel            = $_POST['tx_cancel'];
$ref_status_vest      = $_POST['ref_status_vest'];

CheckFormParameters(array("id",
                          "descricao",
                          "dt_inicial",
                          "dt_final",
                          "nota_maxima",
                          "media_final",
                          "dt_inicio_aula"));

$dt_inicial = InvData($dt_inicial);
$dt_final = InvData($dt_final);
$dt_inicio_aula = InvData($dt_inicio_aula);

if ( $fl_livro_matricula == "yes" )
{ $fl_livro_matricula = '1'; }
else
{ $fl_livro_matricula = '0'; }

$conn = new Connection;

$conn->Open();
$conn->Begin();


$sql = "
INSERT INTO periodos (
    id,
    descricao,
    ref_anterior,
    dt_inicial,
    dt_final,
    ref_status_vest,
    fl_livro_matricula,
    nota_maxima,
    media_final,
    dt_inicio_aula,
    ref_historico,
    ref_historico_dce
) VALUES (
    '$id',
    '$descricao',
    '$ref_anterior',
    '$dt_inicial',
    '$dt_final',
    '$ref_status_vest',
    '$fl_livro_matricula',
    '$nota_maxima',
    '$media_final',
    '$dt_inicio_aula',
    '1',
    '0'
)";

$ok = $conn->Execute($sql);

$err= $conn->GetError();

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Não foi possível inserir o registro!<br /><br />$err");

SuccessPage("Inclusão de Período",
            "location='../periodos.php'",
            "O código do período é <b>$id</b>.",
            "location='../consulta_periodos.php'");
?>
<html>
<head>
  <?=$DOC_TYPE?>
</head>
<body></body>
</html>

