<?php

require_once("../setup.php");
require_once(dirname(__FILE__).'/../../core/login/acl.php');

$conn = new connection_factory($param_conn);

// Definindo as permissoes do usuario quanto ao arquivo
$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Você não tem permissão para acessar este formulário!');
}

$ref_pessoa = $_POST['ref_pessoa'];
$ref_setor  = $_POST['setor'];
$ref_campus  = $_POST['campus'];
$usuario    = $_POST['usuario'];
$senha      = hash('sha256', $_POST['senha']);
$permissoes = $_POST['permissao'];

if($_POST['ativado']){
	$ativado = 't';
}else{
	$ativado = 'f';
}

//Verificando se existe login igual cadastrado
$usuario_existe = $conn->Execute("SELECT id FROM usuario WHERE nome = '$usuario';");

if(empty($usuario_existe->fields[0]))
{
	$sqlUsuario = "INSERT INTO usuario (nome, senha, ref_pessoa, ativado, ref_setor, ref_campus )
	VALUES('$usuario', '$senha', $ref_pessoa, '$ativado', $ref_setor, $ref_campus);";
	
	if($conn->Execute($sqlUsuario)){
		$msg = '<font color="green">Usu&aacute;rio cadastrado com sucesso!</font>';
	}else{
		$msg = 'Erro ao cadastrar usu&aacute;rio!'; 
	}

	$usuario_cadastrado = $conn->Execute("SELECT id FROM usuario WHERE nome = '$usuario';");
	$id_usuario_cadastrado = $usuario_cadastrado->fields[0];

	//Criando as permissoes
	foreach($permissoes as $permissao){
		if(!$conn->Execute("INSERT INTO usuario_papel(ref_usuario, ref_papel) 
							VALUES($id_usuario_cadastrado, $permissao); "))
			$msg = 'Erro ao criar permiss&otilde;oes do usu&aacute;rio!';
	}
}else{
	$msg = 'Usu&aacute;rio j&aacute; cadastrado no sistema!';
}

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Cadastro de usu&aacute;rio</h2>
		<font color="red"><?php echo $msg;?></font>
		<p>
			<a href="index.php">Voltar para o controle de usu&aacute;rios</a>
		</p>
	</body>
</html>
