<?php
require_once("../../app/setup.php");


$Conexao = NewADOConnection("postgres");
$Conexao->PConnect("host=$host dbname=$database port=$port user=$user password=$password");


$contratos = $_POST["contrato"];
$data = $_POST["data"];
$resp = "";

//Formatando a data
$data = explode("/",$data,3);
$data_formatada = $data[2]."-".$data[1]."-".$data[0];//'2009-03-23'


if($contratos != ''){


	foreach($contratos as $contrato){
		
		$sql = "UPDATE contratos 
		SET dt_formatura = '$data_formatada', dt_conclusao = '$data_formatada'
		WHERE id = '$contrato';";
		
		//echo "<p>".$sql."</p>";
			
		
		$RsAtualiza = $Conexao->Execute($sql);
	
		if (!$RsAtualiza){
			
    			$resp .= $Conexao->ErrorMsg();
    			$resp .= "<p><font color=red>O contrato $contrato ".
			"n&atilde;o foi alterado!</font></p>";
	
		}else{

			$resp .= "<p><font color=green>O contrato $contrato ".
			"foi alterado com sucesso!</font></p>";
		}
		
	}
}
else{

	$resp .= "<p><font color=red>Nenhum contrato selecionado!</font></p>";
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>SA</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link href="../../public/styles/formularios.css" rel="stylesheet"	type="text/css" />
</head>
<body>
<center>
	<h1>Cola&ccedil;&atilde;o de grau</h1>
	<div class="panel">
		<?php echo $resp; ?>
		<p>
		<a href="index.php">Realizar nova Cola&ccedil;&atilde;o de Grau</a>
		</p>
	</div>
</center>
</body>
</html>
