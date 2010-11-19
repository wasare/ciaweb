<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

$diario_id = (int) $_GET['diario_id'];

if (!is_diario($diario_id))
        exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario invalido!"); window.close();</script>');

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(isset($_SESSION['sa_modulo']) && $_SESSION['sa_modulo'] == 'web_diario_login') {
  if(!acessa_diario($diario_id,$sa_ref_pessoa)) {
    exit('<script language="javascript" type="text/javascript">
            alert(\'Diário indisponível para consulta!\');
            window.close();</script>');
  }
}
// ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //


// PESQUISA O CURSO E O PERIODO DO DIARIO
$sql = "SELECT ref_curso, ref_periodo
			FROM
            disciplinas_ofer
         WHERE
			id  = $diario_id AND
            is_cancelada = '0';";


$info_diario = $conn->get_row($sql);

if (count($info_diario) == 4) {
  $_GET['curso_id'] = $info_diario['ref_curso'];
  $_GET['periodo_id'] = $info_diario['ref_periodo'];

  require_once($BASE_DIR .'app/web_diario/coordenacao/lista_diarios_coordenacao.php');
}
else {
  exit('<script language="javascript" type="text/javascript">
            alert(\'Diário indisponível para consulta!\');
            window.close();</script>');
}


	
?>
