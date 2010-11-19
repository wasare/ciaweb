<?php

if($_POST) {

    if(!empty($_POST['email'])) {

        //Recebe o email do formulario
        $email = $_POST['email'];

        require_once '../../config/configuracao.php';
        require_once $BASE_DIR .'core/data/connection_factory.php';

        $conn = new connection_factory($param_conn);

        //Seleciona o usuario atraves do email
        $sql_usuario = "
            SELECT
                acesso_aluno.ref_pessoa, acesso_aluno.senha, 
                pessoas.nome, pessoas.email, pessoas.email_alt
            FROM
                acesso_aluno, pessoas
            WHERE
                acesso_aluno.ref_pessoa = pessoas.id AND
                pessoas.email = '".$email."';";

        //Cria um resultset com a sql
        $RsUsuario = $conn->Execute($sql_usuario);

        //Pega o login/username do usuario no caso o id do aluno
        $usuario = $RsUsuario->fields[0];

        $nome_completo     = $RsUsuario->fields[2];
        $email_principal   = $RsUsuario->fields[3];
        $email_alternativo = $RsUsuario->fields[4];

        $msg = '';

        //Verifica se existe o usuario com o email
        if($RsUsuario->RecordCount() === 1) {

            //Gera uma nova senha aleatoria
            $nova_senha = rand(10000000,99999999);

            //SQL que atualiza a nova senha
            $sqlUpdateUsuario = "
                UPDATE acesso_aluno
                SET senha=md5('".$nova_senha."')
                WHERE ref_pessoa = $usuario";

            //Executa a atualizacao
            if($conn->Execute($sqlUpdateUsuario)) {

                $message = 'Dados para acessar a Area do aluno - Usuario: '.
                            $usuario.' - Nova senha: '.$nova_senha;

                //Verifica se o email foi enviado
                if( mail($email, 'SA - Envio de senha', $message, 'From: SA') ) {

                    $msg = "<font color=green>
                            Procedimento efetuado com sucesso!</font><br />
                            <font color=black>
			    A nova senha foi enviada para o(s) email(s): <br />
                            <b>$email_principal</b>";
                    
                    if($email_alternativo != '') {
                        if(mail($email_alternativo, 'SA - Envio de senha', $message, 'From: SA'))
                            $msg .= " e para <b>$email_alternativo</b> ";
                    }
                    $msg .= "</font>";
                }else {
                    $msg = 'Erro ao enviar email! Efetue o procedimento novamente.';
                }
            }else {
                $msg = 'Erro ao atualizar nova senha!';
            }
        }else {
            $msg = 'E-mail n&atilde;o cadastrado! Procure a secretaria do campus.';
        }
    }else {
        $msg = 'O campo <i>e-mail</i> deve ser preenchido!';
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <link href="../../public/images/favicon.ico" rel="shortcut icon" />
    </head>
    <body>
        <center>
            <img src="../../public/images/sa_icon.png" alt="logomarca SA" width="80" height="68" style="margin: 10px;" />
            <h2>Esqueceu a sua senha?</h2>

	Digite o email cadastrado no SA - &Aacute;rea do aluno para iniciar o processo de recupera&ccedil;&atilde;o da senha. <br />
	O sistema enviar&aacute; uma nova senha para o seu email cadastrado.
	Caso n&atilde;o tenha email cadastrado ou <br />o email foi alterado procure a secretaria do campus.
            <form method="post" action="esqueci_senha.php">
                <p>
                    <strong>Entre com o seu email cadastrado:</strong>
                    <br />
                    <input type="text" name="email" id="email" size="40" />
                    <br />
                    <input type="submit" value="Enviar" />
                </p>
                <font color="red"><?=$msg?></font>
            </form>
            <p>
                <font color="#999999">&copy;2009 IFMG Campus Bambu&iacute; -</font>
                <a href="index.php">P&aacute;gina de autentica&ccedil;&atilde;o.</a>
            </p>
        </center>
    </body>
</html>
