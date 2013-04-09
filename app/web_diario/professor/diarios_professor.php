<?php

require_once(dirname(__FILE__) .'/../../setup.php');

if(empty($_SESSION['web_diario_periodo_id']) OR empty($_SESSION['web_diario_periodo_id'])) {
        exit('<script language="javascript" type="text/javascript">
                window.alert("ERRO! Primeiro informe um período!");
                window.close();
        </script>');
}

$conn = new connection_factory($param_conn);

unset($_SESSION['conteudo']);
unset($_SESSION['flag_falta']);

$diario = @explode("|", $_GET['diario']);

if (isset($_GET['id']) AND ( !is_numeric($diario['0']) OR !is_numeric($diario['1'])) ) {
     exit('<script language="javascript" type="text/javascript">
      window.alert("ERRO! Primeiro selecione um diário!"); javascript:window.history.back(1);</script>');
}
else {

  if(isset($diario['2']) &&  $diario['2'] === '1' && in_array($_GET['acao'], $Movimento) ) {

     exit('<script language="javascript" type="text/javascript">
            window.alert("ERRO! Este diário está fechado e não pode ser alterado!"); javascript:window.history.back(1);
     </script>');
    }
}


$qryPeriodo = 'SELECT id, descricao FROM periodos WHERE id = \''. $_SESSION['web_diario_periodo_id'].'\';';

$periodo = $conn->get_row($qryPeriodo);

$sql =  " SELECT DISTINCT o.id as idof, " .
           "        ref_campus, " .
           "        get_campus(ref_campus), " .
           "        ref_curso, " .
           "        curso_desc(ref_curso), " .
           "        fl_finalizada, fl_digitada, ".
           "        descricao_disciplina(o.ref_disciplina) as descricao_extenso, " .
           "        ref_disciplina, " .
           "        get_num_matriculados(o.id) || '/' || num_alunos as qtde_alunos, " .
           "        turma, " .
           "        ref_periodo_turma " .
           " FROM disciplinas_ofer o, disciplinas_ofer_prof p " .
           " WHERE is_cancelada = '0' AND ".
           "       p.ref_professor = '$sa_ref_pessoa' AND ".
           "       o.id = p.ref_disciplina_ofer AND ".
           "       o.ref_periodo = '". $_SESSION['web_diario_periodo_id'] ."'";

$sql = 'SELECT * from ('. $sql .') AS T1 ORDER BY lower(to_ascii(descricao_extenso,\'LATIN1\'));';

//   $diarios = $conn->get_all($sql);

$sql3 = 'SELECT DISTINCT
                d.id,
                d.descricao_disciplina,
                d.descricao_extenso,
                o.id as idof,
                o.fl_finalizada,
                o.fl_digitada
                FROM disciplinas_ofer_prof f, disciplinas_ofer o, disciplinas d
                WHERE
                f.ref_professor = '. $sa_ref_pessoa .' AND
                o.id = f.ref_disciplina_ofer AND
                o.ref_periodo = \''.$_SESSION['web_diario_periodo_id'].'\' AND
                o.is_cancelada = \'0\' AND
                d.id = o.ref_disciplina;';

  $diarios = $conn->get_all($sql);

   if(count($diarios) == 0)
   {
        /*exit('<script language="javascript">
                window.alert("Nenhum diário encontrado para o filtro selecionado!");
        </script>');*/
        $nenhum_diario = "Nenhum di&aacute;rio encontrado para o filtro selecionado.";
   }


// RECUPERA INFORMACOES SOBRE OS PERIODOS DO PROFESSOR
$qry_periodos = 'SELECT DISTINCT o.ref_periodo,p.descricao,p.dt_inicial FROM disciplinas_ofer o, disciplinas_ofer_prof dp, periodos p WHERE dp.ref_professor = '. $sa_ref_pessoa .' AND o.id = dp.ref_disciplina_ofer AND p.id = o.ref_periodo ORDER BY p.dt_inicial DESC;';
$periodos = $conn->get_all($qry_periodos);
// ^ RECUPERA INFORMACOES SOBRE OS PERIODOS DO PROFESSOR ^ //


// RECUPERA INFORMACOES SOBRE DESEMPENHO DOCENTE
$sql_levantamento_docente = 'SELECT DISTINCT n.ref_periodo, l.descricao FROM desempenho_docente_nota n, desempenho_docente_levantamento l WHERE ref_professor = ' . $sa_ref_pessoa . ' AND n.ref_periodo = l.ref_periodo';
$levantamento_docente = $conn->get_all($sql_levantamento_docente);
$num_levantamento = count($levantamento_docente);
// ^  RECUPERA INFORMACOES SOBRE DESEMPENHO DOCENTE ^ //

$qry_periodos_finalizados = 'SELECT DISTINCT o.id, o.ref_periodo, p.descricao,p.dt_final
FROM disciplinas_ofer o, disciplinas_ofer_prof dp, periodos p
WHERE dp.ref_professor = '. $sa_ref_pessoa .' AND o.id = dp.ref_disciplina_ofer
AND o.fl_digitada = \'f\' AND o.is_cancelada = \'0\'
AND p.id = o.ref_periodo AND p.dt_final < \'' . date("Y-m-d") . '\' ORDER BY p.dt_final;';
$periodos_abertos = $conn->get_all($qry_periodos_finalizados);
$diarios_abertos = 0;
$periodos_encerrados = array();
foreach($periodos_abertos as $p) {
    $periodos_encerrados[$p['ref_periodo']] = $p['descricao'];
    $diarios_abertos++;
}
$msg_diarios_abertos = '';
if (count($periodos_encerrados) > 0) {
  $msg_diarios_abertos = 'No(s) período(s) '. implode(', ',$periodos_encerrados);
  $msg_diarios_abertos .= ', já encerrado(s), existe(m) ' . $diarios_abertos;
  $msg_diarios_abertos .= ' diário(s) em aberto que não esta(m) devidamente marcado(s) como preenchido(s), por favor verifique e providencie isto!';
}

?>

<html>
<head>
<title><?=$IEnome?> - web di&aacute;rio</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">

<script type="text/javascript" src="<?=$BASE_URL .'lib/prototype.js'?>"> </script>
<script type="text/javascript" src="<?=$BASE_URL .'lib/jquery.min.js'?>"> </script>

</head>

<body>

<div align="left">
<strong>
      <font size="4" face="Verdana, Arial, Helvetica, sans-serif">
        Per&iacute;odo:
        <font color="red" size="4" face="Verdana, Arial, Helvetica, sans-serif"><?=$periodo['descricao']?></font>
      </font>
</strong>
&nbsp;&nbsp;

<span><a href="#" title="alterar o per&iacute;odo" id="periodos_professor">alterar</a></span>
<br />
<br />
<!-- panel para alteracao dos periodos do professor // inicio //-->
<div id="periodos_professor_pane" style="display:none; border: 0.0015em solid; width:200px; text-align:center;">
<br />

<h4>clique no per&iacute;odo:</h4>
<br />
<?php
    foreach($periodos as $p) {
      echo '<a href="#" title="Per&iacute;odo '. $p['descricao'] .'" alt="Per&iacute;odo '. $p['descricao'] .'" onclick="set_periodo(\'periodo_id='. $p['ref_periodo'] .'\');">'. $p['descricao'] .'</a><br />';
    }
?>
<!--Fim pegar período -->
<br />
</div>
<!-- panel para alteracao dos periodos do professor \\ fim \\ -->
<br />
<?php if (isset($nenhum_diario)) : ?>

 <h4><?=$nenhum_diario?></h4>
 <br />
<?php else : ?>
<span class="diario_aberto"><?=$msg_diarios_abertos?></span><br /><br />
<h4>Clique em "Acessar" para exibir as op&ccedil;&otilde;es do di&aacute;rio:</h4>
<br />
<form id="lista_diarios" name="lista_diarios" method="get" action="professor/diarios_professor.php">
<input type="hidden" name="id" id="id" value="<?=$_SESSION['id']?>" />

<table cellspacing="0" cellpadding="0" class="papeleta">
    <tr bgcolor="#cccccc">
        <th align="center"><strong>Ordem</strong></th>
    <th align="center"><b>Di&aacute;rio</b></th>
        <th align="center"><b>Descri&ccedil;&atilde;o</b></th>
    <th align="center"><b>Alunos / Vagas</b></th>
    <th align="center"><b>Turma</b></th>
        <th align="center"><b>Situa&ccedil;&atilde;o</b></th>
        <th align="center"><b>Op&ccedil;&otilde;es</b></th>
    </tr>
<?php

$i = 1;

$r1 = '#FFFFFF';// '#ccccff';
$r2 = '#FFFFFF';

foreach($diarios as $row3) :

  $descricao_disciplina = $row3["descricao_extenso"];
  $disciplina_id = $row3["idof"];
  $diario_id = $row3["idof"];
  $fl_finalizada = $row3['fl_finalizada'];
  $fl_digitada = $row3['fl_digitada'];
  $qtde_alunos = (!empty($row3['qtde_alunos'])) ? $row3['qtde_alunos'] : '-';
  $turma = (!empty($row3['turma'])) ? $row3['turma'] : '-';

  if($fl_finalizada == 'f' && $fl_digitada == 'f') {
    $fl_situacao = '<font color="green"><b>Aberto</b></font>';
    $acao_situacao = 'Marcar';
  }
  else {

    if($fl_digitada == 't') {
          $fl_situacao = '<font color="blue"><b>Preenchido</b></font>';
      $acao_situacao = 'Desmarcar';
      }

    if($fl_finalizada == 't') {
            $fl_situacao = '<font color="red"><b>Fechado</b></font>';
        }

  }

  $fl_encerrado = ($fl_finalizada == 't')  ? 1 : 0;

    $opcoes_diario = '';
    if ($fl_encerrado == 0) {
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'notas\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .' - Notas\');">Nota</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'chamada\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .' - Chamada\');">Chamada</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'competencias_observacoes\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .' - Competências e Observações\');">Compet&ecirc;ncias <br />Observa&ccedil;&otilde;es</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'altera_chamada\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .' - Altera Chamada\');">Editar faltas</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'exclui_chamada\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .' - Exclui Chamada\');">Excluir chamada</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'marca_diario\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .' - Marca Diário Preenchido\');">'. $acao_situacao .' preenchido</a><br />';
      $opcoes_diario .= '<br />';
    }

    $opcoes_diario .= '<strong>Relat&oacute;rios</strong><hr />';
    $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'papeleta\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .' - Papeleta\');">Papeleta</a><br />';
    $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'papeleta_completa\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .' - Papeleta Completa\');">Papeleta completa</a><br />';
    $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'faltas_completo\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .' - Detalhes de Faltas\');">Faltas detalhado</a><br />';
    $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'conteudo_aula\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .' - Aulas\');">Conte&uacute;do de aula</a><br />';
    $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'carometro\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .' - Fotos da Turma\');">Car&ocirc;metro</a><br />';
    $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'diario_classe\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .' - Diário Classe\');">Diário de Classe (A3)</a><br />';
    //$opcoes_diario .= '<a href="#" onclick="enviar_diario(\'caderno_chamada\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">Caderno de chamada</a>';


    $rcolor = (($i % 2) == 0) ? $r1 : $r2;
    $op_color = ($rcolor == $r2) ? $r1 : $r2;
?>

    <tr bgcolor="<?=$rcolor?>">
      <td width="5%" align="center"><?=$i?>
        <!--<input  type="radio" name="diario" id="diario" value="<\?=$disciplina_id .'|'. $diario_id .'|'. $fl_encerrado?>" />-->
      </td>
      <td width="5%" align="center"><strong><?=$diario_id?></strong></td>
      <td width="50%">&nbsp;&nbsp;<strong><?=$descricao_disciplina?></strong></td>
      <td align="center"><?=$qtde_alunos?></td>
      <td align="center"><?=$turma?></td>
      <td align="center"><?=$fl_situacao?></td>
      <td align="center">
        <a href="#<?=$diario_id?>" id="d_<?=$diario_id . '_pane'?>" title="clique para visualizar / ocultar">Acessar</a>
        <a name="<?=$diario_id?>"></a>
        <!-- panel com as opções do diário // inicio //-->
        <div id="diario_<?=$diario_id?>_pane" style="display:none; margin: 1.2em; padding: 1em; background-color: <?=$op_color?>" class="opcoes_web_diario">
            <?=$sem_opcoes . $opcoes_diario?>
        </div>
        <!-- panel com as opções do diário \\ fim \\ -->
      </td>

    </tr>

<?php

    $i++;

  endforeach;
  echo $msg;
?>
</table>

<br /><br />
<?php if ($num_levantamento > 0) : ?>
<span><a href="#" title="acessar relat&oacute;rios" id="desempenho_professor">Desempenho docente</a></span>
<br />
<br />
<!-- panel para acesso aos relatórios de desempenho docente // inicio //-->
<div id="desempenho_professor_pane" style="display:none; border: 0.0015em solid; width:200px; text-align:center;">
<br />

<h4>Levantamentos:</h4>
<br />
<?php
    foreach($levantamento_docente as $l) {
      echo '<a href="'. $BASE_URL .'app/relatorios/desempenho_docente/lista_desempenho_docente.php?levantamento='. $l['ref_periodo'] .'" title="Levantamento '. $l['descricao'] .'" alt="Levantamento '. $l['descricao'] .'" target="_blank">'. $l['descricao'] .'</a><br />';
    }
?>
<?php endif; ?>
<br />
</div>
<!-- panel para acesso aos relatórios de desempenho docente \\ fim \\ -->

<br />
</form>

<?php endif; ?>

<script language="javascript" type="text/javascript">
  var $j = jQuery.noConflict();
  jQuery(document).ready(function() {
     $j('#periodos_professor').click(function() { $j('#periodos_professor_pane').toggle(); });

    <?php if ($num_levantamento > 0) : ?>
        $j('#desempenho_professor').click(function() { $j('#desempenho_professor_pane').toggle(); });
    <?php 
        endif;
        
        foreach($diarios as $row3) :
          $diario_id = $row3['idof'];
    ?>          
          $j('#d_<?=$diario_id . '_pane'?>').click(function() {
            $j('[id^="diario_"][id$="_pane"]').toggle(false);
            $j('#diario_<?=$diario_id?>_pane').toggle();
            $j('[id^="d_"][id$="_pane"]').show();
            $j('#d_<?=$diario_id?>_pane').hide();
          });
          
    <?php
        endforeach;
    ?>
  });
</script>
</div>

</body>
</html>
