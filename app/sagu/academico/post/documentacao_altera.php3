<?php require_once("../../common.php"); ?>

<html>
<head>
<?php 

$id                  = $_POST['id'];
$rg_num              = $_POST['rg_num'];
$cpf                 = $_POST['cpf'];
$hist_original       = $_POST['hist_original'];
$hist_escolar        = $_POST['hist_escolar'];
$titulo_eleitor      = $_POST['titulo_eleitor'];
$quitacao_eleitoral  = $_POST['quitacao_eleitoral'];
$doc_militar         = $_POST['doc_militar'];
$foto                = $_POST['foto'];
$atestado_medico     = $_POST['atestado_medico'];
$diploma_autenticado = $_POST['diploma_autenticado'];
$solteiro_emancipado = $_POST['solteiro_emancipado'];
$obs_documentos      = $_POST['obs_documentos'];
$anotacoes           = $_POST['anotacoes'];


CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();
$conn->Begin();

$sql = "update documentos set " .
       "    ref_pessoa = '$id',";
if ( ($rg_num=='t') || ($rg_num=='Sim'))
{ $sql = $sql . " rg_num = 't',"; }
else
{ $sql = $sql . " rg_num = 'f',";  }

if ( ($cpf=='t') || ($cpf=='Sim'))
{ $sql = $sql . " cpf = 't',"; }
else
{ $sql = $sql . " cpf = 'f',";  }

if ( ($hist_escolar=='t') || ($hist_escolar=='Sim'))
{ $sql = $sql . " hist_escolar = 't',"; }
else
{ $sql = $sql . " hist_escolar = 'f',";  }

if ( ($hist_original=='t') || ($hist_original=='Sim'))
{ $sql = $sql . " hist_original = 't',"; }
else
{ $sql = $sql . " hist_original = 'f',";  }

if ( ($titulo_eleitor=='t') || ($titulo_eleitor=='Sim'))
{ $sql = $sql . " titulo_eleitor = 't',"; }
else
{ $sql = $sql . " titulo_eleitor = 'f',";  }       

if ( ($quitacao_eleitoral=='t') || ($quitacao_eleitoral=='Sim') )
{ $sql = $sql . " quitacao_eleitoral = 't',"; }
else
{ $sql = $sql . " quitacao_eleitoral = 'f',";  }

if ( ($doc_militar=='t') || ($doc_militar=='Sim') )
{ $sql = $sql . " doc_militar = 't',"; }
else
{ $sql = $sql . " doc_militar = 'f',";  }

if ( ($foto=='t') || ($foto=='Sim') )
{ $sql = $sql . " foto = 't', "; }
else
{ $sql = $sql . " foto = 'f', ";  }

if ( ($atestado_medico=='t') || ($atestado_medico=='Sim') )
{ $sql = $sql . " atestado_medico = 't',"; }
else
{ $sql = $sql . " atestado_medico = 'f',";  }

if ( ($diploma_autenticado=='t') || ($diploma_autenticado=='Sim') )
{ $sql = $sql . " diploma_autenticado = 't',"; }
else
{ $sql = $sql . " diploma_autenticado = 'f',";  }

if ( ($solteiro_emancipado=='t') || ($solteiro_emancipado=='Sim') )
{ $sql = $sql . " solteiro_emancipado = 't',"; }
else
{ $sql = $sql . " solteiro_emancipado = 'f',";  }

$sql = $sql . " obs_documentos = '$obs_documentos', " .
                     " anotacoes = '$anotacoes' ";

$sql = $sql . " where ref_pessoa = '$id'";

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Não foi possível alterar o registro!");
SuccessPage("Atualização de Documentação",
            "location='../consulta_inclui_pessoa.phtml'",
            "Documentação atualizada com sucesso.");
?>
