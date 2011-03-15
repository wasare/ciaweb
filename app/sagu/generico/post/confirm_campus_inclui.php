<?php

require_once('../../common.php');

$ref_empresa   = $_POST['ref_empresa'];
$nome_campus   = $_POST['nome_campus'];
$cidade_campus = $_POST['cidade_campus'];
$ref_campus_sede = $_POST['ref_campus_sede'];


CheckFormParameters(array("ref_empresa","nome_campus","cidade_campus","ref_campus_sede"));
$empresa_nome = GetEmpresa($ref_empresa,true);
$nome_campus_sede = GetCampus($ref_campus_sede,true);

?>
<html>
<head>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<form method="post" action="campus_inclui.php" name="myform">
  <table width="90%" align="center">
    <tr> 
      <td bgcolor="#000099" colspan="2" height="28" align="center"> <font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b> 
        </b></font><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;</font><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>Confirma&ccedil;&atilde;o 
        de Opera&ccedil;&atilde;o</b></font></td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Empresa&nbsp;</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"> 
        <?echo($ref_empresa);
echo("  " . $empresa_nome);?> </font> 
        <input name="ref_empresa" type=hidden value="<?echo($ref_empresa);?>">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome do Campus</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"> 
        <?echo($nome_campus);?> </font> 
        <input name="nome_campus" type=hidden value="<?echo($nome_campus);?>">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome da Cidade</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"> 
        <?echo($cidade_campus);?> </font> 
        <input name="cidade_campus" type=hidden value="<?echo($cidade_campus);?>">
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Campus Sede&nbsp;</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">
        <?=$ref_campus_sede ."  ". $nome_campus_sede?> </font>
        <input name="ref_campus_sede" type="hidden" value="<?=$ref_campus_sede?>">
      </td>
    </tr> 
    <tr> 
      <td colspan="2"> 
        <hr size="1">
      </td>
    </tr>
    <tr> 
      <td colspan="2" align="center"> 
        <input type="submit" value=" Salvar ">
        <input type="reset"  value=" Voltar " onClick="history.go(-1)">
      </td>
    </tr>
  </table>
</form>
</body>
</html>
