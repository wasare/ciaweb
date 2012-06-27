<?php

require_once(dirname(__FILE__). '/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

$periodo_id = (string) $_GET['periodo_id'];
$curso_id = (int) $_GET['curso_id'];
$diario_id = (int) $_GET['diario_id'];

if (empty($periodo_id) OR $curso_id == 0) {

    if ($diario_id == 0) {
		exit('<script language="javascript">
                window.alert("ERRO! Primeiro informe um período e um curso ou um diário!");
				window.close();
		</script>');
	}

    if (!is_diario($diario_id))
        exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario inexistente ou cancelado!");window.close();</script>');

}

// VERIFICA SE O USUARIO TEM DIREITO DE ACESSO
$sql_coordena = ' SELECT count(*)
							FROM coordenador
							WHERE ref_professor = '. $sa_ref_pessoa .' AND ';

if ($diario_id > 0)
  $sql_coordena .= ' ref_curso = '. get_curso($diario_id) .';';
else
  $sql_coordena .= ' ref_curso = '. $curso_id .';';

$coordenacao = $conn->get_one($sql_coordena);

if ($coordenacao == 0) {
  exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.history.back(1);</script>');
}
// ^ VERIFICA SE O USUARIO TEM DIREITO DE ACESSO ^ /

if ($diario_id == 0) {
	$qryCurso = 'SELECT DISTINCT id, descricao as nome FROM cursos WHERE id = '. $curso_id.';';
	$qryPeriodo = 'SELECT id, descricao FROM periodos WHERE id = \''. $periodo_id.'\';';
}
else {
	$qryCurso = 'SELECT c.id, c.descricao as nome FROM cursos c, disciplinas_ofer d WHERE d.ref_curso = c.id AND d.id = '. $diario_id .';';
	$qryPeriodo = 'SELECT p.id, p.descricao FROM periodos p, disciplinas_ofer d WHERE d.ref_periodo = p.id AND d.id = '. $diario_id .';';
}


$curso = $conn->get_row($qryCurso);
$periodo = $conn->get_row($qryPeriodo);


	$sql =  " SELECT id as idof, " .
           "        ref_campus, " .
           "        get_campus(ref_campus), " .
           "        ref_curso, " .
           "        curso_desc(ref_curso), " .
           "		fl_finalizada, fl_digitada, ".
           "        descricao_disciplina(ref_disciplina) as descricao_extenso, " .
           "        ref_disciplina, " .
           "        get_num_matriculados(id) || '/' || num_alunos as qtde_alunos, " .
           "        turma, " .
           "        ref_periodo_turma, " .
		   "     CASE WHEN professor_disciplina_ofer_todos(id) = '' THEN '<font color=\"red\">sem professor</font>' " .
		   "			ELSE professor_disciplina_ofer_todos(id) " .
		   "		END AS \"professor\" " .
           " FROM disciplinas_ofer " .
           " WHERE is_cancelada = '0' ";


			if ($diario_id > 0)
                $sql .= " AND id = ". $diario_id;
			else
				if (!empty($periodo_id) AND is_numeric($curso_id))
				{
					$sql .= " AND ref_periodo = '". $periodo_id ."'";
					$sql .= " AND ref_curso = ". $curso_id;
				}

			$sql = 'SELECT * from ('. $sql .') AS T1 ORDER BY lower(to_ascii(descricao_extenso,\'LATIN1\'));';


   $diarios = $conn->get_all($sql);

   if (count($diarios) == 0) {
		exit('<script language="javascript">
                window.alert("Nenhum diário encontrado para o filtro selecionado!");
                window.close();
		</script>');
   }

?>

<html>
<head>
<title><?=$IEnome?> - consulta di&aacute;rios</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">

<script type="text/javascript" src="<?=$BASE_URL .'lib/prototype.js'?>"> </script>
<script type="text/javascript" src="<?=$BASE_URL .'app/web_diario/web_diario.js'?>"> </script>
        <script language="javascript">
            // consulta ajax com prototype
            function consulta_ajax(){
                var turno = $F('turno');
                if (turno.replace(/\s/g,"") == "") turno = 'X';
                var curso = $F('curso');
                var periodo = $F('periodo');
                var campus = $F('campus');
                // alert(turno);
                var url = 'coordenacao/lista_turmas.php';
                var pars = 'curso_id=' + curso + '&turno=' + turno + '&periodo=' + periodo + '&campus=' + campus;
                var myAjax = new Ajax.Updater('resposta_turmas',url, {method: 'get',parameters: pars});
            }
        </script>


</head>

<body>

<div align="left">

<table cellpadding="0" cellspacing="0" class="papeleta">
  <tr>
  <th>
    <div align="center">
      <font color="#990000" size="4" face="Verdana, Arial, Helvetica, sans-serif">
        <strong>
          <font color="red">Per&iacute;odo: <?=$periodo['descricao']?></font>
        </strong>
      </font>
    </div>
  </th>
  </tr>
</table>

<h4><strong>Curso: </strong><font color="blue"><?=$curso['id'] .' - '. $curso['nome']?></font></h4>

<span>
<input type="button" value="Relat&oacute;rio de notas e faltas do curso no período" id="notas_faltas"/>
</span>

<!-- panel para selecao de turma para o relatorio // inicio //-->
<div id="notas_faltas_pane" style="display:none; border: 0.0015em solid; width:320px; text-align:center;">
<h4>Selecione as informações abaixo:</h4>
<?php
  
  $turno_sql = "SELECT DISTINCT 
                  oc.turno 
               FROM disciplinas_ofer o LEFT JOIN disciplinas_ofer_compl oc 
               ON (o.id = oc.ref_disciplina_ofer)
               WHERE 
                    o.ref_campus = (SELECT id FROM campus WHERE nome_campus = '".       $_SESSION['sa_campus'] ."') AND
                    o.is_cancelada = '0' AND
                    o.ref_curso = $curso_id
              ";

  $arr_turno = $conn->get_all("SELECT id, nome FROM turno WHERE id IN ($turno_sql) ORDER BY nome ;"); 
  
  $campus = $conn->get_one("SELECT DISTINCT id FROM campus WHERE nome_campus = '".       $_SESSION['sa_campus'] ."'"); 
  
?>
<form action="" method="post" name="form1" id="form1">
 <strong>Turno:</strong>
  <select id="turno" name="turno" onchange="consulta_ajax();">
      <option value="">-- selecione --</option>
    <?php
      foreach($arr_turno as $turno):
    ?>
      <option value="<?=$turno['id']?>">
        <?=$turno['nome']?>
      </option>
    <?php endforeach;?>
 </select>
 <input type="hidden" id="curso" name="curso" value="<?=$curso_id?>" />
 <input type="hidden" id="periodo" name="periodo" value="<?=$periodo_id?>" />
 <input type="hidden" id="campus" name="campus" value="<?=$campus?>" />
 <input type="hidden" id="turno" name="turno" value="<?=$turno?>" />
 <input type="hidden" id="turno_desc" name="turno_desc" value="<?=$turno_desc?>" />
 <br /><br />
 

 <div id="resposta_turmas"></div>
 <br />
 </form>

<br />
</div>
<!-- panel para selecao de turma para o relatorio \\ fim \\ -->


<h5>Clique em "Acessar" para exibir as op&ccedil;&otilde;es do di&aacute;rio:</h5>

<form id="change_acao" name="change_acao" method="get" action="">

<table cellspacing="0" cellpadding="0" class="papeleta">
    <tr bgcolor="#cccccc">
	    <th align="center"><strong>Ordem</strong></th>
		<th align="center"><b>Di&aacute;rio</b></th>
        <th align="center"><b>Descri&ccedil;&atilde;o</b></th>
		<th align="center"><b>Alunos / Vagas</b></th>
		<th align="center"><b>Turma</b></th>
		<th align="center"><b>Professor(es)</b></th>
        <th align="center"><b>Situa&ccedil;&atilde;o</b></th>
        <th align="center"><b>Op&ccedil;&otilde;es</b></th>
    </tr>

<?php

$i = 0;

$r1 = '#FFFFFF';
$r2 = '#FFFFF0';

$msg_diarios_aberto = '';

foreach($diarios as $row3) :

	$descricao_disciplina = $row3["descricao_extenso"];
    $disciplina_id = $row3["idof"];
    $diario_id = $row3["idof"];
	$fl_finalizada = $row3['fl_finalizada'];
    $fl_digitada = $row3['fl_digitada'];
	$professor = $row3['professor'];
	$qtde_alunos = $row3['qtde_alunos'];
	$turma = $row3['turma'];

    $diarios_pane[] = $diario_id;

    $fl_encerrado = ($fl_finalizada == 't')  ? 1 : 0;

    $opcoes_diario = '';

	$fl_professor = TRUE;
	if ( preg_match('/sem professor/i', $professor) )
		$fl_professor = FALSE;

	$fl_opcoes = 0;

	if($fl_finalizada == 'f' && $fl_digitada == 'f') {
        $fl_situacao = '<font color="green"><b>Aberto</b></font>';
    }
    else {

        if($fl_digitada == 't') {
            $fl_situacao = '<font color="blue"><b>Preenchido</b></font>';
        }

        if($fl_finalizada == 't') {
            $fl_situacao = '<font color="red"><b>Fechado</b></font>';
            $fl_encerrado = 1;
			$fl_opcoes = 1;
        }
        else {
			$opcoes_diario .= '<a href="#" onclick="enviar_diario(\'marca_fechado\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">fechado para lan&ccedil;amentos</a><br /><br />';
			$fl_opcoes = 1;
		}
    }

    if ($fl_professor === TRUE) {
      $opcoes_diario .= '<strong>Relat&oacute;rios</strong><hr />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'papeleta\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">papeleta</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'papeleta_completa\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">papeleta completa</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'faltas_completo\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">faltas detalhado</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'conteudo_aula\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">conte&uacute;do de aula</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'carometro\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">Car&ocirc;metro</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'diario_classe\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">Diário de Classe (A3)</a><br />';
      //$opcoes_diario .= '<a href="#" onclick="enviar_diario(\'caderno_chamada\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">caderno de chamada</a>';
	  $fl_opcoes = 1;
	}


	$sem_opcoes = ($fl_opcoes == 0) ? '<font color="red">Nenhuma op&ccedil;&atilde;o dispon&iacute;vel.</font>' : '';

    $cont = $i + 1;
    $rcolor = (($i % 2) == 0) ? $r1 : $r2;
	
?>

	<tr bgcolor="<?=$rcolor?>">
      <td align="center"><?=$cont?></td>
      <td align="center"><?=$diario_id?></td>
      <td><?=$descricao_disciplina?></td>
      <td align="center"><?=$qtde_alunos?></td>
      <td align="center"><?=$turma?></td>
      <td><?=$professor?></td>
      <td align="center"><?=$fl_situacao?></td>
      <td align="center">
        <a href="#" id="<?=$diario_id . '_pane'?>" title="clique para visualizar / ocultar">Acessar</a>
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
	if(!empty($msg_diarios_aberto)) echo $msg_diarios_aberto;
?>
</table>
<br />
<input type="button" value="Fecha todos os diários preenchidos" onclick="enviar_diario('fecha_todos',<?=$diario_id?>,<?=$fl_encerrado?>,'<?=$BASE_URL?>','<?=$IEnome?>');" />
&nbsp;&nbsp;
<a href="#" onclick="javascript:window.close();">Fechar</a>
</form>
<script language="javascript" type="text/javascript">

 $('notas_faltas').observe('click', function() { $('notas_faltas_pane').toggle(); });

<?php
    foreach($diarios_pane as $diario_id) :
?>
      $('<?=$diario_id . '_pane'?>').observe('click', function() { $('diario_<?=$diario_id?>_pane').toggle(); });
<?php
   endforeach;
?>

</script>

</div>

</body>
</head>
</html>

