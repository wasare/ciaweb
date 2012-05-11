<?php

require_once(dirname(__FILE__).'/../setup.php');

$conn = new connection_factory($param_conn);

$sem_senha_sql = "SELECT DISTINCT c.ref_pessoa, prontuario FROM contratos c LEFT OUTER JOIN acesso_aluno aa ON (c.ref_pessoa = aa.ref_pessoa) WHERE senha = '' ORDER BY prontuario;";

$alunos_sem_senha = $conn->get_all($sem_senha_sql);

$cont = 0;
$update_sql = 'BEGIN;<br />';
foreach ($alunos_sem_senha as $aluno) {
  //echo $aluno['ref_pessoa'] .' | '. $aluno['prontuario'] .'<br />';
  $update_sql .= "UPDATE acesso_aluno SET senha = MD5('A". $aluno['prontuario'] ."') WHERE ref_pessoa = ". $aluno['ref_pessoa']  .";<br />";
  $cont++;
}


$update_sql .= "COMMIT;<br /><br />";

echo $update_sql;


echo $cont .' senha(s) precisam ser iniciada(s)!';

?>
