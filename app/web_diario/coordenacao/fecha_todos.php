<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

$diario_id = (int) $_GET['diario_id'];
$operacao = (string) $_GET['do'];

if (!is_diario($diario_id))
        exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario invalido!"); window.close();</script>');

// @fixme nao fechar diarios sem prefessor
// @fixme verificar direito de acesso: professor ou secretaria

// MARCA O DIARIO COMO PREENCHIDO
$sql = "SELECT COUNT(*) 
			FROM
            disciplinas_ofer
         WHERE
            fl_digitada = 't' AND
            fl_finalizada = 'f' AND
            ref_curso = ". get_curso($diario_id) ." AND
						ref_periodo = periodo_disciplina_ofer($diario_id) AND
            is_cancelada = '0';";


$num_preenchidos = $conn->get_one($sql);

if($num_preenchidos == 0) {	
  echo '<script type="text/javascript">alert(\'Não existe nenhum diário preenchido para ser fechado!\');window.close();</script>';
}
else {
  $sql1 = "UPDATE
			disciplinas_ofer
         SET
            fl_finalizada = 't' 
         WHERE  
					fl_digitada = 't' AND
          fl_finalizada = 'f' AND
					ref_curso = ". get_curso($diario_id) ." AND
          ref_periodo = periodo_disciplina_ofer($diario_id) AND
          is_cancelada = '0';";

  $conn->Execute($sql1);


  if ($_SESSION['sa_modulo'] == 'sa_login') {

    exit('<script language="javascript" type="text/javascript">
            alert(\''. $num_preenchidos .' diário(s) fechado(s) com sucesso!\');
			window.opener.location.reload();
			setTimeout("self.close()",450);</script>');

  }
  else {
    echo '<script type="text/javascript"> alert(\''. $num_preenchidos .' diário(s) fechado(s) com sucesso!\'); </script>';
  }
}
	
?>
