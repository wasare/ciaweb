<?
  /**
   *
   */
  function GetGrupoDiscipl($id,$SaguAssert)
  {
    $sql = "select descricao from grupos_disciplinas where id = '$id'";

    $conn = new Connection;

    $conn->Open();

    $query = @$conn->CreateQuery($sql);

    if ( @$query->MoveNext() )
      $obj = $query->GetRowValues(1);

    $query->Close();

    $conn->Close();

    if ( $SaguAssert )
      SaguAssert(!empty($obj),"Grupo de Disciplina [<b><i>$id</b></i>] não cadastrado ou código inválido!");

    return $obj;
  }

?>