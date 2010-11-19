<?php

require_once(dirname(__FILE__) ."/../setup.php");
require_once(dirname(__FILE__).'/../../core/login/acl.php');

$conn = new connection_factory($param_conn);

// Definindo as permissoes do usuario quanto ao arquivo
$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Você não tem permissão para acessar este formulário!');
}

$id_usuario = $_GET["id_usuario"];

$sqlUsuario = '
SELECT
    u.id,
    u.nome,
    u.ativado,
    u.ref_pessoa,
    p.nome,
    s.nome_setor,
    c.nome_campus,
    u.ref_campus,
    u.ref_setor
FROM
    usuario u, setor s, pessoas p, campus c
WHERE
    s.id = u.ref_setor AND
    u.ref_pessoa = p.id AND
    c.id = u.ref_campus AND
    u.id = '.$id_usuario;

$RsUsuario = $conn->get_row($sqlUsuario);
$setor = $conn->get_all('SELECT id, nome_setor FROM setor;');
$campus = $conn->get_all('SELECT id, nome_campus FROM campus WHERE id = ref_campus_sede;');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <script>
            function validarSenha(){
                if(document.form1.senha_atual.checked == 1){
                    return true;
                }else{
                    senha1 = document.form1.senha.value;
                    senha2 = document.form1.resenha.value;
                    if(senha1 == ""){
                        alert("O campo senha nao pode ser vazio!");
                        return false;
                    }
                    if (senha1 != senha2){
                        alert("As senhas nao conferem!");
                        return false;
                    }
                    return true;
                }
            }
        </script>
    </head>
    <body>
        <h2>Alterar usu&aacute;rio</h2>

        <form id="form1" name="form1" method="post" action="alterar_action.php" onSubmit="return validarSenha()">
            <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $id_usuario; ?>" />

            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="60">
                        <div align="center">
                            <label class="bar_menu_texto">
                                <input name="save" 
                                       type="image"
                                       src="../../public/images/icons/save.png" />
                                <br />Salvar
                            </label>
                        </div>
                    </td>
                    <td width="60">
                        <div align="center">
                            <a href="javascript:history.back();"
                               class="bar_menu_texto">
                                <img src="../../public/images/icons/back.png"
                                     alt="Voltar"
                                     width="20"
                                     height="20" />
                                <br />Voltar
                            </a>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="panel">
                <strong>Pessoa:</strong><br />
                <?=$RsUsuario[3]?> - <?=$RsUsuario[4]?><br />
                <strong>Campus:</strong><br />
                <select name="campus" id="campus" size="3">
                    <?php
                      foreach($campus as $c) {
                        $selected = ($c['id'] == $RsUsuario[7]) ? 'selected="selected"' : '';
                        echo '<option value="'. $c['id'] .'"'. $selected .' >';
                        echo $c['nome_campus'] ."</option>";
                      }
                    ?>
                </select><br />
                <strong>Setor:</strong><br />
                <select name="setor" id="setor">
                    <?php
                      foreach($setor as $s) {
                        $selected = ($s['id'] == $RsUsuario[8]) ? 'selected="selected"' : '';
                        echo '<option value="'. $s['id'] .'"'. $selected .' >';
                        echo $s['nome_setor'] ."</option>";
                      }
                    ?>
                </select>
                <p>
                    <strong>Usu&aacute;rio:</strong><br />
                    <input type="text"
                           name="usuario"
                           id="usuario"
                           value="<?php echo $RsUsuario[1]; ?>"
                           disabled="disabled" />
                </p>
                <strong>Senha:</strong><br />
                <input type="password" name="senha" id="senha" /><br />
                <strong>Digite a senha novamente:</strong><br />
                <input type="password" name="resenha" id="resenha" />
                <p>
                    Manter senha atual?
                    <input type="checkbox"
                           name="senha_atual"
                           id="senha_atual" />
                    <span class="comentario">Marcado para sim.</span>
                </p>
                <p>
                    <strong>Permiss&otilde;es:</strong><br />
                    <select name="permissao[]" id="permissao[]" multiple="multiple" size="4">
                        <?php

                        //Permissoes de usuario

                        $sqlPapelUsuario =  'SELECT ref_papel '.
                            'FROM usuario_papel '.
                            'WHERE ref_usuario = '.$RsUsuario[0];

                        $arr_papel_usuario = $conn->get_col($sqlPapelUsuario);

                        $arr_papel = $conn->get_all('SELECT papel_id, descricao, nome FROM papel');

                        foreach($arr_papel as $papel) {
                            if(in_array($papel['papel_id'],$arr_papel_usuario)) {
                                echo '<option value="'.$papel['papel_id'].'" selected="selected" >';
                                echo $papel['nome']."</option>";
                            }else {
                                echo '<option value="'.$papel['papel_id'].'" >';
                                echo $papel['nome']."</option>";
                            }
                        }
                        ?>
                    </select>
                </p>
                <p>
                    Usu&aacute;rio ativado?
                    <?php
                    if ($RsUsuario[2] == 't') {
                        echo '<input type="checkbox" checked="checked" name="ativado" id="ativado" />';
                    }
                    else {
                        echo '<input type="checkbox" name="ativado" id="ativado" />';
                    }
                    ?> <span class="comentario">Marcado para sim.</span>
                </p>
            </div>
        </form>
    </body>
</html>
