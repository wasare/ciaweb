<?
  /**
   *
   */
  function GetPais($id,$SaguAssert)
  {
    $sql = "select nome from pais where id = '$id'";

    $conn = new Connection;

    $conn->Open();

    $query = @$conn->CreateQuery($sql);

    if ( @$query->MoveNext() )
      $obj = $query->GetValue(1);

    $query->Close();

    $conn->Close();

    if ( $SaguAssert )
      SaguAssert(!empty($obj),"Pais [<b><i>$id</b></i>] não cadastrado ou código Inválido!");

    return $obj;
  }

?>
