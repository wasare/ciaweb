<?php

require("../common.php");
require("../lib/GetPais.php"); 
require("../lib/GetEstado.php"); 

$conn = new connection_factory($param_conn);

// Verifica as permissoes de acesso do usuario quanto ao arquivo
$ACL_FILE = __FILE__;
require_once($BASE_DIR .'core/login/acesso.php');
// ^ Verifica as permissoes de acesso do usuario quanto ao arquivo ^ //


$id = $_GET['id'];

?>
<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="JavaScript">
var tipo_busca;
function buscaPais()
{
  tipo_busca = 1;
  var wnd = window.open("post/lista_paises.php",'buscaPais','toolbar=no,width=550,height=350,scrollbars=yes');
}

function buscaEstado()
{
  tipo_busca = 2;
  
  var url = "post/lista_estados.php?ref_pais=" + escape(document.myform.ref_pais.value);
  
  var wnd = window.open(url,'buscaEstado','toolbar=no,width=550,height=350,scrollbars=yes');
}

function setResult(id,nome)
{
   if (tipo_busca == 1)
   {
      document.myform.ref_pais.value = id;
      document.myform.pais.value = nome;
   }
   else
   {
      document.myform.ref_estado.value = id;
      document.myform.estado.value = nome;
   }
}

</script>
<?php 

CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();

$sql = "select " .
       "    nome," .
       "    cep," .
       "    ref_estado," .
       "    ref_pais, praca" .
       "  from cidade where id = '$id'";

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro n&atilde;o encontrado!");

list ( $nome,
       $cep,
       $ref_estado,
       $ref_pais, $praca) = $query->GetRowValues();

$query->Close();
$conn->Close();

$pais = GetPais($ref_pais, true);
$estado = GetEstado($ref_estado, true);

?>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<form method="post" action="post/altera_cidade.php" name="myform">
  <table border="0" width="90%" align="center" cellspacing="2" height="40" align="center">
    <tr bgcolor="#000099" align "center">
      <td height="35" colspan="2" align="center">
        <font size="3" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Altera&ccedil;&atilde;o de Cidades</font></b></font>
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
<!--    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Pra&ccedil;a (cod Sicredi)</font></td>
      <td> 
        <input name="praca" type=text size="8" maxlength="8" value="<? echo($praca); ?>">
      </td>
    </tr>-->
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cep&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td> 
        <input name="cep" type=text size="8" maxlength="8" value="<? echo($cep); ?>">
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Pa&iacute;s&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td> 
        <input name="ref_pais" type=text size="10" value="<? echo($ref_pais); ?>">
        <input type="text" name="pais" size="40" value="<? echo($pais); ?>">
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><a href="javascript:buscaPais()"><img src="../images/find.gif" width="16" height="16" border="0"></a></b></font>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Estado&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td> 
        <input name="ref_estado" type=text size="10" value="<? echo($ref_estado); ?>">
        <input type="text" name="estado" size="40" value="<? echo($estado); ?>">
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><a href="javascript:buscaEstado()"><img src="../images/find.gif" width="16" height="16" border="0"></a></b></font>
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
