<?php

require_once(dirname(__FILE__) .'/../setup.php');
//require_once($BASE_DIR .'lib/latin1utf8.class.php');
//require_once($BASE_DIR .'app/matricula/matricula.post.php');
require_once($BASE_DIR .'core/web_diario.php');

// CONEXAO ABERTA PARA TRABALHAR COM TRANSACAO (NÃO PERSISTENTE)
$conn = new connection_factory($param_conn, TRUE);


set_time_limit(0);

/*

TODO: substituir sigla do curso por código do curso
TODO: incluir a informação ref_campus, ref_periodo e ref_periodo_turma no arquivo CSV

Formato do CSV
===============

curso|disciplina abrv|disciplina abrv|turma|course1|prontuario|aluno
ADS|COEA1|COEA1|1|2011-1-ADS-1N-COEA1|1105001|CLEBER ANTONIO DA SILVA
ADS|INGA1|INGA1|1|2011-1-ADS-1N-INGA1|1105001|CLEBER ANTONIO DA SILVA

*/
$csv = dirname(__FILE__).'/csv/matriculas_em_disciplinas_tecnico_111.csv';

$memory_limit = ini_get('memory_limit');



function trocaini($wStr,$w1,$w2) {

  setlocale(LC_CTYPE,"pt_BR");
  $wde = 1;
  $para = 0;
  while($para < 1) {
    $wpos = strpos($wStr, $w1, $wde);
    if ($wpos > 0) {
      $wStr = str_replace($w1, $w2, $wStr);
      $wde = $wpos+1;
    }
    else {
      $para = 2;
    }
  }

  $trocou = $wStr;
  return $trocou;

}

function uc_first_names($string) {

    setlocale(LC_ALL, 'pt_BR');

    $string = ucwords(mb_strtolower(trim($string), 'UTF-8'));

    foreach (array('-', '\'') as $delimiter) {
      if (strpos($string, $delimiter)!== FALSE) {
        $string = implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
      }
    }

    $string = trocaini($string, " E ", " e ");
    $string = trocaini($string, " De ", " de ");
    $string = trocaini($string, " Da ", " da ");
    $string = trocaini($string, " Do ", " do ");
    $string = trocaini($string, " Das ", " das ");
    $string = trocaini($string, " Dos ", " dos ");
    $string = trocaini($string, " Em ", " em ");
    $string = trocaini($string, " Com ", " com ");
    $string = trocaini($string, " Para ", " para ");
    $string = trocaini($string, " Ao ", " ao ");
    $string = trocaini($string, " À ", " à ");
    $string = trocaini($string, " A ", " a ");
    $string = trocaini($string, " Ii", " II");
    $string = trocaini($string, " Iii", " III");
    $string = trocaini($string, " IIi", " III");


    return $string;
}


function _disciplinas_ofertadas_importa($memory_limit, $csv_file) {

    global $conn;

    //$trans = new Latin1UTF8();

    $ref_campus = 6;
    $ref_periodo = '111';
    $ref_periodo_turma = '111';

    $qry = '';

    $nao_sera_importado = '';

    if (!file_exists($csv_file)) {
        echo 'arquivo n&atilde;o encontrado: ' . $csv_file;
        return;
    }

    $csv_file = realpath($csv_file);

    // Automatically detect line endings.
    ini_set('auto_detect_line_endings', '1');
    $handle = @fopen($csv_file, 'r');

    $count_lines = count(file($csv_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

    $count = 0;

    echo '<strong>Arquivo:</strong>&nbsp;&nbsp;'. basename($csv_file) . '<br /><br />';

    while (($line = fgetcsv($handle, 1000, '|')) !== FALSE) {

      set_time_limit(60);

      //if($count > 1) break;

      $curso_sigla = trim($line[0]);
      $abreviatura = trim($line[1]);
      $turma = trim($line[2]);
      $prontuario = mb_strtoupper(trim($line[3]), 'UTF-8');


      // buscar ref_pessoa
      $sql_pessoa = "SELECT ref_pessoa FROM pessoa_prontuario_campus ";
      $sql_pessoa .= " WHERE prontuario = '$prontuario' AND ref_campus = $ref_campus;";
      $ref_pessoa =  $conn->adodb->GetOne($sql_pessoa);

      //echo '<br />'. $sql_pessoa .'<br />';

      // buscar ref_disciplina
      $sql_disciplina = "SELECT id FROM disciplinas ";
      $sql_disciplina .= " WHERE abreviatura = '$abreviatura';";
      $ref_disciplina =  $conn->adodb->GetOne($sql_disciplina);

      //echo '<br />'. $sql_disciplina .'<br />';

      // buscar ref_contrato e ref_curso
      $sql_contrato = "SELECT t1.id, t1.ref_curso FROM contratos t1 LEFT OUTER JOIN cursos t2 ON (t1.ref_curso = t2.id)  ";
      $sql_contrato .= " WHERE t1.ref_pessoa = $ref_pessoa AND t1.ref_campus = $ref_campus AND ";
      $sql_contrato .= " t2.sigla = '$curso_sigla'; ";
      $contrato =  $conn->adodb->GetAll($sql_contrato);
      $ref_contrato = $contrato[0]['id'];
      $ref_curso = $contrato[0]['ref_curso'];

      //echo '<br />'. $sql_contrato .'<br />';
      /*echo '$ref_contrato '. $ref_contrato .'<br />';
      echo '$ref_curso '. $ref_curso .'<br />';
      print_r($contrato);*/


      // buscar ref_disciplina_ofer
      $sql_disciplina_ofer = "SELECT id FROM disciplinas_ofer ";
      $sql_disciplina_ofer .= " WHERE ref_campus = $ref_campus AND ref_periodo = '$ref_periodo' AND ";
      $sql_disciplina_ofer .= " turma = '$turma' AND ref_disciplina = $ref_disciplina AND ";
      $sql_disciplina_ofer .= " ref_curso = $ref_curso; ";
      $ref_disciplina_ofer =  $conn->adodb->GetOne($sql_disciplina_ofer);

      //echo '<br />'. $sql_disciplina_ofer .'<br />';

      // buscar matricula anterior
      $sql_verifica = "SELECT id FROM matricula ";
      $sql_verifica .= " WHERE ref_contrato = $ref_contrato AND ref_curso = $ref_curso AND ";
      $sql_verifica .= " ref_periodo = '$ref_periodo' AND ref_disciplina_ofer = $ref_disciplina_ofer AND ";
      $sql_verifica .= " ref_pessoa = $ref_pessoa AND ref_campus = $ref_campus; ";
      $ja_existe =  (int) count($conn->adodb->GetAll($sql_verifica));

      //echo '<br />'. $sql_verifica .'<br />';

      /*
      			//-- Verifica se tem vaga
			$sqlVerificaVagas = "
				SELECT
					count(*),
					check_matricula_pessoa('$diario','$aluno_id'),
					num_alunos('$diario')
				FROM
					matricula
				WHERE
					ref_disciplina_ofer = '$diario' AND
					dt_cancelamento is null";*/

      //echo '<br />'. $sql_verifica .'<br />';

        if ($ja_existe == 0) {

          $qry_importa = "BEGIN;";

          $qry_importa .= "INSERT INTO matricula (ref_contrato, ref_pessoa, ref_campus, ref_curso, ref_periodo, ref_disciplina, ";
          $qry_importa .= " ref_curso_subst, ref_disciplina_subst, ref_disciplina_ofer, complemento_disc, ";
          $qry_importa .= " fl_exibe_displ_hist, dt_matricula, hora_matricula, status_disciplina )";

          $qry_importa .= " VALUES ($ref_contrato, $ref_pessoa, $ref_campus, $ref_curso, '$ref_periodo', $ref_disciplina, ";
          $qry_importa .= " 0, 0, $ref_disciplina_ofer, get_complemento_ofer($ref_disciplina_ofer), ";
          $qry_importa .= " 'S', date(now()), now(), 'f' );";

          $qry_importa .= "UPDATE disciplinas_ofer SET num_matriculados = (SELECT count(id) FROM disciplinas_ofer WHERE id = $ref_disciplina_ofer);";

          $qry_importa .= "COMMIT;";

          $ret = $conn->adodb->Execute($qry_importa);
          //$ret = TRUE;
          //echo '<br />'. $qry_importa .'<br />';

          if ($ret === FALSE)
            $qry .= $abreviatura . ' - '. $ref_disciplina_ofer  . ' - '. $prontuario .'&nbsp;&nbsp;<font color="red">ERRO</font><br />'. $conn->adodb->ErrorMsg() .'<br />';
          else {
            $qry .= $abreviatura . ' - '. $ref_disciplina_ofer . ' - '. $prontuario  .'&nbsp;&nbsp;<font color="green">OK</font><br />';
            // atualiza diario
            //atualiza_diario("$ref_pessoa","$ref_disciplina_ofer");
          }

        }
        else {
            // prontuário já existe na base de dados
            $nao_sera_importado .= $sql_verifica  .'<br />';
        }

        $count++;

    } // endwhile;

    @fclose($handle);

    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

    if (strlen($nao_sera_importado) > 0) {

        echo '<h3>N&atilde;o foram importados os registros abaixo, pois ocorreu algum erro durante a importa&ccedil;&atilde;o</h3>';
        echo $nao_sera_importado;

    }

    if (strlen($qry) > 0) {
    echo '<h3>Resultado da importa&ccedil;&atilde;o</h3>';
        echo "<br />$qry<br />";
    }
    else {
        echo '<h3>Nenhum registro para importa&ccedil;&atilde;o</h3>';
    }

}


_disciplinas_ofertadas_importa($memory_limit, $csv);



?>

