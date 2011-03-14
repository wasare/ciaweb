<?php

require_once("../../app/setup.php");
require_once(dirname(__FILE__).'/../../core/login/acl.php');

$conn = new connection_factory($param_conn);

// Definindo as permissoes do usuario quanto ao arquivo
$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Você não tem permissão para acessar este formulário!');
}

$id_usuario  = $_GET['id_usuario'];

//Remove as permissoes e o usuario
$conn->Execute("DELETE FROM usuario_papel WHERE ref_usuario=$id_usuario");
$conn->Execute("DELETE FROM usuario WHERE id=$id_usuario");

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Excluir usu&aacute;rio</h2>
        <font color="green">Usu&aacute;rio excluido com sucesso!</font>
        <p>
            <a href="index.php">Voltar para o controle de usu&aacute;rios</a>
        </p>
    </body>
</html>
