<?php require_once("../../common.php"); ?>

<script language="PHP">

CheckFormParameters(array("ref_disciplina_ofer",
                          "descricao_disciplina",
                          "num_alunos",
                          "num_sala"));

$conn = new Connection;

$conn->Open();

$sql = " update disciplinas_ofer set " .
       "    num_alunos = '$num_alunos'" .
       " where id = '$ref_disciplina_ofer'";

$ok = $conn->Execute($sql);
                
SaguAssert($ok,"Não foi possível alterar o registro!");

$sql = " update disciplinas_ofer_compl set " .
       "    num_sala = '$num_sala'" .
       " where ref_disciplina_ofer = '$ref_disciplina_ofer'";

$ok = $conn->Execute($sql);

SaguAssert($ok,"Não foi possível alterar o registro!");

$conn->Close();

SuccessPage("Alteração de Disciplinas Oferecidas",
            "location='../disciplina_ofer.phtml'",
            "Disciplina Oferecida alterada com sucesso.");
</script>
