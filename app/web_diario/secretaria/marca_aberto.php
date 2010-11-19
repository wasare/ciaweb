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

// MARCA O DIARIO COMO CONCLUIDO
$sql1 = "UPDATE disciplinas_ofer
         SET
            fl_finalizada = 'f'
         WHERE
            id = $diario_id;";

$conn->Execute($sql1);

if ($_SESSION['sa_modulo'] != 'web_diario_login') {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Diario reaberto com sucesso!\');
			window.opener.location.reload();
			setTimeout("self.close()",450);</script>');

}

?>
