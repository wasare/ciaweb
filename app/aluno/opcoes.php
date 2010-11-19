<?php

require_once('aluno.conf.php');
include_once('includes/topo.htm');


$msg = (string) '';

if($_POST) {

    //Recebe os dados do formulario
    $senha_atual   = $_POST['senha_atual'];
    $senha_nova_1  = $_POST['senha_nova_1'];
    $senha_nova_2  = $_POST['senha_nova_2'];

    //Monta as mensagens de retorno para o usuario
    $msg = '<font color="red"><strong>ATEN&Ccedil;&Atilde;O:</strong></font>';

    //Verifica se algum campo nao foi setado
    if($senha_atual == '' or $senha_nova_1 == '' or $senha_nova_2 == '') {
        $msg .= ' Preencha todos os campos para alterar a senha!';
    }
    //Verifica se as senhas novas nao sao iguais
    elseif ($senha_nova_1 != $senha_nova_2) {
        $msg .= ' Senhas n&atilde;o conferem!';
    }else {
        
        //SQL que compara a senha atual no banco de dados
        $sql_compara = "
            SELECT COUNT(*) FROM acesso_aluno
            WHERE
                senha = md5('".$senha_atual."') AND
                ref_pessoa = $user";

        $num_usuario = $conn->get_one($sql_compara);


        //Verifica se retornou um usuario
        if($num_usuario == 1){

            //SQL que atualiza a senha
            $sql_atualiza_senha = "
                UPDATE acesso_aluno
                SET senha=md5('".$senha_nova_1."')
                WHERE ref_pessoa = $user";

            $rs_usuario = $conn->Execute($sql_atualiza_senha);

            //Verifica se o resultset esta OK
            if($rs_usuario){
                $msg = '<font color="green"><strong>Senha alterada com sucesso!</strong></font>';                
            }
        }else {
            $msg .= ' Senha atual n&atilde;o confere!';
        }
    }
}
?>

<h2>Op&ccedil;&otilde;es</h2>
<h3>Alterar senha</h3>
<form method="post" id="form1" name="form1" action="opcoes.php">
    Entre com a senha atual:<br />
    <input type="password" id="senha_atual" name="senha_atual" />
    <p>
        Entre com a nova senha:<br />
        <input type="password" id="senha_nova_1" name="senha_nova_1" />
        <br />
        Repita a nova senha:<br />
        <input type="password" id="senha_nova_2" name="senha_nova_2" />
    </p>
    <input type="submit" value="Alterar" />
    <p>
        <?php echo $msg; ?>
    </p>
</form>

<?php include_once('includes/rodape.htm'); ?>