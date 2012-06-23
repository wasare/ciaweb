<?php

//INCLUSAO DE BIBLIOTECAS
require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'lib/adodb5/tohtml.inc.php');

$conn = new connection_factory($param_conn);

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);

$cod_aluno = $_POST["codigo_pessoa"];
$periodo = $_POST["periodo1"];

$sqlAluno = "SELECT id, nome FROM pessoas WHERE id = $cod_aluno;";

$RsAluno = $conn->Execute($sqlAluno);

$sqlDiarios = "
SELECT
m.id, m.ref_disciplina_ofer, d.id, d.descricao_disciplina, m.ref_curso, c.descricao, o.is_cancelada, o.turma
FROM
matricula m, cursos c, disciplinas_ofer o, disciplinas d
WHERE
m.ref_pessoa = '$cod_aluno' AND
m.ref_periodo = '$periodo' AND
m.ref_curso = c.id AND
o.id = m.ref_disciplina_ofer AND
o.ref_disciplina = d.id
ORDER BY c.descricao;";

$RsDiarios = $conn->Execute($sqlDiarios);

if ( $RsDiarios->RecordCount() > 0 ) {

$exibe_diarios = ' <table border="0" cellpadding="0" cellspacing="2">      <tr>
        <td height="32" bgcolor="#CCCCFF">&nbsp;</td>
        <td bgcolor="#CCCCFF">Di&aacute;rio</td>
        <td bgcolor="#CCCCFF">Disciplina</td>
        <td bgcolor="#CCCCFF">Turma</td>
        <td bgcolor="#CCCCFF">Curso</td>
      </tr>';

while(!$RsDiarios->EOF) {

	if($cor == "#E1E1FF")
	{
		$cor = "#FFFFFF";
	} else
	{
		$cor = "#E1E1FF";
	}
	$cancelado = ($RsDiarios->fields[6] == 1) ? '&nbsp;<strong>*</strong>' : '';
	$exibe_diarios .= "<tr bgcolor=\"$cor\">";
	$exibe_diarios .= "<td><input name=\"id_matricula[]\" type=\"checkbox\" value=\"" . $RsDiarios->fields[0] . "\" /></td>";
	$exibe_diarios .= "<td>" . $RsDiarios->fields[1] . $cancelado ."</td>";
	$exibe_diarios .= "<td>" . $RsDiarios->fields[2] . " - " . $RsDiarios->fields[3] . "</td>";
  $exibe_diarios .= "<td>" . $RsDiarios->fields[7] . "</td>";
	$exibe_diarios .= "<td>" . $RsDiarios->fields[4] . " - " . $RsDiarios->fields[5] . "</td>";
	$exibe_diarios .= "</tr>";

	$RsDiarios->MoveNext();

}
  $exibe_diarios .= "</table>";
  $exibe_diarios .= '<strong>(*) Disciplina cancelada.</strong>';
  $exibe_botao = '<p class="msg_erro"><strong>Aten&ccedil;&atilde;o! Esta a&ccedil;&atilde;o n&atilde;o pode ser desfeita!
</strong></p>';
  $exibe_botao .= '<input type="submit" value="Excluir a matr&iacute;cula nas disciplinas selecionadas" />';

}
else
    $exibe_diarios = '<font color="blue"><h3> Nenhuma matr&iacute;cula encontrada! </h3></font>';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SA</title>
<link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
</head>
<body>
<form action="excluir_action.php" name="form1" method="post">
  <input type="hidden" name="cod_aluno" id="cod_aluno" value="<?=$cod_aluno?>" />
  <input type="hidden" name="periodo" id="cod_aluno" value="<?=$periodo?>" />
  <div align="center">
    <h1>Excluir Matr&iacute;cula</h1>
    <div class="panel">
      <strong>Aluno: </strong><?=$RsAluno->fields[0]?> - <?=$RsAluno->fields[1]?><br />
      <strong>Per&iacute;odo: </strong> <?=$periodo?>
    </div>
      <?=$exibe_diarios?>
    <p>
      <?=$exibe_botao?>
    </p>
  </div>
  </form>
</body>
</html>
