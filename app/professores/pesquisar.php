<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once("../../app/setup.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Pesquisar professor</h2>
        <form id="form1" name="form1" method="post" action="pesquisar_action.php" >
            <div class="btn_action">
                <a href="javascript:history.back();" class="bar_menu_texto">
                    <img src="../../public/images/icons/back.png" alt="Voltar" width="20" height="20" />
                    <br />Voltar
                </a>
            </div>
            <div class="panel">
                Nome ou parte do nome do professor:<br />
                <input type="text" id="nome" name="nome" size="40" />
                <input type="submit" value="Pesquisar" />
            </div>
        </form>
    </body>
</html>
