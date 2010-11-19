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
$sql = 'SELECT id, descricao, ref_tipo_motivo FROM motivo WHERE id = '.$_GET['id'];
$arr_motivo = $conn->get_all($sql);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <script src="../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
        <link href="../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Alterar motivo</h2>
        <form id="form1" name="form1" method="post" action="alterar_action.php" >
            <input type="hidden" id="id" name="id" value="<?=$arr_motivo[0]['id']?>" />
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
                Nome do motivo:<br />
                <span id="sprytextfield1">
                    <input type="text" id="nome" name="nome" size="40"  value="<?=$arr_motivo[0]['descricao']?>" />
                    <span class="textfieldRequiredMsg">Valor obrigat&oacute;rio</span>
                </span>
                <br />
                Tipo do motivo:<br />
                <span id="sprytextfield2">
                    <input type="text" id="tipo" name="tipo" size="40" value="<?=$arr_motivo[0]['ref_tipo_motivo']?>" />
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
