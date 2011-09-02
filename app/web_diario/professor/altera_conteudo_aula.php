<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/date.php');

$conn = new connection_factory($param_conn);

$flag = (isset($_POST['ok'])) ? (int) $_POST['flag'] : (int) $_GET['flag'];
$data_chamada = $_GET['data_chamada'];
$diario_id = isset($_GET['diario_id']) ? (int) $_GET['diario_id'] : (int) $_POST['diario_id'];


if ((!isset($_POST['ok']) && $diario_id == 0) || $flag == 0)
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Dados invalidos!");window.close();</script>');

if (is_fechado($diario_id))
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


if(isset($_POST['ok']) && $_POST['ok'] == 'OK1') {

  $atividades = $_POST['atividades'];
  $conteudo = addslashes($_POST['texto']);

  if ($atividades[count($atividades) - 1] == "Outras")
    $atividades[count($atividades) - 1] = trim($_POST['outras']);
 

  $atividades = addslashes(implode('; ', $atividades));

	$sql1 = 'UPDATE diario_seq_faltas SET conteudo = \''. $conteudo .'\',';
	$sql1 .= ' atividades = \''. $atividades .'\' WHERE id = '.$_POST['flag'].';';

	$q = $conn->Execute($sql1);

	echo '<script type="text/javascript">  window.alert("Conteudo de aula alterado com sucesso! ");';
	if(isset($_SESSION['web_diario_do']))
		echo 'self.location.href = "'. $BASE_URL .'app/web_diario/requisita.php?do='. $_SESSION['web_diario_do'] .'&id='.$_POST['diario_id'];
	else
		echo 'self.location.href = "'. $BASE_URL .'app/relatorios/web_diario/conteudo_aula.php?diario_id='.$_POST['diario_id'];

	echo '"</script>';
}
else
{
	$sql1 = "SELECT
            conteudo,
            atividades
               FROM
               diario_seq_faltas
               WHERE
               id = $flag;";

  $conteudos = $conn->get_row($sql1);

  $conteudo = $conteudos['conteudo'];
  $atividades = $conteudos['atividades'];

  $atividades = explode("; ", $atividades);

  $atividades_registradas = array_map('trim', $atividades);

}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title><?=$IEnome?> - Altera&ccedil;&atilde;o de conte&uacute;do de aula</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
<style type="text/css">@import "<?=$BASE_URL .'public/styles/jquery.maxlength.css'?>";</style>
</head>
<body>

<br />
<div align="left" class="titulo1">
        Altera&ccedil;&atilde;o de conte&uacute;do de aula
</div>
<br />
<?=papeleta_header($diario_id)?>
<br />

<form name="conteudo_aula" id="conteudo_aula" method="post" action="altera_conteudo_aula.php">
<table cellspacing="0" cellpadding="0" class="papeleta">

<input type="hidden" name="flag" value="<?=$flag?>" />
<input type="hidden" name="ok" value="OK1" />

<input type="hidden" name="diario_id" id="diario_id" value="<?=$diario_id?>">

  <tr>
    <td colspan="3"><strong>Data chamada: <?=$data_chamada?></strong></td>
  </tr>
  <tr>
    <td colspan="3">
        <div align="center">
					<textarea name="texto" cols="50" rows="6" id="bases_conhecimento"><?=$conteudo?></textarea>
          <br /><span class="maxlength-feedback" id="targetFeedback1"></span> <br />
        </div>
      </td>
  </tr>
  <tr>
    <td colspan="3">
         <br />
         Atividades e avaliações da(s) aula(s):<br />
             <?php

                foreach($ATIVIDADES_AULA as $atividade) :
                  $checked = '';
                  if (in_array($atividade, $atividades_registradas)) :
                     $checked = ' checked="checked" ';
                  endif;
              ?>
                  <br />
                  <input type="checkbox" class="checkbox" name="atividades[]" value="<?=$atividade?>" <?=$checked?> /> <?=$atividade?>

              <?php
                endforeach;

                $outra_atividade = array_pop($atividades_registradas);

                if (in_array($outra_atividade, $ATIVIDADES_AULA)) :
                  $outra_atividade = '';
                endif;

              ?>
                 <br />
                 <input type="checkbox" class="checkbox" name="atividades[]" value="Outras" <?php if(!empty($outra_atividade)) echo ' checked="checked" '; ?> /> Outras - especificar
                 &nbsp;&nbsp;
								 <br />
									&nbsp;&nbsp;&nbsp;&nbsp;<textarea name="outras" cols="48" rows="4" id="atividade11"><?=$outra_atividade?></textarea>
									<br /><span class="maxlength-feedback" id="targetFeedback2"></span> <br />
            <br />
            <br />

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
</form>
<script type="text/javascript" language="javascript" src="<?=$BASE_URL .'lib/jquery.min.js'?>"></script>
<script type="text/javascript" language="javascript" src="<?=$BASE_URL .'lib/jquery.maxlength.pack.js'?>"></script>
<script type="text/javascript">		
		$(function() {
				$('#bases_conhecimento').maxlength({max: 200, feedbackText: 'Usando {c} de {m} caracteres.', feedbackTarget: '#targetFeedback1'});
				$('#atividade11').maxlength({max: 200,feedbackText: 'Usando {c} de {m} caracteres.', feedbackTarget: '#targetFeedback2'});
		});

</script>
</body>
</html>

