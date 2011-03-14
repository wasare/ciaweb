<?
// ----------------------------------------------------------
// Verifica se registro já existe
// ----------------------------------------------------------
  function VerificaChaveUnica($tabela, $campo, $valor)
  {
    $conn = new Connection;

    $conn->Open();

    $sql = "select 1 from $tabela where $campo = $valor";

    $query = $conn->CreateQuery($sql);

    $success = !$query->MoveNext();

    $query->Close();

    $conn->Close();

    return $success;
  }
?>