<?php

require("../common.php"); 

$id = $_GET['id'];

CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();

$sql = "select " .
       "    id," .
       "    area" .
       "  from areas_ensino where id = '$id'";

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro não encontrado!");

list ( $id,
       $area) = $query->GetRowValues();

$query->Close();

$conn->Close();
?>
<html>
<head>
<script language="JavaScript">
  function _init()
  {
    document.myform.area.focus();
  }
</script>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20" onload="_init()">
<form method="post" action="post/areas_ensino_altera.php" name="myform">
  <table width="90%" align="center">
    <tr bgcolor="#000099"> 
      <td height="35" colspan="2"> 
        <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Altera&ccedil;&atilde;o de &Aacute;reas de Ensino</font></b></font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><b></b></font></div>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF" width="149"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo</font></td>
      <td width="347"> <font face="Verdana, Arial, Helvetica, sans-serif" color="#FF0033"> 
        <input type="hidden" name="id" value="<? echo($id); ?>">
        <?php echo($id); ?> </font></td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF" width="149"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Descri&ccedil;&atilde;o&nbsp;</font></td>
      <td width="347"> 
        <input name="area" type=text value="<?echo($area);?>" size="35">
      </td>
    </tr>
    <tr> 
      <td colspan="2">&nbsp;<hr></td>
    </tr>
    <tr> 
      <td colspan="2" align="center"> 
        <input type="submit" name="Submit" value=" Salvar ">
        <input type="button" name="Button" value=" Voltar " onClick="location='areas_ensino.php'">
      </td>
    </tr>
  </table>
</form>
</body>
</html>
