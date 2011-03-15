<?php

require("../../common.php");
require("../../lib/CheckUnique.php");
require("../../properties.php");
require("../../lib/GetField.php");
require("../../lib/InvData.php");



$ref_campus             = $_POST['ref_campus'];
$ref_pessoa             = $_POST['ref_pessoa'];
$pessoa                 = $_POST['pessoa'];
$ref_curso              = $_POST['ref_curso'];
$curso                  = $_POST['curso'];
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
$is_ouvinte             = $_POST['is_ouvinte'];
$is_formando            = $_POST['is_formando'];
$obs                    = $_POST['obs'];
$periodo_id             = $_POST['periodo_id'];




CheckFormParameters(array(
                            "ref_campus",
                            "ref_pessoa",
                            "ref_curso",
                            "ref_last_periodo",
                            "ref_motivo_inicial",
                            "dt_ativacao",
                            "dia_vencimento",
                            "ref_motivo_ativacao"));

if ( $is_ouvinte == "yes" )
{
	$is_ouvinte  = "SIM";
	$is_ouvinte_ = '1';
}
else
{
	$is_ouvinte  = "NAO";
	$is_ouvinte_ = '0';
}

if ( $is_formando == "yes" )
{
	$is_formando  = "SIM";
	$is_formando_ = '1';
}
else
{
	$is_formando  = "NAO";
	$is_formando_ = '0';
}

$id_contrato = GetIdentity('seq_contratos');

$data_ativacao = $dt_ativacao;
$dt_ativacao = InvData($dt_ativacao);

$data_desativacao = $dt_desativacao;
$dt_desativacao = InvData($dt_desativacao);

$data_formatura = $dt_formatura;
$dt_formatura = InvData($dt_formatura);

$data_provao = $dt_provao;
$dt_provao = InvData($dt_provao);

$data_diploma = $dt_diploma;
$dt_diploma = InvData($dt_diploma);

$data_apostila = $dt_apostila;
$dt_apostila = InvData($dt_apostila);

$data_conclusao = $dt_conclusao;
$dt_conclusao = InvData($dt_conclusao);

$conn = new Connection;
$conn->Open();
$conn->Begin();

$sql = " insert into contratos (" .
         "       id," .
         "       ref_campus," .
         "       ref_pessoa," .
         "       ref_curso," .
         "       ref_motivo_inicial," .
         "       dt_ativacao," .
         "       ref_motivo_ativacao," .
         "       obs," .
         "       ref_last_periodo," .
         "       id_vestibular," .
         "       dia_vencimento, " .
         "       cod_status, " .
         "       ref_periodo_formatura, " .
         "       dt_desativacao, " .
         "       obs_desativacao, " .
         "       dt_conclusao, " .
         "       dt_formatura, " .
         "       dt_provao, " .
         "       dt_diploma, " .
         "       dt_apostila, " .
         "       desconto, " .
         "       ref_motivo_desativacao, " .
         "       ref_motivo_entrada, " .
         "       fl_ouvinte," .
         "       fl_formando," .
         "       turma," .
         "       ref_periodo_turma," .
         "       percentual_pago)" .
 
         " values ('$id_contrato', " ;
 
if ($ref_campus == '')
{ $sql=$sql . " null," ;} else { $sql=$sql .  $ref_campus . "," ; }

if ($ref_pessoa == '')
{ $sql=$sql . " null," ;} else { $sql=$sql .  $ref_pessoa . "," ; }

if ($ref_curso == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . $ref_curso . "," ; }

if ($ref_motivo_inicial == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$ref_motivo_inicial'," ; }

if ($dt_ativacao == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$dt_ativacao'," ; }

if ($ref_motivo_ativacao == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$ref_motivo_ativacao'," ; }

if ($obs == '')
{ $sql=$sql . "'' ," ;} else { $sql=$sql . "'$obs'," ; }

if ($ref_last_periodo == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$ref_last_periodo'," ; }

if ($id_vestibular == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$id_vestibular'," ; }
 
if ($dia_vencimento == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$dia_vencimento'," ; }
 
if ($status == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$status'," ; }

if ($ref_periodo_formatura == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$ref_periodo_formatura'," ; }

if ($dt_desativacao == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$dt_desativacao'," ; }

if ($obs_desativacao == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$obs_desativacao'," ; }
 
if ($dt_conclusao == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$dt_conclusao'," ; }

if ($dt_formatura == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$dt_formatura'," ; }

if ($dt_provao == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$dt_provao'," ; }

if ($dt_diploma == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$dt_diploma'," ; }

if ($dt_apostila == '')
{ $sql=$sql . " null," ;} else { $sql=$sql . "'$dt_apostila'," ; }
 
if ($desconto == '')
{ $sql=$sql . " null, " ;} else { $sql=$sql . "'$desconto'," ; }
 
if ($ref_motivo_desativacao == '')
{ $sql=$sql . " null, " ;} else { $sql=$sql . "'$ref_motivo_desativacao'," ; }
 
if ($ref_motivo_entrada == '')
{ $sql=$sql . " null, " ;} else { $sql=$sql . "'$ref_motivo_entrada'," ; }
 
$sql=$sql .  $is_ouvinte_ .  "," ;

$sql=$sql . $is_formando_ .  "," ;

if ($turma == '')
{ $sql=$sql . " null, " ;} else { $sql=$sql . "'$turma'," ; }

if ($ref_periodo_turma == '')
{ $sql=$sql . " null, " ;} else { $sql=$sql . "'$ref_periodo_turma'," ; }

if ($percentual_pago == '')
{ $sql=$sql . " null) " ;} else { $sql=$sql . "'$percentual_pago') " ;}
	

// CRIA O ACESSO PARA TODOS OS ALUNOS QUE AINDA NÃO POSSUEM,
// MAS ESTEJAM MATRICULADOS NO SEMESTRE DO CONTRATO DESTE ALUNO
$sql2 = 'INSERT INTO acesso_aluno (ref_pessoa, senha) ';
$sql2 .= 'SELECT ref_pessoa, md5(lpad(CAST(ref_pessoa AS VARCHAR), \'5\', \'0\')) 
            FROM (   
                    SELECT DISTINCT c.ref_pessoa,a.ref_pessoa AS aluno 
                            FROM 
                                contratos c
                            LEFT OUTER JOIN 
                                acesso_aluno a ON (c.ref_pessoa = a.ref_pessoa) 
                            WHERE c.ref_periodo_turma = \''. $ref_periodo_turma .'\' AND a.ref_pessoa IS NULL) AS T1;';
$ok = $conn->Execute($sql);

$conn->Finish();

$ok2 = $conn->Execute($sql2);

$conn->Close();

SaguAssert($ok,"Nao foi possivel inserir o registro!");


SuccessPage("Inclusão de Contrato",
               "location='../novo_contrato.php'",
               "Contrato incluído com sucesso!!!",
               "location='../consulta_inclui_contratos.php'");

?>
<html>
<head>
</head>
<body>
</body>
</html>
