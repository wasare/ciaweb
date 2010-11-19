<?php

require_once(dirname(__FILE__) .'/../../app/setup.php');
require_once($BASE_DIR .'core/number.php');

$conn  = new connection_factory($param_conn);

$sql = "SELECT
            n.nspname AS esquema, c.relname AS tabela, t.typname AS tipo
        FROM
            pg_class c
        LEFT JOIN
            pg_namespace n ON n.oid = c.relnamespace
        LEFT JOIN
            pg_type t ON t.oid = c.reltype
        WHERE
             c.relkind = 'r' AND
             n.nspname IN ('financeiro','prato','public', 'saed', 'sicad');";


$tables = $conn->get_all($sql);

$num_registros = array();

foreach ($tables as $tb) {
  $sql = 'SELECT COUNT(*) FROM "'. $tb['esquema'] .'"."'. $tb['tabela'] .'";';
  $num_registros[$tb['esquema']] +=  $conn->get_one($sql);
}

arsort($num_registros, SORT_NUMERIC);

echo "<h3>N&uacute;mero de Registros no banco do Sistema Acadêmico <br /><br /> Esquemas</h3>";
foreach($num_registros as $key => $value)
  echo '<h4>'. $key .': <font color="red">'. number::numeric2decimal_br($value,0) .'</font></h4>';

?>
