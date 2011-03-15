<?php

require("../lib/SQLCombo.php");
require("../common.php");

$ref_disciplina_ofer      = $_GET['ref_disciplina_ofer'];
$id_disciplina_ofer_compl = $_GET['id_disciplina_ofer_compl'];
$ref_professor            = $_GET['ref_professor'];

?>
<html>
<head>
<script language="JavaScript">

function buscaPessoa()
{
  var url = '../generico/post/lista_pessoas.php' +
            '?pnome=' + escape(document.myform.professor.value);

  var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
}
function buscaProfessor()
{
  var url = '../generico/post/lista_professores.php' +
            '?pnome=' + escape(document.myform.professor.value);

  var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
}
function setResult(id,nome)
{
  document.myform.ref_professor.value = id;
  document.myform.professor.value = nome;
}
</script>
</head>
<body bgcolor="#FFFFFF">
<br>
<table width="90%" border="0" cellspacing="0" cellpadding="0" height="40" align="center">
  <tr bgcolor="#000099"> 
    <td height="35"> 
      <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Disciplinas Oferecidas Professores</font></b></font></div>
    </td>
  </tr>
</table>
<br>
<form method="post" action="post/disciplina_ofer_prof.php" name="myform">
  <table cols=2 width="90%" align="center">

    <tr>
      <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Disciplina Oferecida</font></td>
      <td width="70%"> <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2"><?echo($ref_disciplina_ofer)?>
         <input type="hidden" name="ref_disciplina_ofer" value="<?echo($ref_disciplina_ofer)?>">
         <input type="hidden" name="ref_disciplina_compl" value="<?echo($id_disciplina_ofer_compl)?>"></font> 
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Professor</font></td>
      <td width="70%" valign="middle" align="left">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
             <font color="#000000">
                <input name="ref_professor" type=text size="6">
                <input name="professor" type=text size="40">
              </font>
            <td>
	         <input type="button" value="..." onClick="buscaProfessor()" name="button">
	    </td>
        </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <hr size="1">
      </td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <div align="center">
          <input type="submit" name="Submit"  value=" Salvar ">
          <input type="button" name="Submit2" value=" Voltar " onClick="history.go(-1)">
        </div>
      </td>
    </tr>
  </table>
</form>
</body>
</html>
