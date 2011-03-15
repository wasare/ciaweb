<?
  /**
   *
   */
  function GetEstado($id,$SaguAssert)
  {
    $sql = "select nome from estado where id = '$id'";

    $conn = new Connection;

    $conn->Open();

    $query = @$conn->CreateQuery($sql);

    if ( @$query->MoveNext() )
      $obj = $query->GetValue(1);

    $query->Close();

    $conn->Close();

    if ( $SaguAssert )
      SaguAssert(!empty($obj),"Estado [<b><i>$id</b></i>] não cadastrado ou código Inválido!");

    return $obj;
  }

?>
