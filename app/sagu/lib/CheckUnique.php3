<?
// ----------------------------------------------------------
// select 1 from table where condition
// ----------------------------------------------------------
function CheckUnique($sql,$msg='')
  {
    $conn = new Connection;

    $conn->Open();

    $query = $conn->CreateQuery($sql);

    $success = !$query->MoveNext();

    $query->Close();

    $conn->Close();

    if ( $msg != '' )
      SaguAssert($success,$msg);

    return $success;
  }
?>
