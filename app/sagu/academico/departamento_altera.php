<?php 

require("../common.php"); 


$id = $_GET['id'];

CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();

$sql = "select " .
       "    id," .
       "    descricao" .
       "  from departamentos where id = '$id'";

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro não encontrado!");

list ( $id,
       $descricao_depto) = $query->GetRowValues();

$query->Close();
$conn->Close();

?>

<html>
<head>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<form method="post" action="post/altera_departamento.php" name="myform">
  <table width="90%" align="center">
    <tr bgcolor="#000099" align "center">
       <td height="35" colspan="2">
         <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>&nbsp;Alteração de Departamento</b></font></div>
       </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo&nbsp;</font></td>
      <td> <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#0000FF"><? echo($id); ?>
      <input type="hidden" name="id" value="<? echo($id); ?>">
       </font></td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Descrição</font></td>
      <td> 
        <input name="descricao_depto" type=text size="50" value="<? echo($descricao_depto); ?>">
      </td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <hr size="1">
      </td>
    </tr>
    <tr> 
      <td colspan="2" align="center"> 
        <input type="submit"  name="Submit"  value=" Salvar ">
        <input type="button"  name="Submit2" value=" Voltar " onClick="history.go(-1)">
      </td>
    </tr>
  </table>
</form>
</body>
</html>