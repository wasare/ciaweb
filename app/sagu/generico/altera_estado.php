<?php

require("../common.php");
require("../lib/GetPais.php"); 


$id = $_GET['id'];

?>

<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="JavaScript">
function buscaPais()
{
  var wnd = window.open("post/lista_paises.php",'buscaPais','toolbar=no,width=550,height=350,scrollbars=yes');
}

function setResult(id,nome)
{
   document.myform.ref_pais.value = id;
   document.myform.pais.value = nome;
}

</script>
<?php 


CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();

$sql = "select " .
       "    id," .
       "    nome," .
       "    ref_pais" .
       "  from estado where id = '$id'";

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro n&atilde;o encontrado!");

list ( $id,
       $nome,
       $ref_pais) = $query->GetRowValues();

$query->Close();
$conn->Close();

$pais = GetPais($ref_pais, true);

?>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<form method="post" action="post/altera_estado.php" name="myform">
  <table width="90%" align="center">
    <tr bgcolor="#000099" align "center">
      <td height="35" colspan="2" align="center">
        <font size="3" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Altera&ccedil;&atilde;o de Estados</font></b></font>
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo&nbsp;</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#0000FF"><? echo($id); ?>
        <input type="hidden" name="id" value="<? echo($id); ?>">
        </font></td>
    </tr> 
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td> 
        <input name="nome" type=text size="50" value="<? echo($nome); ?>">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Pa&iacute;s&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td> 
        <input name="ref_pais" type=text size="5" value="<? echo($ref_pais); ?>">
        <input type="text" name="pais" size="40" value="<?echo($pais);?>">
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><a href="javascript:buscaPais()"><img src="../images/find.gif" width="16" height="16" border="0"></a></b></font> 
      </td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <hr size="1">
      </td>
    </tr>
    <tr> 
      <td align="center" colspan="2"> 
        <input type="submit" name="Submit"  value=" Salvar ">
        <input type="button" name="Submit2" value=" Voltar " onclick="javascript:history.go(-1)">
      </td>
    </tr>
  </table>
</form>
</body>
</html>
