<?php
/*
 * Arquivo com as configuracoes iniciais
*/
require_once("../../app/setup.php");
require_once("../../core/date.php");

$id_pessoa     = $_POST['id_pessoa'];
$departamento  = $_POST['departamento'];
$user          = $_POST['user'];
$setor         = $_POST['setor'];
$password      = $_POST['password'];
$password_hash = hash('sha256',$password);

/*
 * Verifica se as senhas conferem
 */
if($_POST['password'] != $_POST['confirm']) {
    echo '
    <script language="javascript" type="text/javascript">
        alert("As senhas não conferem!");
        history.back(-1);
    </script>';
    exit ();
}

/*
 * Se vazio mantem senha atual
 */
if ($password == '') $manter_senha = true;


/*
 * Convertendo a data para o banco de dados
 */
$date = new date();
$data = $date->convert_date($_POST['data']);

/*
 * Preparando variavel de verificacao se usuario ativo
 */
$ativo = $_POST["ativar"];

if ($ativo == true) $ativo = 'true';
else $ativo = 'false';

/*
 * Realizando as consultas no banco de dados
 */
$conn = new connection_factory($param_conn);

$pessoa = $conn->get_row("SELECT nome, email FROM pessoas WHERE id = $id_pessoa;");

$num_professores = $conn->get_one("SELECT COUNT(*) FROM professores WHERE ref_professor = $id_pessoa");
$num_usuarios    = $conn->get_one("SELECT COUNT(*) FROM usuario WHERE ref_pessoa = $id_pessoa");

/*
 * Verifica se a pessoa e um professor
 */
if($num_professores == 1) {

    /*
     * Verifica se e um usuario do webdiario
     */
    if($num_usuarios == 1) {

        /*
         * Se a senha for mantida
         */
        if ($manter_senha == true )
            $sql_update = "
            begin;
                UPDATE professores SET ref_departamento=$departamento,dt_ingresso='$data'
                WHERE ref_professor = $id_pessoa;

                UPDATE usuario SET ativado=$ativo, ref_setor='$setor'
                WHERE ref_pessoa = $id_pessoa;
            commit;";
        else
            $sql_update = "
            begin;
                UPDATE professores SET
                    ref_departamento=$departamento,dt_ingresso='$data'
                WHERE
                    ref_professor = $id_pessoa;
                UPDATE usuario SET
                    senha='$password_hash',ativado=$ativo,ref_setor='$setor'
                WHERE
                    ref_pessoa = $id_pessoa;
            commit;";

        /*
         * Tenta realizar a atualizacao do usuario
         */
        if($conn->Execute($sql_update)) {
            $msg = '<font color="green">Registro alterado com sucesso!</font>';

            /*
             * Verifica se a senha foi preenchida no formulario para enviar a nova senha
             */
            if ($password != '') {
                
                $message = "Dados de acesso ao Web Diario - Usuario: $user - Senha: $password";

                /*
                 * Envia email com usuario e senha do webdiario
                 */
                if(mail($pessoa['email'], 'SA - Acesso Web Diario', $message, 'From: SA')) {
                    $msg .= "<br /><font color=\"green\">Os dados do usu&aacute;rio foram enviados para
                    o email cadastrado ".$pessoa['email']." de ".$pessoa['nome'].".</font>";
                }
            }
        }
    }else {
        $msg_2 .= 'Existe mais de um usu&aacute;rio registrado com o c&oacute;digo: '.$id_pessoa.'<br />';
    }
}else {
    $msg_2 = 'Existe mais de um professor registrado com o c&oacute;digo: '.$id_pessoa.'<br />';
}

?>
<html>
    <head>
        <?=$DOC_TYPE?>
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Alterar professor</h2>
        <div class="panel">
            <font color="red"><?=$msg?></font>
            <p>
                <a href="index.php" class="bar_menu_texto">
                    Voltar para p&aacute;gina inicial de professores
                </a>
            </p>
        </div>
    </body>
</html>
