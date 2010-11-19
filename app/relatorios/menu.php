<?php

require("../../app/setup.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <title>Menu</title>
        <script type="text/javascript" language="javascript" src="<?=$BASE_URL .'lib/prototype.js'?>"></script>
        <script type="text/javascript" language="javascript" src="<?=$BASE_URL .'app/web_diario/web_diario.js'?>"></script>
    </head>

    <body>
        <h2>Relat&oacute;rios</h2>
        <input type="image" name="voltar"
               src="../../public/images/icons/back.png"
               alt="Voltar"
               title="Voltar"
               id="bt_voltar"
               name="bt_voltar"
               class="botao"
               onclick="history.back(-1);return false;" />
        <br />
        <div class="panel" style="float: left;">
            <div class="box_menu_relatorio" style="float: left;">
                <a href="aprovados_reprovados/pesquisa_aprovados_reprovados.php" title="Alunos aprovados/reprovados" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Alunos Aprovados/Reprovados
                </a>
                <br />
                <a href="dispensados/pesquisa_dispensados.php" title="Alunos Dispensados" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Alunos Dispensados
                </a>
                <br />
                <a href="matriculados/pesquisa_alunos.php" title="Alunos matriculados" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Alunos Matriculados por curso
                </a>
                <br />
                <a href="matriculados_cidade/pesquisa_matriculados_por_cidade.php" title="Alunos matriculados por cidade de origem" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Alunos Matriculados por curso e cidade
                </a>
                <br />
                <a href="boletim/pesquisa_boletim.php"	title="Emiss&atilde;o dos Boletins Escolares" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Boletim Escolar
                </a>
                <br />
                <a href="cursos_andamento/pesquisa_cursos_no_periodo.php" title="Cursos em andamento" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Cursos em andamento
                </a>
                <br />
                <a href="declaracao_matricula/pesquisa_declaracao_matricula.php" title="Declara&ccedil;&atilde;o de matr&iacute;cula" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Declara&ccedil;&atilde;o de matr&iacute;cula
                </a>
                <br />
                <a href="diarios_periodo/pesquisa_diarios.php" title="Di&aacute;rios no per&iacute;odo" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Di&aacute;rios no per&iacute;odo
                </a>
                <br />
                <a href="egressos/pesquisa_egressos.php" title="Egressos" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Egressos
                </a>
                <br />
                <a href="faltas_global/pesquisa_faltas_global.php" title="Faltas global por per&iacute;odo / Curso" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Faltas global
                </a>
                <br />
                <a href="ficha_academica/pesquisa_ficha_academica.php" title="Ficha acad&ecirc;mica" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Ficha acad&ecirc;mica
                </a>
                <br />
                <a href="notas_faltas_global/index.php" title="Notas e faltas por curso no per&iacute;odo" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Notas e faltas por curso no per&iacute;odo
                </a>
                <br />
                <a href="matriculados_pessoas/pesquisa_todos_alunos_periodo.php" title="Pessoas com matr&iacute;cula" target="_self">
                    <img src="../../public/images/icons/lupa.png" />&nbsp;Pessoas com matr&iacute;cula
                </a>
            </div>
            <div class="box_menu_relatorio" style="float: left; border: 0; padding-left: 5em;">
                <form name="acessa_rapido" id="acesso_rapido" method="post" action="">
                    <fieldset style="padding-left: 2em; padding-right: 2em; padding-bottom: 2em">
                        <legend><strong><h3>Acesso R&aacute;pido</h3></strong></legend>
                        <fieldset>
                            <legend><strong>Informações Acad&ecirc;micas</strong></legend>
                            Matr&iacute;cula:&nbsp;<input type="text" name="aluno_id" id="aluno_id" size="6" />
                            <input type="button" name="envia_aluno" id="envia_aluno" value="OK" onclick="abrir('<?=$IEnome?>' + ' web diário', '<?=$BASE_URL?>' + 'app/relatorios/ficha_academica/informacoes_academicas.php?aluno=' + $F('aluno_id'));" />
                        </fieldset>
                        <br />
                        <fieldset>
                            <legend><strong>Consulta Diário</strong></legend>
                            C&oacute;digo:&nbsp;<input type="text" name="diario_id" id="diario_id" size="6" />
                            <input type="button" name="envia_diario" id="envia_diario" value="OK" onclick="abrir( '<?=$IEnome?>' + ' web diário', '<?=$BASE_URL?>' + 'app/web_diario/secretaria/lista_diarios_secretaria.php?diario_id=' + $F('diario_id'));" />
                        </fieldset>
                    </fieldset>
                </form>
            </div>
        </div>
    </body>
</html>
