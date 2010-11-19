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

$aula_tipo = $_POST['aula_tipo'];
$num_aulas = $_POST['num_aulas'];
$data_chamada = $_POST['data_chamada'];

$alunos_faltas = (isset($_POST['faltas'])) ? $_POST['faltas'] : '';

// function que processa as alterações de falta
function processa_alteracao_faltas($alunos_faltas, $num_aulas) {
  
  global $conn, $data_chamada, $sa_ref_pessoa, $periodo, $diario_id, $sem_faltas, $curso, $disciplina;

  $resposta = '';
	
  if(is_array($alunos_faltas)  && count($alunos_faltas) > 0) {
    
    reset($alunos_faltas);
	
    foreach($alunos_faltas as $reg_aluno => $num_faltas) {

	  if($num_faltas <= $num_aulas || empty($num_faltas) || $num_faltas == 0) {

	    $aluno = $conn->get_one("SELECT nome FROM pessoas WHERE id = $reg_aluno;");
        $aluno = '<font color="red"><b>'. $aluno .' ('. $reg_aluno .')</b></font>';
        
        if(registra_faltas($reg_aluno, $diario_id, abs($num_faltas), $data_chamada, $sa_ref_pessoa, TRUE) === TRUE)
            $resposta .= '<strong>'. abs($num_faltas) . '</strong> Falta(s) registrada(s) para '. $aluno .' no dia '. date::convert_date($data_chamada) .'<br />';
	  }
    }
  }
  echo $resposta;
}


$datadehoje = date ("d/m/Y");

$status = 'FALTA REGISTRADA ';

$status .= $aula_tipo;

?>

<html>
  <head>
  <title><?=$IEnome?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
</head>
<body>
<br />
<div align="left" class="titulo1">
  Lan&ccedil;amento de Faltas - Altera&ccedil;&atilde;o
</div>
  
<br /><br />

<?=papeleta_header($diario_id)?>
<br />
<?=processa_alteracao_faltas($alunos_faltas,$num_aulas)?>

<?=reg_log($_SERVER["PHP_SELF"],$status)?>

<br />
<strong>ALTERA&Ccedil;&Atilde;O DE FALTAS REALIZADA!</strong><br /><br /> * Verifique acima se n&atilde;o ocorreu nenhum erro no processo de altera&ccedil;&atilde;o de faltas *<br /> <br />

<a href="<?=$BASE_URL .'app/web_diario/requisita.php?do='. $_SESSION['web_diario_do'] .'&id=' . $diario_id?>">Alterar outras faltas neste di&aacute;rio</a>
&nbsp;&nbsp;&nbsp;ou&nbsp;&nbsp;<a href="#" onclick="javascript:window.close();">fechar</a>
<br /><br />
</body>
</html>