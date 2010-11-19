<?php
/*
 * Arquivo com as configuracoes iniciais
*/
require_once("../../app/setup.php");
require_once("../../core/date.php");


$id = $_GET['id'];

/*
 * Estancia a classe de conexao e abre
*/
$conn = new connection_factory($param_conn);

$sql_professores = "
    SELECT
        p.ref_departamento,
        p.dt_ingresso,
        u.nome as login,
        u.ativado,
        u.ref_setor
    FROM professores p, usuario u
    WHERE
        p.ref_professor = $id AND
        p.ref_professor = u.ref_pessoa";

$arr_professores = $conn->get_row($sql_professores);

$nome_pessoa = $conn->get_one("SELECT nome FROM pessoas WHERE id = $id");

$date = new date();
$data = $date->convert_date($arr_professores['dt_ingresso']);
$ativado = $arr_professores['ativado'];

$arr_departamentos = $conn->get_all('SELECT id, descricao FROM departamentos');

$arr_setor = $conn->get_all('SELECT id, nome_setor FROM setor');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <?=$DOC_TYPE?>
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <script src="../../lib/Spry/widgets/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>
        <script src="../../lib/Spry/widgets/passwordvalidation/SpryValidationPassword.js" type="text/javascript"></script>
        <script src="../../lib/Spry/widgets/confirmvalidation/SpryValidationConfirm.js" type="text/javascript"></script>
        <script src="../../lib/Spry/widgets/selectvalidation/SpryValidationSelect.js" type="text/javascript"></script>
        <link href="../../lib/Spry/widgets/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
        <link href="../../lib/Spry/widgets/passwordvalidation/SpryValidationPassword.css" rel="stylesheet" type="text/css" />
        <link href="../../lib/Spry/widgets/confirmvalidation/SpryValidationConfirm.css" rel="stylesheet" type="text/css" />
        <link href="../../lib/Spry/widgets/selectvalidation/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Alterar professor</h2>
        <form id="form1" name="form1" method="post" action="alterar_action.php" >
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
                Pessoa f&iacute;sica: <strong><?=$id?> - <?=$nome_pessoa?></strong>
                <input type="hidden" id="id_pessoa" name="id_pessoa" value="<?=$id?>" />
                <br />
                Departamento:
                <br />
                <span id="validsel1">
                    <select name="departamento" id="departamento" tabindex="1">
                        <option value="">Selecione o departamento</option>

                        <?php foreach($arr_departamentos as $departamento): ?>

                        <?php if($departamento['id'] == $arr_professores['ref_departamento']):?>
                        <option value="<?=$departamento['id']?>" selected="selected"><?=$departamento['descricao']?></option>
                        <?php endif; ?>

                        <option value="<?=$departamento['id']?>"><?=$departamento['descricao']?></option>

                        <?php endforeach; ?>
                    </select>
                    <span class="selectRequiredMsg">Selecione um item.</span>
                </span>
                <br />
                Data de ingresso:
                <br />
                <span id="date1">
                    <input type="text" name="data" id="data" value="<?=$data?>" />
                    <span class="textfieldRequiredMsg">Valor obrigat&oacute;rio.</span>
                    <span class="textfieldInvalidFormatMsg">Formato inv&aacute;lido.</span>
                </span>
                <h3>Acesso ao Web Di&aacute;rio</h3>
                <p>
                    Usu&aacute;rio: <strong><?=$arr_professores['login']?></strong>
                    <input type="hidden" id="user" name="user" value="<?=$arr_professores['login']?>" />
                    <br />
                    Setor:
                    <br />
                    <span id="validsel2">
                        <select name="setor" id="setor" tabindex="1">
                            <option value="">Selecione o setor do usu&aacute;rio</option>
                            <?php foreach($arr_setor as $setor): ?>

                            <?php if($setor['id'] == $arr_professores['ref_setor']):?>
                            <option value="<?=$setor['id']?>" selected="selected"><?=$setor['nome_setor']?></option>
                            <?php endif; ?>
                            
                            <option value="<?=$setor['id']?>"><?=$setor['nome_setor']?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="selectRequiredMsg">Selecione um item.</span>
                    </span>
                    <br />
                    <input name="ativar" type="checkbox" id="ativar" 
                           <?php if($ativado == 't') : ?>
                           checked="checked"
                           <?php endif; ?> /> Ativar usu&aacute;rio.
                    <br />
                    <br />
                    Nova senha:
                    <br />
                    <span id="sprypassword1">
                        <input type="password" name="password" id="password" />
                        <!-- <span class="passwordRequiredMsg">Valor obrigat&oacute;rio.</span>  -->
                    </span>
                    <br />
                    Confirme a nova senha:
                    <br/>
                    <span id="spryconfirm1">
                        <input type="password" name="confirm" id="confirm" />
                        <!-- <span class="confirmRequiredMsg">Valor obrigat&oacute;rio.</span> -->
                        <span class="confirmInvalidMsg">As senhas n&atilde;o conferem.</span>
                    </span>
                    <br />
                    Para manter a senha atual deixe o campo nova senha em branco.
                </p>
            </div>
        </form>
        <script type="text/javascript">
            <!--
            var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1","integer");
            var validsel1 = new Spry.Widget.ValidationSelect("validsel1", {validateOn:["change"]});
            var validsel2 = new Spry.Widget.ValidationSelect("validsel2", {validateOn:["change"]});
            var date1 = new Spry.Widget.ValidationTextField("date1", "date", {format:"dd/mm/yyyy", hint:"dd/mm/yyyy", validateOn:["blur", "change"], useCharacterMasking:true});
            var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
            var sprypassword1 = new Spry.Widget.ValidationPassword("sprypassword1", {isRequired:false});
            var spryconfirm1 = new Spry.Widget.ValidationConfirm("spryconfirm1", "sprypassword1", {isRequired:false});
            //-->
        </script>
    </body>
</html>
