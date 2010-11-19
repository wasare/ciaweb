<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once("../../app/setup.php");

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
        <h2>Cadastrar cargo</h2>
        <form id="form1" name="form1" method="post" action="cadastrar_action.php" >
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
		C&oacute;digo do cargo:<br />
                <span id="sprytextfield1">
                    <input type="text" id="codigo" name="codigo" size="12" />
                    <span class="textfieldRequiredMsg">Valor obrigat&oacute;rio</span>
                </span>
                <br />
                Descri&ccedil;&atilde;o do cargo:<br />
                <span id="sprytextfield2">
                    <input type="text" id="descricao" name="descricao" size="40" />
                    <span class="textfieldRequiredMsg">Valor obrigat&oacute;rio</span>
                </span>
                <br />
                Descri&ccedil;&atilde;o breve do cargo:<br />
                <input type="text" id="descricao_breve" name="descricao_breve" size="40" />
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
