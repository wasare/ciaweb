<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

$diario_id = (int) $_GET['diario_id'];
$operacao = (string) $_GET['do'];

if (!is_diario($diario_id))
        exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario invalido!"); window.close();</script>');

// @fixme nao fechar diarios sem prefessor
// @fixme verificar direito de acesso: professor ou secretaria

// MARCA O DIARIO COMO PREENCHIDO
$sql = "SELECT DISTINCT id
			FROM
            disciplinas_ofer
         WHERE
            fl_digitada = 't' AND
            fl_finalizada = 'f' AND
            ref_curso = ". get_curso($diario_id) ." AND
						ref_periodo = periodo_disciplina_ofer($diario_id) AND
            is_cancelada = '0';";


$diarios_preenchidos = $conn->get_col($sql);
$num_preenchidos = count($diarios_preenchidos);
$num_fechados = 0;

if($num_preenchidos == 0) {	
  echo '<script type="text/javascript">alert(\'Não existe nenhum diário preenchido para ser fechado!\');window.close();</script>';
}
else {

  $diarios_em_aberto = array();
  $sql_fecha_diarios = 'BEGIN;';
  
  foreach($diarios_preenchidos as $diario) {
  
    // VALIDA A NOTA FINAL DO ALUNO QUE DEVE ESTAR EM INTERVALOS DE 0,5 PONTOS
	  $notas_fora_do_intervalo = 0;

	  $sql_notas_finais = "SELECT
												nota_final
											FROM
												matricula
											WHERE
													ref_disciplina_ofer = $diario;";

	  $notas_finais = $conn->get_col($sql_notas_finais);

	  foreach ($notas_finais as $nota) {

			if ((abs($nota) - (int)(abs($nota)) == 0.5) || (abs($nota) - (int)(abs($nota)) == 0))
				continue;

			$notas_fora_do_intervalo++;
	  }


    if ($notas_fora_do_intervalo > 0) {
      $diarios_em_aberto[] = $diario;
    }
    else {

	    // VERIFICA SE AS COMPETENCIAS DA DISCIPLINA FOI PREENCHIDA
		  $sql1 = "SELECT
            competencias,
            observacoes
               FROM
               disciplinas_ofer
               WHERE
               id = $diario;";

      $diario_info = $conn->get_row($sql1);

      $competencias = $diario_info['competencias'];
      $observacoes = $diario_info['observacoes'];

	    if (empty($competencias) || empty($observacoes)) {
	      $diarios_em_aberto[] = $diario;
	    }
	    else {

	      // VERIFICA SE A CARGA HORÁRIA LANÇADA É MAIOR OU IGUAL A PREVISTA
	      $sql_carga_horaria = "SELECT CAST(get_carga_horaria_realizada($diario) AS INTEGER),CAST(get_carga_horaria(get_disciplina_de_disciplina_of($diario)) AS INTEGER);";
        $carga_horaria = $conn->get_row($sql_carga_horaria);

        if ($carga_horaria['get_carga_horaria_realizada'] < $carga_horaria['get_carga_horaria']) {
          $diarios_em_aberto[] = $diario;
        }
        else {

	        // MARCA O DIARIO COMO FECHADO
	        $sql_fecha_diarios .= "UPDATE disciplinas_ofer
					    SET
							  fl_finalizada = 't' 
					    WHERE
					      fl_digitada = 't' AND
                fl_finalizada = 'f' AND
					      ref_curso = ". get_curso($diario) ." AND
                ref_periodo = periodo_disciplina_ofer($diario) AND
                is_cancelada = '0' AND
							  id = $diario;";
							  
					$num_fechados++;
		    
        }
	    }
    }  
  }
  
  
	$mensagem_fechado = '';		    
  
  if (count($diarios_em_aberto) > 0) {
  
    
    $mensagem_fechado .= '\n\nO(s) diário(s): '. implode(',', $diarios_em_aberto) .' continua(m) aberto(s) devido a alguma pendência.\n ';
     
    $mensagem_fechado .= 'Feche cada um individualmente para verificar as pendências.\n\n';
  
  }
  
  $sql_fecha_diarios .= 'COMMIT;';

  $conn->Execute($sql_fecha_diarios);


  if ($_SESSION['sa_modulo'] == 'sa_login') {

    exit('<script language="javascript" type="text/javascript">
            alert(\''. $mensagem_fechado . $num_fechados .' diário(s) fechado(s) com sucesso!\');
			window.opener.location.reload();
			setTimeout("self.close()",120);</script>');

  }
  else {
    echo '<script type="text/javascript"> alert(\''. $mensagem_fechado . $num_fechados .' diário(s) fechado(s) com sucesso!\'); </script>';
  }
}
	
?>
