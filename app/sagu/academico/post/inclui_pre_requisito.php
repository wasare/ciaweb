<?php 

require("../../common.php");


$ref_curso          = $_POST['ref_curso'];
$ref_disciplina     = $_POST['ref_disciplina'];
$tipo               = $_POST['tipo'];
$curso              = $_POST['curso'];
$disciplina         = $_POST['disciplina'];
$ref_disciplina_pre = $_POST['ref_disciplina_pre'];
$disciplina_pre     = $_POST['disciplina_pre'];
$ref_area           = $_POST['ref_area'];
$area               = $_POST['area'];
$horas_area         = $_POST['horas_area'];

CheckFormParameters(array("ref_curso",
                          "ref_disciplina",
                          "tipo"));

$conn = new Connection;

$conn->Open();
$conn->Begin();

$ref_disciplina_pre = $ref_disciplina_pre ? $ref_disciplina_pre : 'NULL';

$sql = "insert into pre_requisitos ( " .
       "    ref_curso," .
       "    ref_disciplina," .
       "    ref_disciplina_pre," .
       "    ref_area," .
       "    horas_area," .
       "    tipo" .
       "  ) values ( " .
       "    '$ref_curso'," .
       "    '$ref_disciplina'," .
       "    $ref_disciplina_pre," .
       "    '$ref_area'," .
       "    '$horas_area'," .
       "    '$tipo'" .
       "  )";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Nao foi possivel inserir o registro!");

SuccessPage("Pré-Requisito incluído com sucesso!",
            "location='../inclui_pre_requisito.php'",
            "Pré-Requisito incluído com sucesso!",
            "location='../consulta_inclui_pre_requisito.php'");

?>