<?php

require_once("../../../app/setup.php");
require_once("../../../core/reports/header.php");
require_once("../../../core/reports/carimbo.php");
require_once("../../../lib/adodb5/tohtml.inc.php");

$conn = new connection_factory($param_conn);

$carimbo = new carimbo($param_conn);
$header  = new header($param_conn);

$periodo    = $_POST["periodo1"];
$curso_id   = $_POST["codigo_curso"];
$aluno_id   = $_POST["aluno"];

$sql = "
SELECT DISTINCT
    p.id as \"Matrícula\",
    p.nome as \"Nome\",
    SUM(d.carga_horaria) AS \"CH Matriculada\",
    SUM(m.num_faltas) as \"Total Faltas\",
    REPLACE(CAST(ROUND(((CAST(SUM(m.num_faltas) AS NUMERIC) / CAST(SUM(d.carga_horaria) AS NUMERIC)) *100 ),2) AS TEXT) ,'.', ',') AS \"% Faltas\"
    
FROM
    matricula m,
    disciplinas d,
    pessoas p,
    disciplinas_ofer o,
    periodos s
WHERE
    m.ref_pessoa = p.id AND
    p.id IN (
        SELECT DISTINCT
            ref_pessoa 
        FROM
            matricula  
        WHERE 
            ref_periodo = '$periodo' AND
            ref_curso = $curso_id
    ) AND
    m.ref_curso = $curso_id AND
    m.ref_periodo = '$periodo' AND               
    m.dt_matricula >= '2004-01-01' AND
    m.ref_disciplina_ofer = o.id AND
    d.id = o.ref_disciplina AND                
	s.id = o.ref_periodo ";

if ( is_numeric($aluno_id) )
	$sql .= " AND p.id = $aluno_id ";

$sql .= "  GROUP BY p.id, p.nome, m.ref_periodo, m.ref_curso   ORDER BY 2";


$sql = 'SELECT * FROM ('. $sql .') AS T1 ORDER BY lower(to_ascii("Nome"));';


$Result1 = $conn->Execute($sql);


$num_result = $Result1->RecordCount();


$info .= "<strong>Data: </strong>" . date("d/m/Y") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Hora: </strong>" . date("H:i:s") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Total de Registros: </strong>" . $num_result . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Período: </strong> <span>$periodo</span> <br><br>";


$sqlCurso = "
SELECT id || ' - ' || descricao
FROM cursos
WHERE id = '$curso_id';";

$RsCurso = $conn->Execute($sqlCurso);


if(is_numeric($curso_id)) 
	$info .= "<strong>Curso: </strong><span>" . $RsCurso->fields[0] . "</span>";

$info .= '<br /><br /><strong>Aten&ccedil;&atilde;o: </strong><span><font color="red">Os dados abaixo est&atilde;o de acordo 
com o lan&ccedil;amento realizado pelos respons&aacute;veis pelas informa&ccedil;&otilde;es.</font> </span>';
	
$info .="<br><br>";

?>
<html>
<head>
    <title>SA</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="<?=$BASE_URL?>public/styles/style.css" rel="stylesheet" type="text/css">
    <link href="<?=$BASE_URL?>public/styles/print.css" rel="stylesheet" type="text/css" media="print" />
</head>
<body marginwidth="20" marginheight="20">
    <div style="width: 760px;" align="center">
        	<?php echo $header->get_empresa($PATH_IMAGES); ?>
            <h2>RELAT&Oacute;RIO DE FALTAS GLOBAL</h2>
            <?=$info?>
            <?php rs2html($Result1, 'width="90%" cellspacing="0" border="0" class="tabela_relatorio" cellpadding="0"', FALSE, FALSE); ?>
        <p>&nbsp;</p>
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
