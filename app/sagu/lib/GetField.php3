<?
  /**
   *
   */
  function GetField($id,$field,$table,$SaguAssert)
  {
    $sql = "select " . $field . " from " . $table . " where id = '$id'";

    $conn = new Connection;
    $conn->Open();
    $query = @$conn->CreateQuery($sql);

    if ( @$query->MoveNext() )
      $obj = $query->GetValue(1);

    $query->Close();
    $conn->Close();

//    if ( $SaguAssert )
//      SaguAssert(!empty($obj),"$field [<b><i>$id</b></i>] não cadastrado ou código Inválido!");

    return $obj;
  }

  function GetField2($id,$field,$table,$conn)
  {
    $sql = "select " . $field . " from " . $table . " where id = '$id'";

    $query_field2 = $conn->CreateQuery($sql);

    if ( $query_field2->MoveNext() )
      $obj = $query_field2->GetValue(1);

    $query_field2->Close();

    return $obj;
  } 

  function GetField3($id,$field,$table,$field_name,$conn)
  {
    $sql = "select " . $field . " from " . $table . " where " . $field_name . " = '$id'";

    $query_field3 = $conn->CreateQuery($sql);

    if ( $query_field3->MoveNext() )
      $obj = $query_field3->GetValue(1);

    $query_field3->Close();

    return $obj;
  } 

?>
