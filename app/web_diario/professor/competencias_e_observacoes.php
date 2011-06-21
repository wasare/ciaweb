<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/date.php');

$conn = new connection_factory($param_conn);

$diario_id = isset($_GET['diario_id']) ? (int) $_GET['diario_id'] : (int) $_POST['diario_id'];


if ((!isset($_POST['ok']) && $diario_id == 0))
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Dados invalidos!");window.close();</script>');

if (is_finalizado($diario_id))
    exit('<script language="javascript" type="text/javascript">window.alert("Diario fechado para alteracoes!");window.close();</script>');

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if ($_SESSION['sa_modulo'] == 'web_diario_login') {
  if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
  }
}
// ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //


if(isset($_POST['ok']) && $_POST['ok'] == 'OK1' && isset($_POST['competencias'])) {

  $competencias = addslashes($_POST['competencias']);
  $observacoes = addslashes($_POST['observacoes']);

	$sql1 = 'UPDATE disciplinas_ofer SET competencias = \''. $competencias .'\',';
	$sql1 .= ' observacoes = \''. $observacoes .'\' WHERE id = '.$_POST['diario_id'].';';

	$q = $conn->Execute($sql1);
	
	$mensagem_competencias_observacoes = 'Informações alteradas com sucesso!';
	
	$_POST = array();
	
	exit('<script language="javascript" type="text/javascript">
				alert(\''. $mensagem_competencias_observacoes .'\');
				window.opener.location.reload();
				setTimeout("self.close()",450); </script>');
}
else {
	
	$sql1 = "SELECT
            competencias,
            observacoes
               FROM
               disciplinas_ofer
               WHERE
               id = $diario_id;";

  $diario_info = $conn->get_row($sql1);
	
	$competencias = $diario_info['competencias'];
  $observacoes = $diario_info['observacoes'];

}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title><?=$IEnome?> - Compet&ecirc;ncias e Observa&ccedil;&otilde;es do Di&aacute;rio</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
<style type="text/css">@import "<?=$BASE_URL .'public/styles/jquery.maxlength.css'?>";</style>
</head>
<body>

<br />
<div align="left" class="titulo1">
  Compet&ecirc;ncias e Observa&ccedil;&otilde;es do Di&aacute;rio
</div>
<br />
<?=papeleta_header($diario_id)?>
<br />

<form name="conteudo_aula" id="conteudo_aula" method="post" action="">
<table cellspacing="0" cellpadding="0" class="papeleta">

<input type="hidden" name="ok" value="OK1" />

<input type="hidden" name="diario_id" id="diario_id" value="<?=$diario_id?>">

  <tr>
    <td colspan="3">
				<h3>Compet&ecirc;ncias Desenvolvidas</h3>
				
					<textarea name="competencias" cols="50" rows="10" id="competencias"><?=$competencias?></textarea>
          <br /><span class="maxlength-feedback" id="targetFeedback1"></span>
					<br /><br />
      </td>
  </tr>
  <tr>
    <td colspan="3">
             
						<h3>Observa&ccedil;&otilde;es</h3>
						<textarea name="observacoes" cols="50" rows="8" id="observacoes"><?=$observacoes?></textarea>
						<br /><span class="maxlength-feedback" id="targetFeedback3"></span>
						<br /><br />
        </td>
  </tr>

  <tr>
    <td>&nbsp;</td>
    <td>
        <div align="center">
          <input type="submit" name="atualizar" id="atualizar" value="Atualizar">
					&nbsp;&nbsp;&nbsp;
          <a href="#" onclick="javascript:window.close();">Cancelar</a>
        </div>
      </td>
    <td>&nbsp;</td>
  </tr>
</table>
<br /><br />
</form>
<script type="text/javascript" language="javascript" src="<?=$BASE_URL .'lib/jquery.min.js'?>"></script>
<script type="text/javascript" language="javascript" src="<?=$BASE_URL .'lib/jquery.maxlength.pack.js'?>"></script>
<script type="text/javascript">		
		$(function() {
				$('#competencias').maxlength({max: 800, feedbackText: 'Usando {c} de {m} caracteres.', feedbackTarget: '#targetFeedback1'});
				$('#observacoes').maxlength({max: 800,feedbackText: 'Usando {c} de {m} caracteres.', feedbackTarget: '#targetFeedback3'});
		});
</script>
</body>
</html>

