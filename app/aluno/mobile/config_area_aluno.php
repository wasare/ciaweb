<?php

require_once("../setup.php");

$conn = new connection_factory($param_conn);

// Verifica as permissoes de acesso do usuario quanto ao arquivo
$ACL_FILE = __FILE__;
require_once($BASE_DIR .'core/login/acesso.php');
// ^ Verifica as permissoes de acesso do usuario quanto ao arquivo ^ //


if($_POST) {

    $msg = '';
    $reiniciar_senha = $_POST['reiniciar_senha'];
    if($reiniciar_senha == '') {
        $msg = 'Insira um c&oacute;digo de aluno!';
    }else {
    $sql = "SELECT COUNT(*) FROM acesso_aluno
            WHERE ref_pessoa = $reiniciar_senha;";

    $num_result = $conn->get_one($sql);
    
    if($num_result == 1) {
        
        $senha = str_pad($reiniciar_senha, 5,'0',STR_PAD_LEFT);

        $sql2 = "UPDATE acesso_aluno SET senha=md5('".$senha."')
                 WHERE ref_pessoa = $reiniciar_senha;";

        $rs_usuario = $conn->Execute($sql2);

        if($rs_usuario) {
            $msg = "Senha alterada com sucesso!";
        }else {
            $msg = "Erro ao alterar senha!";
        }
    }else {
        $msg = "Nenhum aluno encontrado!";
    }
    }
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Configura&ccedil;&atilde;o da &aacute;rea do aluno</h2>
        <div class="panel">
            <form action="config_area_aluno.php" method="post" >
                <label for="">Reiniciar a senha de acesso do aluno com matr&iacute;cula:</label>
                <br />
                <input name="reiniciar_senha" type="text" />
                <br /> <span class="dica">Este procedimento reinicia a senha do aluno para a senha inicial</span>
                <p>
                    <input type="submit" value="Salvar" />
                </p>
            </form>
        </div>
        <p>
            <?=$msg?>
        </p>
    </body>
</html>
