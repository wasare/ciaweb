<?php
require_once(dirname(__FILE__) .'/../../../config/configuracao.php');
require_once($BASE_DIR .'core/login/session.php');

// inicia a sessao
$sessao = new session($param_conn);
$sessao->destroy();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?=$IEnome?></title>
        <link href="../../../public/images/favicon.ico" rel="shortcut icon" />
        <style>
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
                background-color: #F2F2F2;
                width:15%;
                font-family: Verdana, Arial, Helvetica, sans-serif;
                font-size: 12px;
                border: 4px solid #9C0400;
                padding: 10px 5px 10px 5px;
                margin: 10px 5px 10px 5px;
            }
        </style>
		<?php include("../includes/topoMobile.html"); ?>
    </head>

    <body>
        <div align="center">
        		<font color="#409B01"><h2>Bem vindo ao CIAWEB</h2></font>
                <form name="form1" method="post" action="lista_cursos.php">
                    <table border="0" width="150px" style="font-size:100%">
                        <tr>
                            <td align="right">
                               Prontu&aacute;rio:
                            </td>
                            <td align="center">
                                <input type="text" id="prontuario" name="prontuario" maxlength="20" style="width: 70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                Senha:
                            </td>
                            <td align="center">
                                <input type="password" id="senha" name="senha" maxlength="20" style="width: 70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                                <p>
                                    <input type="image" src="../../../public/images/bt_entrarAluno.jpg" style="width:60px; 40px;" />
                                </p>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="btnOK" value="true" />
                    <input type="hidden" id="sa_login" name="sa_login" value="aluno_login" />
             	</form>
			<table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td valign="top">
                        Utilize a sua senha pessoal de acesso, é a mesma utilizada para logar nos computadores dos laborat&oacute;rios.<br />
                        <b>Qualquer diverg&ecirc;ncia em notas e faltas informe-se com o seu professor.</b><br />

                    </td>
                </tr>
            	</table>
            	<br />
            <table border="0">
                <tr>
                    <td>
                        <img src="../../../public/images/logo.jpg" alt="<?=$IEnome?>" style="margin: 5px;" />
                    </td>
                    <td>
                        <strong>Instituto Federal  S&atilde;o Paulo</strong><br />
                        Campus Caraguatatuba<br />
                    </td>
                </tr>
            </table>
            <p>
                <font color="#999999">&copy;2012  IFSP Campus Caraguatatuba</font>
            </p>
        </div>
		<style type="text/css">
		#buttonvoltar, #buttonprincipal, #buttonsair {
			visibility:hidden;
		}
		</style>
    </body>
</html>

