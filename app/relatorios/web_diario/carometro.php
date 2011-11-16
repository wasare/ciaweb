<?php

require_once(dirname(__FILE__). '/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/reports/header.php');
//require_once($BASE_DIR .'core/number.php');
//require_once($BASE_DIR .'core/situacao_academica.php');

$conn = new connection_factory($param_conn);
$header  = new header($param_conn);

$diario_id = (int) $_GET['diario_id'];

if(!is_numeric($diario_id))
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario invalido!");window.close();</script>');

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(isset($_SESSION['sa_modulo']) && $_SESSION['sa_modulo'] == 'web_diario_login') {
  if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
  }
}
// ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //

if (!existe_matricula($diario_id)) {
  exit('<script language="javascript">window.alert("Este diário ainda não possue alunos matriculados!"); javascript:window.close(); </script>');
}

$sql_alunos = "SELECT
         b.nome, a.ref_pessoa
         FROM matricula a, pessoas b
         WHERE
            (a.dt_cancelamento is null) AND
            a.ref_disciplina_ofer = $diario_id AND
            a.ref_pessoa = b.id AND
            a.ref_motivo_matricula = 0
         ORDER BY lower(to_ascii(nome,'LATIN1'));" ;


$alunos = $conn->get_all($sql_alunos);

?>

<html>
  <head>
  <title>
    <?=$IEnome?> - car&ocirc;metro
  </title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

  <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">

  <style media="print">
  <!--
  .nao_imprime {display:none}

  table.papeleta {
    font: 0.7em verdana, arial, tahoma, sans-serif;
    border: 0.0015em solid;
    border-collapse: collapse;
    border-spacing: 0px;
  }

  .papeleta td {
    font: 0.7em verdana, arial, tahoma, sans-serif;
    border: 0.0015em solid;
    padding: 2px;
    border-collapse: collapse;
    border-spacing: 1px;
  }
  -->
  </style>

  </head>

<body>
<font size="2">

<div align="left">
     <?=$header->get_empresa($PATH_IMAGES, $IEnome)?>
</div>


<?=papeleta_header($diario_id)?>

</font>

<br />
<br />

<table cellspacing="0" cellpadding="0" class="papeleta">
	<tr>

<?php

$i = 0;

foreach($alunos as $aluno) :
   
?>

    <td align="center"><img title="<?=$aluno['nome']?>" src="<?=$BASE_URL?>core/pessoa_foto.php?id=<?=$aluno['ref_pessoa']?>&diario_id=<?=$diario_id?>"
			alt="<?=$aluno['nome']?>" border="1" width="120" /><br />
			<?=$aluno['nome']?>
		</td>
	
<?php   
    $i++;
  
    if ($i % 3 == 0) echo '</tr><tr>';
  
  endforeach;
  
  if ($i % 3 != 0) echo '</tr>';
  
?>



</table>

<hr width="60%" size="1" align="left" color="#FFFFFF">

<br /><br />
<div class="nao_imprime">
<a href="#" onclick="javascript:window.close();">Fechar</a>
</div>
<br /><br />
</body>
</html>

