<?php

require_once("../common.php");
require_once '../../../core/login/acl.php';

$conn = new connection_factory($param_conn);

$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar este formul&aacute;rio!');
}

?>

<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="JavaScript">
function buscaGrupos()
{
  $controle = 1;
  url = '../generico/post/lista_grupos_disciplinas.php' +
         '?id=' + escape(document.myform.ref_grupo.value) +
         '&desc=' + escape(document.myform.nome_grupo.value);

  window.open(url,"busca","toolbar=no,width=530,height=320,top=80,left=55,directories=no,menubar=no,scrollbars=yes");
}

function buscaDepartamento()
{
  $controle = 2;
  url = '../generico/post/lista_departamentos.php' +
         '?id=' + escape(document.myform.ref_departamento.value) +
         '&desc=' + escape(document.myform.nome_departamento.value);

  window.open(url,"busca","toolbar=no,width=530,height=320,top=80,left=55,directories=no,menubar=no,scrollbars=yes");
}


function setResult(arg1,arg2)
{
if ($controle == '1')
{
  document.myform.ref_grupo.value = arg1;
  document.myform.nome_grupo.value = arg2;
}
if ($controle == '2')
{
  document.myform.ref_departamento.value = arg1;
  document.myform.nome_departamento.value = arg2;
}
}
</script>
</head>
<body bgcolor="#FFFFFF" leftmargin="20" topmargin="20" marginwidth="20" marginheight="20">
<form method="post" action="post/disciplinas.php" name="myform">
  <table width="90%" align="center">
    <tr> 
      <td bgcolor="#000099" colspan="2" height="35"> 
        <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>&nbsp;Inclus&atilde;o de Disciplina</b></font></div>
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo&nbsp;<span class="required">*</span> </font></td>
      <td>
        <input type="text" name="id" size="10" maxlength="10">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Grupo&nbsp;<span class="required">*</span> </font></td>
      <td> 
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="11%"> 
              <input name="ref_grupo" type=text size="5" onChange="ChangeCode('ref_grupo','op1')">
            </td>
            <td width="66%"> 
              <input type="text" name="nome_grupo" size="30" maxlength="30">
            </td>
            <td width="23%"> 
              <div align="right"> 
                <input type="button" name="Submit3" value="..." onClick="buscaGrupos()">
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Departamento&nbsp;<span class="required">*</span> </font></td>
      <td> 
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="11%"> 
              <input name="ref_departamento" type=text size="5" onChange="ChangeCode('ref_departamento','op1')">
            </td>
            <td width="66%"> 
              <input type="text" name="nome_departamento" size="30" maxlength="30">
            </td>
            <td width="23%"> 
              <div align="right"> 
                <input type="button" name="Submit3" value="..." onClick="buscaDepartamento()">
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Abreviatura&nbsp;<span class="required">*</span> </font></td>
      <td>
        <input name="abreviatura" type=text size="20" maxlength="20">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Descri&ccedil;&atilde;o Breve&nbsp;<span class="required">*</span> </font></td>
      <td> 
        <input name="descricao_disciplina" type=text maxlength="100" size="40">
        <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="grey">
          <i>Tamanho m&aacute;ximo de 100 caracteres.</i>
        </font>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Descri&ccedil;&atilde;o Completa</font></td>
      <td> 
        <input name="descricao_extenso" type=text size="40" maxlength="160">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;N&uacute;mero de Cr&eacute;ditos</font></td>
      <td> 
        <input name="num_creditos" type=text size="10">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Carga-Hor&aacute;ria</font></td>
      <td> 
        <input name="carga_horaria" type=text size="10">
      </td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <hr size="1">
      </td>
    </tr>
    <tr> 
      <td colspan="2" align="center"> 
        <input type="submit" name="Submit"  value=" Salvar ">
        <input type="reset"  name="Submit2" value=" Limpar ">
        <input type="button" name="Button2" value=" Voltar  " onClick="location='consulta_disciplinas.php'">
      </td>
    </tr>
  </table>
</form>
</body>
</html>
