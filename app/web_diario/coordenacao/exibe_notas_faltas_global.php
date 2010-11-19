<?php
require_once(dirname(__FILE__). '/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

$periodo_id = (string) $_GET['periodo_id'];
$curso_id = (int) $_GET['curso_id'];
$campus = (int) $_GET['campus'];
$turma = (string) $_GET['turma'];

//echo "//Array ( [periodo] => 0902 [campus] => 1 [curso] => 633 [turma] => 1 )";

//die(print_r($_GET));

// VERIFICA SE O USUARIO TEM DIREITO DE ACESSO
$sql_coordena = ' SELECT count(*)
                            FROM coordenador
                            WHERE ref_professor = '. $sa_ref_pessoa .' AND ';

$sql_coordena .= ' ref_curso = '. $curso_id .';';

$coordenacao = $conn->get_one($sql_coordena);

if ($coordenacao == 0) {
  exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.history.back(1);</script>');
}
// ^ VERIFICA SE O USUARIO TEM DIREITO DE ACESSO ^ /

$_POST['periodo'] = $periodo_id;
$_POST['campus'] = $campus;
$_POST['curso'] = $curso_id;
$_POST['turma'] = $turma;

require_once($BASE_DIR .'app/relatorios/notas_faltas_global/notas_faltas_global.php');

?>
