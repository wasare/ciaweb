<?php

require_once("../../../app/setup.php");
require_once("../../../lib/adodb5/tohtml.inc.php");
require_once("../../../core/reports/header.php");
require_once("../../../core/reports/carimbo.php");


$conn = new connection_factory($param_conn);

$header  = new header($param_conn);
$carimbo = new carimbo($param_conn);

$periodo = $_POST['periodo1'];

$sqlCursos = "
select distinct 
    c.id as \"Cód.\", c.descricao as \"Descrição do Curso\"
from 
    matricula m, cursos c
where
    m.ref_periodo = '$periodo' AND
    m.ref_curso = c.id
ORDER BY 2;";

$RsCursos = $conn->Execute($sqlCursos);

$total = $RsCursos->RecordCount();
    
if($total < 1){
  echo "<script>alert('Nenhum registro foi retornado!'); window.close();</script>";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SA</title>
    <link href="<?=$BASE_URL?>public/styles/style.css" rel="stylesheet" type="text/css" />
    <link href="<?=$BASE_URL?>public/styles/print.css" rel="stylesheet" type="text/css" media="print" />
</head>

<body marginwidth="20" marginheight="20">
<div style="width: 760px;">

       	<?php echo $header->get_empresa($PATH_IMAGES, $IEnome); ?>

  <h2>MATRÍCULAS/CIDADES DE ALUNOS POR CURSO</h2>
  <h3>Per&iacute;odo: <?=$periodo; ?></h3>
  <br />
    <?php
        while(!$RsCursos->EOF) {
            echo "<h3>" . $RsCursos->fields[0] . " - " . $RsCursos->fields[1] . "</h3>";
            $id_curso = $RsCursos->fields[0];
            $sqlCursoCidade = "
                SELECT
                    COUNT(p.id) as \"Quant\", a.nome as \"Cidade\", a.ref_estado as \"UF\"
                FROM
                    pessoas p LEFT JOIN cidade a ON(p.ref_cidade = a.id)
                WHERE
                    p.id IN (
                        SELECT DISTINCT
                        ref_pessoa
                        FROM matricula
                        WHERE
                        ref_periodo = '$periodo' AND
                        ref_curso = '$id_curso'
                    )
                GROUP BY a.nome, a.ref_estado
                ORDER BY a.nome";
            $RsCursoCidade = $conn->Execute($sqlCursoCidade);
            rs2html($RsCursoCidade, 'cellspacing="0" border="0" class="tabela_relatorio" cellpadding="0"');
            $RsCursos->MoveNext();
        }
    ?>
    <br /><br />
    <center>
    <div class="carimbo_box">
        _______________________________<br />
        <span class="carimbo_nome">
            <?php echo $carimbo->get_nome($_POST['carimbo']);?>
        </span><br />
        <span class="carimbo_funcao">
            <?php echo $carimbo->get_funcao($_POST['carimbo']);?>
        </span>
    </div>
    </center>
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
