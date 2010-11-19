<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

$diario_id = (int) $_GET['diario_id'];

$periodo_id = (string) $_GET['periodo'];
$curso_id = (int) $_GET['curso'];
$diario_id = (int) $_GET['diario_id'];

if (empty($periodo_id) OR $curso_id == 0) {

    if ($diario_id == 0) {
		exit('<script language="javascript">
                window.alert("ERRO! Primeiro informe um período e um curso ou um diário!");
				window.close();
		</script>');
	}

    if (!is_diario($diario_id))
        exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario inexistente ou cancelado!");window.close();</script>');

}

require_once($BASE_DIR .'app/web_diario/secretaria/lista_diarios_secretaria.php');


if (!is_diario($diario_id))
        exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario invalido!"); window.close();</script>');


?>
