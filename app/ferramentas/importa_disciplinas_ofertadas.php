<?php

require_once(dirname(__FILE__) .'/../setup.php');
require_once($BASE_DIR .'lib/latin1utf8.class.php');

// CONEXAO ABERTA PARA TRABALHAR COM TRANSACAO (NÃO PERSISTENTE)
$conn = new connection_factory($param_conn, TRUE);


set_time_limit(0);

/*

TODO: substituir sigla do curso por código do curso
TODO: incluir a informação ref_campus, ref_periodo e ref_periodo_turma no arquivo CSV

Formato do CSV
===============

curso|disciplina abrv|turma|vagas
ADS|COEA1|1|40
ADS|INGA1|1|40

*/
$csv = dirname(__FILE__).'/csv/disciplinas_ofertadas_tecnico_111.csv';

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

    $trans = new Latin1UTF8();

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

      $curso_sigla = trim($line[0]);
      $abreviatura = trim($line[1]);
      $turma = trim($line[2]);
      $num_alunos = trim($line[3]);

      // buscar ref_disciplina
      $sql_disciplina = "SELECT id FROM disciplinas ";
      $sql_disciplina .= " WHERE abreviatura = '$abreviatura';";
      $ref_disciplina =  $conn->adodb->GetOne($sql_disciplina);

      //echo '<br />'. $sql_disciplina .'<br />';

      // buscar ref_curso
      $sql_curso = "SELECT MAX(id) FROM cursos ";
      $sql_curso .= " WHERE sigla = '$curso_sigla';";
      $ref_curso =  $conn->adodb->GetOne($sql_curso);

      //echo '<br />'. $sql_curso .'<br />';

      // buscar oferta anterior
      $sql_verifica = "SELECT id FROM disciplinas_ofer ";
      $sql_verifica .= " WHERE ref_disciplina = $ref_disciplina AND ref_curso = $ref_curso AND ";
      $sql_verifica .= " ref_periodo = '$ref_periodo' AND turma = '$turma';";
      $ja_existe =  (int) count($conn->adodb->GetAll($sql_verifica));

      //echo '<br />'. $sql_verifica .'<br />';

        if ($ja_existe == 0) {

          $qry_importa = "BEGIN;";

          $qry_importa .= "INSERT INTO disciplinas_ofer (ref_campus, ref_curso, ref_periodo, ref_disciplina, num_alunos, is_cancelada, turma, ref_periodo_turma)";
          $qry_importa .= " VALUES ($ref_campus, $ref_curso, '$ref_periodo', $ref_disciplina, $num_alunos, '0', '$turma', '$ref_periodo_turma');";

          $qry_importa .= " INSERT INTO disciplinas_ofer_compl (ref_disciplina_ofer, dia_semana, num_sala) ";
          $qry_importa .= " VALUES (CURRVAL('seq_disciplinas_ofer_id'), -1, 1);";

          $qry_importa .= "COMMIT;";

          $ret = $conn->adodb->Execute($qry_importa);
          //$ret = TRUE;
          //echo '<br />'. $qry_importa .'<br />';

          if ($ret === FALSE)
            $qry .= $abreviatura . ' - '. $ref_curso  . '&nbsp;&nbsp;<font color="red">ERRO</font><br />'. $conn->adodb->ErrorMsg() .'<br />';
          else
            $qry .= $abreviatura . ' - '. $ref_curso . '&nbsp;&nbsp;<font color="green">OK</font><br />';

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

