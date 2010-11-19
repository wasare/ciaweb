<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once("../../app/setup.php");

/*
 * Estancia a classe de conexao e abre
 */
$conn = new connection_factory($param_conn);

/*
 * Executa uma instrucao SQL no banco de dados
 */
$conn->Execute('DELETE FROM motivo WHERE id='.$_GET['id']);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Excluir motivo</h2>
        <div class="panel">
            <font color="green">Motivo excluido com sucesso!</font>
            <p>
                <a href="index.php">Voltar para o controle de motivos</a>
            </p>
        </div>
    </body>
</html>
