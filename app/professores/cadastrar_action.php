<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once("../../app/setup.php");
require_once("../../core/date.php");


/*
 * Parametros do formulario de cadastro
 */
$id_pessoa       = $_POST['id_pessoa'];
$departamento    = $_POST['departamento'];
$user            = $_POST['user'];
$setor           = $_POST['setor'];
$campus          = $_POST['campus'];
$papel_professor = 3;
$password        = rand(10000000,99999999);
$password_hash   = hash('sha256',$password);

$date = new date();
$data = $date->convert_date($_POST['data']);

$ativo = $_POST["ativar"];

if ($ativo == true) {
    $ativo = true;
}
else {
    $ativo = false;
}


/*
 * Estancia a classe de conexao e abre
 */
$conn = new connection_factory($param_conn);


$sql_conf_pessoa = "
SELECT COUNT(id)
FROM professores
WHERE ref_professor = ".$id_pessoa.";";

$count = $conn->get_one($sql_conf_pessoa);

if($count != 0 ) {

    $msg = '<b>Erro:</b> Pessoa f&iacute;sica j&aacute; cadastrada.';

}else {
    
    $sql_conf_user = "
        SELECT COUNT(id)
        FROM usuario 
        WHERE nome = '$user';";

    $count = $conn->get_one($sql_conf_user);
    
    if($count != 0) {

        $msg = '<b>Erro:</b> Usu&aacute;rio j&aacute; existe.';

    }else {

        $pessoa = $conn->get_row("SELECT nome, email FROM pessoas WHERE id = $id_pessoa");

        //Nivel 1 professor, 2 secretaria
        //2006-06-20

        $sql_insert = "
            begin;
                INSERT INTO professores(ref_professor,ref_departamento,dt_ingresso)
                    VALUES($id_pessoa,$departamento,'$data');
                INSERT INTO  usuario(nome,ref_pessoa,senha,ativado,ref_setor,ref_campus)
                    VALUES('$user',$id_pessoa,'$password_hash','$ativo','$setor','$campus');
                INSERT INTO usuario_papel(ref_usuario, ref_papel)
                    VALUES(CURRVAL('usuario_id_seq'),3);
            commit;";

        //echo $sql_insert;exit;

        if($conn->Execute($sql_insert)){

            $msg = '<font color="green">Cadastro efetuado com sucesso!</font>';

            $message = "Dados de acesso ao Web Diario - Usuario: $user - Senha: $password";

            //envia email com senha webdiario
            if(mail($pessoa['email'], 'SA - Acesso Web Diario', $message, 'From: SA')) {
                $msg .= "<br /><font color=\"green\">Os dados do usu&aacute;rio foram enviados para
                    o email cadastrado ".$pessoa['email']." de ".$pessoa['nome'].".</font>";
            }
        }
    }
}

?>
<html>
    <head>
        <?=$DOC_TYPE?>
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Cadastrar professor</h2>
        <div class="panel">
            <font color="red"><?=$msg?></font>
            <p>
                <a href="cadastrar.php" class="bar_menu_texto">
                    Cadastrar professor
                </a>&nbsp;&nbsp;
                <a href="index.php" class="bar_menu_texto">
                    Voltar para p&aacute;gina inicial de professores
                </a>
            </p>
        </div>
    </body>
</html>