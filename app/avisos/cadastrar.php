<?php

//ARQUIVO DE CONFIGURACAO E CLASSE ADODB
header("Cache-Control: no-cache");
require_once(dirname(__FILE__). '/../setup.php');

// conexão
$conn = new connection_factory($param_conn);

//EXECUTANDO SQL COM ADODB
$aviso = $conn->get_row("SELECT descricao, data FROM avisos WHERE id = 1");

//Iniciando a variavel de mensagem
$msg = null;


if($_POST){

	$texto = $_POST["texto"];

	//Executando a autualizacao
	$Result2 = $conn->Execute("UPDATE avisos SET descricao = '$texto' WHERE id = 1;");

	if ($Result2){

		//Mensagem ok
		$msg = '<script language="javascript">alert("Aviso alterado!");window.close();</script>';
	}
	else {

		//Mensagem erro
		$msg = '<script language="javascript">alert("Erro ao alterar aviso!");window.close();</script>';
	}

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cadastro de avisos</title>

<link rel="stylesheet" href="<?=$BASE_URL?>lib/openwysiwyg/docs/style.css" type="text/css">
<script type="text/javascript" src="<?=$BASE_URL?>lib/openwysiwyg/scripts/wysiwyg.js"></script>
<script type="text/javascript" src="wysiwyg-settings.js"></script>
<script type="text/javascript">
	WYSIWYG.attach('texto', AVISO);
</script>
</head>
<body bgcolor="#E8E8E8">
<center>
<h1>Editar aviso</h1>
<form method="post" action="cadastrar.php" name="myform">
  <textarea id="texto" name="texto" style="width:500px;height:200px;"><?=$aviso['descricao']?></textarea>
  <input type="submit" name="Submit"   value=" Alterar" />
  <input type="button" name="Submit1" value="   Fechar   " onClick="window.close()" />
</form>
</center>
</body>
<?=$msg?>
</html>

