<?php

require_once(dirname(__FILE__) .'/../setup.php');
require_once(dirname(__FILE__).'/../../core/login/acl.php');

$conn = new connection_factory($param_conn);

// Definindo as permissoes do usuario quanto ao arquivo
$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Você não tem permissão para acessar este formulário!');
}

$RsPessoa = $conn->Execute("SELECT nome, email FROM pessoas WHERE id = $sa_ref_pessoa");

$msg = '';

if(isset($_POST['confirm1'])) {

	$senha_ok = FALSE;
    $senha_atual = $_POST['senha_atual'];

    $sqlSenhaAtual = "SELECT COUNT(senha) FROM usuario WHERE id = $sa_usuario_id  AND senha = '".
        hash('sha256',$senha_atual)."';";

    if($conn->get_one($sqlSenhaAtual) != 1) {
        $msg = 'A senha atual n&atilde;o confere!';
    }else {
        $senha = $_POST['senha'];
        $sqlUsuario = "UPDATE usuario SET senha='".hash('sha256',$senha)."' WHERE id = $sa_usuario_id;";

        if($conn->Execute($sqlUsuario)) {

            $msg = '<font color="green">Senha alterada com sucesso!</font>';

			$message = 'Sistema Acad&ecirc;mico - usu&aacute;rio: "'.$sa_usuario;

			$senha_ok = TRUE;

            if(mail($RsPessoa->fields[1], 'SA - Senha alterada', $message, 'From: SA')) {
                $msg .= '<p><font color="green">A nova senha foi enviada para o seu email.</font></p>';
            }else {
                $msg .= '<p>Erro ao enviar email com a nova senha!</p>';
            }
        }else {
            $msg = 'Ocorreu alguma falha ao alterar a senha!';
        }
    }

	if($_POST['operacao'] == 'troca_senha') {

		if($senha_ok === TRUE) {
			// FECHA A JANELA AUTOMATICAMENTE	
			echo '<br />';
			echo '<script language="javascript" type="text/javascript"> 
            setTimeout("self.close()", 8000); </script>';
		}
		else
			$operacao = $_POST['operacao'];
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <script src="../../lib/Spry/widgets/passwordvalidation/SpryValidationPassword.js" type="text/javascript"></script>
        <script src="../../lib/Spry/widgets/confirmvalidation/SpryValidationConfirm.js" type="text/javascript"></script>
        <link href="../../lib/Spry/widgets/passwordvalidation/SpryValidationPassword.css" rel="stylesheet" type="text/css" />
        <link href="../../lib/Spry/widgets/confirmvalidation/SpryValidationConfirm.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Alterar senha do usu&aacute;rio "<?=$sa_usuario?>"</h2>
		<br />
        <form id="form1" name="form1" method="post" action="<?=$BASE_URL .'app/usuarios/alterar_senha.php'?>">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="60">
                        <div align="center">
                            <label class="bar_menu_texto">
                                <input name="save" 
                                       type="image"
                                       src="../../public/images/icons/save.png" />
                                <br />Salvar
                            </label>
                        </div>
                    </td>
                    <td width="60">
                        <div align="center">
							<?php
								if($_POST['operacao'] == 'troca_senha' || $operacao == 'troca_senha'):
									$btn_txt = 'Fechar';
									$btn_img = 'close.png';							
							?>
								<a href="javascript:window.close();"
							<?php 
								else: 
									$btn_txt = 'Voltar';
									$btn_img = 'back.png';
							?>
							     <a href="javascript:history.back();"
							<?php
								endif;
							?>
								class="bar_menu_texto">
                                <img src="../../public/images/icons/<?=$btn_img?>"
                                     alt="<?=$btn_txt?>"
									 title="<?=$btn_txt?>"
                                     width="20"
                                     height="20" />
                                <br /><?=$btn_txt?>
                            </a>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="panel">
                <strong>Seu nome:</strong><br />
                <?=$RsPessoa->fields[0]?><br />
                <p>
                    <span id="sprypassword1">
                        <strong>Senha atual:</strong><br />
                        <input type="password" name="senha_atual" id="senha_atual" />
                        <span class="passwordRequiredMsg">Valor obrigat&oacute;rio</span>
                    </span>
                </p>
                <p>
                    <span id="sprypassword2">
                        <strong>Nova senha:</strong><br />
                        <input type="password" name="senha" id="senha" />
                        <span class="passwordRequiredMsg">Valor obrigat&oacute;rio</span>
                    </span><br />
                    <span id="spryconfirm1">
                        <strong>Confirme a nova senha:</strong><br />
                        <input type="password" name="confirm1" id="confirm1" />
                        <span class="confirmRequiredMsg">Valor obrigat&oacute;rio</span>
                        <span class="confirmInvalidMsg">As senhas n&atilde;o conferem.</span>
                    </span>
                </p>
            </div>
            <p>
                <font color="red"><strong><?php echo $msg;?></strong></font>
            </p>
			    <input type="hidden" name="operacao" id="operacao" value="<?=$operacao?>" />
        </form>
        <script type="text/javascript">
            <!--
            var sprypass1 = new Spry.Widget.ValidationPassword("sprypassword1");
            var sprypass2 = new Spry.Widget.ValidationPassword("sprypassword2");
            var spryconf1 = new Spry.Widget.ValidationConfirm("spryconfirm1", "sprypassword2");
            //-->
        </script>
    </body>
</html>
