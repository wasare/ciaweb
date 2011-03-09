<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once("../../app/setup.php");
require_once("../../core/data/grid.php");

/*
 * Estancia a classe de conexao e abre
 */
$conn = new connection_factory($param_conn);
$grid = new grid();

$sql = '
SELECT
    p.id as "Cód.",
    p.nome as "Nome", 
    u.nome as "Usuário",
    to_char(o.dt_ingresso,\'DD/MM/YYYY\') as "Data de entrada",
    d.descricao as "Departamento"
    '. $grid->options('p.id', 'alterar.php') .'
FROM
    professores o LEFT OUTER JOIN usuario u
    ON (u.ref_pessoa = o.ref_professor),
    pessoas p, departamentos d
WHERE
    o.ref_professor = p.id AND
    d.id = o.ref_departamento
ORDER BY to_ascii(p.nome,\'LATIN1\')';

?>
<html>
    <head>
        <?php echo $DOC_TYPE; ?>
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Controle de professores</h2>
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
            <p>
                <strong>Lista de professores cadastrados</strong>
            </p>
            <?php $grid->render($conn->adodb, $sql, 10); ?>
            <span class="comentario"></span>
        </div>
    </body>
</html>
