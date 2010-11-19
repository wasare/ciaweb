<?php

require_once(dirname(__FILE__) .'/../../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/date.php');

$conn = new connection_factory($param_conn);

$diario_id = (int) $_POST['diario_id'];

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
}
// ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //

if (!existe_chamada($diario_id)) {
  exit('<script language="javascript" type="text/javascript">window.alert("Nenhuma chamada registrada para este diario!");window.close(); </script>');
}

if (is_finalizado($diario_id)){

    echo '<script language="javascript" type="text/javascript">';
    echo 'alert("ERRO! Este diário está finalizado e não pode ser alterado!");';
    echo 'window.close();';
    echo '</script>';
    exit;
}

$sql1 = "SELECT id,
               dia,
               conteudo,
			   flag
            FROM
               diario_seq_faltas
               WHERE
               ref_disciplina_ofer = $diario_id
               ORDER BY dia DESC ;";

$chamadas = $conn->get_all($sql1);


?>
<html>
<head>
<title><?=$IEnome?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
</head>

<body>

<div align="left" class="titulo1">
  <h3>Altera&ccedil;&atilde;o de Faltas</h3>
</div>
 <br />
<?=papeleta_header($diario_id)?>
<br />
<div align="left" class="titulo">
  <h4>Chamadas Realizadas</h4>
</div>
<a href="<?=$BASE_URL .'app/relatorios/web_diario/faltas_completo.php?diario_id='. $diario_id  ?>" target="_blank">Exibir relat&oacute;rio Completo de Faltas Lan&ccedil;adas</a>
<br />
<br />
<table cellspacing="0" cellpadding="0" class="papeleta">
  <tr bgcolor="#666666"> 
    <td align="center">
    	<div align="center"><font color="#FFFFFF">&nbsp;</font><b><font color="#FFFFFF">DATA</font></b></div>
    </td>
    <td align="center"><font color="#FFFFFF"><b>AULAS</b></font></td>
    <td align="center"><font color="#FFFFFF"><b>CONTE&Uacute;DO DE AULA</b></font></td>
    <td align="center"><font color="#FFFFFF"><b>&nbsp;&nbsp;A&Ccedil;&Atilde;O</b></font></td>
  </tr>
<?php 

$st = '';
	
  foreach( $chamadas as $aula ) :

	$data_chamada = $aula["dia"];
    $conteudo = $aula["conteudo"];
    $chamada_id = $aula["id"];
    $aulas = $aula["flag"];
	
	if ( $st == '#F3F3F3') $st = '#E3E3E3'; else $st ='#F3F3F3';
  ?>

  <tr bgcolor="<?=$st?>">
    <td align="center"><?=date::convert_date($data_chamada)?></td>
	<td align="center"><?=$aulas?></td>
    <td align="left"><?=$conteudo?></td>
	<td> 
      <a href="<?=$BASE_URL .'app/web_diario/professor/chamada/altera_faltas.php?chamada='. $chamada_id .'&flag='. $aulas .'&diario_id='. $diario_id?>">Alterar faltas</a>
    </td>
  </tr>
<?php
  endforeach;
?>
</table>

<br />
<div>
<input type="button" value="Imprimir" onClick="window.print()">
&nbsp;&nbsp;&nbsp;
<a href="#" onclick="javascript:window.close();">fechar</a>
</div>

<br /><br />
</body>
</html>
