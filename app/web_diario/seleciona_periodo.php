<?php

require_once(dirname(__FILE__) .'/../setup.php');

$conn = new connection_factory($param_conn);

if($_POST['periodo_id']) {
	$_SESSION['web_diario_periodo_id'] = $_POST['periodo_id'];
	echo 'pane_diarios';
}

if($_POST['periodo_coordena_id']) {
    $_SESSION['web_diario_periodo_coordena_id'] = $_POST['periodo_coordena_id'];
	echo 'pane_coordenacao';
}

?>
