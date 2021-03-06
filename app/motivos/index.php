<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once("../../app/setup.php");

/*
 * Estancia a classe de conexao e abre
 */
$conn = new connection_factory($param_conn);

// Verifica as permissoes de acesso do usuario quanto ao arquivo
$ACL_FILE = __FILE__;
require_once($BASE_DIR .'core/login/acesso.php');
// ^ Verifica as permissoes de acesso do usuario quanto ao arquivo ^ //

/*
 * Realiza uma consulta no banco de dados retornando um vetor multidimensional
 */
$sql = 'SELECT
            id, descricao, ref_tipo_motivo
        FROM motivo
        ORDER BY descricao
        LIMIT 20;';

$arr_motivo = $conn->get_all($sql);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Controle de motivos</h2>
        <div class="btn_action">
            <a href="cadastrar.php" class="bar_menu_texto">
                <img src="../../public/images/icons/new.png" alt="Novo" width="20" height="20" />
                <br />Novo
            </a>
        </div>
        <div class="btn_action">
            <a href="pesquisar.php" class="bar_menu_texto">
                <img src="../../public/images/icons/lupa.png" alt="Novo" width="20" height="20" />
                <br />Pesquisar
            </a>
        </div>
        <div class="btn_action">
            <a href="javascript:history.back();" class="bar_menu_texto">
                <img src="../../public/images/icons/back.png" alt="Voltar" width="20" height="20" />
                <br />Voltar
            </a>
        </div>
        <div class="panel">
            <table class="grid">
                <tr  class="grid_head">
                    <td>Nome</td>
					<td>Tipo</td>
                    <td width="60" align="center">Op&ccedil;&otilde;es</td>
                </tr>

                <!-- Percorrendo vertor multidimensional -->
                <?php foreach($arr_motivo as $motivo) : ?>

                <tr class="grid_row">
                    <td><?=$motivo['descricao']?></td>
					<td><?=$motivo['ref_tipo_motivo']?></td>
                    <td align="center">
                        <a href="alterar.php?id=<?=$motivo['id']?>">
                            <img src="../../public/images/icons/edit.png" alt="Alterar" title="Alterar" />
                        </a>&nbsp;
                        <a href="excluir_action.php?id=<?=$motivo['id']?>" onclick="return confirm('Deseja realmente excluir?')">
                            <img src="../../public/images/icons/delete.png" alt="Excluir" title="Excluir" />
                        </a>
                    </td>
                </tr>

                <?php endforeach; ?>

            </table>
            <span class="comentario">
            Exibindo somente os primeiros 20 registros.<br />
            Para mais registros utilize a pesquisa.
            </span>
        </div>
    </body>
</html>
