<?php

set_time_limit(600);

require_once(dirname(__FILE__). '/../../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/situacao_academica.php');
require_once($BASE_DIR .'core/number.php');
require_once($BASE_DIR .'core/date.php');
require_once(dirname(__FILE__). '/diario_classe.php');
require_once(dirname(__FILE__). '/correcao_posicao.inc.php');

define('DIARIO_CLASSE_PDF_TMP_DIR', dirname(__FILE__) .'/diarios_classe/pdf_tmp/');

$conn = new connection_factory($param_conn);

$diario_id = (int) $_GET['diario_id'];

if($diario_id == 0)
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario invalido!");window.close();</script>');

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if ($_SESSION['sa_modulo'] == 'web_diario_login') {
  if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
  }
}
// ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //


remove_files(DIARIO_CLASSE_PDF_TMP_DIR);

$sql3 = "SELECT DISTINCT dia, flag FROM diario_seq_faltas WHERE ref_disciplina_ofer = $diario_id ORDER BY dia;";
$num_chamadas = $conn->get_all($sql3);

if(count($num_chamadas) == 0) {
  echo '<script language="javascript">window.alert("Nenhuma chamada realizada para este diário!"); javascript:window.close(); </script>';
  exit;
}

$sql_observacoes_competencias = "SELECT
            competencias,
            observacoes
               FROM
               disciplinas_ofer
               WHERE
               id = $diario_id;";

$sql_conteudos = "SELECT dia, conteudo, atividades
               FROM
               diario_seq_faltas
               WHERE
               ref_disciplina_ofer = $diario_id AND
               dia >= '%s' AND dia <= '%s'
               ORDER BY dia;";

// ALUNOS
$sql_alunos_diario = "SELECT
              b.nome, ppc.ref_pessoa, c.prontuario, a.num_faltas, a.nota_final
              FROM matricula a, pessoas b, pessoa_prontuario_campus ppc, contratos c

              WHERE
                 a.ref_disciplina_ofer = $diario_id AND
                 a.ref_pessoa = b.id AND
                 a.ref_pessoa = ppc.ref_pessoa AND
                 a.ref_campus = ppc.ref_campus AND
                 a.ref_contrato = c.id AND
                 ppc.prontuario = c.prontuario AND
                 a.dt_cancelamento is null AND
                 a.ref_motivo_matricula = 0
              ORDER BY lower(to_ascii(nome,'LATIN1'));" ;
$alunos_diario = $conn->get_all($sql_alunos_diario);


$sql_faltas_aluno = "SELECT dia, CASE
                            WHEN faltas IS NULL THEN '0'
                            ELSE faltas
                        END AS faltas, aulas
    FROM
    (
    SELECT DISTINCT
              c.ra_cnec, data_chamada, count(CAST(c.ra_cnec AS INTEGER)) as faltas
        FROM diario_chamadas c
             WHERE
               c.ref_disciplina_ofer = $diario_id AND
               CAST(c.ra_cnec AS INTEGER) = %s
            GROUP BY c.ra_cnec, data_chamada
    ) AS T1
    FULL OUTER JOIN
    (
    SELECT DISTINCT dia, flag as aulas FROM diario_seq_faltas WHERE ref_disciplina_ofer = $diario_id ORDER BY dia
    ) AS T2 ON (data_chamada = dia)

    ORDER BY dia;";


$sql_notas_aluno = "
    SELECT ref_diario_avaliacao, nota, nota_distribuida  FROM
    (
    SELECT  DISTINCT
          a.ref_disciplina_ofer, b.id as ref_pessoa,
          c.ref_diario_avaliacao, c.nota
      FROM
          matricula a, pessoas b, diario_notas c
      WHERE
          (a.dt_cancelamento is null) AND
          a.ref_disciplina_ofer =  $diario_id AND
          b.id = %s AND
          a.ref_pessoa = b.id AND
          b.ra_cnec = c.ra_cnec AND
          c.d_ref_disciplina_ofer = a.ref_disciplina_ofer AND
          a.ref_motivo_matricula = 0
    ) AS T1
    LEFT JOIN
    (
    SELECT nota_distribuida, CAST(prova AS INTEGER)
      FROM
          diario_formulas
      WHERE
          grupo ILIKE '%s-$diario_id') AS T2
      ON ( T1.ref_diario_avaliacao = T2.prova)
      ORDER BY ref_diario_avaliacao, prova;";

// ^ ALUNOS ^ //

$sql_dario_info = "SELECT
      DISTINCT
          curso_desc(curso_disciplina_ofer(o.id)) AS curso,
          descricao_periodo(o.ref_periodo),
          descricao_disciplina(get_disciplina_de_disciplina_of(o.id)) AS disciplina,
          turma,
          professor_disciplina_ofer_todos(o.id) AS professores,
          o.ref_periodo,
          d.abreviatura,
          o.id as diario,
          o.fl_finalizada
    FROM
      disciplinas_ofer o, disciplinas d
   WHERE o.id = $diario_id AND o.is_cancelada = '0' AND o.ref_disciplina = d.id;";
$diario_info = $conn->get_row($sql_dario_info);


$sql_notas_distribuidas = "SELECT nota_distribuida, CAST(prova AS INTEGER)
      FROM
          diario_formulas
      WHERE
          grupo ILIKE '%-$diario_id' ORDER BY prova;";
$notas_distribuidas = $conn->get_all($sql_notas_distribuidas);


$sql_carga_horaria = "SELECT get_carga_horaria_realizada($diario_id), get_carga_horaria(get_disciplina_de_disciplina_of($diario_id));";
$carga_horaria = $conn->get_row($sql_carga_horaria);

$sql_datas_chamadas = "SELECT DISTINCT dia, flag FROM diario_seq_faltas WHERE ref_disciplina_ofer = $diario_id ORDER BY dia;";
$datas_chamadas = $conn->get_all($sql_datas_chamadas);

$periodo_id = $conn->get_one("SELECT periodo_disciplina_ofer($diario_id);");
$TURNO = utf8_decode($conn->get_one("SELECT get_turno_(turno_disciplina_ofer_todos($diario_id));"));
$ANO = utf8_decode(implode('', get_ano_periodo($periodo_id)));
$SEMESTRE = utf8_decode(get_descricao_sequencial_periodo($periodo_id));
$ABREVIATURA = $diario_info['abreviatura'];
$TURMA = $diario_info['turma'];

$NOTAS = mediaPeriodo($periodo_id);
$MEDIA_FINAL_APROVACAO = $NOTAS['media_final'];
$NOTA_MAXIMA = $NOTAS['nota_maxima'];

// N° DE AULAS
$NO_AULAS = ($carga_horaria['get_carga_horaria_realizada'] > 74 ) ? '1|74' : '1|'. $carga_horaria['get_carga_horaria_realizada'];

// CALCULA NO Nº TOTAL DE PÁGINAS
$NO_PAGINAS = ceil(($carga_horaria['get_carga_horaria_realizada'] / 74)) * 2;

$PAGINA_ATUAL = 1;

// ARQUIVOS DE TEMPLATE
$frente_tpl = dirname(__FILE__) .'/Diario_Frente.pdf';
$verso_tpl = dirname(__FILE__). '/Diario_Verso.pdf';


// FUNÇÕES
function diario_classe_preenche_rodape_frente(&$pdf) {
  global $carga_horaria;

  $pdf->SetY(283);

  // CH PREVISTA
  $pdf->SetX(256);
  $pdf->Write(0, $carga_horaria['get_carga_horaria']);

  // CH REALIZADA
  $pdf->SetX(327);
  $pdf->Write(0, $carga_horaria['get_carga_horaria_realizada']);

}


function diario_classe_preenche_cabecalho_frente(&$pdf) {
  global $diario_info, $ANO, $TURNO, $SEMESTRE;

  // CABEÇALHO
  
  
  // DIARIO EM ABERTO
  if ($diario_info['fl_finalizada'] == 'f') {
  
    $pdf->SetFont('Times','BU',14);

    // CONFIGURA POSICAO
    $pdf->SetXY(160, 8);
    
    $pdf->Write(0, utf8_decode('DIÁRIO NÃO FECHADO, PASSÍVEL DE ALTERAÇÕES'));
  
  }
  

  // CONFIGURA FONTE
  $pdf->SetFont('Times','',11);

  // ALTURA INICIAL
  $pdf->SetY(14.5);

  // DATA DE IMPRESSAO
  $pdf->SetX(292);
  $pdf->Write(0, date("d/m/Y H:s i\s"));

  // COMPONENTE CURRICULAR
  $pdf->SetXY(14, 26);
  $pdf->CellFitScale(113,0,utf8_decode($diario_info['disciplina'] .' ('. $diario_info['abreviatura']  .')'),0,0,'C',0);

  // PROFESSORES
  $pdf->SetX(133);
  $pdf->CellFitScale(55,0,utf8_decode($diario_info['professores']),0,0,'C',0);

  // BIMESTRE / SEMESTRE
  $pdf->SetX(194);
  $pdf->CellFitScale(33,0, $SEMESTRE,0,0,'C',0);

  // ANO
  $pdf->SetX(234);
  $pdf->CellFitScale(21,0, $ANO,0,0,'C',0);

  // CURSO / MÓDULO
  $pdf->SetX(261);
  $pdf->CellFitScale(58,0,utf8_decode($diario_info['curso']),0,0,'C',0);

  // TURNO
  $pdf->SetX(322);
  $pdf->CellFitScale(30,0, $TURNO,0,0,'C',0);

  // TURMA
  $pdf->SetX(355);
  $pdf->CellFitScale(56,0,utf8_decode($diario_info['turma']),0,0,'C',0);

  // ^ CABEÇALHO ^//

}

function diario_classe_preenche_cabecalho_verso(&$pdf) {
  global $diario_info, $ANO, $TURNO;

  // CABEÇALHO VERSO
  
  // DIARIO EM ABERTO
  if ($diario_info['fl_finalizada'] == 'f') {
  
    $pdf->SetFont('Times','BU',14);

    // CONFIGURA POSICAO
    $pdf->SetXY(200, 28);
    
    $pdf->Write(0, utf8_decode('DIÁRIO NÃO FECHADO, PASSÍVEL DE ALTERAÇÕES'));
  
  }
  

  // CONFIGURA FONTE
  $pdf->SetFont('Times','',11);

    // ALTURA INICIAL
  $pdf->SetY(13.5);

  // COMPONENTE CURRICULAR
  $pdf->SetX(141.5);
  $pdf->CellFitScale(126,0,utf8_decode($diario_info['disciplina'] .' ('. $diario_info['abreviatura']  .')'),0,0,'C',0);

  // TURMA
  $pdf->SetX(320.5);
  $pdf->CellFitScale(26.5,0,utf8_decode($diario_info['turma']),0,0,'C',0);

  // ANO
  $pdf->SetX(382);
  $pdf->CellFitScale(21.5,0, $ANO,0,0,'C',0);

  // CURSO / MÓDULO / TURNO
  $pdf->SetXY(90.5, 26);
  $pdf->CellFitScale(102,0,utf8_decode($diario_info['curso']) .'   /   '. $TURNO,0,0,'C',0);

  // ^ CABEÇALHO  VERSO ^//

}

function diario_classe_preenche_bases_conhecimento_e_atividades($data, &$pdf) {
    global $conn, $diario_info, $sql_conteudos;

    list($data_inicial, $data_final) = explode('|', $data);

    $conteudos = $conn->get_all(sprintf($sql_conteudos, $data_inicial, $data_final));

    $bases_conhecimentos = $atividades = '';

    foreach ($conteudos as $conteudo) {
        $data_chamada = date::convert_date($conteudo['dia']);
        $bases_conhecimentos .= trim(preg_replace( '/\s+/', ' ', $data_chamada .'  '. $conteudo['conteudo'])) ."\n";
        $atividades .= trim(preg_replace( '/\s+/', ' ', $data_chamada .' - '. $conteudo['atividades'])) ."\n";
    }

    $pdf->SetFont('Times','',10);

    $pdf->SetY(53.5);
    $pdf->SetX(30);
    
    $no_linhas = $pdf->MultiCellCountLines(178,4.15,utf8_decode($bases_conhecimentos),0,'J', FALSE);
    
    $pdf->SetY(53.5);
    $pdf->SetX(30);
    
    if ($no_linhas > 40)
      $pdf->MultiCellFitLineScale(178,4.15,utf8_decode($bases_conhecimentos),0,'J', FALSE);
    else
      $pdf->MultiCell(178,4.15,utf8_decode($bases_conhecimentos),0,'J', FALSE);

    $Y_atual = $pdf->GetY(); 
    
    if ($diario_info['fl_finalizada'] == 't') { 
      // INUTILIZA ESPAÇO EM BRANCO
      if ($Y_atual < 222.75) {
        $pdf->Line(28, $Y_atual, 210.5, $Y_atual); // LINHA
        $pdf->Line(28, $Y_atual, 210.5, 222.5); // TRAÇO DIAGONAL
      }
    }

    $pdf->SetY(53.5);
    $pdf->SetX(214.5);
    
    $no_linhas = $pdf->MultiCellCountLines(188,4.15,utf8_decode($atividades),0,'J', FALSE);
    
    $pdf->SetY(53.5);
    $pdf->SetX(214.5);
    
    if ($no_linhas > 40)
      $pdf->MultiCellFitLineScale(188,4.15,utf8_decode($atividades),0,'J', FALSE);
    else
      $pdf->MultiCell(188,4.15,utf8_decode($atividades),0,'J', FALSE); 
    
    $Y_atual = $pdf->GetY();
    
    if ($diario_info['fl_finalizada'] == 't') { 
      // INUTILIZA ESPAÇO EM BRANCO
      if ($Y_atual < 222.75) {
        $pdf->Line(210.5, $Y_atual, 403.5, $Y_atual); // LINHA
        $pdf->Line(210.5, $Y_atual, 403.5, 222.5); // TRAÇO DIAGONAL
      }
    }
}

function diario_classe_preenche_observacoes_competencias(&$pdf) {
    global $conn, $diario_info, $sql_observacoes_competencias;

    $anotacoes_diario = $conn->get_row($sql_observacoes_competencias);

    $pdf->SetFont('Times','',10);

    $pdf->SetY(222.5);
    $pdf->SetX(38);
    $pdf->MultiCell(170,3.9,utf8_decode(preg_replace( '/\s+/', ' ', $anotacoes_diario['observacoes'])),0,'J', FALSE);

    if ($diario_info['fl_finalizada'] == 't') { 
      // INUTILIZA ESPAÇO EM BRANCO
      $pdf->Line(34, $pdf->GetY(), 210.5, $pdf->GetY()); // LINHA
      $pdf->Line(34, $pdf->GetY(), 210.5, 268.5); // TRAÇO DIAGONAL
    }

    $pdf->SetY(222.5);
    $pdf->SetX(220);
    $pdf->MultiCell(180,3.9,utf8_decode(preg_replace( '/\s+/', ' ', $anotacoes_diario['competencias'])),0,'J', FALSE);

    if ($diario_info['fl_finalizada'] == 't') { 
      // INUTILIZA ESPAÇO EM BRANCO
      $pdf->Line(216.5, $pdf->GetY(), 403.5, $pdf->GetY()); // LINHA
      $pdf->Line(216.5, $pdf->GetY(), 403.5, 268.5); // TRAÇO DIAGONAL
    }

}

function diario_classe_preenche_alunos(&$pdf, $aulas='1|74') {
  global $alunos_diario, $diario_info, $PAGINA_ATUAL, $NO_PAGINAS, $NOTA_MAXIMA;

  $pdf->SetXY(20, 50);
  $pdf->SetFont('Times','',10);

  $numero_ordem = 0;

  foreach($alunos_diario as $aluno) {

    $nome = $aluno['nome'];
    $ref_pessoa = $aluno['ref_pessoa'];
    $prontuario = $aluno['prontuario'];

    // NÚMERO DE ORDEM
    $pdf->SetX(14);
    $pdf->Write(0, ++$numero_ordem);
    $pdf->SetX(121.25);
    $pdf->Write(0, $numero_ordem);

    // NOME ALUNO
    $pdf->SetX(20);
    $pdf->Write(0, utf8_decode($nome));

    // PRONTUARIO ALUNO
    $pdf->SetX(102);
    $pdf->Write(0, utf8_decode($prontuario));

    diario_classe_preenche_faltas_aluno($ref_pessoa, &$pdf, $aulas);

    $posicao_X = $pdf->GetX();
    $posicao_Y = $pdf->GetY();

    if ($PAGINA_ATUAL == ($NO_PAGINAS - 1)) {

      if ($numero_ordem == 1)
        diario_classe_preenche_notas_distribuidas(&$pdf);

      if ($aluno['nota_final'] < $NOTA_MAXIMA)
        $nota_final = number::numeric2decimal_br($aluno['nota_final'],1);
      else
        $nota_final = $aluno['nota_final'];

      $falta_total = $aluno['num_faltas'];

      diario_classe_preenche_notas_aluno($ref_pessoa, &$pdf);

      //// NOTA FINAL
      $pdf->SetX(398.25);
      $pdf->Write(0, utf8_decode($nota_final));

      // TOTAL DE FALTAS
      $pdf->SetX(406);
      $pdf->Write(0, utf8_decode($falta_total));

    }

    // PRÓXIMA LINHA
    $pdf->Ln(4);
  }

  if ($diario_info['fl_finalizada'] == 't') { 
    // INUTILIZA ESPAÇO EM BRANCO - ÁREA ALUNOS
    $pdf->Line(14, $pdf->GetY(), 126.5, $pdf->GetY()); // LINHA HORIZONTAL 1
    $pdf->Line(14, $pdf->GetY(), 126.5, 280.5); // TRAÇO DIAGONAL 1

    // INUTILIZA ESPAÇO EM BRANCO - ÁREA FALTAS
    $pdf->Line(126.5, $pdf->GetY(),  $posicao_X + 2.5, $pdf->GetY()); // LINHA HORIZONTAL 2
    $pdf->Line(126.5, $pdf->GetY(), $posicao_X + 4, 280.5); // TRAÇO DIAGONAL 2

    // INUTILIZA ESPAÇO EM BRANCO - ÁREA NOTAS
    if ($PAGINA_ATUAL == ($NO_PAGINAS - 1)) {
      $pdf->Line(355, $pdf->GetY(), 411.5, $pdf->GetY()); // LINHA HORIZONTAL 3
      $pdf->Line(355, $pdf->GetY(), 411.5, 280.5); // TRAÇO DIAGONAL 3
    }
    else
      $pdf->Line(355, 43, 411.5, 280.5); // TRAÇO DIAGONAL 3
  }

  // ^ ALUNOS ^ //
}

function diario_classe_preenche(&$pdf, $aulas='1|74') {

  diario_classe_preenche_cabecalho_frente(&$pdf);
  diario_classe_preenche_rodape_frente(&$pdf);
  diario_classe_preenche_chamada(&$pdf, $aulas);

}

function diario_classe_preenche_chamada(&$pdf, $aulas='1|74') {
  global $frente_tpl, $verso_tpl, $NO_PAGINAS, $PAGINA_ATUAL, $correcao_posicao, $carga_horaria, $datas_chamadas, $diario_info;

  list($aula_inicial, $aula_final) = explode('|', $aulas);

  $pdf->SetFont('Times','',8);

  $posicao_X = 125.685;
  $cont_aulas = $cont_posicao = 0;
  $data_inicial = $data_final = '';

  foreach($datas_chamadas as $data) {

    if ($cont_aulas > $aula_final) break;

    list($ano, $mes, $dia) = explode('-', $data['dia']);

    for($i = 0; $i < $data['flag']; $i++) {

      $cont_aulas++;

      if ($cont_aulas < $aula_inicial) continue;
      if ($cont_aulas > $aula_final) break;

      $posicao_X += $correcao_posicao[++$cont_posicao];

      // MES | DIA
      $pdf->TextWithDirection($posicao_X,42,$mes,'U');
      $pdf->TextWithDirection($posicao_X,47,$dia,'U');

      $data_inicial = (empty($data_inicial)) ? $data['dia'] : $data_inicial;
      $data_final = ($cont_aulas == $aula_final) ? $data['dia'] : $data_final;
    }
  }

  if ($PAGINA_ATUAL % 2 != 0) diario_classe_preenche_alunos(&$pdf, $aulas);

  if ($diario_info['fl_finalizada'] == 't') {
    // INUTILIZA ESPAÇO EM BRANCO
    $posicao_X += $correcao_posicao[++$cont_posicao];
    if ($cont_posicao == 73) {
       $pdf->Line($posicao_X - 1, 41, $posicao_X - 1, 280.5); // LINHA VERTICAL 1
       $posicao_X += $correcao_posicao[++$cont_posicao];
       $pdf->Line($posicao_X - 1, 41, $posicao_X - 1, 280.5); // LINHA VERTICAL 2
    }
    if ($cont_posicao > 0 && $cont_posicao < 73) {
       $pdf->Line($posicao_X - 1, 42,  $posicao_X - 1, 280.5); // LINHA VERTICAL 1
       $pdf->Line($posicao_X - 1, 42, 352.5, 280.5); // TRAÇO DIAGONAL 1
    }
  }

  $aula_inicial = $aula_final + 1;

  for ($j = $aula_inicial; $j <= $carga_horaria['get_carga_horaria_realizada']; $j++) {
    $aula_final++;
    if ($aula_final % 74 == 0) break;
  }

  $aulas = implode('|', array($aula_inicial, $aula_final));

  // ADICIONA NOVO VERSO
  $pdf->AddPage();
  $pdf->setSourceFile($verso_tpl);
  $tplIdx = $pdf->importPage(1);
  $pdf->useTemplate($tplIdx);
  $pdf->SetMargins(0, 0, 0);
  $pdf->SetLineWidth(0.4);
  ++$PAGINA_ATUAL;

  diario_classe_preenche_cabecalho_verso(&$pdf);
  diario_classe_preenche_bases_conhecimento_e_atividades("$data_inicial|$data_final", &$pdf);

  if ($PAGINA_ATUAL >= $NO_PAGINAS) {
      diario_classe_preenche_observacoes_competencias(&$pdf);
      return;
  }
  else {
    if ($diario_info['fl_finalizada'] == 't') { 
      // INUTILIZA ESPAÇO EM BRANCO - ÁREA OBSERVAÇÕES E COMPETÊNCIAS
      $pdf->Line(33, 222.75, 210.5, 268.5); // TRAÇO DIAGONAL 1
      $pdf->Line(216, 222.75, 403.5, 268.5); // TRAÇO DIAGONAL 2
    }
  }

  // ADICIONA NOVA FRENTE
  $pdf->AddPage();
  $pdf->setSourceFile($frente_tpl);
  $tplIdx = $pdf->importPage(1);
  $pdf->useTemplate($tplIdx);
  $pdf->SetMargins(0, 0, 0);
  $pdf->SetLineWidth(0.4);
  ++$PAGINA_ATUAL;

  // PREENCHE DEMAIS PÁGINAS DE FORMA RECURSIVA
  diario_classe_preenche(&$pdf, $aulas);

}

function diario_classe_preenche_notas_aluno($ref_pessoa, &$pdf) {
   global $conn, $sql_notas_aluno;

  $posicao_X = 355;

  $aluno_notas = $conn->get_all(sprintf($sql_notas_aluno, $ref_pessoa, '%'));

  $nota_arredondamento = 0;

  foreach($aluno_notas as $nota) {

    if ($nota['ref_diario_avaliacao'] == 6) {
        $nota_arredondamento = $nota['nota'];
        continue;
    }

    if ($nota['ref_diario_avaliacao'] == 7) {
        if ($nota['nota'] != -1) {
            $nota['nota'] = $nota['nota_distribuida'] =  $nota['nota'] + $nota_arredondamento;
        }
        else
            $nota['nota'] = $nota['nota_distribuida'] = $nota_arredondamento;
    }

    $nota_parcial = ($nota['nota_distribuida'] > 0) ? number::numeric2decimal_br($nota['nota'],1) : '  -';

    //// NOTA PARCIAL
    $pdf->SetX($posicao_X);

    $pdf->Write(0, utf8_decode($nota_parcial));

    $posicao_X += 7.15;

  }

  $pdf->SetFont('Times','',10);
}

function diario_classe_preenche_notas_distribuidas(&$pdf) {
  global $notas_distribuidas;

  $pdf->SetFont('Times','',10);
  $pdf->SetY(45.5);

  $posicao_X = 355;

  foreach($notas_distribuidas as $nota) {

    $nota_distribuida = ($nota['nota_distribuida'] > 0) ? number::numeric2decimal_br($nota['nota_distribuida'],1) : '  -';

    //// NOTA PARCIAL
    $pdf->SetX($posicao_X);

    $pdf->Write(0, utf8_decode($nota_distribuida));

    $posicao_X += 7.15;

  }

  $pdf->SetXY(20, 50);
}


function diario_classe_preenche_faltas_aluno($ref_pessoa, &$pdf, $aulas='1|74') {
  global $conn, $sql_faltas_aluno, $correcao_posicao;

  list($aula_inicial, $aula_final) = explode('|', $aulas);

  $pdf->SetFont('Times','',8);

  $posicao_X = 125.685;
  $cont_posicao = $cont_aulas = 0;

  $aluno_faltas = $conn->get_all(sprintf($sql_faltas_aluno, $ref_pessoa));

  foreach($aluno_faltas as $chamada) {

    if ($cont_aulas > $aula_final) break;

    for($j = 0; $j < $chamada['aulas']; $j++) {

      $cont_aulas++;

      if ($cont_aulas < $aula_inicial) continue;
      if ($cont_aulas > $aula_final) break;

      if ($cont_posicao == 0) $pdf->SetX($posicao_X + 1.25); else $pdf->SetX($posicao_X + 0.5);

      if($chamada['faltas'] > 0) {
        $pdf->Write(0, 'F');
        $chamada['faltas']--;
      }
      else {
        $pdf->Write(0, '.');
      }

      $posicao_X += $correcao_posicao[++$cont_posicao];

    }
  }

  $pdf->SetFont('Times','',10);

}

// INICIALIZA O DOCUMENTO PDF
$pdf = new DiarioClassePDF('L','mm','A3');

// PREPARA O DOCUMENTO A PRIMEIRA PÁGINA
$pdf->AddPage();
$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->setSourceFile($frente_tpl);

// IMPORTA A PRIMEIRA PÁGINA DO TEMPLATE
$tplIdx = $pdf->importPage(1);

// SELECIONA A PÁGINA DE TRABALHO
$pdf->useTemplate($tplIdx);
$pdf->SetMargins(0, 0, 0);
$pdf->SetLineWidth(0.4);

// PREENCHE O DIARIO DE CLASSE
diario_classe_preenche(&$pdf, $NO_AULAS);


// MSIE hacks. Need this to be able to download the file over https
// All kudos to http://in2.php.net/manual/en/function.header.php#74736
//header("Content-Transfer-Encoding", "binary");
//header('Cache-Control: maxage=3600'); //Adjust maxage appropriately
//header('Pragma: public');


// GRAVA O ARQUIVO PDF E ENVIO AO NAVEGADOR
$nome_arquivo = "Diario_Classe_". $ABREVIATURA ."_". $TURMA ."_". $ANO."_". $SEMESTRE ."_SEMESTRE.pdf";

$pdf->Output("$nome_arquivo", 'D');

//header("Location: diarios_classe/pdf_tmp/$nome_arquivo");


?>

