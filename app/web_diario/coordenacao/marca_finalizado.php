<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

$diario_id = (int) $_GET['diario_id'];
$operacao = $_GET['do'];

if (!is_diario($diario_id))
        exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario invalido!"); window.close();</script>');

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(isset($_SESSION['sa_modulo']) && $_SESSION['sa_modulo'] == 'web_diario_login') {
  if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
  }
  // ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //
}

// VERIFICA SE O DIARIO FOI PREVIAMENTE CONCLUIDO
$sql1 = "SELECT
            fl_digitada
		 FROM
			disciplinas_ofer
         WHERE
            id = $diario_id;";

$fl_digitada = $conn->get_one($sql1);

if ($fl_digitada == 't') {
	
	// MARCA O DIARIO COMO CONCLUIDO
	$sql1 = "UPDATE disciplinas_ofer
					 SET
							fl_finalizada = 't' 
					 WHERE  
							id = $diario_id;";
	
	$conn->Execute($sql1);
	
	$mensagem_finalizado = 'Diário finalizado com sucesso!';
}
else {
	$mensagem_finalizado = 'Este diário ainda não foi concluído, por isso não pode ser finalizado.\n';
	$mensagem_finalizado .= 'A operação foi cancelada!';
}

if ($_SESSION['sa_modulo'] == 'sa_login') {

	exit('<script language="javascript" type="text/javascript">
				alert(\''. $mensagem_finalizado .'\');
				window.opener.location.reload();
				setTimeout("self.close()",450); </script>');

}
	
?>
