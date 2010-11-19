<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/number.php');
require_once($BASE_DIR .'core/reports/header.php');

  
$conn = new connection_factory($param_conn);
$header  = new header($param_conn);

$levantamento_id = (string) $_GET['levantamento'];

if (!is_string($levantamento_id))
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Dados invalidos!");window.close();</script>');



//$sql_levantamento_docente = "SELECT DISTINCT ref_periodo FROM desempenho_docente_nota WHERE ref_professor = $sa_ref_pessoa;";

$sql_levantamento = "SELECT descricao, nota_maxima FROM desempenho_docente_levantamento WHERE ref_periodo = '$levantamento_id';";
    
$sql_criterios = "SELECT 
                    criterio_id, 
                    descricao 
                FROM 
                    desempenho_docente_criterio 
                WHERE criterio_id IN
                                (
                                    SELECT DISTINCT ref_criterio
                                    FROM 
                                        desempenho_docente_nota 
                                    WHERE 
                                        ref_professor = $sa_ref_pessoa AND
                                        ref_periodo = '$levantamento_id'
                    
                                )
                ORDER by criterio_id;";

$sql_avaliacao = " SELECT 
                        ref_disciplina_ofer, 
                        descricao_disciplina(get_disciplina_de_disciplina_of(ref_disciplina_ofer)), 
                        ref_criterio, 
                        nota_media 
                    FROM 
                        desempenho_docente_nota 
                    WHERE 
                        ref_professor = $sa_ref_pessoa AND
                        ref_periodo = '$levantamento_id'
                    ORDER by ref_disciplina_ofer, ref_criterio;";


	
$avaliacao = $conn->get_all($sql_avaliacao);
	
$count_avaliacao = count($avaliacao);

if ($count_avaliacao == 0)
  exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Nenhum dado encontrado para o levantamento informado!");window.close();</script>');


$nome_professor = $conn->get_one('SELECT nome FROM pessoas WHERE id = '. $sa_ref_pessoa .';');
$levantamento = $conn->get_row($sql_levantamento);
$criterios = $conn->get_all($sql_criterios);

$num_criterios = count($criterios);


?>
<html>
<head>
  <title><?=$IEnome?> - Sistema Acad&ecirc;mico</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link href="<?=$BASE_URL?>public/styles/relatorio.css" rel="stylesheet" type="text/css">
  <link href="<?=$BASE_URL?>public/styles/print.css" rel="stylesheet" type="text/css" media="print" />
</head>
<body>
	<div align="left">
      	<?=$header->get_empresa($PATH_IMAGES)?>
   </div> 
      <h2>Desempenho Docente</h2>
    <div id="cabecalho" style="text-align: left;">
      <font color="#000000" size="2"><strong> Professor(a): </strong><?=$nome_professor?>
        <br /><strong>Levantamento: </strong><?=$levantamento['descricao']?> &nbsp;&nbsp;&nbsp;
             <strong>Nota m&aacute;xima: </strong><?=$levantamento['nota_maxima']?>
      </font><br />
    </div>
    <h4>Disciplinas Avaliadas</h4>
    <table cellpadding="0" cellspacing="0" class="relato">
	  <tr bgcolor="#666666">
	    <th><div align="center"><font color="#FFFFFF"><b>C&oacute;d. do Di&aacute;rio</b></font></div></th>
            <th><div align="center"><font color="#FFFFFF"><b>Disciplina</b></font></div></th>

            <?php foreach ($criterios as $c) : ?>
                <th><div align="center"><font color="#FFFFFF"><b><?=$c['descricao']?></b></font></div></th>
            <?php endforeach; ?>
	  </tr>

        <?php
            
            $diario_tmp = 0;

            $count_criterios = 1;

            foreach ($avaliacao as $diario) : 
                
        ?>

        <?php if($diario_tmp == $diario['ref_disciplina_ofer']) : ?>

            <td align="center">
                <?=number::numeric2decimal_br($diario['nota_media'],1)?>
            </td>
        <?php

            $diario_tmp = $diario['ref_disciplina_ofer'];            
            continue;
                 
            else : 
                $st = ($st == '#F3F3F3') ? '#FFFFFF' : '#F3F3F3';
        ?>
            <tr bgcolor="<?=$st?>">

            <td align="center">
                <?=$diario['ref_disciplina_ofer']?>
            </td>
            <td align="center">
                <?=$diario['descricao_disciplina']?>
            </td> 
    
            <td align="center">
                <?=number::numeric2decimal_br($diario['nota_media'],1)?>
            </td>
        <?php 
            endif; 
            $diario_tmp = $diario['ref_disciplina_ofer'];
            $count_criterios++;
            if ($count_criterios == $num_criterios) :
                $count_criterios = 1;
            
        ?>
            </tr>

        <?php
            endif; 
            endforeach; 
        ?>
    </table>
    <br />


<span style="color: teal; font-size: 0.8em;font-style: italic;"><strong>*</strong>&nbsp;&nbsp;D&uacute;vidas quanto a sua avalia&ccedil;&atilde;o procure a Coordena&ccedil;&atilde;o de Assuntos Did&aacute;dicos e Pedag&oacute;gicos.</span>

<br /><br />

<div class="nao_imprime">
<input type="button" value="Imprimir" onClick="window.print()">
&nbsp;&nbsp;&nbsp;
<a href="#" onclick="javascript:window.close();">Fechar</a>
</div>
<div style="clear: both;line-height: .3em;">
 <br /><hr color="#868686" size="2">
</div>
<br />
</body>
</html>
