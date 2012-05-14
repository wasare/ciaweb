<?php

require_once(dirname(__FILE__) .'/../../config/configuracao.php');
require_once($BASE_DIR .'core/data/connection_factory.php');
require_once($BASE_DIR .'core/login/session.php');
require_once($BASE_DIR .'core/login/auth.ldap.php');
require_once($BASE_DIR .'core/date.php');
require_once($BASE_DIR .'core/number.php');
require_once($BASE_DIR .'core/situacao_academica.php');

// Inicia a sessao
$sessao = new session($param_conn);
$conn = new connection_factory($param_conn_aluno);

/*
 * Verifica se as variaveis de sessao do usuario foram setadas
 */
if (isset($_SESSION['sa_aluno_user']) && $_SESSION['sa_aluno_user'] != '') {
  $prontuario_upper  = $_SESSION['sa_aluno_user'];
  $senha = $_SESSION['sa_aluno_senha'];
}
else {
    /*
     * Verifica se o formulario de autenticacao
     * enviou parametros
     */
    if($_POST['prontuario'] && $_POST['senha']) {
        $prontuario_upper = mb_strtoupper($_POST['prontuario'], 'UTF-8');
        $senha = md5($_POST['senha']);

        $_SESSION['sa_aluno_user']  = $prontuario_upper;
        $_SESSION['sa_aluno_senha'] = $senha;
    }
    else {
        /*
         * Em caso de sessao expirada retorna para
         * o formulario de autenticacao
         */
        header('location: index.php');
    }
}

$prontuario_lower = mb_strtolower($prontuario_upper, 'UTF-8');

$sql_verifica = "SELECT senha, ppc.prontuario, ppc.ref_pessoa FROM acesso_aluno a LEFT OUTER JOIN pessoa_prontuario_campus ppc ON (a.ref_pessoa = ppc.ref_pessoa) WHERE ppc.prontuario = '$prontuario_upper'; ";

$aluno_info = $conn->get_row($sql_verifica);
$aluno_id = $aluno_info['ref_pessoa'];
$senha_banco = $aluno_info['senha'];

// verifica usuário na base LDAP e na base SQL
$authLDAP = new authLDAP($param_ldap_aluno);

// autentica na base LDAP e atualiza a senha caso necessário
if ($authLDAP->authenticate('a'. $prontuario_lower, $_POST['senha'])) {
    
  // atualiza senha no banco com base na autenticação feita no LDAP
  if ($senha_banco != $senha) {
    $atualiza_senha = "UPDATE acesso_aluno SET senha = '$senha' WHERE ref_pessoa = ". $aluno_id .";";
    $usuario_atualizado = $conn->Execute($atualiza_senha);
  }

  $aluno_info = $conn->get_row($sql_verifica);
  $senha_banco = $aluno_info['senha'];
}

$acesso_cont = ($senha == $senha_banco) ? 1 : 0;

if ($acesso_cont == 0) {
    print '<script language="javascript">
           window.alert("Dados de acesso incorretos!");
           javascript:window.history.back(1);
           </script>';
    exit;
}
?>
