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

			$sql = 'SELECT * from ('. $sql .') AS T1 ORDER BY lower(to_ascii(descricao_extenso));';


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
<div id="notas_faltas_pane" style="display:none; border: 0.0015em solid; width:200px; text-align:center;">
<h4>clique na turma para exibir o relat&oacute;rio:</h4>
<?php
	$sql = "
		SELECT DISTINCT turma
			FROM contratos
			WHERE
    			ref_curso = ". $curso_id ." AND
    			turma is not null AND turma <> ''; ";

	$arr_turmas = $conn->get_all($sql);

	$count = 0;

    //$periodo_id = (string) $_GET['periodo_id'];
	//$curso_id = (int) $_GET['curso_id'];
	//$campus = (int) $_GET['campus'];
	//$turma = (string) $_POST['turma'];

	foreach($arr_turmas as $turma) :
        $url = '';
        $url .= $BASE_URL .'app/web_diario/coordenacao/exibe_notas_faltas_global.php?curso_id='. $curso_id;
        $url .= '&periodo_id='. $periodo_id;
        $url .= '&campus=1'; // TODO: selecionar campus de outra maneira
        $url .= '&turma='. $turma['turma'];
?>
		<a href="#" onclick="abrir('Sistema Acadêmico', '<?=$url?>')" title="clique para visualizar"><?=$turma['turma']?></a>		     <br />
<?php
    endforeach;
?>
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
            $fl_situacao = '<font color="blue"><b>Conclu&iacute;do</b></font>';
        }

        if($fl_finalizada == 't') {
            $fl_situacao = '<font color="red"><b>Finalizado</b></font>';
            $fl_encerrado = 1;
			$fl_opcoes = 1;
        }
        else {				
          $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'marca_finalizado\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">finaliza para lan&ccedil;amentos</a><br /><br />';
          $fl_opcoes = 1;
		}
    }

    if ($fl_professor === TRUE) {
      $opcoes_diario .= '<strong>Relat&oacute;rios</strong><hr />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'papeleta\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">papeleta</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'papeleta_completa\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">papeleta completa</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'faltas_completo\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">relat&oacute;rio de faltas completo</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'conteudo_aula\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">conte&uacute;do de aula</a><br />';
      $opcoes_diario .= '<a href="#" onclick="enviar_diario(\'caderno_chamada\',\''. $diario_id .'\',\''. $fl_encerrado .'\',\''. $BASE_URL .'\',\''. $IEnome .'\');">caderno de chamada</a>';
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
?>
</table>
<br />
<input type="button" value="Finaliza todos os diários concluídos" onclick="enviar_diario('finaliza_todos',<?=$diario_id?>,<?=$fl_encerrado?>,'<?=$BASE_URL?>','<?=$IEnome?>');" />
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
