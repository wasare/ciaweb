<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?
  /**
   *
   */
  function GetEscola($id,$SaguAssert)
  {
    $sql = "select nome from instituicoes where id = '$id'";

    $conn = new Connection;

    $conn->Open();

    $query = @$conn->CreateQuery($sql);

    if ( @$query->MoveNext() )
      $obj = $query->GetValue(1);

    $query->Close();

    $conn->Close();

    if ( $SaguAssert )
      SaguAssert(!empty($obj),"Escola [<b><i>$id</b></i>] não cadastrada ou código Inválido!");

    return $obj;
  }

?>
<HTML><HEAD>
<META name="GENERATOR" content="IBM WebSphere Homepage Builder V4.0.0 for Linux"></HEAD>
<BODY></BODY>
</HTML>
