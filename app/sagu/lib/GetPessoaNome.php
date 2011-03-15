<?
  /**
   *
   */
  function GetPessoaNome($id,$SaguAssert=true)
  {
    $sql = "select nome from pessoas where id=$id";

    $conn = new Connection;

    $conn->Open();

    $query = @$conn->CreateQuery($sql);

    if ( @$query->MoveNext() )
      $obj = $query->GetValue(1);

    $query->Close();

    $conn->Close();

    if ( $SaguAssert )
      SaguAssert(!empty($obj),"Pessoa [<b><i>$id</b></i>] não cadastrada ou código inválido!");

    return $obj;
  }
?>