<?php

require_once(dirname(__FILE__) .'/../setup.php');
require_once($BASE_DIR .'core/login/acl.php');
require_once($BASE_DIR .'core/date.php');

$conn = new connection_factory($param_conn);

// VERIFICA SE O USUARIO TEM DIREITO DE ACESSO
$acl = new acl();
$papeis = $acl->get_roles($sa_ref_pessoa, $conn);

if (count(array_intersect($papeis, $PAPEIS_WEB_DIARIO)) == 0) {
  exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.history.back(1);</script>');
}
// ^ VERIFICA SE O USUARIO TEM DIREITO DE ACESSO ^ /

?>

<html>
<head>
<title><?=$IEnome?> - web di&aacute;rio</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">

<script type="text/javascript" src="<?=$BASE_URL .'lib/prototype.js'?>"> </script>

</head>

<body>

<div align="left">

<h5>clique nos links abaixo para acessar as funções desejadas</h5>
<br />

<span><a href="#" title="consultar aluno" id="consulta_aluno">Consultar aluno</a></span>
&nbsp;&nbsp;
<div id="consulta_aluno_pane" style="display:none; text-align:left;">
  <br />
<form name="pesquisa_aluno" id="pesquisa_aluno" method="post" action="">
  <strong> Matr&iacute;cula ou nome do aluno:</strong> &nbsp;<input name="campo_aluno" id="campo_aluno" type="text" maxlength="30" size="15" />
   <input type="button" name="envia_pesquisa_aluno" id="envia_pesquisa_aluno" value="Consultar" onclick="enviar_diario('pesquisa_aluno',null,null,'<?=$BASE_URL?>','<?=$IEnome?>');" />
 </form>
<br />
</div>
<br />
<br />

<span><a href="#" id="trocar_senha" onclick="abrir('<?=$IEnome?>' + '- web diário', 'requisita.php?do=troca_senha');">Alterar senha de acesso</a></span>
&nbsp;&nbsp;
<br />
<br />

<span><a href="#" title="log de acesso" id="log_acesso">Log de acessos</a></span>
&nbsp;&nbsp;
<div id="log_acesso_pane" style="display:none; text-align:left; font-size: 0.9em;">
<br />

<h5>&Uacute;ltimos 20 acessos</h5>

<br />

<?php

  $sql1 = "SELECT
              usuario, data, hora
           FROM
              diario_log
           WHERE
                usuario = '". $sa_usuario ."' AND
                data <= '". date("d/m/Y") ."' AND
                status = 'LOGIN ACEITO'
          ORDER BY data DESC, hora DESC LIMIT 20;";

  $logs_acesso = $conn->get_all($sql1);

  if (count($logs_acesso) > 0) :

?>
<table cellspacing="3" cellpadding="3" class="papeleta">
<tr bgcolor="#CCCCCC">
      <th><b>Usu&aacute;rio</b></th>
      <th><b>Data</b></th>
      <th><b>Hora</b></th>
    </tr>

<?php
	foreach($logs_acesso as $linha) :
		$st = ($st == '#FFFFFF') ? '#FFFFF0' : '#FFFFFF';
?>
      <tr bgcolor="<?=$st?>">
        <td><?=$linha['usuario']?></td>
        <td align="center"><?=date::convert_date($linha['data'])?></td>
        <td align="center"><?=$linha['hora']?></td>
	  </tr>
<?php
    endforeach;
  else:
    echo 'Não foi encontrado nenhum registro';
  endif;
?>
</table>
<br />
<br />
</div>
<br />
<br />

<span><a href="#" title="acesso aos programas" id="acessa_programas">Programas</a></span>
&nbsp;&nbsp;
<div id="programas_pane" style="display:none; text-align:left; font-size: 0.85em;">
<br />
<h5>programas para leitura/impress&atilde;o do caderno de chamada</h5>
<br />
<h5><a href="<?=$BASE_URL .'public/programas/gs851w32.exe'?>">1 - GhostScript</a></h5>
	<br />
<h5><a href="<?=$BASE_URL .'public/programas/gsv48w32.exe'?>">2 - GhostView</a></h5>
<br />
<br />
</div>
<br /><br />
<br /><br />
</div>
  
<script language="javascript" type="text/javascript">
    $('consulta_aluno').observe('click', function() { $('consulta_aluno_pane').toggle(); });
    $('log_acesso').observe('click', function() { $('log_acesso_pane').toggle(); });
    $('acessa_programas').observe('click', function() { $('programas_pane').toggle(); });

    $('campo_aluno').observe('keydown', function (e) {
        if ( e.keyCode == 13 ) {
            $('envia_pesquisa_aluno').simulate('click');
            e.stop();
		}
	});

</script>

</body>
</html>

