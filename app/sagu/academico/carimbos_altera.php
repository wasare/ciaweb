<?php

require("../common.php");
require("../lib/SQLCombo.php");


$id = $_GET['id'];

$op_opcoes = SQLArray("select nome_setor, id from setor order by nome_setor");


CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();

$sql = " select id," .
       "        nome," .
       "        texto," .
       "        ref_setor" .
       " from carimbos where id = '$id'";

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro n�o encontrado!");

list ( $id,
       $nome,
       $texto,
       $ref_setor) = $query->GetRowValues();

$query->Close();

$conn->Close();

?>
<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="Javascript">
function ChangeOption(opt,fld)
{
  var i = opt.selectedIndex;

  if ( i != -1 )
    fld.value = opt.options[i].value;
  else
    fld.value = '';
}

function ChangeOp()
{
  ChangeOption(document.myform.op,document.myform.ref_setor);
}
</script>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20" onload="_init()">
<table width="500" border="0" cellspacing="0" cellpadding="0" height="40" align="center">
  <tr bgcolor="#000099"> 
    <td height="35"> 
      <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF"> 
        Altera&ccedil;&atilde;o de Carimbo</font></b></font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><b></b></font></div>
    </td>
  </tr>
</table>
<br>
<form method="post" action="post/carimbos_altera.php" name="myform">
  <table cols=2 width="500" align="center">
    <tr> 
      <td bgcolor="#CCCCFF" width="149"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo</font></td>
      <td width="347"> <font face="Verdana, Arial, Helvetica, sans-serif" color="#FF0033"> 
        <input type="hidden" name="id" value="<? echo($id); ?>">
        <?php echo($id); ?> </font></td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF" width="149"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td width="347"> 
        <input name="nome" type=text value="<?echo($nome);?>" size="35">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF" width="149"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Fun&ccedil;&atilde;o&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td width="347"> 
        <input name="texto" type=text value="<?echo($texto);?>" size="35">
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF" width="24%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099">&nbsp;Setor&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td colspan="2"> <font color="#000000">
        <input type="text" name="ref_setor" size="10" value="<?echo($ref_setor);?>">
        <?php 
          ComboArray("op",$op_opcoes,$ref_setor,"ChangeOp()");
        ?>
        </font></td>
    </tr>
    <tr> 
      <td colspan="2">
        <hr>
      </td>
    </tr>
    <tr> 
      <td width="149">&nbsp;</td>
      <td width="347"> 
        <input type="submit" name="Submit" value="Gravar">
        <input type="reset" name="Submit2" value="Limpar">
        <input type="button" name="Button" value="Voltar" onClick="location='carimbos.php'">
      </td>
    </tr>
  </table>
</form>
</body>
</html>
