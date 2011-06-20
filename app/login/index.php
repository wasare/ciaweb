<?php

require_once('../../config/configuracao.php');
require_once($BASE_DIR .'lib/adLDAP.php');
require_once($BASE_DIR .'core/data/connection_factory.php');
require_once($BASE_DIR .'core/login/session.php');
require_once($BASE_DIR .'core/login/auth.php');

$sessao = new session($param_conn);

// INICIA UM NOVO PROCESSO DE LOGIN
if(!isset($_POST['uid']) || !isset($_POST['pwd'])) {
		
	// FAZ O PROCESSO DE LOGOUT EXCLUINDO A SESSAO DO BANCO
	list($sa_usuario,$sa_senha,$sa_usuario_id,$sa_ref_pessoa) = explode(":",$_SESSION['sa_auth']);
    $cont = 0;
    while(isset($_SESSION['sa_auth'])) {
      @$sessao->clear_session($sa_usuario, NULL);
      @$sessao->destroy();
      if($cont == 2) break;
      $cont++;
    }
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?=$IEnome?> - Controle de Informa&ccedil;&otilde;es Acad&ecirc;micas</title>
        <link href="../../public/images/favicon.ico" rel="shortcut icon" />
        <link href="../../public/styles/style.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            #alert_login{
                font-family:verdana,arial;
                font-size:14;
                font-weight:bold;
                color: red;
                position:absolute;
                top: 50%;
                left: 50%;
                margin-left:-170px;
                margin-top:-120px;
                width:300px;
                height:180px;
                z-index:1;
                background-color:#FFF6D5;
                padding: 4px;
                border: 4px solid orange;
            }
            #alert_login a{
                text-align:right;
            }
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
                        <h3>Bem-vindo ao Controle de Informa&ccedil;&otilde;es Acad&ecirc;micas (CIAWEB).</h3>
                        Para iniciar entre com os dados de sua conta.<br />
                        <br />
                    </td>
                </tr>
            </table>
            <h2>Entre com sua conta</h2>
            <div id="caixa_login">
                <form method="post" action="<?=$BASE_URL .'app/login/seleciona_modulo.php'?>" name="myform">
                    <table border="0">
                      <tr>
                            <td>
                              &nbsp;
                            </td>
                            <td>
                              &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                Usu&aacute;rio:
                            </td>
                            <td>
                                <input type="text" name="uid" maxlength="20" style="width: 140px;" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                Senha:
                            </td>
                            <td>
                                <input type="password" name="pwd" maxlength="20" style="width: 140px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                                <p>
                                    <input type="image" src="../../public/images/bt_entrar.png" />
                                </p>
                                <!--<a href="../../public/esqueci_senha.php">Esqueci minha senha.</a>-->
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
        <!-- Mensagens -->
        <?php if($_SESSION['sa_msg']) { ?>
        <div id="alert_login">
            <table border="0">
                <tr>
                    <td colspan="2" align="right">
                        <a href="#" onclick="document.getElementById('alert_login').style.display = 'none'">Fechar</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="../../public/images/alert.png" alt="Aten&c&ccedil;&ati&atilde;o" />
                    </td>
                    <td>
                        <?=$_SESSION['sa_msg']?>
                    </td>
                </tr>
            </table>
        </div>
        <?php } ?>
    </body>
</html>

