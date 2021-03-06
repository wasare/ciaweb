<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/reports/header.php');
require_once($BASE_DIR .'core/date.php');

$conn = new connection_factory($param_conn);
$header  = new header($param_conn);

$diario_id = (int) $_GET['diario_id'];

if($diario_id == 0)
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario invalido!");window.close();</script>');

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if($_SESSION['sa_modulo'] == 'web_diario_login') {
  if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
  }
  // ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //
}

if (!existe_chamada($diario_id)) {
  exit('<script language="javascript" type="text/javascript">window.alert("Nenhuma conteudo registrado para este diario!");window.close(); </script>');
}

$sql1 ="SELECT id,
               dia,
               conteudo,
               flag,
               atividades
               FROM
               diario_seq_faltas
               WHERE
               ref_disciplina_ofer = $diario_id
               ORDER BY dia desc;";


$conteudos = $conn->get_all($sql1);

$fl_finalizado = is_fechado($diario_id);


// RECUPERA COMPETENCIAS E OBSERVAÇÕES DO DIÁRIO
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



?>
<html>
<head>
<title><?=$IEnome?> - conte&uacute;do de aula</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">

<script type="text/javascript" src="<?=$BASE_URL .'lib/prototype.js'?>"> </script>
</head>

<body>

<div align="left">
     <?=$header->get_empresa($PATH_IMAGES, $IEnome)?>
</div>
<br />
<div align="left" class="titulo1">
   Conte&uacute;do de Aula
</div>
<br />
<?=papeleta_header($diario_id)?>

<br />
<br />
<span><a href="#" title="Exibir Compet&ecirc;ncias Desenvolvidas e Observa&ccedil;&otilde;es" id="competencias_observacoes">Compet&ecirc;ncias e Observa&ccedil;&otilde;es</a></span>
<br />

<!-- panel para exibir as competências e observações do diário // inicio //-->
<div id="competencias_observacoes_pane" style="display:none; border: 0.0015em solid; width:740px; text-align:left; text-indent: 2em;">
<br />

<h4>Compet&ecirc;ncias Desenvolvidas</h4>
<p><?=$competencias?></p>
<br />
<h4>Observa&ccedil;&otilde;es</h4>
<p><?=$observacoes?></p>
<br />
</div>
<!-- panel para acesso aos relatórios de desempenho docente \\ fim \\ -->


<br />
<br />
<div align="left">
  <font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">
    <?php
      if(!$fl_finalizado) :
    ?>
    <strong>*Para alterar o conte&uacute;do de aula clique na data da chamada!</strong>
    <?php
      endif;
    ?>
  </font>
</div>

<table cellspacing="0" cellpadding="0" class="papeleta" width="60%">
  <tr bgcolor="#666666">
    <th align="center">
    	<div align="center"><font color="#FFFFFF">&nbsp;</font><b><font color="#FFFFFF">Data</font></b></div>
    </th>
    <th align="center"><font color="#FFFFFF"><b>Aulas</b></font></th>
    <th><font color="#FFFFFF"><b>Bases e conhecimentos</b></font></th>
    <th><font color="#FFFFFF"><b>Atividades e avalia&ccedil;&otilde;es</b></font></th>
  </tr>
<?php

$st = '';

foreach($conteudos as $linha1) :

	$data_chamada = $linha1["dia"];
	$conteudo = $linha1["conteudo"];
	$chamada_id = $linha1["id"];
	$aulas = $linha1["flag"];
	$atividades = $linha1["atividades"];

	if ( $st == '#F3F3F3') $st = '#E3E3E3'; else  $st ='#F3F3F3';
?>

  <tr bgcolor="<?=$st?>">
    <td align="center">
      <?php
          if ($fl_finalizado) :
            echo date::convert_date($data_chamada);
         else :
      ?>
            <a href="<?=$BASE_URL?>app/web_diario/professor/altera_conteudo_aula.php?flag=<?=$chamada_id?>&diario_id=<?=$diario_id?>&data_chamada=<?=date::convert_date($data_chamada)?>" title="clique para alterar"><?=date::convert_date($data_chamada)?></a>
      <?php
        endif;
      ?>
    </td>
    <td align="center"><?=$aulas?></td>
	<td><?=$conteudo?></td>
	<td><?=$atividades?></td>
  </tr>

<?php
   endforeach;
?>

</table>
<br><br>
<input type="button" value="Imprimir" onClick="window.print()">
&nbsp;&nbsp;
<a href="#" onclick="javascript:window.close();">Fechar</a>
<script type="text/javascript">		
	$('competencias_observacoes').observe('click', function() { $('competencias_observacoes_pane').toggle(); });
</script>
</body>
</html>

