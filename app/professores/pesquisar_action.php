<?php
/*
 * Arquivo com as configuracoes iniciais
*/
require_once("../../app/setup.php");

/*
 * Estancia a classe de conexao e abre
*/
$conn = new connection_factory($param_conn);

$sql = "
SELECT
    p.id,
    p.nome,
    to_char(o.dt_ingresso,'DD/MM/YYYY') as data,
    d.descricao
FROM
    professores o, pessoas p, departamentos d
WHERE
    o.ref_professor = p.id AND
    d.id = o.ref_departamento AND
    lower(to_ascii(p.nome,'LATIN1')) LIKE
    lower(to_ascii('%".$_POST['nome']."%','LATIN1'))
ORDER BY to_ascii(nome,'LATIN1') DESC
LIMIT 10;";

$arr = $conn->get_all($sql);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Controle de setores</h2>
        <div class="btn_action">
            <a href="javascript:history.back();" class="bar_menu_texto">
                <img src="../../public/images/icons/back.png" alt="Voltar" width="20" height="20" />
                <br />Voltar
            </a>
        </div>
        <div class="panel">
            <table class="grid">
                <tr class="grid_head">
                    <td>C&oacute;d.</td>
                    <td>Nome</td>
                    <td>Data de entrada</td>
                    <td>Departamento</td>
                    <td width="60" align="center">Op&ccedil;&otilde;es</td>
                </tr>

                <!-- Percorrendo vertor multidimensional -->
                <?php foreach($arr as $prof) : ?>

                <tr class="grid_row">
                    <td><?=$prof['id']?></td>
                    <td><?=$prof['nome']?></td>
                    <td><?=$prof['data']?></td>
                    <td><?=$prof['descricao']?></td>
                    <td align="center">
                        <a href="alterar.php?id=<?=$prof['id']?>">
                            <img src="../../public/images/icons/edit.png" alt="Alterar" title="Alterar" />
                        </a>
                    </td>
                </tr>

                <?php endforeach; ?>

            </table>
            <p>
                <a href="index.php">Voltar para o controle de professores</a>
            </p>
            <span class="comentario">
                Exibindo somente os primeiros 30 registros.<br />
                Para mais registros utilize a pesquisa.
            </span>
        </div>
    </body>
</html>
