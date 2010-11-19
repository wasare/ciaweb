<?php

require_once("../../../app/setup.php");
require_once("../../../core/reports/header.php");
require_once("../../../lib/adodb5/tohtml.inc.php");


$conn = new connection_factory($param_conn);

$header  = new header($param_conn);


$periodo    = $_POST['periodo1'];
$tipo_curso = $_POST['tipo'];



if ( is_numeric($_POST['cidade']) ) {

	$cidade = ' o.ref_campus = '. $_POST['cidade'] .' AND';
    
    $RsCidade = $conn->Execute("SELECT cidade_campus FROM campus WHERE id = " . $_POST['cidade'] . ";");
    
	$txt_cidade = "&nbsp;&nbsp;-&nbsp;&nbsp;<strong>Cidade: </strong>" . $RsCidade->fields[0];

}else{
    $cidade = '';
}


$sqlTipoCurso = '';

if ( is_numeric($tipo_curso) ){
	$sqlTipoCurso = ' AND c.ref_tipo_curso = \''. $tipo_curso."'";
}

$sqlCursos = "
SELECT
	t1.id as \"Cód.\",
	t1.descricao AS \"Descrição do Curso\",
	tipo_curso AS \"Tipo Curso\",
	t2.mat AS \"Alunos\"
FROM
    ( 
        SELECT DISTINCT
            c.id ,
			c.descricao,
			t.descricao as tipo_curso
        FROM
            matricula m,
			cursos c,
			tipos_curso t 
        WHERE
            m.ref_periodo = '$periodo' AND
			m.ref_curso = c.id AND
			c.ref_tipo_curso = t.id $sqlTipoCurso
    ) AS t1
    INNER JOIN 
    (
        SELECT
            ref_curso,
			count(mat) as mat 
        FROM (
            SELECT DISTINCT
                m.ref_curso,
				m.ref_pessoa as mat
            FROM 
                matricula m,
				disciplinas_ofer o
            WHERE 
                m.ref_periodo = '$periodo' AND
                m.ref_disciplina_ofer = o.id AND
				$cidade 
                o.is_cancelada = '0'
            ) as T
        GROUP BY ref_curso                 
    ) AS t2         
    ON (t1.id = t2.ref_curso)
ORDER BY 3, 4 DESC;";

$sqlMatriculas = "
SELECT SUM(mat)
FROM 
(
SELECT
	t1.id ,
	t2.mat
FROM
    ( 
        SELECT DISTINCT
            c.id ,
			c.descricao
        FROM
            matricula m,
			cursos c,
			tipos_curso t 
        WHERE
            m.ref_periodo = '$periodo' AND
			m.ref_curso = c.id AND
			c.ref_tipo_curso = t.id $sqlTipoCurso
    ) AS t1
    INNER JOIN 
    (
        SELECT
            ref_curso,
			count(mat) as mat 
        FROM (
            SELECT DISTINCT
                m.ref_curso,
				m.ref_pessoa as mat
            FROM 
                matricula m,
				disciplinas_ofer o
            WHERE 
                m.ref_periodo = '$periodo' AND
                m.ref_disciplina_ofer = o.id AND
				$cidade
                o.is_cancelada = '0'
        ) as T
	    GROUP BY ref_curso                 
    ) AS t2         
    ON (t1.id = t2.ref_curso) 
) AS M;";

$RsCursos = $conn->Execute($sqlCursos);

$RsMatriculas = $conn->Execute($sqlMatriculas);

$Matriculas = $RsMatriculas->fields[0];


$info .= "<strong>Data: </strong>" . date("d/m/Y") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Hora: </strong>" . date("H:i:s") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Per&iacute;odo: </strong> <span>$periodo</span> <br /><br />";
$info .= "<strong>Total de Matr&iacute;culas: </strong>" . $Matriculas . $txt_cidade . '<br /><br />' ;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>SA</title>
	<link href="<?=$BASE_URL?>public/styles/style.css" rel="stylesheet" type="text/css">
    <link href="<?=$BASE_URL?>public/styles/print.css" rel="stylesheet" type="text/css" media="print" />
</head>
<body marginwidth="20" marginheight="20">
	<div style="width: 760px;" align="center">
      	<?php echo $header->get_empresa($PATH_IMAGES); ?>
  
		<h2>CURSOS COM ALUNOS MATRICULADOS NO PERÍODO</h2>
		<?php 
	        echo $info;
			rs2html($RsCursos, 'cellspacing="0" border="0" class="tabela_relatorio" cellpadding="0"'); 
		?>
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
