<?php require_once("../common.php"); ?>

<html>
    <head>
        <title>Inclusão de Filiação</title>
        <script language="JavaScript">
            function _init()
            {
                document.myform.pai_nome.focus();
            }
        </script>
    </head>
    <body bgcolor="#FFFFFF" onload="_init()">
        <form method="post" action="post/confirm_filiacao_inclui.php" name="myform">
            <table width="90%" align="center">
                <tr bgcolor="#000099">
                    <td colspan="2" height="35">
                        <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Inclus&atilde;o de Filia&ccedil;&atilde;o</font></b></font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><b></b></font></div>
                    </td>
                </tr>

                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome do Pai</font></td>
                    <td>
                        <input name="pai_nome" type=text size="35" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Telefone do Pai</font></td>
                    <td>
                        <input name="pai_fone" type=text size="15" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Profiss&atilde;o do Pai</font></td>
                    <td>
                        <input name="pai_profissao" type=text size="35" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Grau de instru&ccedil;&atilde;o do Pai</font></td>
                    <td>
                        <input name="pai_instrucao" type=text size="35" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Local de trabalho do Pai</font></td>
                    <td>
                        <input name="pai_loc_trabalho" type=text size="35" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome da m&atilde;e</font></td>
                    <td>
                        <input name="mae_nome" type=text size="35" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Telefone da m&atilde;e</font></td>
                    <td>
                        <input name="mae_fone" type=text size="15" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Profiss&atilde;o da m&atilde;e</font></td>
                    <td>
                        <input name="mae_profissao" type=text size="35" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Grau de instru&ccedil;&atilde;o da m&atilde;e</font></td>
                    <td>
                        <input name="mae_instrucao" type=text size="35" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Local de trabalho da m&atilde;e</font></td>
                    <td>
                        <input name="mae_loc_trabalho" type=text size="35" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" height="26">
                        <hr size="1">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div align="center">
                            <input type="submit" name="Submit"   value=" Prosseguir ">
                            <input type="reset"  name="Submit2"  value="   Limpar   ">
                            <input type="button" name="Submit22" value="   Voltar   " onclick="javascript:window.close()">
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
