<?php

require_once("../../app/setup.php");

$conn = new connection_factory($param_conn);

$codigo_pessoa = $_GET["id_pessoa"];

$sql = "SELECT login, nivel, ativo, nome_completo FROM public.diario_usuarios WHERE id_nome = '$codigo_pessoa';";

$Result1 = $conn->Execute($sql);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Untitled Document</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <script language="javascript">
            function habilita() {
                document.form1.senha.disabled = (document.form1.senha.disabled) ? 0 : 1;
            }
        </script>

    </head>
    <body>
        <form id="form1" name="form1" method="post" action="alterar_action.php">
            <input type="hidden" id="codigo_pessoa" name="codigo_pessoa" value="<?php echo $codigo_pessoa; ?>" />
            <h2>Alterar acesso ao Web Di&aacute;rio</h2>
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="60"><div align="center">
                            <label class="bar_menu_texto">
                                <input name="save" type="image" src="../../public/images/icons/save.png" />
                                <br />
                                Salvar</label></td>
                    <td width="60"><div align="center"><a href="javascript:history.back();" class="bar_menu_texto"><img src="../../public/images/icons/back.png" alt="Voltar" width="20" height="20" /><br />
                                Voltar</a></div></td>
                </tr>
            </table>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#E6E6E6" class="linha">
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td width="19%">Login:</td>
                    <td width="81%"><?php echo $Result1->fields[0]; ?></td>
                </tr>
                <tr>
                    <td>Senha:</td>
                    <td>
                        <input type="password" name="senha" id="senha" disabled="disabled" />
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <label>
                            <input type="checkbox" name="manter_senha" id="manter_senha" checked="checked" onchange="habilita()" />
			Manter senha atual.
                        </label>
                        <label>
                            <?php

                            if ($Result1->fields[2] == 't') {
                                echo '<input type="checkbox" checked="checked" name="ativar" id="ativar" />';
                            }
                            else {
                                echo '<input type="checkbox" name="ativar" id="ativar" />';
                            }
                            ?>
		Ativar/Desativar usu&aacute;rio.
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>N&iacute;vel:</td>
                    <td><label>
                            <?php

                            if ($Result1->fields[1] == 1) {
                                $option1 = "<option value=\"1\" selected=\"selected\">Professor</option>
                                            <option value=\"2\">Secretaria</option>";
                            }
                            if ($Result1->fields[1] == 2) {
                                $option1 = "<option value=\"1\">Professor</option>
                                            <option value=\"2\" selected=\"selected\">Secretaria</option>";
                            }

                            ?>
                            <select name="nivel" id="nivel">
                                <?php echo $option1;?>
                            </select>
                        </label></td>
                </tr>
                <tr>
                    <td>Nome completo:</td>
                    <td><?php echo $Result1->fields[3];?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </form>
    </body>
</html>
