<?php

require_once(dirname(__FILE__) .'/../../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/number.php');
require_once($BASE_DIR .'core/situacao_academica.php');

// CONEXAO ABERTA PARA TRABALHAR COM TRANSACAO (NÃO PERSISTENTE)
$conexao = new connection_factory($param_conn, FALSE);

$diario_id = (int) $_POST['diario_id'];
$periodo = $_SESSION['web_diario_periodo_id'];
$operacao = $_POST['operacao'];
$valor_avaliacao = $_POST['valor_avaliacao'];

// VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
}


if (is_finalizado($diario_id)){

    echo '<script language="javascript" type="text/javascript">';
    echo 'alert("ERRO! Este diário está finalizado e não pode ser alterado!");';
    echo 'window.close();';
    echo '</script>';
    exit;
}

$periodo = $_SESSION['web_diario_periodo_id'];

$notas = (array) $_POST['notas'];
$matriculas = (array) $_POST['matricula'];
$prova = $_POST['codprova'];

$NOTAS = mediaPeriodo($periodo);
$MEDIA_FINAL_APROVACAO = $NOTAS['media_final'];
$NOTA_MAXIMA = $NOTAS['nota_maxima'];

foreach($qrynotas_parciais as $aluno) {

   $nota = $notas[$aluno['ref_pessoa']];
   $nota = number::decimal_br2numeric($nota,1);

   $aluno_id = $aluno['ref_pessoa'];
   $nota_parcial = $aluno['notaparcial'];
   $nota_atual = $aluno['notaatual'];
   $nota_extra = $aluno['notaextra'];
   $nome_aluno = $aluno['nome'];

   if(!is_numeric($nota) || empty($nota) || $nota == 0 || $nota < 0) { $nota = -1; }

	 // NOTA EXTRA
    if($nota_extra > -1) { $flag_extra = 1; } else { $flag_extra = 0; }

    // NOTA DIFERENTE
	if($nota != $nota_extra) { $flag_diff = 1; } else { $flag_diff = 0; }

   // CALCULA NOTA TOTAL

   // TODO: Selecionar método de cálculo da nota final com base em parâmetros do sistema
   // SE FOR NOTA DE RECUPERACAO / REAVALIACAO CALCULA CONFORME CRITERIOS DE CADA CURSO
   if($nota != -1 && $nota > 0) {
      $NotaFinal = calcula_nota_extra($nota_parcial,$nota);
   }
   else {
      $NotaFinal = $nota_parcial;
   }

   if($nota_parcial >= $MEDIA_FINAL_APROVACAO) { $flag_media = 1; } else {  $flag_media = 0;}

   if($NotaFinal > $NOTA_MAXIMA || $nota > $NOTA_MAXIMA ) { $flag_maior = 1;} else { $flag_maior = 0;}

   $NotaReal = number::numeric2decimal_br((double) $nota,1);

    // VERIFICA CONDICOES PARA REGISTRAR A NOTA
	// GRAVA AS NOTAS NO BANCO DE DADOS
	// SO ATUALIZA A NOTA SE A MEDIA FOR MAIOR QUE $MEDIA_FINAL_APROVACAO E
	// SE A NOTA FINAL OU A NOTA EXTRA NÃO FOR MAIOR QUE $NOTA_MAXIMA E
	// SE A NOTA EXTRA ESTIVER SENDO ALTERADA
	if($flag_diff == 1 && $flag_media == 0 && $flag_maior == 0 && $nota != -1) {

		$flag_grava = 1;
	}
    else { $flag_grava = 0;  }

      // GRAVA AS NOTAS NO BANCO DE DADOS
      // SO ATUALIZA A NOTA SE NAO EXISTIR A NOTA EXTRA E A SOMA FOR MENOR OU IGUAL A $NOTA_MAXIMA
      // E SE NOTA EXTRA FOR MAIOR DO QUE 0
	  if($flag_grava == 1 || $nota == -1) {

        	$sql_update .= "UPDATE matricula
                             SET
							nota_final = $NotaFinal
                          WHERE
                             ref_pessoa = $aluno_id AND
                             ref_disciplina_ofer = $diario_id AND
                             ref_periodo = '$periodo' AND
                             ref_motivo_matricula = 0; ";

			// AND ref_disciplina = '$getdisciplina'
         	$sql_update .= "UPDATE
                     diario_notas
                  SET
                     nota = $nota
                  WHERE
                     ref_diario_avaliacao = '$prova' AND
					 d_ref_disciplina_ofer = $diario_id AND
					 ra_cnec = '$aluno_id';";

            if($nota > -1 || $flag_grava == 1 && $nota > 0) {
		      		$msg_registros .= "<font color=\"#000000\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Nota <font color=\"#FF0000\"><strong>$NotaReal</strong></font> registrada para o aluno(a) <strong>$nome_aluno</strong>($aluno_id)<br></font>";
            }

			if($nota == -1 && $nota_extra != -1)
			{
				$msg_registros .= "<font color=\"#000000\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><font color=\"blue\" ><strong>Nota extra ". number::numeric2decimal_br((double) $nota_extra,1) ." eliminada!</strong></font>: aluno(a) <strong>$nome_aluno</strong>($aluno_id) <br></font>";
			}
      }
      else {

	      if($flag_diff == 0) {

		    $msg_registros .= "<font color=\"#000000\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><font color=\"blue\" ><strong>Nenhuma altera&ccedil;&atilde;o: </strong></font> aluno(a) <strong>$nome_aluno</strong>($aluno_id) <br></font>";
		}
		else {

		 // A NOTA DO ALUNO ULTRAPASSOU A NOTA MÁXIMA OU JA FOI LANCADA A NOTA EXTRA
		 if($nota != -1) {

        	if($flag_maior == 1 ) {

				 $msg_registros .= "<font color=\"#000000\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><font color=\"blue\" ><strong>Nota $NotaReal n&atilde;o registrada, poss&iacute;veis causas: </strong></font><font color=\"#FF0000\"><strong>NOTA EXTRA OU M&Eacute;DIA > $NOTA_MAXIMA  pontos</strong></font>: aluno(a) <strong>$nome_aluno</strong>($aluno_id) <br></font>";

			}
		    else {
				if($flag_media == 1) {

            	$msg_registros .= "<font color=\"#000000\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><font color=\"blue\" ><strong>Nota $NotaReal n&atilde;o registrada, poss&iacute;veis causas: </strong></font><font color=\"#FF0000\"><strong>M&Eacute;DIA >= $MEDIA_FINAL_APROVACAO pontos</strong></font>: aluno(a) <strong>$nome_aluno</strong>($aluno_id) <br></font>";
			   }
			}
		 }
       }
	}
} // while  0


$sql_update .= 'COMMIT;';

$conexao->Execute($sql_update);

echo $msg_registros;

// GRAVA LOG
$ip = $_SERVER["REMOTE_ADDR"];
$pagina = $_SERVER["PHP_SELF"];
$status = "NOTA REGISTRADA";
$Data = date("Y-m-d");
$Hora = date("H:i:s");
$sqllog = "INSERT INTO diario_log (usuario, data, hora, ip_acesso, pagina_acesso, status, senha_acesso) VALUES('$sa_usuario','$Data','$Hora','$ip','$pagina','$status','NA')";

$conexao->Execute($sqllog);

?>

<br />

<div align="left">
<a href="<?=$BASE_URL . 'app/web_diario/requisita.php?do='. $operacao .'&id='. $diario_id?>" target="_self">Continuar a lan&ccedil;ar notas</a>&nbsp;&nbsp;ou&nbsp;&nbsp;
<a href="#" onclick="javascript:window.close();">Fechar</a>

</div>
</body>
</html>

