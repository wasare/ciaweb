<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

$diario_id = (int) $_GET['diario_id'];
$operacao = (string) $_GET['do'];

if(!is_numeric($diario_id))
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario invalido!");window.close();</script>');

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(isset($_SESSION['sa_modulo']) && $_SESSION['sa_modulo'] == 'web_diario_login') {
  if(!acessa_diario($diario_id,$sa_ref_pessoa)) {
    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
  }
  // ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //
}

// INVERTE A MARCACAO DE ESTADO DO DIARIO
$sql1 = "SELECT
            fl_digitada
		 FROM
			disciplinas_ofer
         WHERE
            id = $diario_id;";

$fl_digitada = $conn->get_one($sql1);

if($fl_digitada === 'f') 
	$flag = 't';
else
	$flag = 'f';

$mensagem_concluido = '';

// VALIDA A NOTA FINAL DO ALUNO QUE DEVE ESTAR EM INTERVALOS DE 0,5 PONTOS
if ($flag == 't') {
	
	$notas_fora_do_intervalo = 0;
	
	$sql_notas_finais = "SELECT
												nota_final
											FROM
												matricula
											WHERE
													ref_disciplina_ofer = $diario_id;";
												
	$notas_finais = $conn->get_col($sql_notas_finais);
	
	foreach ($notas_finais as $nota) {
			
			if ((abs($nota) - (int)(abs($nota)) == 0.5) || (abs($nota) - (int)(abs($nota)) == 0))
				continue;
			
			$notas_fora_do_intervalo++;
	}
}

if ($notas_fora_do_intervalo > 0 && $flag == 't') {
	$mensagem_concluido = 'Existem '. $notas_fora_do_intervalo . ' notas fora do intervalo de 0,5 pontos.\n';
	$mensagem_concluido .= 'Ajuste esta(s) nota(s) antes de marcá-lo como concluído.\nA operação foi cancelada!';
}
else {
	// MARCA/DESMARCA O DIARIO COMO CONCLUIDO
	$sql2 = "UPDATE 
			disciplinas_ofer
         SET
            fl_digitada = '$flag' 
         WHERE  
            id = $diario_id;";

	$conn->Execute($sql2);
	
	$mensagem_concluido = 'Diário marcado / desmarcado com sucesso!';
}

?>
