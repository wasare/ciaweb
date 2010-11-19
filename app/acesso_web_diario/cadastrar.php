<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SA</title>
<link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
<script src="../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script language="JavaScript">
<!--
function consulta_pessoa() {

	window.open("../consultas_rapidas/pessoas/index.php",'consulta_pessoa','resizable=yes, toolbar=no,width=400,height=500,scrollbars=yes,top=0,left=0');
}
-->
</script>
</head>
<body>
<form id="form1" name="form1" method="post" action="cadastrar_action.php">
  <h2>Cadastrar novo acesso ao Web Di&aacute;rio</h2>
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
      <td width="81%"><span id="sprytextfield2">
        <label>
        <input type="text" name="login" id="login" />
        </label>
      <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span></span></td>
    </tr>
    <tr>
      <td>Senha:</td>
      <td><span id="sprytextfield3">
        <label>
        <input type="password" name="senha" id="senha" />
        </label>
      <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span></span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>N&iacute;vel:</td>
      <td><label>
        <select name="nivel" id="nivel">
          <option value="1">Professor</option>
          <option value="2">Secretaria</option>
        </select>
        </label></td>
    </tr>
    <tr>
      <td>C&oacute;digo do usu&aacute;rio:</td>
      <td><span id="sprytextfield1">
        <label>
        <input name="codigo_pessoa" type="text" id="codigo_pessoa" size="20" />
        </label>
        <a href="javascript:consulta_pessoa()"><img src="../../public/images/icons/lupa.png" alt="Pesquisar usuário" width="20" height="20" /></a> <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span><span class="textfieldInvalidFormatMsg">Formato inv&aacute;lido.</span></span></td>
    </tr>
    <tr>
      <td>Nome Completo:</td>
      <td><label><span id="sprytextfield4">
        <input name="nome_completo" type="text" id="nome_completo" size="50" />
      <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span></span></label></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Ativar/Desativar:</td>
      <td><label>
        <input name="ativar" type="checkbox" id="ativar" checked="checked"/>
      </label></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <p>&nbsp;</p>
</form>
<p>&nbsp;</p>
<script type="text/javascript">
<!--
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
//-->
</script>
</body>
</html>
