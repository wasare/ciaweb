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
$sql = "INSERT INTO cargo(id, descricao, descricao_breve)
        VALUES('".$_POST['codigo']."','".$_POST['descricao']."','".$_POST['descricao_breve']."');";

$conn->Execute($sql);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Cadastrar cargo</h2>
        <div class="panel">
            <font color="green">Cargo cadastrado com sucesso!</font>
            <p>
                <a href="index.php">Voltar para o controle de cargos</a>
            </p>
        </div>
    </body>
</html>
