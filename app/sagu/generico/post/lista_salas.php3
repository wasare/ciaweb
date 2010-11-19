<?php

require("../../common.php");


$ref_campus = $_GET['ref_campus'];

CheckFormParameters(array('ref_campus'));

?>
<html>
<head>
  <title>Disciplinas</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script language="JavaScript">
     function _init()
     {
       document.myform.id.focus();
     }

     function _select(id,numero,capacidade)
     {
       if ( window.callSetResult )
         window.callSetResult(id,numero,capacidade);
       else
         window.opener.setResult(id,numero,capacidade);

       window.close();
     }
  </script>
</head>
<body bgcolor="#FFFFFF" onload="_init()">

<table border="0" cellspacing="2" cellpadding="0" align="center" width="500">
  <tr bgcolor="#0066CC"> 
    <td colspan="4" height="28" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><b>Salas 
      Dispon&iacute;veis</b></font></td>
  </tr>
  <tr bgcolor="#CCCCCC"> 
    <td width="20"><font face="Arial, Helvetica, sans-serif" size="2"> &nbsp; 
      </font></td>
    <td width="50"><font face="Arial, Helvetica, sans-serif" size="2"> C&oacute;digo 
      </font></td>
    <td widht="430" width="50"> <font face="Arial, Helvetica, sans-serif" size="2"> 
      Capacidade </font><font face="Arial, Helvetica, sans-serif" size="2"> </font><font face="Arial, Helvetica, sans-serif" size="2"> 
      </font></td>
    <td widht="430" width="380">&nbsp;</td>
  </tr>
  
<?
  $conn = new Connection;
  
  $conn->Open();

  // note the parantheses in the where clause !!!
  $sql = "select numero," .
         "       capacidade" .
         " from  salas" .
         " where ref_campus = '$ref_campus'";
  
  if ( $num_sala )
     $sql .= " and   numero = '$num_sala'";
         
  $sql .= " order by numero";

  $query = $conn->CreateQuery($sql);

  for ( $i=0; $query->MoveNext(); $i++ )
  {
    list ( $numero, $capacidade ) = $query->GetRowValues();

    $href = "<a href=\"javascript:_select('$numero',$capacidade)\"><img src=\"../../images/select.gif\" alt='Selecionar' border=0></a>";

    if ( $i % 2 == 0)
    {
?>
  <tr bgcolor="#EEEEFF"> 
    <td width="20"><font face="Arial, Helvetica, sans-serif" size="2"> 
      <? echo($href); ?>
      </font></td>
    <td width="50"> <font face="Arial, Helvetica, sans-serif" size="2"> 
      <? echo($numero); ?>
      </font></td>
    <td width="50" align="right"> <font face="Arial, Helvetica, sans-serif" size="2"> 
      <? echo($capacidade); ?>
      &nbsp; </font><font face="Arial, Helvetica, sans-serif" size="2"> </font><font face="Arial, Helvetica, sans-serif" size="2"> 
      </font></td>
    <td width="380">&nbsp;</td>
  </tr>
<?
    }
    else 
    {
?>
  <tr bgcolor="#FFFFEE"> 
    <td width="20"><font face="Arial, Helvetica, sans-serif" size="2"> 
      <? echo($href); ?>
      </font></td>
    <td width="50"> <font face="Arial, Helvetica, sans-serif" size="2"> 
      <? echo($numero); ?>
      </font></td>
    <td width="50" align="right"> <font face="Arial, Helvetica, sans-serif" size="2"> 
      <? echo($capacidade); ?>
      &nbsp; </font><font face="Arial, Helvetica, sans-serif" size="2"> </font><font face="Arial, Helvetica, sans-serif" size="2"> 
      </font></td>
    <td width="380">&nbsp;</td>
  </tr>
<?
    } // else
  } // for

  $query->Close();
  $conn->Close();

  if ( $num_sala )
  {
     if ( $num_sala == $numero ) 
     {
     ?>
        <script language="JavaScript">
           window._select('<? echo($numero); ?>','<? echo($capacidade); ?>');
        </script>
     <?
     }
     else
     {
     ?>
        <script language="JavaScript">
           window.close();
        </script>
     <?
     }
  }
  
?>

</table>
</body>
</html>
