<?php

require_once($BASE_DIR .'core/login/acl.php');
$acl = new acl();
if(!$acl->has_access($ACL_FILE, $conn)) {
    exit ('<br /><h4>Você não tem permissão para acessar esta função!</h4>');
}


?>
