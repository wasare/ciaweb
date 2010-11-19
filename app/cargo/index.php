<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once("../../app/setup.php");

/*
 * Estancia a classe de conexao e abre
 */
$conn = new connection_factory($param_conn);

/*
 * Realiza uma consulta no banco de dados retornando um vetor multidimensional
 */
$sql = 'SELECT
            id, descricao, descricao_breve
        FROM cargo
        ORDER BY descricao
        LIMIT 20;';

$arr_cargo = $conn->get_all($sql);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Controle de cargos</h2>
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
                    <td>Descri&ccedil;&atilde;o</td>
                    <td>Descri&ccedil;&atilde;o breve</td>
                    <td width="60" align="center">Op&ccedil;&otilde;es</td>
                </tr>

                <!-- Percorrendo vertor multidimensional -->
                <?php foreach($arr_cargo as $cargo) : ?>

                <tr class="grid_row">
                    <td><?=$cargo['descricao']?></td>
                    <td><?=$cargo['descricao_breve']?></td>
                    <td align="center">
                        <a href="alterar.php?id=<?=$cargo['id']?>">
                            <img src="../../public/images/icons/edit.png" alt="Alterar" title="Alterar" />
                        </a>&nbsp;
                        <a href="excluir_action.php?id=<?=$cargo['id']?>" onclick="return confirm('Deseja realmente excluir?')">
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
