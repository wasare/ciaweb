<?php

if($_POST) {

    if(!empty($_POST['user'])) {

        require_once('../config/configuracao.php');
        require_once($BASE_DIR .'core/data/connection_factory.php');

        $conn = new connection_factory($param_conn);

        $sqlUsuario = "
		SELECT u.id,u.nome,p.email
		FROM usuario u, pessoas p
		WHERE
           	u.ref_pessoa = p.id AND
           	(
               	u.nome = '".$_POST['user']."' OR
               	p.id in (
               		SELECT id FROM pessoas WHERE email = '".$_POST['user']."'
                )
            ); ";

        $RsUsuario    = $conn->Execute($sqlUsuario);
        $nome_usuario = $RsUsuario->fields[1];

        if($RsUsuario->RecordCount() === 1) {
            $nova_senha = rand(10000000,99999999);
            $sqlUpdateUsuario = "
			UPDATE usuario
            SET senha = '".hash('sha256',$nova_senha)."'
			WHERE nome = '$nome_usuario'; ";

            if($conn->Execute($sqlUpdateUsuario)) {
                $message = 'Dados para acessar o SA - Usuário: '.$nome_usuario
                        .' - Nova senha: '.
                        $nova_senha;

                if(mail($RsUsuario->fields[2], 'SA - Envio de senha', $message, 'From: SA')) {
                    $msg = '<font color=green>Procedimento efetuado com sucesso!
			    Acesse a sua conta de email para ter acesso a nova senha.</font>';
                }else {
                    $msg = 'Erro ao enviar email!';
                }
            }else {
                $msg = 'Erro ao atualizar nova senha!';
            }
        }else {
            $msg = 'Usu&aacute;rio n&atilde;o cadastrado!
					Procure a secretaria do campus.';
        }
    }else {
        $msg = 'O campo <i>Nome do usu&aacute;rio</i> ou
				<i>Email cadastrado no SA</i> devem ser preenchidos!';
    }
}

?>
<html>
    <head>
        <title>SA</title>
        <link href="styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <center>
            <img src="images/sa_icon.png" alt="logomarca SA" width="80" height="68" style="margin: 10px;" />
            <h2>Esqueceu a sua senha?</h2>
	Digite o seu nome de usu&aacute;rio ou email cadastrado no SA para iniciar o processo de recupera&ccedil;&atilde;o da senha. <br />
	O sistema enviar&aacute; uma nova senha para o seu email cadastrado. 
	Caso n&atilde;o tenha email cadastrado ou <br />o email foi alterado procure a secretaria do campus.
            <form method="post" action="esqueci_senha.php">
                <p>
                    <strong>Entre com usu&aacute;rio ou email cadastrado no SA:</strong>
                    <br />
                    <input type="text" name="user" id="user" size="40" />
                    <br />
                    <input type="submit" value="Enviar" />
                </p>
                <font color="red"><?=$msg?></font>
            </form>
            <p>
                <font color="#999999">&copy;2011 IFSP Campus Caraguatatuba -</font>
                <a href="../index.php">P&aacute;gina inicial do SA.</a>
            </p>
        </center>
    </body>
</html>
