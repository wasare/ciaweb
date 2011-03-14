<?php

require_once("../../common.php");
require("../../lib/InvData.php3");


$id                     = $_POST['id'];
$ref_campus             = $_POST['ref_campus'];
$ref_pessoa             = $_POST['ref_pessoa'];
$ref_curso              = $_POST['ref_curso'];
$turma                  = $_POST['turma'];
$ref_periodo_turma      = $_POST['ref_periodo_turma'];
$id_vestibular          = $_POST['id_vestibular'];
$dia_vencimento         = $_POST['dia_vencimento'];
$dt_ativacao            = $_POST['dt_ativacao'];
$ref_motivo_inicial     = $_POST['ref_motivo_inicial'];
$ref_motivo_ativacao    = $_POST['ref_motivo_ativacao'];
$status                 = $_POST['status'];
$dt_conclusao           = $_POST['dt_conclusao'];
$dt_desativacao         = $_POST['dt_desativacao'];
$ref_motivo_desativacao = $_POST['ref_motivo_desativacao'];
$obs_desativacao        = $_POST['obs_desativacao'];
$dt_formatura           = $_POST['dt_formatura'];
$dt_provao              = $_POST['dt_provao'];
$dt_diploma             = $_POST['dt_diploma'];
$dt_apostila            = $_POST['dt_apostila'];
$ref_last_periodo       = $_POST['ref_last_periodo'];
$ref_periodo_formatura  = $_POST['ref_periodo_formatura'];
$fl_ouvinte             = $_POST['fl_ouvinte'];
$fl_formando            = $_POST['fl_formando'];
$semestre               = $_POST['semestre'];
$obs                    = $_POST['obs'];



CheckFormParameters(array("id",
                          "ref_campus",
                          "ref_pessoa",
                          "ref_curso",
                          "dt_ativacao",
                          "ref_motivo_ativacao",
                          "ref_motivo_inicial",
                          "ref_last_periodo",
                          "fl_ouvinte",
                          "fl_formando"));


if ( $fl_ouvinte == "yes" )
{ $is_ouvinte = '1'; }
else
{ $is_ouvinte = '0'; }

if ( $fl_formando == "yes" )
{ $is_formando = '1'; }
else
{ $is_formando = '0'; }

$conn = new Connection;
$conn->Open();
$conn->Begin();

$dt_ativacao = InvData($dt_ativacao);
$dt_desativacao = InvData($dt_desativacao);
$dt_formatura = InvData($dt_formatura);
$dt_provao = InvData($dt_provao);
$dt_diploma = InvData($dt_diploma);
$dt_apostila = InvData($dt_apostila);
$dt_conclusao = InvData($dt_conclusao);
$semestre = $semestre ? $semestre : '0';

$sql = " update contratos set " .
       "    ref_campus = '$ref_campus', " .
       "    semestre = '$semestre', " .
       "    ref_curso = '$ref_curso', " .
       "    dt_ativacao = '$dt_ativacao'," .
       "    id_vestibular = '$id_vestibular'," .	   
       "    ref_motivo_entrada = '$ref_motivo_entrada'," .
       "    ref_motivo_ativacao = '$ref_motivo_ativacao'," .
       "    ref_motivo_desativacao = '$ref_motivo_desativacao'," .
       "    ref_motivo_inicial = '$ref_motivo_inicial',";

if ( $dt_desativacao=='' )
{ $sql = $sql . "  dt_desativacao = null,"; }
else
{ $sql = $sql . "  dt_desativacao = '$dt_desativacao',";  }

if ( $obs_desativacao=='' )
{ $sql = $sql . "  obs_desativacao = null,"; }
else
{ $sql = $sql . "  obs_desativacao = '$obs_desativacao',";  }

$sql = $sql . " obs = '$obs'," .
       "    desconto = '$desconto',";

if ( $dt_conclusao=='' )
{ $sql = $sql . "  dt_conclusao = null,"; }
else
{ $sql = $sql . "  dt_conclusao = '$dt_conclusao',";  }

if ( $dt_formatura=='' )
{ $sql = $sql . "  dt_formatura = null,"; }
else
{ $sql = $sql . "  dt_formatura = '$dt_formatura',";  }

if ( $ref_periodo_turma=='' )
{ $sql = $sql . "  ref_periodo_turma = null,"; }
else
{ $sql = $sql . "  ref_periodo_turma = '$ref_periodo_turma',";  }

if ( $turma=='' )
{ $sql = $sql . "  turma = null,"; }
else
{ $sql = $sql . "  turma = '$turma',";  }

if ( $dt_provao=='' )
{ $sql = $sql . "  dt_provao = null,"; }
else
{ $sql = $sql . "  dt_provao = '$dt_provao',";  }

if ( $dt_diploma=='' )
{ $sql = $sql . "  dt_diploma = null,"; }
else
{ $sql = $sql . "  dt_diploma = '$dt_diploma',";  }

if ( $dt_apostila=='' )
{ $sql = $sql . "  dt_apostila = null,"; }
else
{ $sql = $sql . "  dt_apostila = '$dt_apostila',";  }

$sql = $sql . " ref_last_periodo = '$ref_last_periodo'," .
       "    fl_ouvinte = '$is_ouvinte'," .
       "    fl_formando = '$is_formando'," .
       "    percentual_pago = '$percentual_pago'," .
       "    cod_status = '$status', " .
       "    ref_periodo_formatura = '$ref_periodo_formatura', " . 
       "    dia_vencimento = '$dia_vencimento'" .
       "  where" .
       "    id = '$id'";


$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Nao foi possivel de atualizar o registro!");

SuccessPage("Alteração do Contrato",
            "location='../consulta_inclui_contratos.phtml'",
            "Contrato alterado com sucesso.");
?>

