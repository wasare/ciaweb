<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/situacao_academica.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/reports/header.php');
require_once($BASE_DIR .'core/date.php');

  
$conn = new connection_factory($param_conn);
$header  = new header($param_conn);

$aluno_id = (int) $_GET['aluno'];

if ($aluno_id == 0)
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Dados invalidos!");window.close();</script>');

$sql1 = "SELECT DISTINCT
    d.id, 
    s.descricao as periodo, 
    d.descricao_disciplina as descricao, 
    d.carga_horaria, 
    m.ref_periodo, 
    CAST(m.num_faltas AS INTEGER) as faltas, 
    CAST(m.nota_final AS FLOAT) as nota_final, 
    m.nota as nota, 
    m.ref_disciplina_ofer as oferecida,
    m.ref_motivo_matricula,
    m.ref_curso,
    c.id as contrato_id,
    professor_disciplina_ofer_todos(o.id),
    get_carga_horaria_realizada(o.id) as carga_horaria_realizada,
    o.fl_finalizada
    FROM 
        matricula m, disciplinas d, disciplinas_ofer o, periodos s, contratos c
    WHERE 
        m.ref_pessoa = $aluno_id AND
        c.id = m.ref_contrato AND
        m.ref_periodo = s.id AND
        m.ref_disciplina_ofer = o.id AND 
        d.id = o.ref_disciplina AND
        o.is_cancelada = '0' AND
        s.id = o.ref_periodo
    ORDER BY s.descricao, 3";

	
$ficha_academica = $conn->get_all($sql1);
	
$contMatriculada = count($ficha_academica);

if ($contMatriculada == 0)
  exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Nenhum dado encontrado para o aluno / contrato informado!");window.close();</script>');


$nome_aluno = $conn->get_one('SELECT nome FROM pessoas WHERE id = '. $aluno_id .';');

$contratos = $conn->get_all('SELECT DISTINCT c.id, pessoa_nome(c.ref_pessoa) AS nome , c.ref_curso, curso_desc(c.ref_curso), c.dt_formatura, c.dt_ativacao, c.dt_desativacao, get_campus(c.ref_campus) as campus FROM contratos c WHERE c.ref_pessoa = '. $aluno_id .' ORDER BY c.dt_ativacao, nome;');

?>
<html>
<head>
  <title><?=$IEnome?> - Sistema Acad&ecirc;mico</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="<?=$BASE_URL?>public/styles/relatorio.css" rel="stylesheet" type="text/css">
  <link href="<?=$BASE_URL?>public/styles/print.css" rel="stylesheet" type="text/css" media="print" />
</head>
<body>
	<div align="left">
      	<?=$header->get_empresa($PATH_IMAGES)?>
   </div> 
      <h2>Informa&ccedil;&otilde;es Acad&ecirc;micas</h2>
    <div id="cabecalho" style="text-align: left;">
      <font color="#000000" size="2"><b> Aluno: </b><?=$nome_aluno?>
        <a target="_blank" href="<?=$BASE_URL?>/app/relatorios/pessoas/lista_pessoa.php?pessoa_id=<?=$aluno_id?>">
          <img src="<?=$BASE_URL?>/public/images/icons/pessoa.png" width="20" height="20" border="0" title="Informa&ccedil;&otilde;es pessoais" alt="Informa&ccedil;&otilde;es pessoais" />
        </a>
        <br /><b>Matr&iacute;cula: </b><?=str_pad($aluno_id, 5, "0", STR_PAD_LEFT)?></font><br>
    </div>
    <h4>Contratos</h4>
    <table cellpadding="0" cellspacing="0" class="relato">
	  <tr bgcolor="#666666">
	    <th><div align="center"><font color="#FFFFFF"><b>Contrato</b></font></div></th>
	    <th><div align="center"><font color="#FFFFFF"><b>Curso</b></font></div></th>
        <th><div align="center"><font color="#FFFFFF"><b>Campus</b></font></div></th>
        <th><div align="center"><font color="#FFFFFF"><b>Ativa&ccedil;&atilde;o</b></font></div></th>
        <th><div align="center"><font color="#FFFFFF"><b>Cola&ccedil;&atilde;o de grau</b></font></div></th>
        <th><div align="center"><font color="#FFFFFF"><b>Desativa&ccedil;&atilde;o</b></font></div></th>
        <th><div align="center"><font color="#FFFFFF"><b>Exibir</b></font></div></th>
	  </tr>
<?php
// c.id, pessoa_nome(c.ref_pessoa) AS nome , c.ref_curso, curso_desc(c.ref_curso), c.dt_formatura, c.dt_ativacao, c.dt_desativacao
   foreach ($contratos as $c) :

?>
     <tr>
        <td align="center">
          <?=$c['id']?>
        </td>
		<td><span id="<?=$oferecida?>" title="Di&aacute;rio: <?=$oferecida?>  - Professor(es): <?=$professor?>">
            <?=$c['ref_curso']?>&nbsp;-&nbsp;<?=$c['curso_desc']?></span></td>
        <td align="center"><?=$c['campus']?></td>
		<td align="center"><?=date::convert_date($c['dt_ativacao'])?></td>
        <td align="center"><?=date::convert_date($c['dt_formatura'])?></td>
        <td align="center"><?=date::convert_date($c['dt_desativacao'])?></td>
        <td align="center">
          &nbsp;
          <a target="_blank" href="lista_ficha_academica.php?aluno=<?=$aluno_id?>&cs=<?=$c['ref_curso']?>&contrato=<?=$c['id']?>">
            <img src="<?=$BASE_URL?>/public/images/icons/report.png" width="20" height="20" border="0" title="Visualizar ficha acad&ecirc;mica" alt="Visualizar ficha acad&ecirc;mica" />
          </a>
          &nbsp;&nbsp;
          <a target="_blank" href="<?=$BASE_URL?>/app/relatorios/integralizacao_curso/lista_integralizacao_curso.php?aluno=<?=$aluno_id?>&cs=<?=$c['ref_curso']?>&contrato=<?=$c['id']?>">
            <img src="<?=$BASE_URL?>/public/images/icons/verifica.png" width="20" height="20" border="0" title="Verifica integraliza&ccedil;&atilde;o do curso" alt="Verifica integraliza&ccedil;&atilde;o do curso" />
          </a>
          &nbsp;
        </td>
     </tr>
<?php
   endforeach;
?>
    </table>
     <br />
    <h4>Disciplinas</h4>

	<table cellpadding="0" cellspacing="0" class="relato">
	  <tr bgcolor="#666666">
	    <th><div align="center"><font color="#FFFFFF"><b>Per&iacute;odo</b></font></div></th>
        <th><div align="center"><font color="#FFFFFF"><b>Curso</b></font></div></th>
	    <th><div align="center"><font color="#FFFFFF"><b>Componente Modular</b></font></div></th>
	    <th><div align="center"><font color="#FFFFFF"><b>M&eacute;dia</b></font></div></th>
	    <th><div align="center"><font color="#FFFFFF"><b>Faltas</b></font></div></th>        
	    <th><div align="center"><font color="#FFFFFF"><b>Matr&iacute;cula</b></font></div></th>
	    <th><div align="center"><font color="#FFFFFF"><b>Situa&ccedil;&atilde;o</b></font></div></th>
	  </tr>
<?php	

//VARIAVEIS --

//nota total aprovado
$notaAprovado = 0;
//contador
$contAprovado = 0;
//percentual de faltas
$percFaltasAprovado = 0;
//carga horaria realizada
$chRealizadaAprovado = 0;
  
//nota total matriculada
$notaMatriculada = 0;
//percentual de faltas
$percFaltasMatriculada = 0;
//carga horaria realizada
$chRealizadaMatriculada = 0;


foreach ($ficha_academica as $disc) :
	$fcolor = '#000000';
// id	periodo	descricao	carga_horaria	ref_periodo	faltas	nota_final	nota	oferecida	ref_motivo_matricula	professor_disciplina_ofer_todos	carga_horaria_realizada
	$nome_materia = $disc['id'] .' - '. $disc['descricao'];
    $periodo = $disc['periodo'];
    $faltas_materia = $disc['faltas'];
    $ref_periodo = $disc['ref_periodo'];
    $carga_prevista = $disc['carga_horaria'];
    $carga_realizada = $disc['carga_horaria_realizada'];
    $oferecida = $disc['oferecida'];
    $ref_motivo_matricula = $disc['ref_motivo_matricula'];
    $nota_final = $disc['nota_final'];
	$professor = $disc['professor_disciplina_ofer_todos'];
    $curso_id = $disc['ref_curso'];
    $contrato_id = $disc['contrato_id'];
    $fl_finalizada = $disc['fl_finalizada'];

    // APROVEITAMENTO DE ESTUDOS 2
    // CERTIFICACAO DE EXPERIENCIAS 3
    // EDUCACAO FISICA 4
    switch ($ref_motivo_matricula) {
            case 0:
                $matricula = 'CI';
                break;
            case 2:
                $matricula = 'AE';
                break;
            case 3:
                $matricula = 'CE';
                break;
            case 4:
                $matricula = 'DEF';
                break;
    }

    $situacao = '';
    // verifica aprovacao a qualquer tempo considerando qualquer disciplina equivalente, dispensa, etc, em relacao ao contrato
    if(verificaAprovacaoContrato($aluno_id,$curso_id,$contrato_id,$oferecida))
		$situacao = 'A'; 
    else
	    $situacao = 'R';

    // verifica aprovacao considerando exatamente a disciplina matriculada ou dispensada em relacao ao contrato
    if(verificaAprovacaoContratoDisciplina($aluno_id,$curso_id,$contrato_id,$oferecida))
        $situacao = 'A';
    else
        $situacao = 'R'; 
   
    if(!verificaPeriodo($ref_periodo) && $fl_finalizada == 'f')
        $situacao = 'M';

    if(verificaEquivalencia($curso_id,$oferecida))
        $matricula .= ' / DE';

	if($nota_final == ''){
		$nota_final = ' - ';
	}  
   
	$pfaltas = 0;
	$stfaltas = 0;
	if (!empty($carga_realizada)) {
    	$perfaltas = ($faltas_materia * 100) / $carga_realizada;
        $pfaltas = substr($perfaltas,0,5);
		
		$stfaltas = $pfaltas;
        //$stfaltas = getNumeric2Real($pfaltas) . ' %';
    }
    else {
		//$pfaltas = '-'; 
		$stfaltas = $pfaltas;
		$carga_realizada = 0;
	}
    
	
    if ($situacao == 'R') { 
		$fcolor = '#FF0000';
	}
   
    //  DADOS PARA CONTABILIZAR MEDIAS
    if ($situacao == 'A') 
	{
		$contAprovado++;
		//total notas aprovado
		$notaAprovado += $nota_final;
		//total percentual de faltas
		$percFaltasAprovado += $stfaltas;
		//Total carga horaria realizada
		$chRealizadaAprovado += $carga_realizada;
	}

     //total notas matriculada
     $notaMatriculada += $nota_final;
     //total percentual de faltas
     $percFaltasMatriculada += $stfaltas;
     //Total carga horaria realizada
     $chRealizadaMatriculada += $carga_realizada;
	
	if ($st == '#F3F3F3') {
   		$st = '#FFFFFF';
	}
	else {
		$st ='#F3F3F3';
	}

	if (strstr($stfaltas,'.'))
		$stfaltas = number_format($stfaltas,'2',',','.');	
    
	if (strstr($nota_final,'.'))
        $nota_final = number_format($nota_final,'1',',','.');
?>
    
	<tr bgcolor="<?=$st?>">
        <td><font color="<?=$fcolor?>"><?=$periodo?></font></td>
        <td align=center><font color="<?=$fcolor?>"><?=$curso_id?></font></td>
		<td><span id="<?=$oferecida?>" title="Di&aacute;rio: <?=$oferecida?>  - Professor(es): <?=$professor?>">
            <font color="<?=$fcolor?>"><?=$nome_materia?></font></span></td>
		<td align=center><font color="<?=$fcolor?>"><?=$nota_final?></font></td>
        <td align=center><font color="<?=$fcolor?>"><?=$faltas_materia?></font></td>        
        <td align=center><font color="<?=$fcolor?>"><?=$matricula?></font></td>
        <td align=center><font color="<?=$fcolor?>"><?=$situacao?></font></td>
     </tr>
<?php

  endforeach; //FIM FOREACH
                 
?>
</table>
<br /><br />
<div align="left" class="relato" style="font-size: 0.75em;">
    <h4>Legenda</h4>
    <strong>CI</strong> - Disciplina Cursada na Institui&ccedil;&atilde;o<br />
    <strong>AE</strong> - Aproveitamento de Estudos <br />
    <strong>CE</strong> - Certifica&ccedil;&atilde;o Experi&ecirc;ncia <br />
    <strong>DEF</strong> - Dispensado de Educa&ccedil;&atilde;o f&iacute;sica<br /><br />
    <strong>A</strong> - Aprovado<br />
    <strong>R</strong> - Reprovado <br />
    <strong>M</strong> - Matriculado <br /><br />
    <strong>DE</strong> - Disciplina Equivalente<br />
</div>
<br />

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
