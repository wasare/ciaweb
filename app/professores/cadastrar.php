<?php
/*
 * Arquivo com as configuracoes iniciais
*/
require_once("../../app/setup.php");

/*
 * Estancia a classe de conexao e abre
*/
$conn = new connection_factory($param_conn);

$arr_departamentos = $conn->get_all('SELECT id, descricao FROM departamentos');
$arr_campus        = $conn->get_all('SELECT id, nome_campus FROM campus ORDER BY nome_campus;');
$arr_setor         = $conn->get_all('SELECT id, nome_setor FROM setor');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <?=$DOC_TYPE?>
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <script src="../../lib/prototype.js" type="text/javascript"></script>
        <script src="../../lib/Spry/widgets/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>
        <script src="../../lib/Spry/widgets/passwordvalidation/SpryValidationPassword.js" type="text/javascript"></script>
        <script src="../../lib/Spry/widgets/confirmvalidation/SpryValidationConfirm.js" type="text/javascript"></script>
        <script src="../../lib/Spry/widgets/selectvalidation/SpryValidationSelect.js" type="text/javascript"></script>
        <link href="../../lib/Spry/widgets/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
        <link href="../../lib/Spry/widgets/passwordvalidation/SpryValidationPassword.css" rel="stylesheet" type="text/css" />
        <link href="../../lib/Spry/widgets/confirmvalidation/SpryValidationConfirm.css" rel="stylesheet" type="text/css" />
        <link href="../../lib/Spry/widgets/selectvalidation/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript">

            function verifica(id,obj) {
                var parametro = null;
                var objAjax = null;
                var carregando = null;
                parametro = parametro + '&id=' + id;
                carregando = '&nbsp;Verificando...';
                $('span_usuario').innerHTML = carregando;
                objAjax = new Ajax.Request('verifica.php', {method: 'post', parameters: parametro, onComplete: exibeStatusUsuario});
            }

            function exibeStatusUsuario(resposta){
                var s = unescape(resposta.responseText);
                // Mostra o HTML recebido
                $('span_usuario').innerHTML = s;
            }
        </script>
    </head>
    <body>
        <h2>Cadastrar professor</h2>
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
                C&oacute;digo de pessoa f&iacute;sica:<br />
                <span id="sprytextfield1">
                    <input type="text" id="id_pessoa" name="id_pessoa" />
                    <span class="textfieldRequiredMsg">Valor obrigat&oacute;rio</span>
                    <span class="textfieldInvalidFormatMsg">Somente n&uacute;mero inteiro.</span>
                </span>
                <br />
                Departamento:
                <br />
                <span id="validsel1">
                    <select name="departamento" id="departamento" tabindex="1">
                        <option value="">Selecione o departamento</option>
                        <?php foreach($arr_departamentos as $departamento): ?>
                        <option value="<?=$departamento['id']?>"><?=$departamento['descricao']?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="selectRequiredMsg">Selecione um item.</span>
                </span>
                <br />
                Data de ingresso:
                <br />
                <span id="date1">
                    <input type="text" name="data" id="data" />
                    <span class="textfieldRequiredMsg">Valor obrigat&oacute;rio.</span>
                    <span class="textfieldInvalidFormatMsg">Formato inv&aacute;lido.</span>
                </span>
                <br />
                Campus:
                <br />
                <span id="validsel3">
                    <select name="campus" id="campus" tabindex="1">
                        <option value="">Selecione o campus</option>
                        <?php foreach($arr_campus as $campus): ?>
                        <option value="<?=$campus['id']?>"><?=$campus['nome_campus']?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="selectRequiredMsg">Selecione um item.</span>
                </span>
                <h3>Acesso ao Web Di&aacute;rio</h3>
                <p>
                    Usu&aacute;rio:
                    <br />
                    <span id="sprytextfield2">
                        <input type="text" id="user" name="user"
                               onkeyup="javascript:this.value=this.value.toLowerCase();
                                   verifica(this.value,'span_usuario');" />
                        &nbsp;<span id="span_usuario"></span>
                        <input type="hidden" id="flg_user" name="flg_user" value="">
                        <span class="textfieldRequiredMsg">Valor obrigat&oacute;rio</span>
                    </span>
                    <br />
                    Setor:
                    <br />
                    <span id="validsel2">
                        <select name="setor" id="setor" tabindex="1">
                            <option value="">Selecione o setor do usu&aacute;rio</option>
                            <?php foreach($arr_setor as $setor): ?>
                            <option value="<?=$setor['id']?>"><?=$setor['nome_setor']?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="selectRequiredMsg">Selecione um item.</span>
                    </span>
                    <br />
                    <input name="ativar" type="checkbox" id="ativar" checked="checked"/> Ativar usu&aacute;rio.
                </p>
            </div>
        </form>
        <script type="text/javascript">
            <!--
            var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1","integer");
            var validsel1 = new Spry.Widget.ValidationSelect("validsel1", {validateOn:["change"]});
            var validsel2 = new Spry.Widget.ValidationSelect("validsel2", {validateOn:["change"]});
            var validsel3 = new Spry.Widget.ValidationSelect("validsel3", {validateOn:["change"]});
            var date1 = new Spry.Widget.ValidationTextField("date1", "date", {format:"dd/mm/yyyy", hint:"dd/mm/yyyy", validateOn:["blur", "change"], useCharacterMasking:true});
            var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
            //var sprypassword1 = new Spry.Widget.ValidationPassword("sprypassword1");
            //var spryconfirm1 = new Spry.Widget.ValidationConfirm("spryconfirm1", "sprypassword1");
            //-->
        </script>
    </body>
</html>
