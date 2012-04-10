<?php
/*
 * Arquivo com as configuracoes iniciais
*/
require_once('../../../../app/setup.php');
require_once($BASE_DIR .'core/reports/header.php');
require_once($BASE_DIR .'core/situacao_academica.php');
require_once($BASE_DIR .'core/date.php');
require_once($BASE_DIR .'lib/latin1utf8.class.php');


$header  = new header($param_conn);
$trans = new Latin1UTF8();

/**
 * Parametros do formulario
 */
$periodo = (string) $_POST['periodo'];
$campus  = (int) $_POST['campus'];
$curso   = (int) $_POST['curso'];
$turma   = (string) $_POST['turma'];
$turno   = (string) $_POST['turno'];
$turno_desc   = (string) $_POST['turno_desc'];

if(empty($periodo) || $campus == 0 || $curso == 0) {
    die('<script language="javascript">window.alert("Nenhum diario a ser exibido!");window.close();</script>');
}

/*
 * Estancia a classe de conexao e abre
*/
$conn = new connection_factory($param_conn);


$NOTAS = mediaPeriodo($periodo);
$MEDIA_FINAL_APROVACAO = $NOTAS['media_final'];
$NOTA_MAXIMA = $NOTAS['nota_maxima'];


/**
 * Prepara SQL para filtar o turno
 */
$turno_sql = '';
if (!is_numeric($turno) && !empty($turno)) {

  $turno_sql = " AND id IN (SELECT DISTINCT 
                                  o.id 
                                FROM disciplinas_ofer o LEFT JOIN disciplinas_ofer_compl oc 
                                ON (o.id = oc.ref_disciplina_ofer)
                                WHERE 
                                    oc.turno = '$turno' AND
                                    o.ref_campus = $campus AND
                                    o.ref_periodo = '$periodo' AND
                                    o.is_cancelada = '0'
                              )";

}

/**
 * Busca a descricao e as datas de início e término do periodo
 */
$sql_periodo = '
SELECT DISTINCT descricao, dt_inicial, dt_final
FROM periodos WHERE id = \''. $periodo.'\';';

$dados_periodo = $conn->get_row($sql_periodo);

$desc_periodo = $dados_periodo['descricao'];
$inicio_periodo = $dados_periodo['dt_inicial'];
$termino_periodo = $dados_periodo['dt_final'];


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
    o.fl_finalizada,
    d.abreviatura,
    o.turma
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
                $turno_sql
        ) AND
        a.ref_pessoa = b.id AND
        a.ref_pessoa IN(
            SELECT DISTINCT ref_pessoa
            FROM contratos
            WHERE
                ref_curso = $curso
        ) AND
        a.ref_motivo_matricula = '0'

        ) AND
    is_cancelada = '0'
ORDER BY diario;";

$arr_legenda = $conn->get_all($sql_legenda);


$r1 = '#FFFFFF';
$r2 = '#FFFFCC';
$l = $t = 0;
$diarios_turma = array();

$data_emissao = date("d/m/Y H:m s");
$num_total_semanas = ceil(date::datediff('d', strtotime($inicio_periodo, 0), strtotime($termino_periodo , 0)) / 7) - 4;


//$num_total_semanas = ceil(abs(strtotime($termino_periodo , 0) - strtotime($inicio_periodo, 0)) / 86400 / 7);

$num_semanas_atual = ceil(date::datediff('d', strtotime($inicio_periodo, 0), strtotime(date('Y-m-d'), 0)) / 7) - 4;

//$num_semanas_atual = ceil(abs(strtotime(date('Y-m-d'), 0) - strtotime($inicio_periodo, 0)) / 86400 / 7);

$nome_arquivo_csv = 'situacao_diarios_'. $curso .'_'. $periodo .'_'. $campus .'.csv';
$arquivo_csv = $BASE_DIR .'/public/relat/web_diario/'. $nome_arquivo_csv;

if (is_file($arquivo_csv)) @unlink($arquivo_csv);

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
			      jQuery('#situacao_diarios').floatHeader({
				      fadeIn: 250, 
				      fadeOut: 250
			      });
		      });
	      //-->
        </script>
    </head>
    <body>
        <?php echo $header->get_empresa($PATH_IMAGES, $IEnome); ?>
        <h2>Resumo de preenchimento dos di&aacute;rios</h2>

        <strong>Per&iacute;odo:</strong> <?=$desc_periodo?><br />
        <strong>Curso:</strong> <?=$desc_curso[0]?><br />
        <strong>Turma do curso:</strong> <?=$turma?><br />
        <strong>Campus:</strong> <?=$desc_curso[1]?><br />
        <?php if (!empty($turno_desc)) : ?>
          <strong>Turno:</strong> <?=$turno_desc?><br />
        <?php endif; ?>
        <strong>Semanas no per&iacute;odo:</strong> <?=$num_total_semanas?><br />
        <strong>Semanas realizadas:</strong> <?=$num_semanas_atual?><br />     
        
        <strong>Data de emiss&atilde;o:</strong> <?=$data_emissao?><br />
        
        <br /><br />
        <b>LEGENDA</b>
        <table cellpadding="0" cellspacing="0" class="relato" id="situacao_diarios">
          <thead>
            <tr bgcolor="#cccccc" class="header">
                <th align="center"><strong>Di&aacute;rio</strong></th>
                <th align="center"><strong>Disciplina - Turma</strong></th>
                <th align="center"><strong>Descri&ccedil;&atilde;o</strong></th>
                <th align="center"><strong>Professor(a)</strong></th>
                <th align="center"><strong>CH Semanal</strong></th>
                <th align="center"><strong>CH Total Prevista</strong></th>                
                <th align="center"><strong>CH Prevista Atual</strong></th>
                <th align="center"><strong>CH Lan&ccedil;ada</strong></th>                
                <th align="center"><strong>N Distribu&iacute;da</strong></th>
                <th align="center"><strong>Situa&ccedil;&atilde;o</strong></th>
            </tr>
         </thead>
         <tbody>
            <?php
            
            $csv_cabecalho = "\r\n\r\n";
            $csv_cabecalho .= '"Período: '. $desc_periodo .'",'. "\r\n";
            $csv_cabecalho .= '"Curso: '. $desc_curso[0] .'",'. "\r\n";
            $csv_cabecalho .= '"Turma do curso: '. $turma .'",'. "\r\n";
            $csv_cabecalho .= '"Campus: '. $desc_curso[1] .'",'. "\r\n";
            $csv_cabecalho .= '"Data de emissão: '. $data_emissao .'",'. "\r\n";
            
            $csv_cabecalho0 = '"Cód. Diário","Disciplina - Turma",';
            $csv_cabecalho0 .= 'Descrição,"Professor(a)","CH Semanal","CH Total Prevista",';
            $csv_cabecalho0 .= '"CH Prevista Atual","CH Lançada","N Distribuída","Situação",'. "\r\n";
            
            $csv_dados = $csv_cabecalho0;
            
            $situacao_diarios = array();
            
            foreach($arr_legenda as $legenda) : 
              
              $lcolor = ( ($l % 2) == 0) ? $r1 : $r2;
              $l++;
              
              // Situacao do diario
              if($legenda['fl_finalizada'] == 'f' && $legenda['fl_digitada'] == 'f') {
                $situacao_diario = '<font color="green"><b>Aberto</b></font>';
                $csv_situacao = 'Aberto';
                $situacao_diarios[$legenda['diario']] = 'Aberto';
              }
              else {
                if($legenda['fl_digitada'] == 't') {
                  $situacao_diario = '<font color="blue"><b>Preenchido</b></font>';
                  $csv_situacao = 'Preenchido';
                  $situacao_diarios[$legenda['diario']] = 'Preenchido';
                }
                if($legenda['fl_finalizada'] == 't') {
                  $situacao_diario = '<font color="red"><b>Fechado</b></font>';
                  $csv_situacao = 'Fechado';
                  $situacao_diarios[$legenda['diario']] = 'Fechado';
                }   
              }      
              
              $ch_semanal = ceil($legenda['carga_horaria'] / $num_total_semanas);
              $ch_prevista_atual = $num_semanas_atual * ceil($legenda['carga_horaria'] / $num_total_semanas);
              
            ?>
            <tr bgcolor="<?=$lcolor?>">
                <td align="center"><?=$legenda['diario']?></td>
                <td align="center"><?=$legenda['abreviatura']?>-<?=$legenda['turma']?></td>
                
                <td><?=$legenda['descricao_extenso']?></td>
                <td><?=$legenda['prof']?></td>
                <td align="center"><?=$ch_semanal?></td>
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
                        $siglas_diarios[$legenda['diario']] = $legenda['abreviatura'] .'-'. $legenda['turma'];                    
                        
                        ?>
                <td align="center" <?=$destaca_carga_realizada?>>
                  <?=$ch_prevista_atual?>
                </td>
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
                        
                        $nota_distribuida = ($nota_distribuida > 0) ? number_format($nota_distribuida,1,',','.') : $nota_distribuida;
                        
                        $arr_diarios[$legenda['diario']]['nota_distribuida'] = $nota_distribuida;
                        
                        ?>
                <td align="center" <?=$destaca_nota_distribuida?>>
                  <?=$nota_distribuida?>
                </td>
                <td>
                  <?=$situacao_diario?>
                </td>
            </tr>
            <?php 
                
                  $csv_dados .= '"'. $legenda['diario'] .'","'. $legenda['abreviatura'] .'-'. $legenda['turma']. '","';
                  $csv_dados .= $legenda['descricao_extenso'] .'","';
                  $csv_dados .= $legenda['prof'] .'","'. $ch_semanal .'","';
                  $csv_dados .=  $legenda['carga_horaria'] .'","'. $ch_prevista_atual .'","';
                  $csv_dados .= $carga_realizada .'","'. $nota_distribuida .'","';
                  $csv_dados .= $csv_situacao .'",'. "\r\n";
                
                endforeach; ?>
          </tbody>
        </table>
        <br /><br />
        <?php
          
          $csv_dados .= "\r\n\r\n\r\n";       
        
        ?>

         <?php
           $csv_dados .= $csv_cabecalho;
           
           // GRAVA ARQUIVO CSV TEMPORÁRIO 
           $fp = fopen($arquivo_csv, 'w');
           fwrite($fp, $trans->mixed_to_latin1($csv_dados));
           fclose($fp)
         ?>
        <br />
        <div class="nao_imprime">
            <input type="button" value="Imprimir" onClick="window.print()" />
            &nbsp;&nbsp;&nbsp;
            <?php 
                if (is_file($arquivo_csv)) :
            ?>
            <a href="<?=$BASE_URL?>/public/relat/web_diario/<?=$nome_arquivo_csv?>" target="_blank">Baixar em arquivo CSV</a>
            &nbsp;&nbsp;&nbsp;
            <?php endif; ?>
            <a href="#" onclick="javascript:window.close();">Fechar</a>
        </div>
        <br />
    </body>
</html>
