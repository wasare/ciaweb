<?php

require_once('matriculados.php');
require_once("../../../lib/excel.inc.php");

$gerar= new sql2excel($colunas,$sql, $conn->adodb);

?>