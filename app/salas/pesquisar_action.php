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
$sql = "SELECT id, ref_campus, numero, capacidade
        FROM salas
        WHERE
            lower(to_ascii(numero)) LIKE
            lower(to_ascii('%".$_POST['numero']."%'))
        ORDER BY numero
        DESC LIMIT 10;";

$arr_setor = $conn->get_all($sql);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Controle de salas</h2>
        <div class="btn_action">
            <a href="javascript:history.back();" class="bar_menu_texto">
                <img src="../../public/images/icons/back.png" alt="Voltar" width="20" height="20" />
                <br />Voltar
            </a>
        </div>
        <div class="panel">
            <table class="grid">
                <tr  class="grid_head">
                    <td>Numero</td>
                    <td>Capacidade</td>
                    <td width="60" align="center">Op&ccedil;&otilde;es</td>
                </tr>

                <!-- Percorrendo vertor multidimensional -->
                <?php foreach($arr_setor as $setor) : ?>

                <tr class="grid_row">
                    <td><?=$setor['numero']?></td>
                    <td><?=$setor['capacidade']?></td>
                    <td align="center">
                        <a href="alterar.php?id=<?=$setor['id']?>">
                            <img src="../../public/images/icons/edit.png" alt="Alterar" title="Alterar" />
                        </a>&nbsp;
                        <a href="excluir_action.php?id=<?=$setor['id']?>" onclick="return confirm('Deseja realmente excluir?')">
                            <img src="../../public/images/icons/delete.png" alt="Excluir" title="Excluir" />
                        </a>
                    </td>
                </tr>

                <?php endforeach; ?>

            </table>
            <p>
                <a href="index.php">Voltar para o controle de salas</a>
            </p>
            <span class="comentario">
                Exibindo somente os primeiros 30 registros.<br />
                Para mais registros utilize a pesquisa.
            </span>
        </div>
    </body>
</html>
