<?php
/*
 * Arquivo com as configuracoes iniciais
*/
require_once("../../../app/setup.php");
require_once($BASE_DIR .'core/reports/header.php');
require_once($BASE_DIR .'core/situacao_academica.php');

$header  = new header($param_conn);

/**
 * Parametros do formulario
 */
$periodo = (string) $_POST['periodo'];
$campus  = (int) $_POST['campus'];
$curso   = (int) $_POST['curso'];
$turma   = (string) $_POST['turma'];

if(empty($periodo) || $campus == 0 || $curso == 0 || empty($turma)) {
    echo '<script language="javascript">window.alert("Nenhum diario a ser exibido!");</script>';
    echo '<meta http-equiv="refresh" content="0;url=index.php">';
    exit;
}

/*
 * Estancia a classe de conexao e abre
*/
$conn = new connection_factory($param_conn);


$NOTAS = mediaPeriodo($periodo);
$MEDIA_FINAL_APROVACAO = $NOTAS['media_final'];
$NOTA_MAXIMA = $NOTAS['nota_maxima'];


/**
 * Busca a descricao do periodo
 */
$sql_periodo = '
SELECT DISTINCT descricao
FROM periodos WHERE id = \''. $periodo.'\';';

$desc_periodo = $conn->get_one($sql_periodo);


/**
 * Busca a descricao do curso
 */
$sql_curso = "
SELECT DISTINCT
    a.ref_curso || ' - ' || c.descricao AS curso, b.nome_campus
FROM
    disciplinas_ofer a, cursos c, campus b
WHERE
    a.ref_periodo = '".$periodo."' AND
    c.id = a.ref_curso AND
    a.ref_curso = ".$curso." AND
    a.ref_campus = b.id AND
    b.id = $campus; ";

$desc_curso = $conn->get_row($sql_curso);


/**
 * Conteudo da legenda
 */
$sql_legenda = "
SELECT DISTINCT
    o.id AS diario,
    d.id,
    d.descricao_disciplina,
    d.descricao_extenso,
    d.carga_horaria,
    professor_disciplina_ofer_todos(o.id) AS prof,
    o.fl_digitada,
    o.fl_finalizada
FROM
    disciplinas d, disciplinas_ofer o, disciplinas_ofer_prof dp
WHERE
    d.id = o.ref_disciplina AND
    dp.ref_disciplina_ofer = o.id AND
    dp.ref_professor IS NOT NULL AND
    o.id IN (
			SELECT DISTINCT
        ref_disciplina_ofer
    FROM
        matricula a, pessoas b
    WHERE
        (a.dt_cancelamento is null) AND
        a.ref_disciplina_ofer IN (
            SELECT
                id from disciplinas_ofer
            WHERE
                is_cancelada = '0' AND
                ref_curso = $curso AND
                ref_periodo = '$periodo'
        ) AND
        a.ref_pessoa = b.id AND
        a.ref_pessoa IN(
            SELECT DISTINCT ref_pessoa
            FROM contratos
            WHERE
                ref_curso = $curso AND
                turma = '$turma'
        ) AND
        a.ref_motivo_matricula = '0'

        ) AND
    is_cancelada = '0'
ORDER BY diario;";

$arr_legenda = $conn->get_all($sql_legenda);


/**
 * Consulta principal
 */
$sql_rel = "
SELECT * FROM (
    SELECT DISTINCT
        b.nome, b.id as matricula, a.nota_final, a.num_faltas, ref_disciplina_ofer
    FROM
        matricula a, pessoas b
    WHERE
        (a.dt_cancelamento is null) AND
        a.ref_disciplina_ofer IN (
            SELECT
                id from disciplinas_ofer
            WHERE
                is_cancelada = '0' AND
                ref_curso = $curso AND
                ref_periodo = '$periodo'
        ) AND
        a.ref_pessoa = b.id AND
        a.ref_pessoa IN(
            SELECT DISTINCT ref_pessoa
            FROM contratos
            WHERE
                ref_curso = $curso AND
                turma = '$turma'
        ) AND
        a.ref_motivo_matricula = '0'
) AS T1
ORDER BY lower(to_ascii(nome,'LATIN1')), ref_disciplina_ofer";


$sql_rel = "
SELECT * FROM (
    SELECT DISTINCT
        b.nome, b.id as matricula, a.nota_final, a.num_faltas, ref_disciplina_ofer

    FROM
        matricula a, pessoas b
    WHERE
        (a.dt_cancelamento is null) AND
        a.ref_disciplina_ofer IN (
            %s
        ) AND
        a.ref_pessoa = b.id AND
        a.ref_pessoa IN(
            SELECT DISTINCT ref_pessoa
            FROM contratos
            WHERE
                ref_curso = $curso AND
                turma = '$turma'
        ) AND

        a.ref_motivo_matricula = '0'
) AS T1
ORDER BY lower(to_ascii(nome,'LATIN1')), ref_disciplina_ofer";


$r1 = '#FFFFFF';
$r2 = '#FFFFCC';
$l = $t = 0;
$diarios_turma = array();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title><?=$IEnome?></title>
        <link href="<?=$BASE_URL?>public/styles/relatorio.css" rel="stylesheet" type="text/css">
        <link href="<?=$BASE_URL?>public/styles/print.css" rel="stylesheet" type="text/css" media="print" />
        
        <script type="text/javascript" language="javascript" src="<?=$BASE_URL .'lib/jquery.min.js'?>"></script>
        <script type="text/javascript" language="javascript" src="<?=$BASE_URL .'lib/jquery.floatheader.min.js'?>"></script>
        <script type="text/javascript">
	      <!--
		      jQuery(document).ready(function() {
			      jQuery('#faltas_nota_global').floatHeader({
				      fadeIn: 250, 
				      fadeOut: 250
			      });
		      });
	      //-->
        </script>
    </head>
    <body>
        <?php echo $header->get_empresa($PATH_IMAGES, $IEnome); ?>
        <h2>Resumo de notas e faltas do curso no per&iacute;odo</h2>
        <p>
            <b style="color:red;">Atenção:</b><br />
            <span style="color: teal; font-size: 0.8em;font-style: italic;">
            <!-- Este relatório exibe somente os diários preenchidos ou fechados.<br />-->
            - Lista todos os alunos com matrícula no período
            selecionado e que tenham <br />&nbsp;&nbsp;vínculo no curso/turma selecionados,
            independente da situação atual do aluno;
            <br />
            - As notas globais são calculadas com base nas notas distribu&iacute;das, <br />&nbsp;&nbsp;
             ou seja aquelas efetivamente lançadas pelo professor.
            <br />
            - As faltas globais em % é calculada com base na carga horária <br />&nbsp;&nbsp;
             efetivamente realizada (das chamadas lançadas pelo professor).
            </span>
        </p>
        <strong>Per&iacute;odo:</strong> <?=$desc_periodo?><br />
        <strong>Curso:</strong> <?=$desc_curso[0]?><br />
        <strong>Turma:</strong> <?=$turma?><br />
        <strong>Campus:</strong> <?=$desc_curso[1]?><br />
        <strong>Data de emiss&atilde;o:</strong> <?=date("d/m/Y H:m s")?><br />
        
        <br /><br />
        <b>LEGENDA</b>
        <table cellpadding="0" cellspacing="0" class="relato">
            <tr bgcolor="#cccccc" class="header">
                <th align="center"><strong>C&oacute;d. Di&aacute;rio</strong></th>
                <th align="center"><strong>Descri&ccedil;&atilde;o</strong></th>
                <th align="center"><strong>Professor(a)</strong></th>
                <th align="center"><strong>CH Prevista</strong></th>
                <th align="center"><strong>CH Realizada</strong></th>
                <th align="center"><strong>N Distribu&iacute;da</strong></th>
                <th align="center"><strong>Situa&ccedil;&atilde;o</strong></th>
            </tr>
            <?php foreach($arr_legenda as $legenda) : 
              
              $lcolor = ( ($l % 2) == 0) ? $r1 : $r2;
              $l++;
            ?>
            <tr bgcolor="<?=$lcolor?>">
                <td align="center"><?=$legenda['diario']?></td>
                <td><?=$legenda['id']?> - <?=$legenda['descricao_extenso']?></td>
                <td><?=$legenda['prof']?></td>
                <td align="center"><?=$legenda['carga_horaria']?></td>
                
                        <?php
                        //Carga horaria realizada
                        $sql_realizada = "
                            SELECT SUM(CAST(flag AS INTEGER)) AS carga
                            FROM  diario_seq_faltas
                            WHERE  ref_disciplina_ofer = ".$legenda['diario']." ;";

                        $carga_realizada = $conn->get_one($sql_realizada);

                        $destaca_carga_realizada = '';
                        if ( $carga_realizada == "") {
                            $carga_realizada = 0;
                            $destaca_carga_realizada = ' bgcolor="#cccccc"';
                        }
                        
                        $arr_diarios[$legenda['diario']]['diario'] = $legenda['diario'];
                        $arr_diarios[$legenda['diario']]['ch_realizada'] = $carga_realizada;
                        $diarios_turma[] = $legenda['diario'];                      
                        
                        ?>
                <td align="center" <?=$destaca_carga_realizada?>>
                <?=$carga_realizada?>
                </td>               
                        <?php
                        //Nota distribuida
                        $sql_distribuida = "
                            SELECT SUM(nota_distribuida) AS nota
                            FROM  diario_formulas
                            WHERE  grupo ILIKE '%-".$legenda['diario']."' ;";

                        $nota_distribuida = $conn->get_one($sql_distribuida);

                        
                        if ( $nota_distribuida == "") {
                            $nota_distribuida = 0;
                        }                     
                        
                        $destaca_nota_distribuida = ($nota_distribuida == 0) ? ' bgcolor="#cccccc"' : ''; 
                        
                        $arr_diarios[$legenda['diario']]['nota_distribuida'] = $nota_distribuida;
                        
                        ?>
                <td align="center" <?=$destaca_nota_distribuida?>>
                  <?=$nota_distribuida?>
                </td>
                <td>
                        <?php
                        //Situacao do diario
                        if($legenda['fl_digitada'] == 't') {
                            echo '<font color="red"><b>Fechado</b></font>';
                        }
                        elseif($legenda['fl_digitada'] == 'f' AND $legenda['fl_finalizada'] == 't') {
                            echo '<font color="blue"><b>Preenchido</b></font>';
                        }
                        elseif($legenda['fl_digitada'] == 'f' AND $legenda['fl_finalizada'] == 'f') {
                            echo '<font color="green"><b>Aberto</b></font>';
                        }
                        ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br /><br />
        <?php
        
          $diarios = implode(',', $diarios_turma);
          
          $arr_rel = $conn->get_all(sprintf($sql_rel,$diarios));

          $arr_diarios_aluno = array();

          // prepara as matrizes com as informacoes
          foreach($arr_rel as $rel) {
            $diario_id = $rel['ref_disciplina_ofer'];
            
            $destaca_diario_nota = $destaca_diario_faltas = FALSE;
    
            $arr_diarios_aluno[$rel['matricula']][$diario_id]['nota']  = number_format($rel['nota_final'],1,',','.');
            $arr_diarios_aluno[$rel['matricula']][$diario_id]['faltas']  = $rel['num_faltas']; 
            $arr_diarios_aluno[$rel['matricula']]['nome']  = $rel['nome'] .' ('. $rel['matricula'] .')';  
            
            $arr_diarios_aluno[$rel['matricula']]['faltas_global']  += $rel['num_faltas'];
            $arr_diarios_aluno[$rel['matricula']]['nota_global']  += $rel['nota_final'];
            $arr_diarios_aluno[$rel['matricula']]['disciplinas_matriculadas']++;
            $arr_diarios_aluno[$rel['matricula']]['ch_realizada_disciplinas_matriculadas'] += $arr_diarios[$diario_id]['ch_realizada'];
            
            $nota_distribuida  = $arr_diarios[$diario_id]['nota_distribuida'];
            
            if($arr_diarios[$diario_id]['nota_distribuida'] > 0) {
              $aproveitamento = (($rel['nota_final'] * 100) / $arr_diarios[$diario_id]['nota_distribuida']) / $NOTA_MAXIMA;
              $arr_diarios_aluno[$rel['matricula']]['disciplinas_nota_distribuida']++;
            }
            else
              $aproveitamento = $rel['nota_final'];
              
            $arr_diarios_aluno[$rel['matricula']]['aproveitamento_global'] += $aproveitamento;
            
            if($aproveitamento < $MEDIA_FINAL_APROVACAO && $arr_diarios[$diario_id]['nota_distribuida'] > 0) 
              $arr_diarios_aluno[$rel['matricula']][$diario_id]['destaca_nota'] = TRUE;
            else
              $arr_diarios_aluno[$rel['matricula']][$diario_id]['destaca_nota'] = FALSE;
            
            
            if( $rel['num_faltas'] > ($arr_diarios[$diario_id]['ch_realizada'] * 0.25) ) 
              $arr_diarios_aluno[$rel['matricula']][$diario_id]['destaca_faltas'] = TRUE;
            else
              $arr_diarios_aluno[$rel['matricula']][$diario_id]['destaca_faltas'] = FALSE;
                          
          }       
        
        ?>

        <table cellpadding="0" cellspacing="0" class="relato" id="faltas_nota_global">
          <thead>
            <tr bgcolor="#cccccc" class="header">
                <th rowspan="2">
                    <strong>Aluno</strong>
                </th>
                <?php foreach($diarios_turma as $diario) : ?>
                <th colspan="2"><strong><?=$diario?></strong></th>
                <?php endforeach; ?>
                <th colspan="2"><strong>Global</strong></th>
            </tr>
            <tr bgcolor="#cccccc" class="header">
                <?php for($i = 0; $i <= count($diarios_turma); $i++): ?>
                <th><strong>N</strong></th>
                <th><strong>F</strong></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>

            <?php foreach($arr_diarios_aluno as $aluno_id => $aluno) : 
            
              $tcolor = ( ($t % 2) == 0) ? $r1 : $r2;
              $t++;
              
              $falta_global = ($aluno['faltas_global'] * 100 ) / $aluno['ch_realizada_disciplinas_matriculadas'];              
              $destaca_falta_global = ($falta_global >= 25) ? ' bgcolor="#cccccc"' : '';
              $falta_global = number_format($falta_global,1,',','.');
              
              if($aluno['aproveitamento_global'] > 0) {              
                $nota_global = ($aluno['aproveitamento_global'] / $aluno['disciplinas_nota_distribuida']);
                $destaca_nota_global = ($nota_global < $MEDIA_FINAL_APROVACAO) ? ' bgcolor="#cccccc"' : '';
                $nota_global = number_format($nota_global,1,',','.');                
              }
              else {
                $nota_global = '-';              
                $destaca_nota_global = '';
              }
              
            ?>

            <tr valign="top" bgcolor="<?=$tcolor?>">
                <td width="300">
                    <?=$aluno['nome']?>
                </td>
                    <?php foreach($diarios_turma as $diario): 
                    
                       if($aluno[$diario]['destaca_nota'])
                        $destaca_nota = ' bgcolor="#cccccc"';
                       else
                        $destaca_nota = '';
                        
                       if($aluno[$diario]['destaca_faltas'])
                        $destaca_faltas = ' bgcolor="#cccccc"';
                       else
                        $destaca_faltas = '';
                    
                    ?>
                <td align="center" <?=$destaca_nota?>>
                    <?php
                    if (array_key_exists($diario, $aluno)) : ?>
                      <?=$aluno[$diario]['nota']?></td>
                      <td align="center" <?=$destaca_faltas?>><?=$aluno[$diario]['faltas']?>
                    <?php else : ?>
                      -</td><td align="center">-                     
                    <?php endif;?>
                </td>                
                <?php endforeach; ?>
                <td align="center" <?=$destaca_nota_global?>><?=$nota_global?></td>
                <td align="center" <?=$destaca_falta_global?>><?=$falta_global?>&nbsp;%</td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <br />
        <div class="nao_imprime">
            <input type="button" value="Imprimir" onClick="window.print()" />
            &nbsp;&nbsp;&nbsp;
            <a href="#" onclick="javascript:window.close();">Fechar</a>
        </div>
        <br />
    </body>
</html>
