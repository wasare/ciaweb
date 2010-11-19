<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once("../../app/setup.php");

/*
 * Estancia a classe de conexao e abre
 */
$conn = new connection_factory($param_conn);

if($_POST) {
/*
 * Realiza uma consulta no banco de dados retornando um vetor multidimensional
 */
    $sql = 'SELECT id, ref_campus, numero, capacidade FROM salas WHERE numero';
    $arr_setor = $conn->get_all();
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Pesquisar salas</h2>
        <form id="form1" name="form1" method="post" action="pesquisar_action.php" >
            <div class="btn_action">
                <a href="javascript:history.back();" class="bar_menu_texto">
                    <img src="../../public/images/icons/back.png" alt="Voltar" width="20" height="20" />
                    <br />Voltar
                </a>
            </div>
            <div class="panel">
                Nome da sala:<br />
                <input type="text" id="numero" name="numero" size="40" />
                <input type="submit" value="Pesquisar" />
            </div>
        </form>
    </body>
</html>
