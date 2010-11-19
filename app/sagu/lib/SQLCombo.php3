<?
function SQL_Combo($nome,$sql,$default,$onchange)
{
  SaguAssert(false,"Use SQLArray em combinação com ComboArray!");

  $conn = new Connection;
  
  $conn->Open();

  $query = $conn->CreateQuery($sql);

  if ( $onchange != "" )
    echo("<select name=\"$nome\" onchange=\"$onchange\">");
  else
    echo("<select name=\"$nome\">");
    
  for ( $i=1; $query->MoveNext(); $i++ )
  {
    list ( $text, $value ) = $query->GetRowValues();
      
    if ( $value == $default )
      echo("  <option value=\"$value\" selected>$text</option>\n");
    else
      echo("  <option value=\"$value\">$text</option>\n");
  }

  echo("</select>");

  $query->Close();

  $conn->Close();
}

// ---------------------------------------------------------------------
// 
// ---------------------------------------------------------------------
function SQLArray($sql)
{
  $conn = new Connection;

  $conn->Open();

  $query = $conn->CreateQuery($sql);

  while ( $query->MoveNext() )
    $result[] = $query->GetRowValues();

  $query->Close();

  $conn->Close();

  return $result;
}

// ---------------------------------------------------------------------
// 
// ---------------------------------------------------------------------
function ComboArray($nome,$array,$default,$onchange,$multiple=null)
{
  echo("<select ");

  if ( $multiple )
    echo(" name=\"$nome") . '[]' . ("\" multiple size=\"$multiple\">");
  else
    echo(" name=\"$nome\"");

  if ( $onchange )
    echo(" onchange=\"$onchange\">");
  else
    echo(" >");

  $n = count($array);

  if ( ! $multiple )
    echo("  <option value=\"0\" selected>------- Clique Aqui -------</option>\n");      

  for ( $i=0; $i<$n; $i++ )
  {
    list ( $text, $value ) = $array[$i];
    if ( $value == $default )
      echo("  <option value=\"$value\" selected>$text</option>\n");
    else
      echo("  <option value=\"$value\">$text</option>\n");
  }

  echo("</select>");
}
?>
