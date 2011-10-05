<?php

require_once("../../../app/setup.php");
require_once("../../../lib/adodb5/tohtml.inc.php");
require_once("../../../core/reports/header.php");
require_once("../../../core/reports/carimbo.php");

$conn = new connection_factory($param_conn);

$header  = new header($param_conn);
$carimbo = new carimbo($param_conn);

$periodo = (string) $_POST['periodo1'];
$pronturio = (string) $_POST['prontuario'];

$campus_id = (int) $_POST['campus_id'];

$aluno_id = (int) $conn->get_one("SELECT DISTINCT ref_pessoa FROM contratos WHERE ref_campus = $campus_id AND prontuario = '$prontuario';");

if ($aluno_id == 0 && !empty($prontuario))
	exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Aluno não encontrado!");window.close();</script>');


if ($aluno_id == 0 && empty($periodo)) {
  exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Dados invalidos!");window.close();</script>');
}

$filtro = $periodo_dispensa = '';
if ($aluno_id > 0) {
  $filtro = ' AND m.ref_pessoa = '. $aluno_id;
  $periodo_dispensa = 'descricao_periodo(m.ref_periodo) AS "Período da disciplina dispensada",';
}
else {
  $filtro = ' AND m.ref_periodo = \''. $periodo .'\' ';

  if ($campus_id > 0)
    $filtro .= ' AND o.ref_campus = '. $campus_id;



}

$sql_dispensas = '
SELECT
	ref_pessoa AS "Matrícula", 
    p.nome AS "Aluno", 
    m.ref_curso || \' - \' || curso_desc(m.ref_curso) AS "Curso",
    descricao_disciplina(get_disciplina_de_disciplina_of(m.ref_disciplina_ofer)) AS "Disciplina",
    CASE WHEN m.ref_motivo_matricula <> 2  THEN \'<div align="center"> - </div> \'
    ELSE m.obs_aproveitamento
    END AS "Disciplina de origem",    
    CASE WHEN instituicao_nome(m.ref_instituicao) IS NULL THEN \'<div align="center"> - </div> \'
    ELSE instituicao_nome(m.ref_instituicao)
    END AS "Instituição de origem",
    to_char(m.nota_final,\'9G999D9\') AS "Nota",
	CASE WHEN m.ref_motivo_matricula = 2 THEN \'<div align="center"><font color="blue">AE</font></div>\'
         WHEN m.ref_motivo_matricula = 3 THEN \'<div align="center"><font color="green">CE</font></div>\'
         WHEN m.ref_motivo_matricula = 4 THEN \'<div align="center"><font color="red">EF</font></div>\'
    END AS "Motivo",
    get_campus(campus_disciplina_ofer(m.ref_disciplina_ofer)) AS "Campus",
    to_char(m.hora_matricula, \'dd/mm/yyyy\') AS "Data lançamento",
    '. $periodo_dispensa .'
    CASE WHEN m.obs_final = \'\' OR  m.obs_final IS NULL THEN \'<div align="center"> - </div> \'
    ELSE m.obs_final
    END AS "Observação"

	FROM
		matricula m, 
        pessoas p, 
        disciplinas_ofer o
	WHERE
	    ref_motivo_matricula in (2,3,4) AND 
		m.dt_cancelamento is null AND
		ref_pessoa = p.id AND 
		ref_disciplina_ofer = o.id
        '. $filtro .'
	ORDER BY lower(to_ascii(nome,\'LATIN1\')),"Curso";';

$Result1 = $conn->Execute($sql_dispensas);

$total = $Result1->RecordCount();

if($total < 1){
    echo "<script>alert('Nenhum registro foi retornado!'); window.close();</script>";
}

//Informacoes de cabecalho
$info .= "<strong>Data: </strong>" . date("d/m/Y") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Hora: </strong>" . date("H:i:s") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Total de Registros: </strong>" . $total . "&nbsp;&nbsp;";
if ($aluno_id == 0) {
  $info .= "-&nbsp;&nbsp;<strong>Período: </strong> <span>$periodo</span> <br />";
}
$legenda = '<font color="blue">AE = Aproveitamento de estudos</font>&nbsp;&nbsp;-&nbsp;&nbsp;';
$legenda .= '<font color="green">CE = Certifica&ccedil;&atilde;o de experi&ecirc;ncia</font>&nbsp;&nbsp;-&nbsp;&nbsp;<font color="red">EF = Dispensa de Educa&ccedil;&atilde;o F&iacute;sica</font>';

?>
<html>
<head>
        <title>Lista de di&aacute;rios</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="<?=$BASE_URL?>public/styles/style.css" rel="stylesheet" type="text/css">
        <link href="<?=$BASE_URL?>public/styles/print.css" rel="stylesheet" type="text/css" media="print" />
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<div style="width: 95%;" align="center">
   	<?php echo $header->get_empresa($PATH_IMAGES, $IEnome); ?>
    <h2>RELAT&Oacute;RIO DE ALUNOS DISPENSADOS</h2>
    <?=$info?>
    <br />
    <?php 
      rs2html($Result1, 'width="95%" cellspacing="0" border="0" class="tabela_relatorio" cellpadding="0"', FALSE, FALSE);
    ?>
    <br />
   	<?=$legenda?>
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
