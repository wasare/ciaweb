<?php

require_once(dirname(__FILE__).'/setup.php');
require_once($BASE_DIR .'core/login/acl.php');

$conn = new connection_factory($param_conn);

// VERIFICA SE O USUARIO TEM DIREITO DE ACESSO
$acl = new acl();

// @todo melhorar o retorno ao usuário usando um metódo de logout
if (!$acl->has_role($sa_ref_pessoa, $PAPEIS_SA, $conn)) {
    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.history.back(1);</script>');
}
// ^ VERIFICA SE O USUARIO TEM DIREITO DE ACESSO ^ //

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>SA</title>
        <link href="../public/styles/style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript">
            function iframeAutoHeight(quem){
                if(navigator.appName.indexOf("Internet Explorer")>-1){
                    var func_temp = function(){
                        var val_temp = quem.contentWindow.document.body.scrollHeight + 30
                        quem.style.height = val_temp + "px";
                    }
                    setTimeout(function() { func_temp() },100) //ie sucks
                }else {
                    var val = quem.contentWindow.document.body.parentNode.offsetHeight + 30
                    quem.style.height= val + "px";
                }
            }
        </script>
        <script src="../lib/SpryAssets/SpryMenuBar.js" type="text/javascript"></script>
        <link href="../lib/SpryAssets/SpryMenuBarHorizontal.css" rel="stylesheet" type="text/css" />
        <link href="../public/images/favicon.ico" rel="shortcut icon" />
    </head>

    <body style="border: 0; overflow: visible">
        <div align="center">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="2" valign="middle" height="40">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="50" valign="middle">
                                    <a href="index.php">
                                        <img src="../public/images/icon_sa.gif"
                                             alt="Principal"
                                             width="40"
                                             height="34" />
                                    </a>
                                </td>
                                <td width="230">
                                    <a href="index.php" class="titulo1">Sistema Acad&ecirc;mico</a>
                                </td>
                                <td valign="top">
                                    <div align="right" class="texto1">
                                        <strong>Desenvolvimento: </strong>
                                    </div>
                                </td>
                                <td valign="middle">&nbsp;
                                    <a href="<?=$IEurl?>" target="_blank">
                                        <img src="../public/images/ifmg.jpg"
                                             alt="IFMG - Campus Bambu&iacute;"
                                             title="IFMG - Campus Bambu&iacute;" />
                                    </a>&nbsp;&nbsp;
                                    <img src="../public/images/gti.jpg"
                                         alt="Ger&ecirc;ncia TI"
                                         title="Ger&ecirc;ncia de TI"
                                         width="50"
                                         height="34" />
                                         <?php
                                         if ($_SERVER['HTTP_HOST'] == 'dev.cefetbambui.edu.br' || $host != 'dados.bambui.ifmg.edu.br')
                                             echo '&nbsp;&nbsp;&nbsp;&nbsp;<strong>Servidor de BD: </strong>'. $host;
                                         ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="23" class="menu">
                        <a href="index.php">
                            <img src="../public/images/home_icon.gif"
                                 alt="P&aacute;gina inicial"
                                 title="P&aacute;gina inicial" />
                        </a>
                    </td>
                    <td width="526" class="menu">

                        <ul id="MenuBar1" class="MenuBarHorizontal">
                            <li><a class="MenuBarItemSubmenu" href="#">Sistema</a>
                                <ul>
                                    <li>
                                        <a href="#" class="MenuBarItemSubmenu">Exportar</a>
                                        <ul>
                                            <li>
                                                <a href="exportar/exportar_sistec.php" target="frame2">SISTEC</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#" class="MenuBarItemSubmenu">Configura&ccedil;&otilde;es</a>
                                        <ul>
                                            <li>
                                                <a href="sagu/academico/consulta_periodos.phtml"
                                                   target="frame2">Per&iacute;odos</a>
                                            </li>
                                            <li>
                                                <a href="sagu/academico/consulta_inclui_departamentos.phtml"
                                                   target="frame2">Departamentos</a>
                                            </li>
                                            <li>
                                                <a href="sagu/academico/areas_ensino.phtml"
                                                   target="frame2">&Aacute;rea de ensino</a>
                                            </li>
                                            <li>
                                                <a href="aluno/config_area_aluno.php"
                                                   target="frame2">&Aacute;rea do aluno</a>
                                            </li>
                                            <li>
                                                <a href="sagu/academico/carimbos.phtml"
                                                   target="frame2">Carimbos</a>
                                            </li>
                                             <li>
                                                <a href="sagu/generico/configuracao_empresa.phtml"
                                                   target="frame2">Empresas</a>
                                            </li>
                                            <li>
                                                <a href="sagu/generico/campus_inclui.phtml"
                                                   target="frame2">Campus</a>
                                            </li> 
                                            <?php if($acl->has_access(dirname(__FILE__).'/usuarios/index.php', $conn)) : ?>
                                            <li>
                                                <a href="usuarios/index.php"
                                                   target="frame2">Usu&aacute;rios do sistema</a>
                                            </li>
                                            <?php else : ?>
                                            <li>
                                                <a style="color: gray;">Usu&aacute;rios do sistema</a>
                                            </li>
                                            <?php endif;?>
                                            <li>
                                                <a href="papeis/index.php"
                                                   target="frame2">Permiss&otilde;es</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li><a class="MenuBarItemSubmenu" href="#">Cadastros</a>
                                <ul>
                                    <li>
                                        <a href="#" class="MenuBarItemSubmenu">Gen&eacute;rico</a>
                                        <ul>
                                            <li>
                                                <a href="sagu/generico/paises_inclui.phtml"
                                                   target="frame2">Pa&iacute;ses</a>
                                            </li>
                                            <li>
                                                <a href="sagu/generico/consulta_inclui_estados.phtml"
                                                   target="frame2">Estados</a>
                                            </li>
                                            <li>
                                                <a href="sagu/generico/consulta_cidades.phtml"
                                                   target="frame2">Cidades</a>
                                            </li>
                                            <li>
                                                <a href="sagu/generico/consulta_inclui_instituicoes.phtml"
                                                   target="frame2">Institui&ccedil;&otilde;es</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="sagu/academico/consulta_inclui_pessoa.phtml"
                                           target="frame2">Pessoas F&iacute;sicas</a>
                                    </li>
                                    <li>
                                        <a href="sagu/academico/consulta_inclui_contratos.phtml"
                                           target="frame2">Contratos</a>
                                    </li>
                                    <li>
                                        <a href="colacao_grau/index.php"
                                           target="frame2">Cola&ccedil;&atilde;o de grau</a>
                                    </li>
                                    <li>
                                        <a href="professores/index.php"
                                           target="frame2">Professores</a>
                                    </li>
                                    <li>
                                        <a href="sagu/academico/coordenadores.phtml"
                                           target="frame2">Coordenadores</a>
                                    </li>
                                    <li>
                                        <a href="setor/index.php"
                                           target="frame2">Setores</a>
                                    </li>
                                    <li>
                                        <a href="cargo/index.php"
                                           target="frame2">Cargos</a>
                                    </li>
                                    <li>
                                        <a href="motivos/index.php"
                                           target="frame2">Motivos</a>
                                    </li>
                                    <li>
                                        <a href="salas/index.php"
                                           target="frame2">Salas</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="MenuBarItemSubmenu">Matrizes</a>
                                <ul>
                                    <li>
                                        <a href="sagu/academico/consulta_cursos.phtml" target="frame2">Cursos</a>
                                    </li>
                                    <li>
                                        <a href="sagu/academico/consulta_disciplinas.phtml"
                                           target="frame2">Disciplinas</a>
                                    </li>
                                    <li>
                                        <a href="sagu/academico/consulta_inclui_cursos_disciplinas.phtml"
                                           target="frame2">Cursos / Disciplinas</a>
                                    </li>
                                    <li>
                                        <a href="sagu/academico/consulta_inclui_pre_requisito.phtml"
                                           target="frame2">Pr&eacute;-requisitos</a>
                                    </li>
                                    <li>
                                        <a href="sagu/academico/consulta_disciplinas_equivalentes.phtml"
                                           target="frame2">Disciplinas Equivalentes</a>
                                    </li>
                                </ul>
                            </li>
                            <li><a href="#" class="MenuBarItemSubmenu">Matr&iacute;culas</a>
                                <ul>
                                    <li>
                                        <a href="sagu/academico/disciplina_ofer.phtml"
                                           target="frame2">Disciplinas Oferecidas</a>
                                    </li>
                                    <li>
                                        <a href="matricula/matricula_aluno.php"
                                           target="frame2">Matr&iacute;cula</a>
                                    </li>
                                    <li>
                                        <a href="dispensa_disciplina/dispensa_aluno.php"
                                           target="frame2">Dispensa de Disciplina</a>
                                    </li>
                                    <li>
                                        <a href="matricula/remover_matricula/filtro.php"
                                           target="frame2">Excluir Matr&iacute;cula</a>
                                    </li>
                                    <li>
                                        <a href="web_diario/secretaria/diarios_secretaria.php"
                                           target="frame2">Di&aacute;rios</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="relatorios/menu.php"
                                   title="relatorios"
                                   target="frame2">Relat&oacute;rios</a>
                            </li>
                            <li>
                                <a href="../index.php">Sair</a>
                            </li>
                        </ul>
                    </td>
                    <td width="193" class="menu">
                        <span class="texto1">
                            <img src="../public/images/icons/bola_verde.gif" width="10" height="10" />
                            <strong><?=$sa_usuario?></strong>&nbsp;&nbsp;
                        </span>
                        <a href="usuarios/alterar_senha.php" target="frame2">Alterar senha</a>
                    </td>
                </tr>
            </table>

            <iframe id='frame2'
                    name='frame2'
                    src='diagrama.php'
                    onload='iframeAutoHeight(this)'
                    frameborder='0'></iframe>
        </div>
        <script type="text/javascript">
            <!--
            var MenuBar1 = new Spry.Widget.MenuBar("MenuBar1", {imgDown:"../lib/SpryAssets/SpryMenuBarDownHover.gif", imgRight:"../lib/SpryAssets/SpryMenuBarRightHover.gif"});
            //-->
        </script>
    </body>
</html>
