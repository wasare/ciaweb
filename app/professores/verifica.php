<?php

require_once("../../app/setup.php");

$conn = new connection_factory($param_conn);

if($_POST['id'] != ''){
    $sql = "SELECT COUNT(id) FROM usuario WHERE nome = '".$_POST['id']."';";

    $count = $conn->get_one($sql);

    if($count != 0){
        echo '<font color="red">Usu&aacute;rio indispon&iacute;vel.</font>';
    }else {
        echo '<font color="green">Usu&aacute;rio dispon&iacute;vel.</font>';
    }
}

?>