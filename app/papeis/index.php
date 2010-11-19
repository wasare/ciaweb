<?php

require_once("../../app/setup.php");

$conn = new connection_factory($param_conn);

$sqlPapeis = iconv(
		'utf-8',
		'utf-8',
		'SELECT papel_id, descricao, nome FROM papel ORDER BY nome'
	     );

$RsPapeis = $conn->Execute($sqlPapeis);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>

    <body onLoad="pesquisar();">
        <h2>Permiss&otilde;es de usu&aacute;rio</h2>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="60">
                    <div align="center">
                        <a href="javascript:history.back();" class="bar_menu_texto">
                            <img src="../../public/images/icons/back.png" alt="Voltar" width="20" height="20" />
                            <br />
                            Voltar
                        </a>
                    </div>
                </td>
            </tr>
        </table>

        <table width="80%" border="0">
            <tr  style="font-weight:bold; color: white; background-color: #666666;">
                <td>Nome</td>
                <td>Descri&ccedil;&atilde;o</td>
            </tr>
            <?php 
		
            while(!$RsPapeis->EOF) {

            ?>
            <tr bgcolor="#F3F3F3">
                <td align="left"><?=$RsPapeis->fields[2]?></td>
                <td align="left"><?=$RsPapeis->fields[1]?></td>
            </tr>
            <?php
                $RsPapeis->MoveNext();
            }
            ?>
        </table>
    </body>
</html>
