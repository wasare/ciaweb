<?php

require_once("../../../app/setup.php");
require_once("../../../lib/adodb5/tohtml.inc.php");
require_once("../../../core/reports/carimbo.php");
require_once("../../../core/reports/header.php");


$conn = new connection_factory($param_conn);

$carimbo = new carimbo($param_conn);
$header  = new header($param_conn);

$periodo    = $_POST["periodo1"];
$tipo       = $_POST["tipo"];
$campus     = $_POST["campus"];


$sql = "
SELECT DISTINCT
    o.id AS \"Diário\",
    d.descricao_disciplina || '(' || d.id || ')' AS \"Disciplina\",
    t.descricao AS \"Tipo\",
    o.turma AS \"Turma\",
    get_turno_(c.turno) AS \"Turno\",
    
	CASE WHEN professor_disciplina_ofer_todos(o.id) = '' THEN '<font color=\"red\">sem professor</font>'
         ELSE professor_disciplina_ofer_todos(o.id)
    END AS \"Professor\",

    s.abreviatura AS \"Curso\",

    CASE WHEN o.fl_finalizada = TRUE THEN '<font color=\"red\">Fechado</font>'
         WHEN o.fl_digitada = TRUE THEN '<font color=\"blue\">Preenchido</font>'
         ELSE '<font color=\"green\">Aberto</font>'
    END AS \"Situação\",

    '<a href=\"#\" onclick=\"abrir(\'Diario_Classe\', \'../../web_diario/requisita.php?do=diario_classe&id=' || o.id || '\');\"  id=\" || o.id || \">Imprimir</a>' AS \"Diário Classe (A3)\"

FROM
    disciplinas_ofer o,
    disciplinas d,
    disciplinas_ofer_compl c,
    cursos s,
    tipos_curso t,
    campus m

WHERE
    o.ref_curso = s.id AND o.id = c.ref_disciplina_ofer AND ";

if($tipo != '')
	$sql .= " t.id = '$tipo' AND ";

$sql .= " s.ref_tipo_curso = t.id AND
    o.ref_periodo = '$periodo' AND
    o.is_cancelada = '0' AND
    d.id = o.ref_disciplina AND ";

if($campus != '')
	$sql .= " o.ref_campus = '$campus' AND ";

$sql .= " o.ref_campus = m.id
		ORDER BY \"Disciplina\";";


$Result1 = $conn->Execute($sql);

$num_result = $Result1->RecordCount();


$info .= "<strong>Data: </strong>" . date("d/m/Y") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Hora: </strong>" . date("H:i:s") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Total de Registros: </strong>" . $num_result . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Período: </strong> <span>$periodo</span> <br><br>";


if($campus != ''){
    $RsCampus = $conn->Execute("SELECT nome_campus FROM campus WHERE id = $campus;");
	$info .= "<strong>Campus: </strong><span>" . $RsCampus->fields[0] . "</span>&nbsp;&nbsp;-&nbsp;&nbsp;";
}

if($tipo != '') {
    $RsTipo = $conn->Execute("SELECT descricao FROM tipos_curso WHERE id = $tipo;");
	$info .= "<strong>Tipo de curso: </strong><span>" . $RsTipo->fields[0] . "</span>";
}

$info .="<br><br>";

$rodape = '<span style="font-size: 12px;">' . $resp_nome . "</span><br>";
$rodape .= '<span style="font-size: 9px;"><strong>' . $resp_cargo . "</strong></span><br>";

?>
<html>
<head>
    <title>Lista de di&aacute;rios</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="<?=$BASE_URL?>public/styles/style.css" rel="stylesheet" type="text/css">
    <link href="<?=$BASE_URL?>public/styles/print.css" rel="stylesheet" type="text/css" media="print" />
    <script type="text/javascript" src="<?=$BASE_URL .'lib/prototype.js'?>"> </script>
    <script type="text/javascript" src="<?=$BASE_URL .'app/web_diario/web_diario.js'?>"> </script>

</head>
<body marginwidth="20" marginheight="20">
    <div style="width: 760px;" align="center">
      <?php echo $header->get_empresa($PATH_IMAGES, $IEnome); ?>
        <h2>RELAT&Oacute;RIO DE ANDAMENTO DOS DI&Aacute;RIOS</h2>
        <?=$info?>
        <?php rs2html($Result1, 'width="90%" cellspacing="0" border="0" class="tabela_relatorio" cellpadding="0"', FALSE, FALSE); ?>
        <br /><br />
        <div class="carimbo_box">
            _______________________________<br>
        	<span class="carimbo_nome">
        		<?php echo $carimbo->get_nome($_POST['carimbo']);?>
        	</span><br />
        	<span class="carimbo_funcao">
        		<?php echo $carimbo->get_funcao($_POST['carimbo']);?>
        	</span>
        </div>
    </div>
<br />
<div class="nao_imprime">
  <input type="button" value="Imprimir" onClick="window.print()" />
  &nbsp;&nbsp;&nbsp;
  <a href="#" onclick="javascript:window.close();">Fechar</a>
</div>
<br />
</body>
</html>

