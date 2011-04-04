<?php

require_once(dirname(__FILE__) .'/../setup.php');
require_once($BASE_DIR .'lib/latin1utf8.class.php');

// CONEXAO ABERTA PARA TRABALHAR COM TRANSACAO (NÃO PERSISTENTE)
$conn = new connection_factory($param_conn, TRUE);


set_time_limit(0);

/*
Formato do CSV
===============

prontuario|aluno
1105001|CLEBER ANTONIO DA SILVA
1105019|BRUNO PALUMBO MARGARIDO DOS SANTOS

*/

$csv = dirname(__FILE__).'/csv/alunos_superior.csv';

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


function _alunos_importa($memory_limit, $csv_file) {

    global $conn;

    $trans = new Latin1UTF8();

    $ref_campus = 6;

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

      $prontuario = mb_strtoupper(trim($line[0]), 'UTF-8');
      $nome = addslashes(uc_first_names(trim($line[1])));
      $nome = $trans->mixed_to_utf8($nome);


      //echo $prontuario . ' - '. $nome . '<br />';
      if (strlen($prontuario) != 7) continue;

      // verifica importação anterior -> pessoa_prontuario_campus
      $sql_verifica = "SELECT ref_pessoa FROM pessoa_prontuario_campus ";
      $sql_verifica .= " WHERE prontuario = '$prontuario';";

      //echo $sql_verifica;
      $ja_existe = count($conn->get_all($sql_verifica));

       //echo 'associa: '.$verifica_associacao . '<br />';

        if ($ja_existe == 0) {

          $qry_importa = "BEGIN;";

          $qry_importa .= "INSERT INTO pessoas (nome)";
          $qry_importa .= " VALUES ('$nome');";
          $qry_importa .= " INSERT INTO documentos(ref_pessoa) ";
          $qry_importa .= " VALUES (CURRVAL('seq_pessoas'));";
          $qry_importa .= " INSERT INTO pessoa_prontuario_campus ";
          $qry_importa .= " VALUES (CURRVAL('seq_pessoas'), '$prontuario', $ref_campus);";
          $qry_importa .= " INSERT INTO acesso_aluno (ref_pessoa, senha) ";
          $qry_importa .= " VALUES (CURRVAL('seq_pessoas'), '');";

          $qry_importa .= "COMMIT;";

          $ret = $conn->adodb->Execute($qry_importa);

          if ($ret === FALSE)
            $qry .= $prontuario . ' - '. $nome  . '&nbsp;&nbsp;<font color="red">ERRO</font><br />'. $conn->adodb->ErrorMsg() .'<br />';
          else
            $qry .= $prontuario . ' - '. $nome . '&nbsp;&nbsp;<font color="green">OK</font><br />';

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


_alunos_importa($memory_limit, $csv);



?>

