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

// VERIFICA SE O DIARIO FOI PREVIAMENTE MARCADO COMO PREENCHIDO
$sql1 = "SELECT
            fl_digitada
		 FROM
			disciplinas_ofer
         WHERE
            id = $diario_id;";

$fl_digitada = $conn->get_one($sql1);

if ($fl_digitada == 't') {

  // VALIDA A NOTA FINAL DO ALUNO QUE DEVE ESTAR EM INTERVALOS DE 0,5 PONTOS
	$notas_fora_do_intervalo = 0;

	$sql_notas_finais = "SELECT
												nota_final
											FROM
												matricula
											WHERE
                        ref_motivo_matricula = 0 AND
												ref_disciplina_ofer = $diario_id;";

	$notas_finais = $conn->get_col($sql_notas_finais);

	foreach ($notas_finais as $nota) {

			if ((abs($nota) - (int)(abs($nota)) == 0.5) || (abs($nota) - (int)(abs($nota)) == 0))
				continue;

			$notas_fora_do_intervalo++;
	}


  if ($notas_fora_do_intervalo > 0) {
	  $mensagem_fechado = 'Existem '. $notas_fora_do_intervalo . ' notas fora do intervalo de 0,5 pontos.\n';
	  $mensagem_fechado .= 'O professor(a) deve ajustar esta(s) nota(s) antes de marcá-lo como fechado.\n';
	  $mensagem_fechado .= 'DICA: Utilizar a "Nota Extra" para ajustar esta(s) nota(s).\n';
	  $mensagem_fechado .= '\nA operação foi cancelada!';
  }
  else {

	  // VERIFICA SE AS COMPETENCIAS DA DISCIPLINA FOI PREENCHIDA
		$sql1 = "SELECT
            competencias,
            observacoes
               FROM
               disciplinas_ofer
               WHERE
               id = $diario_id;";

    $diario_info = $conn->get_row($sql1);

    $competencias = $diario_info['competencias'];
    $observacoes = $diario_info['observacoes'];

	  if (empty($competencias) || empty($observacoes)) {
		  $mensagem_fechado = 'As Competências Desenvolvidas e/ou as Observações não foram informadas no campo apropriado!\n\n';
		  $mensagem_fechado .= 'O professor(a) deve informar as Competências Desenvolvidas e/ou Observações da disciplina\nantes de marcar o diário como fechado.\n';
		  $mensagem_fechado .= '\nA operação foi cancelada!';
	  }
	  else {

	   // VERIFICA SE A CARGA HORÁRIA LANÇADA É MAIOR OU IGUAL A PREVISTA
	   $sql_carga_horaria = "SELECT CAST(get_carga_horaria_realizada($diario_id) AS INTEGER),CAST(get_carga_horaria(get_disciplina_de_disciplina_of($diario_id)) AS INTEGER);";
           $carga_horaria = $conn->get_row($sql_carga_horaria);

        if ($carga_horaria['get_carga_horaria_realizada'] < $carga_horaria['get_carga_horaria']) {

          $mensagem_fechado = 'A carga horária realizada está menor que a carga horária prevista!\n\n';
					$mensagem_fechado .= 'O professor(a) deve lançar das chamadas faltantes para completar a carga horária.\n';
					$mensagem_fechado .= '\nA operação foi cancelada!';

        }
        else {

	        // MARCA O DIARIO COMO FECHADO
	        $sql1 = "UPDATE disciplinas_ofer
					    SET
							  fl_finalizada = 't'
					    WHERE
							  id = $diario_id;";

	        $conn->Execute($sql1);

	        $mensagem_fechado = 'Diário fechado com sucesso!';

        }
	  }
  }

}
else {
	$mensagem_fechado = 'Este diário ainda não foi preenchido, por isso não pode ser fechado.\n';
	$mensagem_fechado .= 'A operação foi cancelada!';
}

if ($_SESSION['sa_modulo'] == 'sa_login') {

	exit('<script language="javascript" type="text/javascript">
				alert(\''. $mensagem_fechado .'\');
				window.opener.location.reload();
				setTimeout("self.close()",120); </script>');

}

?>
