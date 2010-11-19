<?php require_once("../../common.php"); ?>
<html>
<head>
<title><?=$title?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php 

$hasmore = false;

CheckFormParameters(array('title','sql'));

function ListaResultado($sql)
{
    $hasmore = true;

    $script_args = '';
    
    // cores fundo
    $bg0 = "#000000";
    $bg1 = "#EEEEFF";
    $bg2 = "#FFFFEE";

    // cores fonte
    $fg0 = "#FFFFFF";
    $fg1 = "#000099";
    $fg2 = "#000099";

    $pessoa = strtoupper($pessoa);
  
    $conn = new Connection;
  
    $conn->Open();

    $query = $conn->CreateQuery($sql);

    echo("<table width=\"96%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");

    echo("  <tr bgcolor=\"$bg0\">\n");

    $n = $query->GetColumnCount();

    echo("    <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">&nbsp;</font></b></td>\n");

    for ( $j=1; $j<=$n; $j++ )
    {
      $name = $query->GetColumnName($j);

      echo("    <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg0\">$name</font></b></td>\n");
    }

    echo("  </tr>\n");

    for ( $i=0; $query->MoveNext(); $i++ )
    {
      $cols = $query->GetRowValues();
      
      if ( $i % 2 )
      {
        $bg = $bg1;
        $fg = $fg1;
      }
      
      else
      {
        $bg = $bg2;
        $fg = $fg2;
      }

      echo("  <tr bgcolor=\"$bg\">\n");

      $argv = '';

      for ( $j=0; $j<count($cols); $j++ )
      {
        $argv .= ( $j > 0 ) ? ",'$cols[$j]'" : "'$cols[$j]'";

        if ( $i==0 )
          $script_args .= ( $j > 0 ) ? ",arg$j" : "arg$j";
      }

      $href = "<a href=\"javascript:Select($argv)\"><img src=\"../../images/select.gif\" border=0 alt='Selecionar'></a>";

      echo("    <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$href</font></b></td>\n");

      for ( $j=0; $j<count($cols); $j++ )
        echo("    <td><b><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\" color=\"$fg\">$cols[$j]&nbsp;</font></b></td>\n");

      echo("  </tr>\n");
    }

  $hasmore = $query->MoveNext();
  $query->Close();
  $conn->Close();

	      
  echo("</table>");
  echo("<" . "script language=\"JavaScript\">\n");
  echo("function Select($script_args)\n");
  echo("{\n");
  echo("  window.opener.setResultCallback($script_args);\n");
  echo("  window.close();\n");
  echo("}\n");
  echo("<" . "/script>\n");
}

?>
</head>
<body bgcolor="#FFFFFF">
<font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000">
</font></b></font></font> <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000"> 
</font></b></font></font> <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000"> 
</font></b></font></font> <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000"> 
</font></b></font></font> <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000"> 
</font></b></font></font> 
<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" vspace="0" hspace="0">
  <tr> 
    <td> 
      <div align="center"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><b><font size="4"><b> 
        <font color="#000099"> 
        <?php echo($title);?>
        </font> </b></font></b></font></div>
    </td>
  </tr>
  <tr> 
    <td> 
      <hr size="1" width="98%">
    </td>
  </tr>
  <tr> 
    <td>
      <script language="PHP">
  ListaResultado($sql);
</script>
      <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000">
      <script language="PHP">
if ( $hasmore )
  echo("<br><br>A Pesquisa excedeu " . $limite_list . " registros.");
</script>
      </font></b></font></font> </td>
  </tr>
  <tr>
    <td align=center>
<form method="post" action="">
          <hr size="1" width="98%">
          <input type="button" name="Button" value="Voltar" onClick="javascript:window.close()">
        </form>
    </td>
  </tr>
</table>
<div align="center"> </div>
</body>
</html>
