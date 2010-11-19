<?php

require_once(dirname(__FILE__) .'/../../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/date.php');

// CONEXAO ABERTA PARA TRABALHAR COM TRANSACAO (NÃƒO PERSISTENTE)
$conn = new connection_factory($param_conn,FALSE);

$diario_id = (int) $_POST['diario_id'];
$operacao = $_POST['operacao'];

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
}
// ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //

if (is_finalizado($diario_id)){

    echo '<script language="javascript" type="text/javascript">';
    echo 'alert("ERRO! Este diário está finalizado e não pode ser alterado!");';
    echo 'window.close();';
    echo '</script>';
    exit;
}

$periodo = $_SESSION['web_diario_periodo_id'];
$conteudo = addslashes($_SESSION['conteudo']);
$aula_tipo = $_POST['aula_tipo'];

if(!isset($_POST['num_aulas']) || empty($_POST['num_aulas']))
  $num_aulas = $aula_tipo[strlen($aula_tipo) - 1];
else
  $num_aulas = $_POST['num_aulas'];

$alunos_faltas = (isset($_POST['faltas'])) ? $_POST['faltas'] : '';
/*
if(!isset($_POST['data_chamada']) || empty($_POST['data_chamada'])) {

  if(empty($_POST['select_dia']))
    die('<font size=2><b>Voc&ecirc; n&atilde;o selecionou o DIA ! <a href="javascript:history.go(-1);">Voltar</a>!</b></font>');
  else
    $select_dia = $_POST['select_dia'];

  if(empty($_POST['select_mes']))
    die('<font size=2><b>Voc&ecir;  n&atilde;o selecionou o M&Ecirc;S ! <a href="javascript:history.go(-1);">Voltar</a>!</b></font>');
  else
    $select_mes = $_POST['select_mes'];

  if(empty($_POST['select_ano']))
    die('<font size=2><b>Voc&ecirc; n&atilde;o selecionou o ANO ! <a href"javascript:history.go(-1);">Voltar</a>!</b></font>');
  else
    $select_ano = $_POST['select_ano'];
}
else
*/
  $data_chamada = $_POST['data_chamada'];


// VERIFICA SE NAO EXISTE CHAMADA NESTA DATA
if(existe_chamada($diario_id, $data_chamada))
	die('<script language="javascript" type="text/javascript"> window.alert("Já existe chamada realizada para esta data.");window.history.back(1); </script>');
// ^ VERIFICA SE NAO EXISTE CHAMADA NESTA DATA ^ //

$sem_faltas = '';

// HOUVE FALTAS PARA A CHAMADA
$sem_faltas = (isset($_POST['flag_falta']) && $_POST['flag_falta'] == 'F') ? '<h3><font color="blue"><b>Nenhum aluno faltou &agrave;(s) '. $num_aulas .' aula(s)  do dia '. $data_chamada .'</b></font></h4>' : '';

$curso = get_curso($diario_id);
$disciplina = get_disciplina($diario_id);

function processa_chamada($alunos_faltas, $num_aulas, $sql_chamada) {
  
  global $conn, $data_chamada, $sa_ref_pessoa, $periodo, $diario_id, $sem_faltas;

  // registra a chamada no banco de dados
  $conn->Execute($sql_chamada);

  $resposta .= $sem_faltas;
	
  if(is_array($alunos_faltas)  && count($alunos_faltas) > 0) {

    reset($alunos_faltas);
	
    foreach($alunos_faltas as $reg_aluno => $num_faltas) {

	  $sqlFaltas = 'BEGIN;';
     
	  if($num_faltas > 0 && $num_faltas <= $num_aulas) {

        $aluno = $conn->get_one("SELECT nome FROM pessoas WHERE id = $reg_aluno;");
        $aluno = '<font color="red"><b>'. $aluno .' ('. $reg_aluno .')</b></font>';

        if(registra_faltas($reg_aluno, $diario_id, abs($num_faltas), $data_chamada, $sa_ref_pessoa) === TRUE)
            $resposta .= '<strong>'. $num_faltas . '</strong> Falta(s) registrada(s) para '. $aluno .' no dia '. $data_chamada .'<br />';        
	  }
    }
  }
  echo $resposta;
}


$datadehoje = date ("d/m/Y");

$sql_chamada = 'BEGIN; INSERT INTO diario_seq_faltas (id_prof, periodo, curso, disciplina, dia, conteudo, flag, ref_disciplina_ofer) VALUES ';
$sql_chamada .= " ('$sa_ref_pessoa','$periodo','$curso','$disciplina','$data_chamada','$conteudo', '$num_aulas', $diario_id);COMMIT;";

$status = 'FALTA REGISTRADA ';

$status .= $aula_tipo;

?>

<html>
  <head>
  <title><?=$IEnome?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
</head>
<body>
<br />
<div align="left" class="titulo1">
  Lan&ccedil;amento de Chamada / Faltas
</div>
  
<br /><br />

<?=papeleta_header($diario_id)?>
<br />
<?=processa_chamada($alunos_faltas,$num_aulas,$sql_chamada)?>

<?=reg_log($_SERVER["PHP_SELF"],$status)?>

<br />
<strong>CHAMADA REALIZADA!</strong><br /><br /> * Verifique acima se n&atilde;o ocorreu nenhum erro no processo de incluir faltas *<br /> <br />

<a href="<?=$BASE_URL .'app/web_diario/requisita.php?do='. $operacao .'&id=' . $diario_id?>">Fazer nova chamada</a>
&nbsp;&nbsp;ou&nbsp;&nbsp;<a href="#" onclick="javascript:window.close();">fechar</a>
<br /><br />
</body>
</html>
