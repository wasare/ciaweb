<?php

require("../../common.php");

$ref_curso = $_GET['ref_curso'];

CheckFormParameters(array('ref_curso'));

?>
<html>
<head>
<title>Disciplinas</title>
<script language="JavaScript">
function _init()
{
  document.myform.id.focus();
}

function _select(id,nome,num_creditos)
{
  if ( window.callSetResult )
    window.callSetResult(id,nome,num_creditos);
  else
    window.opener.setResult(id,nome,num_creditos);

  window.close();
}
</script>
</head>
<body bgcolor="#FFFFFF" onload="_init()">
<table border="0" cellspacing="2" cellpadding="0" align="center">
  <tr bgcolor="#0066CC"> 
    <td colspan="4" height="28" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><b>Disciplinas por Curso</b></font></td>
  </tr>
  <tr bgcolor="#CCCCCC"> 
    <td width="5%"><font face="Arial, Helvetica, sans-serif" size="2">&nbsp;</font>
    </td>
    <td width="15%"> <font face="Arial, Helvetica, sans-serif" size="2"> C&oacute;digo</font>
    </td>
    <td width="70%"> <font face="Arial, Helvetica, sans-serif" size="2"> Descri&ccedil;&atilde;o</font>
    <td width="10%"> <font face="Arial, Helvetica, sans-serif" size="2"> Créditos</font>
    </td>
  </tr>
  <script language="PHP">
  $conn = new Connection;
  
  $conn->Open();

  $sql = " select B.id as c, " .
      	 "        B.descricao_disciplina as d, " .
	     "	      B.num_creditos as e" .
         " from cursos_disciplinas A, disciplinas B" .
         " where A.ref_curso = '$ref_curso' and " .
         "       B.id = A.ref_disciplina" .
         " order by c";

  $query = $conn->CreateQuery($sql);

  for ( $i=0; $query->MoveNext(); $i++ )
  {
    list ( $id, $nome, $num_creditos) = $query->GetRowValues();

    $href = "<a href=\"javascript:_select($id,'$nome','$num_creditos')\"><img src=\"../../images/select.gif\" alt='Selecionar' border=0></a>";

    if ( $i % 2 == 0)
    {
  </script>
  <tr bgcolor="#EEEEFF"> 
    <td width="5%"><font face="Arial, Helvetica, sans-serif" size="2"> 
      <? echo($href);?>
      </font></td>
    <td width="15%"> <font face="Arial, Helvetica, sans-serif" size="2"> 
      <?echo($id);?>
      </font></td>
    <td width="70%"> <font face="Arial, Helvetica, sans-serif" size="2"> 
      <? echo($nome);?>
      </font></td>
      <td width="10%"> <font face="Arial, Helvetica, sans-serif" size="2"> 
      <?echo($num_creditos);?>
      </font></td>
  </tr>
  <?
    } // if 

    else 
    {
  ?>
  <tr bgcolor="#FFFFEE"> 
    <td width="5%"><font face="Arial, Helvetica, sans-serif" size="2"> 
      <?echo($href);?>
      </font></td>
    <td width="15%"> <font face="Arial, Helvetica, sans-serif" size="2"> 
      <?echo($id);?>
      </font></td>
    <td width="70%"> <font face="Arial, Helvetica, sans-serif" size="2"> 
      <?echo($nome);?>
      </font></td>
    <td width="10%"> <font face="Arial, Helvetica, sans-serif" size="2"> 
      <?echo($num_creditos);?>
      </font></td>
  </tr>
  <?
    } // else
  } // for

  $hasmore = $query->MoveNext();

  $query->Close();
  $conn->Close();
  ?>
  <tr> 
    <td colspan=4 align="center"> 
      <hr size="1" width="500">
      <?
      if ( $hasmore )
    	echo("<center><br>Se a Disciplina não estiver listada, seja mais específico.<br></center>");
      ?>
      <input type="button" name="Button" value=" Voltar " onClick="javascript:window.close()">
    </td>
  </tr>
</table>
</body>
</html>
