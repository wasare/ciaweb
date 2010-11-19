<?php

//ARQUIVO DE CONFIGURACAO E CLASSE ADODB
header("Cache-Control: no-cache");
require_once("../../app/setup.php");

//Criando a classe de conexão
$Conexao = NewADOConnection("postgres");

//Setando como conexão persistente
$Conexao->PConnect("host=$host dbname=$database user=$user password=$password");

//EXECUTANDO SQL COM ADODB
$Result1 = $Conexao->Execute("SELECT descricao, data FROM avisos WHERE id = 1");

$avisos = array();
$avisos[0] = $Result1->fields[0];

//Iniciando a variavel de mensagem
$msg = null;


if($_POST){

	$texto = $_POST["texto"];

	//Executando a autualizacao
	$Result2 = $Conexao->Execute("UPDATE avisos SET descricao = '$texto' WHERE id = 1;");
	
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Lista alunos matriculados</title>

<link rel="stylesheet" href="openwysiwyg_v1.4.7/docs/style.css" type="text/css">
<script type="text/javascript" src="openwysiwyg_v1.4.7/scripts/wysiwyg.js"></script>
<script type="text/javascript" src="openwysiwyg_v1.4.7/scripts/wysiwyg-settings.js"></script>
<script type="text/javascript">
	WYSIWYG.attach('texto', cefetsmall);
</script>
</head>
<body bgcolor="#E8E8E8">
<center>
<h1>Editar aviso</h1>
<form method="post" action="cadastrar.php" name="myform">
  <textarea id="texto" name="texto" style="width:500px;height:200px;"><?php echo $avisos[0]; ?></textarea>
  <input type="submit" name="Submit"   value=" Alterar" />
  <input type="button" name="Submit1" value="   Fechar   " onClick="window.close()" />
</form>
</center>
</body>
<?php echo $msg; ?>
</html>
