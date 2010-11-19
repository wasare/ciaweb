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
 * Realiza uma consulta no banco de dados retornando um vetor multidimensional
 */
$sql = 'SELECT id, nome_setor, email FROM setor WHERE id = '.$_GET['id'];
$arr_setor = $conn->get_all($sql);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <script src="../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
        <link href="../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Alterar setor</h2>
        <form id="form1" name="form1" method="post" action="alterar_action.php" >
            <input type="hidden" id="id" name="id" value="<?=$arr_setor[0]['id']?>" />
            <div class="btn_action">
                <label class="btn_action">
                    <input name="save" type="image" src="../../public/images/icons/save.png" />
                    <br />Salvar
                </label>
            </div>
            <div class="btn_action">
                <a href="javascript:history.back();" class="bar_menu_texto">
                    <img src="../../public/images/icons/back.png" alt="Voltar" width="20" height="20" />
                    <br />Voltar
                </a>
            </div>
            <div class="panel">
                Nome do setor:<br />
                <span id="sprytextfield1">
                    <input type="text" id="nome" name="nome" size="40"  value="<?=$arr_setor[0]['nome_setor']?>" />
                    <span class="textfieldRequiredMsg">Valor obrigat&oacute;rio</span>
                </span>
                <br />
                E-mail do setor:<br />
                <span id="sprytextfield2">
                    <input type="text" id="email" name="email" size="40" value="<?=$arr_setor[0]['email']?>" />
                    <span class="textfieldRequiredMsg">Valor obrigat&oacute;rio</span>
                </span>
                <br />
            </div>
        </form>
        <script type="text/javascript">
            <!--
            var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
            var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
            //-->
        </script>
    </body>
</html>
