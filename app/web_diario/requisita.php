<?php

require_once(dirname(__FILE__) .'/../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

$diario_id = (int) $_GET['id'];
$operacao = (string) $_GET['do'];

$_SESSION['web_diario_do'] = $operacao;

if (($diario_id == 0 || empty($operacao)) && $operacao != 'troca_senha' && $operacao != 'pesquisa_aluno') {
    exit('<script language="javascript" type="text/javascript">
                window.alert("ERRO! Dados invalidos!!");
                window.close();
    </script>');
}

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if($operacao != 'lista_diarios_coordenacao' && $operacao != 'troca_senha' && $operacao != 'pesquisa_aluno' && $_SESSION['sa_modulo'] == 'web_diario_login') {

    if (!is_diario($diario_id))
        exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario invalido!"); window.close();</script>');

    if(!acessa_diario($diario_id,$sa_ref_pessoa)) {
        exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
    }
}
// ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //

// REGISTRA VARIAVEIS ENVIAR COM SE FOSSE VIA FORMULARIO
$_GET['diario_id'] = $diario_id;
$_POST['diario_id'] = $diario_id;


$menu_superior = '';

if ($_SESSION['sa_modulo'] == 'web_diario_login') {
    $menu_superior = '<div class="nao_imprime">';

    if(isset($_SESSION['web_diario_periodo_id']))
        $menu_superior .= '<a href="#" onclick="window.opener.reload_parent_pane(\'pane_diarios\');window.close();">Meus di&aacute;rios</a>&nbsp;|&nbsp;';

    if(isset($_SESSION['web_diario_periodo_coordena_id']))
        $menu_superior .= '<a href="#" onclick="window.opener.reload_parent_pane(\'pane_coordenacao\');window.close();">Coordena&ccedil;&atilde;o</a>&nbsp;|&nbsp;';


    $menu_superior .= '<a href="#" onclick="window.opener.location.href=\''. $BASE_URL .'\';window.close();">Sair</a>&nbsp;&nbsp;&nbsp;&nbsp;';
    $menu_superior .= '<img src="'. $BASE_URL .'public/images/icons/bola_verde.gif" width="10" height="10" />&nbsp;' . $sa_usuario .'&nbsp;&nbsp;';

    $menu_superior .= '<br /></div>';
}


?>

<html>
    <head>
        <title><?=$IEnome?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
    </head>
    <body>
        <?=$menu_superior?>
        <br />
        <?php
        // OPERACOES COM ALTERACAO DE DADOS
        if($operacao == 'notas') {
            if(!is_inicializado($diario_id)) {
                if (ini_diario($diario_id)) {
                    echo '<script type="text/javascript">window.alert("Diario iniciado com sucesso!"); </script>';
                }
                else {
                    // @todo: informar ao administrador/desenvolvedor quando ocorrer erro
                    echo '<script language=javascript> window.alert("Falha ao inicializar o diario!"); window.close(); </script>';
                    exit;
                }
            }
            require_once($BASE_DIR .'app/web_diario/professor/notas/lanca_notas.php');
            exit;
        }

        if($operacao == 'chamada') {
            require_once($BASE_DIR .'app/web_diario/professor/chamada/chamadas.php');
            exit;
        }

        if($operacao == 'altera_chamada') {
            require_once($BASE_DIR .'app/web_diario/professor/chamada/faltas.php');
            exit;
        }


        if($operacao == 'exclui_chamada') {
            require_once($BASE_DIR .'app/web_diario/professor/chamada/exclui_chamada.php');
            exit;
        }

        if($operacao == 'marca_diario') {
            echo papeleta_header($diario_id);
            require_once($BASE_DIR .'app/web_diario/professor/marca_concluido.php');
            echo '<br />';
            echo '<script language="javascript" type="text/javascript">
			alert(\'Diario marcado / desmarcado com sucesso!\');
			window.opener.location.reload();
			setTimeout("self.close()",450); </script>';
            exit;
        }

        if($operacao == 'troca_senha') {
            require_once($BASE_DIR .'app/usuarios/alterar_senha.php');
            exit;
        }

// OPERACAO DA COORDENACAO

        if($operacao == 'marca_finalizado') {
            echo papeleta_header($diario_id);
            require_once($BASE_DIR .'app/web_diario/coordenacao/marca_finalizado.php');
            echo '<br />';
            exit('<script language="javascript" type="text/javascript">
			alert(\'Diario finalizado com sucesso!\');
			window.opener.location.reload();
			setTimeout("self.close()",450); </script>');
        }

        if($operacao == 'finaliza_todos') {
            require_once($BASE_DIR .'app/web_diario/coordenacao/finaliza_todos.php');
            echo '<br />';
            exit('<script language="javascript" type="text/javascript">
			window.opener.location.reload();
			setTimeout("self.close()",450); </script>');
        }

// ^ OPERACOES COM ALTERACAO DE DADOS   ^ //

        if($operacao == 'lista_diarios_coordenacao') {
            unset($_GET['diario_id']);
            unset($_POST['diario_id']);
            $_GET['curso_id'] = $diario_id;
            $_GET['periodo_id'] = $_SESSION['web_diario_periodo_coordena_id'];
            require_once($BASE_DIR .'app/web_diario/coordenacao/lista_diarios_coordenacao.php');
            exit;
        }

        if($operacao == 'pesquisa_diario_coordenacao') {
            require_once($BASE_DIR .'app/web_diario/coordenacao/pesquisa_diario_coordenacao.php');
            exit;
        }
// ^ OPERACAO DA COORDENACAO ^


// RELATORIOS
        if($operacao == 'papeleta') {
            require_once($BASE_DIR .'app/relatorios/web_diario/papeleta.php');
            exit;
        }

        if($operacao == 'papeleta_completa') {
            require_once($BASE_DIR .'app/relatorios/web_diario/papeleta_completa.php');
            exit;
        }

        if($operacao == 'faltas_completo') {
            require_once($BASE_DIR .'app/relatorios/web_diario/faltas_completo.php');
            exit;
        }

        if($operacao == 'caderno_chamada') {
            require_once($BASE_DIR .'app/relatorios/web_diario/caderno_chamada.php');
            exit;
        }

        if($operacao == 'conteudo_aula') {
            require_once($BASE_DIR .'app/relatorios/web_diario/conteudo_aula.php');
            exit;
        }

        if($operacao == 'pesquisa_aluno') {
            require_once($BASE_DIR .'app/web_diario/consultas/pesquisa_aluno.php');
            exit;
        }

// ^ RELATORIOS ^ //


        ?>
        <script language="javascript" type="text/javascript">
            window.focus();
        </script>
    </body>
</html>
