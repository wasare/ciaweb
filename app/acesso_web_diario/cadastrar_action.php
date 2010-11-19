<?php

require_once("../../app/setup.php");

$conn = new connection_factory($param_conn);

$login = $_POST["login"];
$senha = $_POST["senha"];
$nome_completo = $_POST["nome_completo"];
$nivel = $_POST["nivel"];
$codigo_pessoa = $_POST["codigo_pessoa"];
$ativo = $_POST["ativar"];

if ($ativo == true) {
    $ativo = true;
}
else {
    $ativo = false;
}

$sql = "INSERT INTO  public.diario_usuarios(login,senha,nome_completo,nivel,id_nome,ativo) 
VALUES('$login',md5('$senha'),'$nome_completo','$nivel','$codigo_pessoa','$ativo')";

$sql2 = "SELECT id_nome FROM public.diario_usuarios WHERE id_nome = $codigo_pessoa";

$RsConfPessoa = $conn->Execute($sql2);

$NumPessoa = $RsConfPessoa->RecordCount();


if($NumPessoa > 0) {

    $msg = "<p class=\"msg_erro\">Erro: não foi possível cadastrar.<br>Usuário já cadastrado!</p>";
}
else {

//Exibindo a descricao do curso caso setado
    $Result1 = $conn->Execute($sql);

    if ($Result1) {

        $msg = "<p class=\"msg_sucesso\">Cadastro realizado com sucesso!</p>";
    }
    else {

        $msg = "<p class=\"msg_erro\">Erro ao realizar cadastro!</p>";
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Untitled Document</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <h2>Cadastrar novo acesso ao Web Diário</h2>
        <?php echo $msg; ?>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="60"><div align="center"><a href="javascript:history.back();" class="bar_menu_texto"><img src="../../public/images/icons/back.png" alt="Voltar" width="20" height="20" /><br />
                            Voltar</a></div></td>
            </tr>
        </table>
    </body>
</html>
