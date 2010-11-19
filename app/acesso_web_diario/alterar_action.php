<?php

//ARQUIVO DE CONFIGURACAO E CLASSE ADODB
header("Cache-Control: no-cache");
require_once("../../app/setup.php");


$codigo_pessoa = $_POST["codigo_pessoa"];
$senha = $_POST["senha"];
$nivel = $_POST["nivel"];

$manter_senha = $_POST["manter_senha"];


$ativo = $_POST["ativar"];
if ($ativo == true){

	$ativo = 't';
}
else{

	$ativo = 'f';
}


if($manter_senha == true ){

	$sql = "UPDATE public.diario_usuarios 
		SET nivel = '$nivel', ativo = '$ativo' 
		WHERE id_nome = '$codigo_pessoa';";
}
else {

	$sql = "UPDATE public.diario_usuarios 
		SET senha = md5('$senha'), nivel = '$nivel', ativo = '$ativo' 
		WHERE id_nome = '$codigo_pessoa';";
}


//echo $sql;
//die();



//Criando a classe de conexão ADODB
$Conexao = NewADOConnection("postgres");

//Setando como conexão persistente
$Conexao->PConnect("host=$host dbname=$database user=$user password=$password");

//Exibindo a descricao do curso caso setado
$Result1 = $Conexao->Execute($sql);
	
//Se executado com sucesso
if ($Result1) {
	
	$msg = "<p class=\"msg_sucesso\">Alteração realizada com sucesso!</p>";
}
else {
	
	$msg = "<p class=\"msg_erro\">Erro ao realizar alteração!</p>";
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>SA</title>
	<link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h2>Alterar acesso ao Web Diário</h2>
<?php echo $msg; ?>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="60">
    	<div align="center">
    		<a href="index.php" class="bar_menu_texto">
    			<img src="../../public/images/icons/back.png" alt="Voltar" width="20" height="20" />
    			<br />
      			Voltar
      		</a>
     	</div>
    </td>
  </tr>
</table>
</body>
</html>
