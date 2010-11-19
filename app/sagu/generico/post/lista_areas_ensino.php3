<?php 

	require_once("../../common.php"); 
	$id = $_POST['id'];
    $area = $_POST['area']
?>
<html>
<head>
<title><?=$title?></title>
<script language="JavaScript">
function _select(area_id,area)
{
  if ( window.callSetResult )
    window.callSetResult(area_id,area);
  else
    window.opener.setResult(area_id,area);
  window.close();
}
</script>
</head>
<body bgcolor="#FFFFFF">
<font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000">
</font></b></font></font> <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000"> 
</font></b></font></font> <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000"> 
</font></b></font></font> <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000"> 
</font></b></font></font> <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000"> 
</font></b></font></font> 
<form method="post" action="lista_areas_ensino.php3">
  <table width="500" border="0" cellspacing="2" cellpadding="0" align="center">
    <tr bgcolor="#0066CC"> 
      <td colspan="4" height="28" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><b>Consulta &Aacute;reas de Ensino</b></font></td>
    </tr>
    <tr bgcolor="#CCCCCC"> 
      <td>&nbsp;</td>
      <td><font size="2" face="Arial, Helvetica, sans-serif">C&oacute;digo:</font></td>
      <td><font size="2" face="Arial, Helvetica, sans-serif">Descricao:</font></td>
      <td width="50">&nbsp;</td>
    </tr>
    <tr> 
      <td>&nbsp;</td>
      <td> 
        <input type="text" name="id" size="8" value="<?echo($id);?>">
      </td>
      <td> 
        <input type="text" name="area" value="<?echo($area);?>" size="40">
      </td>
      <td width="50"> 
        <input type="submit" name="Submit" value="Localizar">
      </td>
    </tr>
    <tr> 
      <td colspan="4"> 
        <hr size="1" width="500">
      </td>
    </tr>
    <tr bgcolor="#CCCCCC"> 
      <td width="20" align="left">&nbsp;</td>
      <td width="50"> <font face="Arial, Helvetica, sans-serif" size="2" color="#000000"> 
        C&oacute;digo </font></td>
      <td colspan="2"> <font face="Arial, Helvetica, sans-serif" size="2" color="#000000"> &Aacute;rea de Ensino</font></td>
    </tr>
    
<script language="PHP">
if ( $id != '' || $area != '' )
{
  $conn = new Connection;
  $conn->Open();

  // note the parantheses in the where clause !!!
  $sql = "select id, area from areas_ensino";
  $where = '';

  if ( $id != '' )
    $where .= " id = $id";

  if ( $area != '' )
    if ( $where != '' )
		$where .= " AND lower(to_ascii(area)) SIMILAR TO lower(to_ascii('%". $area."%'))";
    else
		$where .= " lower(to_ascii(area)) SIMILAR TO lower(to_ascii('%". $area."%'))";
  
	$sql .= " where" . $where . " order by id;";

  $query = $conn->CreateQuery($sql);

  for ( $i=0; $i< $limite_list && $query->MoveNext(); $i++ )
  {
    list ( $id, $area ) = $query->GetRowValues();
    $href = "<a href=\"javascript:_select($id, '$area')\"><img src=\"../../images/select.gif\" title='Selecionar' border=0></a>";

    if ( $i % 2 == 0)
    {
</script>
    <tr bgcolor="#EEEEFF" valign="top"> 
      <td width="20"><font face="Arial, Helvetica, sans-serif" size="2"> 
<script language="PHP">
echo($href);
</script>
        </font></td>
      <td width="50"> <font face="Arial, Helvetica, sans-serif" size="2"> 
<script language="PHP">
echo($id);
</script>
        </font></td>
      <td colspan="2"> <font face="Arial, Helvetica, sans-serif" size="2"> 
<script language="PHP">
echo($area);
</script></font></td>
    </tr>
<script language="PHP">
    } // if 

    else 
    {
</script>
    <tr bgcolor="#FFFFEE" valign="top"> 
      <td width="20"><font face="Arial, Helvetica, sans-serif" size="2"> 
<script language="PHP">
echo($href);
</script>
        </font></td>
      <td> <font face="Arial, Helvetica, sans-serif" size="2"> 
<script language="PHP">
echo($id);
</script>
        </font></td>
      <td colspan="2"> <font face="Arial, Helvetica, sans-serif" size="2"> 
<script language="PHP">
echo($area);
</script>
        </font></td>
    </tr>
<script language="PHP">
    } // else
  } // for

  $hasmore = $query->MoveNext();

  $query->Close();
  $conn->Close();
} // if
</script>
    <tr> 
      <td colspan="4" align="center"> 
        <script language="PHP">
        if ( $hasmore )
              echo("<br>Resultado maior do que $limite_list linhas<br>");
        </script>
        <hr size="1" width="500">
        <input type="button" name="Button" value="Cancelar" onClick="javascript:window.close()">
      </td>
    </tr>
  </table>
</form>
</body>
</html>
