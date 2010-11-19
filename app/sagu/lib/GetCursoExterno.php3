<?
  /**
   *
   */
  function GetCursoExterno($id,$SaguAssert)
  {
    $sql = "select nome from cursos_externos where id = '$id'";

    $conn = new Connection;

    $conn->Open();

    $query = @$conn->CreateQuery($sql);

    if ( @$query->MoveNext() )
      $obj = $query->GetValue(1);

    $query->Close();

    $conn->Close();

    if ( $SaguAssert )
      SaguAssert(!empty($obj),"Curso Externo [<b><i>$id</b></i>] não cadastrada ou código Inválido!");

    return $obj;
  }

?>
