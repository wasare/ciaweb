<?php

require("../../common.php");
require("../../lib/VerificaChaveUnica.php3");

$id                  = $_POST['id'];
$descricao           = $_POST['descricao'];
$abreviatura         = $_POST['abreviatura'];
$sigla               = $_POST['sigla'];
$total_creditos      = $_POST['total_creditos'];
$total_carga_horaria = $_POST['total_carga_horaria'];
$total_semestres     = $_POST['total_semestres'];
$grau_academico      = $_POST['grau_academico'];
$exigencias          = $_POST['exigencias'];
$agrupo_curso        = $_POST['agrupo_curso'];
$ref_area            = $_POST['ref_area'];
$reconhecimento      = $_POST['reconhecimento'];
$autorizacao         = $_POST['autorizacao'];
$turno               = $_POST['turno'];
$ref_tipo_curso      = $_POST['ref_tipo_curso'];
$cabecalho_historico = $_POST['cabecalho_historico'];


CheckFormParameters(array("id",
                            "descricao",
                            "abreviatura",
                            "turno",
                            "agrupo_curso",
                            "ref_tipo_curso"));

SaguAssert(VerificaChaveUnica("cursos", "id", "$id"), "Já existente Curso com esse código!");

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = " insert into cursos (id," .
       "                     descricao," . 
       "                     abreviatura," . 
       "                     sigla," . 
       "                     total_creditos," .
       "                     total_carga_horaria," .
       "                     total_semestres," .
       "                     grau_academico," .
       "                     exigencias," .
       "                     agrupo_curso," .
       "                     ref_area," .
       "                     reconhecimento, " .
       "                     autorizacao, " .
       "                     turno, " .
       "                     ref_tipo_curso, " .
       "                     historico) " .
       " values ('$id'," .
       "         '$descricao'," . 
       "         '$abreviatura',".
       "         '$sigla',".
       "         '$total_creditos',".
       "         '$total_carga_horaria',".
       "         '$total_semestres',".
       "         '$grau_academico',".
       "         '$exigencias',".
       "         '$agrupo_curso',".
       "         '$ref_area',".
       "         '$reconhecimento',".
       "         '$autorizacao',".
       "         '$turno',".
       "         '$ref_tipo_curso', " .
       "         '$cabecalho_historico')";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Não foi possível inserir o curso!!!");

SuccessPage("Inclusão de Cursos",
            "location='../curso_ins.phtml'",
            "Curso incluído com sucesso!!!",
            "location='../consulta_cursos.phtml'");

?>
<html>
<head>
<title></title>
</head>
<body>
</body>
</html>
