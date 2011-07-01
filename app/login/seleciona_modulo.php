<?php

require_once('../../config/configuracao.php');
require_once($BASE_DIR .'lib/adLDAP.php');
require_once($BASE_DIR .'core/data/connection_factory.php');
require_once($BASE_DIR .'core/login/session.php');
require_once($BASE_DIR .'core/login/auth.php');
require_once($BASE_DIR .'core/login/acl.php');

$sessao = new session($param_conn);
$conn = new connection_factory($param_conn);

// verifica usuário na base LDAP e na base SQL
$adLdap = new adLDAP($param_ldap);
$autentica = new auth($BASE_URL, $adLdap);
$autentica->log_file($BASE_DIR .'logs/login.log');

// INICIA UM NOVO PROCESSO DE LOGIN
if(isset($_POST['uid']) && !empty($_POST['uid']) && isset($_POST['pwd']) ) {	
		
		$pessoa_sql = "SELECT ref_pessoa FROM usuario WHERE nome = '". $_POST['uid'] ."';";	
    $pessoa_id = $conn->get_one($pessoa_sql);		
		
		// VERIFICA SE O USUARIO TEM DIREITOS DE ACESSO
		$acl = new acl();		
		$papeis = $acl->get_roles($pessoa_id, $conn);
		
		if (count($papeis) == 0) {
				$_SESSION['sa_msg'] = 'Sem permissão de acesso';
				$_POST = array();
				exit(header('Location: '. $BASE_URL .'index.php'));
    }
		else {
				$autenticado = FALSE;
				
				$autenticado = $autentica->login(trim($_POST['uid']),trim($_POST['pwd']),'web_diario_login', $conn);
				if($autenticado == FALSE)
						$autenticado = $autentica->login(trim($_POST['uid']),trim($_POST['pwd']),'sa_login', $conn);				
		}	
		
		if($autenticado == FALSE) {
				$_SESSION['sa_msg'] = 'Sem permissão de acesso';
				$_POST = array();
				exit(header('Location: '. $BASE_URL .'index.php'));
		}
		
		// REDIRECIONA DIRETAMENTE AO WEBDIÁRIO QUANDO O USUÁRIO TEM SOMENTE ESTE PAPEL
		if (count($papeis) == 1 && in_array(3, $papeis)) {
				if($autentica->login(trim($_POST['uid']),trim($_POST['pwd']),'web_diario_login', $conn) === TRUE) {
						unset($_SESSION['sa_msg']);
						$_POST = array();
						exit(header('Location: '. $BASE_URL .'app/web_diario/'));
				}
		}
		
		// REDIRECIONA DIRETAMENTE AO MÓDULO SECRETARIA QUANDO O USUÁRIO NÃO É COORDENADOR E/OU PROFESSOR
		if (count($papeis) == 1 && !in_array(3, $papeis) && !in_array(0, $papeis)) {
				if($autentica->login(trim($_POST['uid']),trim($_POST['pwd']),'sa_login', $conn) === TRUE) {
						unset($_SESSION['sa_msg']);
						$_POST = array();
						exit(header('Location: '. $BASE_URL .'app/'));
				}
		}	
		
		if (count($papeis) > 1)	{
				
				$modulos = array();
				
				foreach($papeis as $p) {
						if (in_array($p, $PAPEIS_WEB_DIARIO))
								$modulos['web_diario_login'] = 'Professor <br /> <br />Coordenação';
								
						if (in_array($p, $PAPEIS_SA))
								$modulos['sa_login'] = 'Secretaria';
				}
				
				asort($modulos);
				
				$_SESSION['sa_uid'] = $_POST['uid'];
				$_SESSION['sa_pwd'] = $_POST['pwd'];
				
				$_POST = array();				
		}

}
else {
		if (isset($_SESSION['sa_uid']) && isset($_SESSION['sa_pwd']) && isset($_GET['do'])) {
		
				$_SESSION['sa_modulo'] = $_GET['do'];
				
				$uid = $_SESSION['sa_uid'];
				$pwd = $_SESSION['sa_pwd'];
				
				unset($_SESSION['sa_uid']);
				unset($_SESSION['sa_pwd']);
		
				// REDIRECIONA DE ACORDO COM O MODULO SELECIONADO
				switch ($_SESSION['sa_modulo']) {
						case 'sa_login':
								if($autentica->login(trim($uid),trim($pwd),$_SESSION['sa_modulo'], $conn) === TRUE) {
										exit(header('Location: '. $BASE_URL .'app/'));
								}
								else {
										$_SESSION['sa_msg'] = 'Senha ou usuário inválido';
										exit(header('Location: '. $BASE_URL .'index.php'));
								}
						break;
						case 'web_diario_login':
								if($autentica->login(trim($uid),trim($pwd),$_SESSION['sa_modulo'], $conn) === TRUE) {
										exit(header('Location: '. $BASE_URL .'app/web_diario/'));
								}
								else {
										$_SESSION['sa_msg'] = 'Senha ou usuário inválido';
										exit(header('Location: '. $BASE_URL .'index.php'));
								}
						break;
						default:
								$_SESSION['sa_msg'] = 'Sessão inválida';
								exit(header('Location: '. $BASE_URL .'index.php'));
				}		
		}
		else {
				// FAZ O PROCESSO DE LOGOUT EXCLUINDO A SESSAO DO BANCO
				list($sa_usuario,$sa_senha,$sa_usuario_id,$sa_ref_pessoa) = explode(":",$_SESSION['sa_auth']);
				$cont = 0;
				while(isset($_SESSION['sa_auth'])) {
						@$sessao->clear_session($sa_usuario, NULL);
						@$sessao->destroy();
						if($cont == 2) break;
						$cont++;
				}
				$_SESSION['sa_msg'] = 'Sem permissão de acesso';
				exit(header('Location: '. $BASE_URL .'index.php'));
		}
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?=$IEnome?> - Controle de Informa&ccedil;&otilde;es Acad&ecirc;micas</title>
        <link href="<?=$BASE_URL .'public/images/favicon.ico'?>" rel="shortcut icon" />
        <link href="<?=$BASE_URL .'public/styles/style.css'?>" rel="stylesheet" type="text/css" />
				<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
				    <style type="text/css">
            #caixa_login {
                background-color: #CEE7FF;
                width:300px;
                font-family: Verdana, Arial, Helvetica, sans-serif;
                font-size: 12px;
                border: 4px solid #3399FF;
                padding: 10px 5px 10px 5px;
                margin: 10px 5px 10px 5px;
            }
        </style>
    </head>
    <body>
        <div align="center">
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <img src="../../public/images/sa_icon.png" alt="logomarca SA" width="80" height="68" style="margin: 10px;" />
                    </td>
                    <td valign="top">
                        <h3>Controle de Informa&ccedil;&otilde;es Acad&ecirc;micas.</h3>
                        Clique no m&oacute;dulo que deseja acessar.<br />
                        <br />
                    </td>
                </tr>
            </table>
            <div id="caixa_login">
                <form method="post" action="<?=$BASE_URL .'app/login/seleciona_modulo.php'?>" name="myform">
                    <table border="0">
                        <tr>
                          <td colspan="2">
                            <fieldset style="padding-left: 2em; padding-right: 2em; padding-bottom: 2em; width: 150px; ">
                              <legend><strong><h4>M&oacute;dulos Dispon&iacute;veis</h4></strong></legend>
															  <br />
																<?php foreach($modulos as $key => $value) : ?>
																		<br /><span><a href="?do=<?=$key?>" title="<?=$value?>"><?=$value?></a></span><br />
																<?php endforeach; ?>
                              </fieldset>
														  <br />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <table border="0">
                <tr>
                    <td>
                        <img src="../../public/images/logo.jpg" alt="IFSP Campus Caraguatatuba" style="margin: 10px;" />
                    </td>
                    <td>
                        <strong>Instituto Federal S&atilde;o Paulo</strong><br />
                        Campus Caraguatatuba<br />
                    </td>
                </tr>
            </table>
			<p>
				<font color="#999999">&copy;2011 IFSP Campus Caraguatatuba</font>
			</p>
        </div>        
    </body>
</html>

