<?php require_once("../common.php"); ?>

<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="JavaScript">
function _init()
{
    document.myform.nome.focus();
}
</script>

</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20" onload="_init()">
<form method="post" action="post/confirm_ins_instituicao.php" name="myform">
  <table width="90%" align="center">
    <tr bgcolor="#000099"> 
      <td height="35" colspan="2"> 
        <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Inclus&atilde;o de Institui&ccedil;&otilde;es</font></b></font></div>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome da Institu&ccedil;&atilde;o&nbsp;<span class="required">*</span>&nbsp;</font></td>      
      <td> 
        <input name="nome" type=text size="40" maxlength="100">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Sucinto</font></td>
      <td> 
        <input name="sucinto" type=text size="40" maxlength="50">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome Atual</font></td>
      <td> 
        <input name="nome_atual" type=text size="40" maxlength="100">
      </td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <hr size="1">
      </td>
    </tr>
    <tr> 
      <td colspan="2" align="center"> 
        <input type="submit" name="Submit"  value=" Prosseguir ">
        <input type="reset"  name="Submit2" value="   Limpar   ">
        <input type="button" name="Button2" value="   Voltar   " onClick="location='consulta_inclui_instituicoes.php'">
      </td>
    </tr>
  </table>
</form>
</body>
</html>
