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

$nota_distribuida = number::decimal_br2numeric($valor_avaliacao,1);

// VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
}

if (is_fechado($diario_id)){

	echo '<script language="javascript" type="text/javascript">';
    echo 'alert("ERRO! Este diário está fechado e não pode ser alterado!");';
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


$msg_registros = '';

$sql_update = 'BEGIN;';

foreach($notas as $n) {
   if(number::decimal_br2numeric($n) > $nota_distribuida) {

      exit('<script language="javascript" type="text/javascript">
            alert(\'Você não pode lançar uma nota ('. $n .') maior que a nota distribuída ('. $valor_avaliacao .')! \n\n Retorne e corrija!\');
            window.history.back(1);</script>');
      break;
   }

}

?>
<html>
<head>
<title><?=$IEnome?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
</head>

<body>
<table width="90%" height="73" border="0">
  <tr>
    <td width="471">
      <div align="left" class="titulo1">
        Lan&ccedil;amento / Altera&ccedil;&atilde;o da
        <?php if($prova == 7) { echo '<font color="blue"> Nota Extra</font>.'; } else { echo 'Nota <font color="blue"> P'.$prova.'</font>.';} ?>
      </div>
</td>
  </tr>
</table>

<?=papeleta_header($diario_id)?>

<br />

<?php

reset($notas);
reset($matriculas);

$sql12 = 'SELECT * FROM (';
$sql12 .= "SELECT   DISTINCT
                    matricula.ordem_chamada, pessoas.nome, pessoas.id, SUM(d.nota) AS notaparcial
            FROM
                matricula
            INNER JOIN pessoas ON (matricula.ref_pessoa = pessoas.id)
            INNER JOIN diario_notas d ON (id_ref_pessoas = pessoas.id AND
                                            d.id_ref_pessoas = matricula.ref_pessoa AND
                                            d.id_ref_periodos = '$periodo' AND
											d.d_ref_disciplina_ofer = $diario_id AND
                                            d.ref_diario_avaliacao <> '$prova'  AND
                                            d.ref_diario_avaliacao <> '7')
            WHERE
                (matricula.ref_disciplina_ofer = $diario_id) AND
                (matricula.dt_cancelamento is null) AND
				(matricula.ref_motivo_matricula = 0)
			GROUP BY
					 matricula.ordem_chamada, pessoas.nome, pessoas.id, d.id_ref_pessoas
            ORDER BY pessoas.nome ";

$sql12 .= ') AS T1 INNER JOIN (';


$sql12 .= "SELECT DISTINCT
               pessoas.id, d.nota AS notaatual
            FROM
               matricula INNER JOIN
               pessoas ON (matricula.ref_pessoa = pessoas.id) INNER JOIN
               diario_notas d ON (id_ref_pessoas = pessoas.id AND
                                 d.id_ref_pessoas = matricula.ref_pessoa AND d.id_ref_periodos = '$periodo' AND
								 d.d_ref_disciplina_ofer = $diario_id AND d.ref_diario_avaliacao = '$prova')
            WHERE
               (matricula.ref_disciplina_ofer = $diario_id) AND (matricula.dt_cancelamento is null) AND (matricula.ref_motivo_matricula = 0)";


$sql12 .= ') AS T2 ON (T2.id = T1.id) INNER JOIN (';


$sql12 .= "SELECT DISTINCT
               pessoas.id AS ref_pessoa, d.nota AS notaextra
            FROM
               matricula INNER JOIN
               pessoas ON (matricula.ref_pessoa = pessoas.id) INNER JOIN
               diario_notas d ON (id_ref_pessoas = pessoas.id AND
                                 d.id_ref_pessoas = matricula.ref_pessoa AND
								 d.id_ref_periodos = '$periodo' AND
								 d.d_ref_disciplina_ofer = $diario_id AND d.ref_diario_avaliacao = '7')
            WHERE
               (matricula.ref_disciplina_ofer = $diario_id) AND (matricula.dt_cancelamento is null) AND (matricula.ref_motivo_matricula = 0)";

$sql12 .= ') AS T3 ON (T3.ref_pessoa = T2.id) ORDER BY to_ascii(nome,\'LATIN1\');';


$qrynotas_parciais = $conexao->get_all($sql12);

if($prova == 7) {
	require_once('grava_nota_extra.php');
	exit;
}

/* INICIO NOTA DISTRIBUIDA */
if(!is_numeric($nota_distribuida) || $nota_distribuida <= 0) { $flag_nota_distribuida = 1;} else { $flag_nota_distribuida = 0; }


$sql_total = "
SELECT
sum(nota_distribuida) as nota_distribuida
FROM
diario_formulas
WHERE grupo ILIKE '%-$diario_id'  AND
prova <> '$prova'";

$nota_distribuida_parcial = $conexao->get_one($sql_total);

$total_nota_distribuida = $nota_distribuida_parcial + $nota_distribuida;

if($total_nota_distribuida > $NOTA_MAXIMA) {
  $msg_registros .= '<font color="red"><b>Erro: Não foi possível gravar, resultado do somatório das notas distribuídas superior a '. $NOTA_MAXIMA .'!</b></font>';
  $flag_nota_distribuida_maior = 1;
}
else {
  $flag_nota_distribuida_maior = 0;
}
/* FIM NOTA DISTRIBUIDA*/

$flag_elimina_notas = (array_sum($notas) == 0 && $nota_distribuida == 0) ? 1 : 0;

// SOMENTE PROCESSA AS NOTAS SE A NOTA DISTRIBUÍDA FOR VÁLIDA
// E O SOMATÓRIO DAS NOTAS DISTRIBUÍDAS NÃO PASSAR DE $NOTA_MAXIMA
if(($flag_nota_distribuida == 0 && $flag_nota_distribuida_maior == 0) || $flag_elimina_notas == 1) {

   // SQL NOTA DISTRIBUIDA
  $sql_update .= "UPDATE diario_formulas SET nota_distribuida = $nota_distribuida
					WHERE grupo ILIKE '%-$diario_id' AND prova = '$prova';";

  $msg_registros .= "<font color=\"brownn\" >Nota distribuída <font color=\"blue\">(<strong>". number::numeric2decimal_br($nota_distribuida,1) ." pontos</strong>)</font> registrada com sucesso!</b></font><br /><br />";
  // ^ SQL NOTA DISTRIBUIDA ^

  foreach($qrynotas_parciais as $aluno)
  {
    /*
    $flag_extra = 0;
    $flag_diff = 0;
    $flag_media = 0;
    $flag_maior = 0;
    $flag_grava = 0;
    $flag_nota_distribuida = 0;
    $flag_nota_distribuida_maior = 0;
    */

    $nota = $notas[$aluno['ref_pessoa']];
    $nota = number::decimal_br2numeric($nota,1);

    $aluno_id = $aluno['ref_pessoa'];
    $nota_parcial = $aluno['notaparcial'];
    $nota_atual = $aluno['notaatual'];
    $nota_extra = $aluno['notaextra'];
    $nome_aluno = $aluno['nome'];


    if(!ereg("[0-9]*\.?[0-9]+$", $nota) || $nota == '') { $nota = 0; }

    // NOTA MAIOR QUE NOTA DISTRIBUIDA
    if($nota > $nota_distribuida) { $flag_nota_distribuida_maior = 1;} else { $flag_nota_distribuida_maior = 0; }

	 // NOTA EXTRA
    if($nota_extra > -1) { $flag_extra = 1; } else { $flag_extra = 0; }

    // NOTA DIFERENTE
	if($nota != $nota_atual) { $flag_diff = 1; } else { $flag_diff = 0; }

   if($flag_diff == 1) {
        $NotaFinal = ($nota_parcial + $nota);
   }
   else {
		$NotaFinal = ($nota_parcial + $nota_atual);
   }

   if($NotaFinal > $NOTA_MAXIMA) { $flag_maior = 1;} else { $flag_maior = 0; }

   $NotaReal = number::numeric2decimal_br($nota,1);

		// SE NOTA EXTRA NÃO FOI LANCADA,
		// E A NOTA FOR DIFERENTE DA ANTERIOR E NÃO FOR MAIOR QUE $NOTA_MAXIMA GRAVA
        // E O SOMATÓRIO DAS NOTAS DISTRIBUIDA É MENOR/IGUAL A $NOTA_MAXIMA
        // E A NOTA É MENOR/IGUAL O VALOR DA NOTA DISTRIBUIDA
		if($flag_extra == 0 && $flag_diff == 1 && $flag_maior == 0 && $flag_nota_distribuida_maior == 0) {
				$flag_grava = 1;
		}
		else {  $flag_grava = 0; }

      // GRAVA AS NOTAS NO BANCO DE DADOS
      // SO ATUALIZA A NOTA SE NAO EXISTIR A NOTA EXTRA E A SOMA FOR MENOR OU IGUAL A $NOTA_MAXIMA
	  if($flag_grava == 1) {

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
				     d_ref_disciplina_ofer = $diario_id AND
                     ref_diario_avaliacao = '$prova' AND
                     ra_cnec = '$aluno_id';";
				// rel_diario_formulas_grupo = '$grupo' AND


			$msg_registros .= "<font color=\"#000000\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">Nota <font color=\"#FF0000\"><strong>$NotaReal</strong></font> registrada para o aluno(a) <strong>$nome_aluno</strong>($aluno_id)<br></font>";
      }
      else {
		    if($flag_extra == 1) {

					$msg_registros .= "<font color=\"#000000\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><font color=\"blue\" ><strong>Nota $NotaReal n&atilde;o registrada, motivo: </strong></font><font color=\"#FF0000\"><strong>NOTA EXTRA J&Aacute; LAN&Ccedil;ADA!</strong></font>: aluno(a) <strong>$nome_aluno</strong>($aluno_id) <br></font>";
			}
			else {
				if($flag_maior == 1) {

					$msg_registros .= "<font color=\"#000000\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><font color=\"blue\" ><strong>Nota $NotaReal n&atilde;o registrada, causa: </strong></font><font color=\"#FF0000\"><strong>M&Eacute;DIA > $NOTA_MAXIMA pontos</strong></font>: aluno(a) <strong>$nome_aluno</strong>($aluno_id) <br></font>";
				}
				else {

                    if($flag_nota_distribuida_maior == 1) {
                  		$msg_registros .= "<font color=\"#000000\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><font color=\"#cc0000\" ><strong>Nota $NotaReal n&atilde;o registrada, causa: </strong></font><font color=\"#FF0000\"><strong>NOTA > Nota Distribuída</strong></font>: aluno(a) <strong>$nome_aluno</strong>($aluno_id) <br></font>";
					}
                    elseif($flag_diff == 0) {
                  		$msg_registros .= "<font color=\"#000000\" size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\"><font color=\"blue\" ><strong>Nota $NotaReal Mantida</strong></font>: aluno(a) <strong>$nome_aluno</strong>($aluno_id) <br></font>";
					}
        		}
      		}

	  }
      // print ($sqlupdatematricula."<BR>");
} // while  0

}
else
   $msg_registros .= '<br /><font color="red"><b>Erro: Não foi possível gravar, nota distribuída inválida ou não informada!</b></font>';


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
<br />
<br />
<div align="left">
<a href="<?=$BASE_URL . 'app/web_diario/requisita.php?do='. $operacao .'&id='. $diario_id?>" target="_self">Continuar a lan&ccedil;ar notas</a>&nbsp;&nbsp;ou&nbsp;&nbsp;
<a href="#" onclick="javascript:window.close();">Fechar</a>

</div>
</body>
</html>

