<?
  /**
   *
   */
  function GetCidade($id,$SaguAssert)
  {
    $sql = "select nome from cidade where id = '$id'";

    $conn = new Connection;

    $conn->Open();

    $query = @$conn->CreateQuery($sql);

    if ( @$query->MoveNext() )
      $obj = $query->GetValue(1);

    $query->Close();

    $conn->Close();

    if ( $SaguAssert )
      SaguAssert(!empty($obj),"Cidade [<b><i>$id</b></i>] não cadastrada ou código Inválido!");

    return $obj;
  }

?>
