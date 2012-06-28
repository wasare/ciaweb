<?php

require_once(dirname(__FILE__) .'/../../setup.php');

if(empty($_SESSION['web_diario_periodo_coordena_id'])) {
       exit ('<script language="javascript">
                window.alert("ERRO! Primeiro informe um período!");
                window.close();
        </script>');
}

$conn = new connection_factory($param_conn);

unset($_SESSION['conteudo']);
unset($_SESSION['flag_falta']);

$qryPeriodo = 'SELECT id, descricao FROM periodos WHERE id = \''. $_SESSION['web_diario_periodo_coordena_id'].'\';';

$periodo = $conn->get_row($qryPeriodo);

$cursos = '';
$cont = 1;
foreach($_SESSION['web_diario_cursos_coordenacao'] as $c) {
	$cursos .= $c;
    if(count($_SESSION['web_diario_cursos_coordenacao']) > $cont)
		$cursos .= ',';
	$cont++;
}

$sql_cursos = " SELECT DISTINCT
    a.ref_curso || ' - ' || c.descricao AS curso, a.ref_curso, ref_tipo_curso
      FROM
          disciplinas_ofer a FULL OUTER JOIN cursos c ON (a.ref_curso = c.id)
            WHERE
                a.ref_periodo = '". $_SESSION['web_diario_periodo_coordena_id'] ."' AND
                a.ref_curso IN (". $cursos .")
            ORDER BY ref_tipo_curso;";

$cursos = $conn->get_all($sql_cursos);

$has_curso = FALSE;

if(count($cursos)  > 0) $has_curso = TRUE;

// RECUPERA INFORMACOES SOBRE oS PERIODOS DA COORDENACAO
$qry_periodos = 'SELECT DISTINCT o.ref_periodo,p.descricao,p.dt_inicial FROM disciplinas_ofer o, periodos p WHERE  o.ref_periodo = p.id AND o.ref_curso IN (SELECT DISTINCT ref_curso FROM coordenador WHERE ref_professor = '. $sa_ref_pessoa .') ORDER BY p.dt_inicial DESC;';
$periodos = $conn->get_all($qry_periodos);
// ^ RECUPERA INFORMACOES SOBRE oS PERIODOS DA COORDENACAO ^ //

$qry_periodos_naofinalizados = 'SELECT DISTINCT o.ref_periodo, o.id, p.descricao, p.dt_final
FROM disciplinas_ofer o, periodos p
WHERE  o.ref_periodo = p.id AND (o.fl_finalizada = \'f\' OR o.fl_digitada = \'f\') AND o.is_cancelada = \'0\'  AND o.ref_curso
IN (SELECT DISTINCT ref_curso FROM coordenador WHERE ref_professor = '. $sa_ref_pessoa .');';
$periodos_abertos = $conn->get_all($qry_periodos_naofinalizados);

$periodos_encerrados = array();
$diario_naofinalizado = 0;
foreach($periodos_abertos as $p) {
	if (time() - strtotime($p['dt_final']) > 0) {
		$periodos_encerrados['ref_periodo'] = $p['descricao'];
		$diario_naofinalizado++;
	}
}
$msg_diarios_abertos = '';
if (count($periodos_encerrados) > 0 || empty($msg_diarios_abertos)) {
	$msg_diarios_abertos = 'No(s) período(s) '. implode(', ',$periodos_encerrados);
	$msg_diarios_abertos .= ', já encerrado(s), existe(m) ' . $diario_naofinalizado;
	$msg_diarios_abertos .= ' diário(s) em aberto e/ou preenchido(s) que devia(m) estar devidamente fechado(s), por favor verifique e providencie o(s) seu(s) fechamento(s)!';
}
?>

<html>
<head>
<title><?=$IEnome?> - web di&aacute;rio</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">

<script type="text/javascript" src="<?=$BASE_URL .'lib/prototype.js'?>"> </script>

</head>

<body>

<div align="left">

<strong>
            <font size="4" face="Verdana, Arial, Helvetica, sans-serif">
                Per&iacute;odo de coordenação:
                <font color="red" size="4" face="Verdana, Arial, Helvetica, sans-serif"><?=$periodo['descricao']?></font>
            </font>
</strong>
&nbsp;&nbsp;

<span><a href="#" title="alterar o per&iacute;odo" id="periodos_coordenacao">alterar</a></span>
<br />
<br />
<!-- panel para alteracao dos periodos do coordenador // inicio //-->
<div id="periodos_coordenacao_pane" style="display:none; border: 0.0015em solid; width: 200px; text-align:center;">
<br />

<h4>clique no per&iacute;odo:</h4>
<br />
<?php
    foreach($periodos as $p) {
        echo '<a href="#" title="Per&iacute;odo '. $p['descricao'] .'" alt="Per&iacute;odo '. $p['descricao'] .'" onclick="set_periodo(\'periodo_coordena_id='. $p['ref_periodo'] .'\');">'. $p['descricao'] .'</a><br />';
    }
?>
<br />
</div>
<!-- panel para alteracao dos periodos do coordenador \\ fim \\ -->
<br />
<?php
    if (!$has_curso) :
        exit('<h3>Nenhum curso encontrado para o período selecionado</h3>');
    else :
?>

<span class="diario_aberto"><?=$msg_diarios_abertos?></span><br />

<strong>
            <font size="4" face="Verdana, Arial, Helvetica, sans-serif">
                Cursos desta coordenação
            </font>
</strong>
<br /> <br />

<h5>clique no curso para acessar os diários</h5>
<br />

<?php
    foreach($cursos as $c) {
		$onclick = 'onclick="abrir(\''. $IEnome .' - web diário\', \'requisita.php?do=lista_diarios_coordenacao&id='. $c['ref_curso'] .'\');"';
        echo '<a href="#" title="Curso '. $c['curso'] .'" alt="Curso '. $c['curso'] .'" '. $onclick .'>'. $c['curso'] .'</a><br />';
    }
?>

<br /><br />

<form name="acessa_diario" id="acesso_diario" method="post" action="">
<strong>Acesso rápido</strong> <br />
Código do diário:
<input type="text" name="diario_id" id="diario_id" size="10" />
<input type="button" name="envia_diario" id="envia_diario" value="Consultar" onclick="enviar_diario('pesquisa_diario_coordenacao',null,null,'<?=$BASE_URL?>','<?=$IEnome?>');" />
</form>

<?php endif; ?>

<br />
</div>
<script language="javascript" type="text/javascript">
    $('periodos_coordenacao').observe('click', function() { $('periodos_coordenacao_pane').toggle(); });
</script>

</body>
</html>
