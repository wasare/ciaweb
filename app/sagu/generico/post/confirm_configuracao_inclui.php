<?php 

require("../../common.php");


$razao_social = $_POST['razao_social'];
$sigla        = $_POST['sigla'];
$rua          = $_POST['rua'];
$complemento  = $_POST['complemento'];
$bairro       = $_POST['bairro'];
$cep          = $_POST['cep'];
$ref_cidade   = $_POST['ref_cidade'];


CheckFormParameters(array("razao_social",
                          "sigla",
                          "rua",
                          "bairro",
                          "cep",
                          "ref_cidade"));

?>
<html>
<head>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<form method="post" action="configuracao_empresa.php" name="myform">
  <table width="90%" align="center">
    <tr> 
      <td bgcolor="#000099" colspan="2" height="28" align="center"> <font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>&nbsp;Confirma&ccedil;&atilde;o 
        de Opera&ccedil;&atilde;o</b></font></td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Raz&atilde;o 
        Social&nbsp;</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"> 
        <?echo($razao_social);?> </font> 
        <input name="razao_social" type=hidden value="<?echo($razao_social);?>">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Sigla&nbsp;</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"> 
        <?echo($sigla);?> </font> 
        <input name="sigla" type=hidden value="<?echo($sigla);?>">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Rua&nbsp;</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"> 
        <?echo($rua);?> </font> 
        <input name="rua" type=hidden value="<?echo($rua);?>">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Complemento&nbsp;</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"> 
        <?echo($complemento);?> </font> 
        <input name="complemento" type=hidden value="<?echo($complemento);?>">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Bairro&nbsp;</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"> 
        <?echo($bairro);?> </font> 
        <input name="bairro" type=hidden value="<?echo($bairro);?>">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cidade&nbsp;</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"> 
        <?echo($ref_cidade);?> </font> 
        <input name="ref_cidade" type=hidden value="<?echo($ref_cidade);?>">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cep&nbsp;</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"> 
        <?echo($cep);?> </font> 
        <input name="cep" type=hidden value="<?echo($cep);?>">
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
        <input type="button" name="Submit2" value=" Alterar " onClick="history.go(-1)">
      </td>
    </tr>
  </table>
</form>
</body>
</html>
