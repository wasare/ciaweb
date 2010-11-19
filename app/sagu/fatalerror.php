<?php

	require_once(dirname(__FILE__) .'/common.php');

?>

<html>
    <head>
        <title>Erro fatal</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>
    <body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
        <table border="0" cellspacing="0" cellpadding="8" align="center" width="500">
            <tr bgcolor="#FFCC00" align="center">
                <td colspan="3">
                <b><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#FF0000"><?=$msg?></font></b></td>
            </tr>
            <tr>
                <td align=center rowspan="3" width="104">
                <img src="<?=$PATH_SAGU_IMAGES.'attention.gif'?>" width="50" height="50"></td>
                <td colspan="2" align=center>&nbsp;</td>
            </tr>
            <tr>
                <td valign="top">
                    <div align="center"> <font size="2"><b><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000">Causa:</font></b></font></div>
                </td>
                <td valign="top" width="100%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?=$info?>&nbsp;</font></td>
            </tr>
            <tr>
                <td colspan="2" align=center>&nbsp;</td>
            </tr>
            <tr>
                <td align=center colspan="3">
                    <form method="post" action="">
                        <input type="button" name="Submit" value="Voltar" onclick="javascript:history.go(-1)">
                    </form>
                </td>
            </tr>
        </table>
    </body>
</html>
