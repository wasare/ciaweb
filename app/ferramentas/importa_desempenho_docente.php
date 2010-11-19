<?php

require_once(dirname(__FILE__) .'/../setup.php');
require_once($BASE_DIR .'core/number.php');

// CONEXAO ABERTA PARA TRABALHAR COM TRANSACAO (NÃO PERSISTENTE)
$conn = new connection_factory($param_conn, TRUE);


set_time_limit(0);


$csv = dirname(__FILE__).'/csv/Z10.csv';

$memory_limit = ini_get('memory_limit');


function _desempenho_docente_importa($memory_limit, $csv_file) {

    global $conn;

    $levantamento = '1001';

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

        //$verifica_associacao = 0;    
        set_time_limit(60);

        $professor = (int) trim($line[1]);
        $disciplina_ofer = (int) trim($line[0]);            

        echo $professor . ' - '. $disciplina_ofer . '<br />';
        if ((!is_numeric($professor) || $professor == 0) && (!is_numeric($disciplina_ofer) || $disciplina_ofer == 0)) continue;
        //if (!is_numeric($disciplina_ofer) || $disciplina_ofer = 0) continue;
        //echo $disciplina_ofer . ' - ';
     
        // verifica associação professor <-> disciplina_ofer
        $sql_verifica = "SELECT ref_professor FROM disciplinas_ofer_prof ";
        $sql_verifica .= " WHERE ref_disciplina_ofer = $disciplina_ofer AND ";
        $sql_verifica .= " ref_professor = $professor;";

        //echo $sql_verifica;
        $verifica_associacao = count($conn->adodb->GetAll($sql_verifica));

        echo 'associa: '.$verifica_associacao . '<br />';

        if ($verifica_associacao == 1) {

            $qry_avaliacao = "BEGIN;";

            $nota_criterio_1 = number::decimal_br2numeric(trim($line[2]),2);
            $qry_avaliacao .= "INSERT INTO desempenho_docente_nota VALUES ($disciplina_ofer, $professor, 1, $levantamento, $nota_criterio_1);";

            $nota_criterio_2 = number::decimal_br2numeric(trim($line[3]),2);
            $qry_avaliacao .= "INSERT INTO desempenho_docente_nota VALUES ($disciplina_ofer, $professor, 2, $levantamento, $nota_criterio_2);";

            $nota_criterio_3 = number::decimal_br2numeric(trim($line[4]),2);
            $qry_avaliacao .= "INSERT INTO desempenho_docente_nota VALUES ($disciplina_ofer, $professor, 3, $levantamento, $nota_criterio_3);";

            $nota_criterio_4 = number::decimal_br2numeric(trim($line[5]),2);
            $qry_avaliacao .= "INSERT INTO desempenho_docente_nota VALUES ($disciplina_ofer, $professor, 4, $levantamento, $nota_criterio_4);";

            $nota_criterio_5 = number::decimal_br2numeric(trim($line[6]),2);
            $qry_avaliacao .= "INSERT INTO desempenho_docente_nota VALUES ($disciplina_ofer, $professor, 5, $levantamento, $nota_criterio_5);";
            
            $nota_criterio_6 = number::decimal_br2numeric(trim($line[7]),2);
            $qry_avaliacao .= "INSERT INTO desempenho_docente_nota VALUES ($disciplina_ofer, $professor, 6, $levantamento, $nota_criterio_6);";

            $qry_avaliacao .= "COMMIT;";

            $ret = $conn->adodb->Execute($qry_avaliacao);

            if ($ret === FALSE)
                $qry .= $professor . ' - '. $disciplina_ofer . '&nbsp;&nbsp;<font color="red">ERRO</font><br />'; 
            else
                $qry .= $professor . ' - '. $disciplina_ofer . '&nbsp;&nbsp;<font color="green">OK</font><br />';
                
        }
        else {
            // não existe associação professor <-> disciplina_ofer
            $sql_info = "SELECT descricao_disciplina(get_disciplina_de_disciplina_of($disciplina_ofer)) || ' (' || $disciplina_ofer || ') - '";
            $sql_info .= " || pessoa_nome($professor) || ' (' || $professor || ')';"; 

            $nao_sera_importado .= $conn->adodb->GetOne($sql_info) .'  ' . $sql_verifica  .'<br />';


        }

        $count++;
    
    } // endwhile;  
  
    @fclose($handle);

    if (strlen($nao_sera_importado) > 0) {

        echo '<h3>N&atilde;o ser&aacute; importado os registros abaixo, pois n&atilde;o existe associa&ccedil;&atilde;o entre o professor(a) e a disciplina informadas</h3>';
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


_desempenho_docente_importa($memory_limit, $csv);



?>
