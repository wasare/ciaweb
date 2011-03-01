<?php
// die('<h2>Sistema em manuten&ccedil;&atilde;o</h2>');
/* DEBUG
print_r($_POST);
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
*/
/**
 * Inclui arquivo com as configuracoes do sistema
 */
require_once(dirname(__FILE__).'/../config/configuracao.php');

/**
 * Arquivos requeridos
 */
require_once($BASE_DIR .'lib/adLDAP.php');
require_once($BASE_DIR .'core/data/connection_factory.php');
require_once($BASE_DIR .'core/login/session.php');
require_once($BASE_DIR .'core/login/auth.php');

/*
 * Inicia a sessao
 */
$sessao = new session($param_conn);

/*
 * Dados do usuario autenticado
 */
list($sa_usuario,$sa_senha,$sa_usuario_id,$sa_ref_pessoa) = explode(":",$_SESSION['sa_auth']);

/*
 * Verifica a autenticacao do usuario
 */
$sa_verifica_login = new auth($BASE_URL);
$sa_verifica_login->log_file($BASE_DIR .'logs/login.log');
$sa_verifica_login->check_login($sessao);


?>

