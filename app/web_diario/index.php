<?php

require_once(dirname(__FILE__) .'/../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/login/acl.php');

$conn = new connection_factory($param_conn);

// VERIFICA SE O USUARIO TEM DIREITO DE ACESSO
$acl = new acl();

// @todo melhorar o retorno ao usuário usando um metódo de logout
if (!$acl->has_role($sa_ref_pessoa, $PAPEIS_WEB_DIARIO, $conn)) {
  exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.history.back(1);</script>');
}
// ^ VERIFICA SE O USUARIO TEM DIREITO DE ACESSO ^ //


// @todo verificar se quem acessou possui pelo menos um diário ou coordena pelo menos um curso

unset($_SESSION['conteudo']);
unset($_SESSION['flag_falta']);

$is_coordenador = FALSE;
$is_professor = FALSE;

// RECUPERA INFORMACOES SOBRE OS PERIODOS DO PROFESSOR
$qry_periodo = 'SELECT DISTINCT o.ref_periodo,p.descricao FROM disciplinas_ofer o, disciplinas_ofer_prof dp, periodos p WHERE dp.ref_professor = '. $sa_ref_pessoa .' AND o.id = dp.ref_disciplina_ofer AND p.id = o.ref_periodo ORDER BY ref_periodo DESC LIMIT 1;';

$periodo = $conn->get_row($qry_periodo);

if(count($periodo) > 0) {
	$_SESSION['web_diario_periodo_id'] = isset($_SESSION['web_diario_periodo_id']) ? $_SESSION['web_diario_periodo_id'] : $periodo['ref_periodo'];
	$is_professor = TRUE;
}
// ^ RECUPERA INFORMACOES SOBRE OS PERIODOS DO PROFESSOR ^ //

// RECUPERA INFORMACOES SOBRE OS PERIODOS DO COORDENADOR
$sql_coordena = 'SELECT DISTINCT o.ref_periodo,p.descricao FROM disciplinas_ofer o, periodos p WHERE  o.ref_periodo = p.id AND o.ref_curso IN (SELECT DISTINCT ref_curso FROM coordenador WHERE ref_professor = '. $sa_ref_pessoa .') ORDER BY ref_periodo DESC LIMIT 1;';

$periodo_coordenacao = $conn->get_row($sql_coordena);

if(count($periodo_coordenacao) > 0) {
    $_SESSION['web_diario_periodo_coordena_id'] = isset($_SESSION['web_diario_periodo_coordena_id']) ? $_SESSION['web_diario_periodo_coordena_id'] : $periodo_coordenacao['ref_periodo'];
	$is_coordenador = TRUE;
}

if ($is_coordenador) {
	
	// ^ RECUPERA INFORMACOES SOBRE OS PERIODOS DO COORDENADOR ^ //

	// RECUPERA INFORMACOES SOBRE OS CURSOS DO COORDENADOR
	$sql_coordena = 'SELECT DISTINCT ref_curso
                    FROM coordenador
                    WHERE
                    ref_professor = '. $sa_ref_pessoa .';';

	$cursos_coordenacao = $conn->get_col($sql_coordena);

	if(count($cursos_coordenacao) > 0) 	{
		$is_coordenador = TRUE;
		$_SESSION['web_diario_cursos_coordenacao'] = $cursos_coordenacao;	
	}

	// ^ RECUPERA INFORMACOES SOBRE OS PERIODOS E CURSOS DO COORDENADOR ^ //
}

// recurso para carregar a página padrão
$class_coordenacao = ($is_coordenador && !$is_professor) ? ' class="active"' : '';
$class_diarios = ($is_professor) ? ' class="active"' : '';

?>

<html>
<head>
<title><?=$IEnome?> - web di&aacute;rio</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">

<script type="text/javascript" src="<?=$BASE_URL .'lib/prototype.js'?>"> </script>
<script type="text/javascript" src="<?=$BASE_URL .'lib/tabbed_pane.js'?>"> </script>
<script type="text/javascript" src="<?=$BASE_URL .'app/web_diario/web_diario.js'?>"> </script>
<script type="text/javascript" src="<?=$BASE_URL .'lib/event.simulate.js'?>"> </script>

</head>

<body>

<div align="center">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="50" valign="middle">
        <a href="index.php">
          <img src="<?=$BASE_URL .'public/images/sa_icon.png'?>" alt="Principal" width="40" height="34" border="0" />
        </a>
     </td>
     <td width="230">
       <a href="index.php" class="titulo_topo">Web Di&aacute;rio</a>
     </td>
     <td valign="top">
        <div align="right" style="font-size: 0.8em;">
          &nbsp;
        </div>
     </td>    
     <td valign="middle">&nbsp;
       <a href="<?=$IEurl?>" target="_blank">
        <img src="<?=$BASE_URL .'public/images/if.jpg'?>" alt="IFSP - Campus Caraguatatuba" title="IFSP - Campus Caraguatatuba" border="0" />
       </a>&nbsp;&nbsp;
     </td>
        <?php
              if ($host != '127.0.0.1' || $host != 'localhost') {
				echo '<td>';
				echo '&nbsp;&nbsp;&nbsp;&nbsp;<strong>Servidor de BD: </strong>'. $host;
				echo '</td>';
            }
        ?>

   </tr>
</table>

<div class="tabbed-pane" align="center">
    <ol class="guias">
      <li style="font-size: 0.65em;">
        <a href="index.php">
          <img src="<?=$BASE_URL .'public/images/home_icon.gif'?>" border="0" alt="P&aacute;gina inicial" title="P&aacute;gina inicial" />
        </a>
      </li>
		<?php
			if ($is_professor)
				echo '<li><a href="#" '. $class_diarios .' id="pane_diarios">Meus di&aacute;rios</a></li>';

            if ($is_coordenador)
                echo '<li><a href="#" '. $class_coordenacao .' id="pane_coordenacao">Coordena&ccedil;&atilde;o</a></li>';
        ?>
        
		<li><a href="#" id="pane_ferramentas">Ferramentas</a></li>
        <li><a href="<?=$BASE_URL .'index.php'?>">Sair</a></li>
        <li>&nbsp;&nbsp;&nbsp;
          <img src="<?=$BASE_URL .'public/images/icons/bola_verde.gif'?>" width="10" height="10" alt="Usu&aacute;rio <?=$sa_usuario?>" title="Usu&aacute;rio <?=$sa_usuario?>" />
          <?=$sa_usuario?>
        </li>
    </ol>
   
    <div id="pane_container" class="tabbed-container">
        <div id="pane_overlay" class="overlay" style="display: none">
            <h2> <img src="<?=$BASE_URL .'public/images/carregando.gif'?>" alt="carregando..." /> &nbsp;&nbsp; carregando&#8230; </h2>
        </div>
        <div id="web_guias" class="pane"></div>
    </div>
</div>

</div>


<script language="javascript" type="text/javascript">

var thePane = new TabbedPane('web_guias',
    {
        <?php
			if ($is_professor)
                echo "'pane_diarios': 'professor/diarios_professor.php',";

            if ($is_coordenador)
                echo "'pane_coordenacao': 'coordenacao/cursos_coordenacao.php',";
        ?>
        'pane_ferramentas': 'ferramentas.php'
    },
    {
        onClick: function(e) {
            $('pane_overlay').show();
        },
       
        onSuccess: function(e) {
            $('pane_overlay').hide();
            e = unescape(e.responseText);
            
        },
        contentType: 'text/html',
        encoding: 	'UTF-8'
    });

</script>

</body>
</html>
