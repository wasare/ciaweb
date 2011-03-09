<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/number.php');
require_once($BASE_DIR .'core/reports/header.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);
$header  = new header($param_conn);

$diario_id = (int) $_GET['diario_id'];

if(!is_numeric($diario_id))
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Diario invalido!");window.close();</script>');

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(isset($_SESSION['sa_modulo']) && $_SESSION['sa_modulo'] == 'web_diario_login') {
  if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
  }
  // ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //
}

//  INICIALIZA O DIARIO CASO NECESSARIO
if(!is_inicializado($diario_id)) 
{
    if(!ini_diario($diario_id))
    {
        echo '<script type="text/javascript">  window.alert("Falha ao inicializar o diário!!!!!!!"); </script>';
        envia_erro('Falha ao inicializar o diário '. $diario_id .'!!!!!!!');
        exit; 
    }
}
//^  INICIALIZA O DIARIO CASO NECESSARIO ^ //

// ATUALIZA NOTAS E FALTAS CASO O DIARIO TENHA SIDO INICIALIZADO
// SERÁ NECESSARIO PRINCIPALMENTE EM CASOS DE DISPENSA, ONDE O DIARIO É INICIALIZADO SOMENTE PARA O ALUNO DISPENSADO
$qryNotas = 'SELECT
        m.ref_pessoa, id_ref_pessoas
        FROM
            matricula m
        LEFT JOIN (
                SELECT DISTINCT
                d.id_ref_pessoas
            FROM
                diario_notas d
            WHERE
                d.d_ref_disciplina_ofer = ' . $diario_id . '
              ) tmp
        ON ( m.ref_pessoa = id_ref_pessoas )
        WHERE
            m.ref_disciplina_ofer = ' . $diario_id . ' AND
            id_ref_pessoas IS NULL AND
			(m.dt_cancelamento is null) AND
			(m.ref_motivo_matricula = 0)

        ORDER BY
                id_ref_pessoas;';

$qry = $conn->get_all($qryNotas);

foreach($qry as $registro)
{
    $ref_pessoa = $registro['ref_pessoa'];
    atualiza_diario("$ref_pessoa","$diario_id");
}

// ^ ATUALIZA NOTAS E FALTAS CASO O DIARIO TENHA SIDO INICIALIZADO ^//


if (!existe_matricula($diario_id)) {
  exit('<script language="javascript">window.alert("Este diário ainda não possue alunos matriculados!"); javascript:window.close(); </script>');
}

$sql3 = 'SELECT 
            b.nome, b.ra_cnec, a.ordem_chamada, a.nota_final, c.ref_diario_avaliacao, c.nota, a.num_faltas 
        FROM 
            matricula a, pessoas b, diario_notas c 
        WHERE    
            (a.dt_cancelamento is null) AND 
            a.ref_disciplina_ofer = '. $diario_id .' AND 
            a.ref_pessoa = b.id AND 
            b.ra_cnec = c.ra_cnec AND 
            c.d_ref_disciplina_ofer = a.ref_disciplina_ofer AND 
            a.ref_motivo_matricula = 0 
        ORDER BY 
            lower(to_ascii(nome,\'LATIN1\')), ref_diario_avaliacao;';


$matriculas = $conn->get_all($sql3);

if($matriculas === FALSE) {
    exit(envia_erro($sql3));
}


$sql5 = " SELECT fl_finalizada, fl_digitada
            FROM
                disciplinas_ofer
            WHERE
               id = $diario_id;";

$qry5 = $conn->get_row($sql5);

$fl_finalizada = $qry5['fl_finalizada'];
$fl_digitada = $qry5['fl_digitada'];


// APROVEITAMENTO DE ESTUDOS 2
// CERTIFICACAO DE EXPERIENCIAS 3
// EDUCACAO FISICA 4
$msg_dispensa = '';

$sql_dispensas = "SELECT COUNT(*) 
                    FROM 
                        matricula a, pessoas b
                    WHERE 
            
                    (a.dt_cancelamento is null) AND            
                    a.ref_disciplina_ofer = $diario_id AND
                    a.ref_pessoa = b.id AND
                    a.ref_motivo_matricula IN (2,3,4) ;" ;

$dispensas = $conn->get_one($sql_dispensas);

if ($dispensas > 0 ) {
    if($dispensas == 1)
        $msg_dispensa .= '<font size="-1" color="brown"><strong>*</strong> ' . $dispensas . ' aluno dispensado neste di&aacute;rio. </font>';
    else
        $msg_dispensa .= '<font size="-1" color="brown"><strong>*</strong> ' . $dispensas . ' alunos dispensados neste di&aacute;rio. </font>';
}

?>

<html>
<head>
<title><?=$IEnome?> - papeleta completa</title>

<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">

<style media="print">
<!--

.nao_imprime {display:none}

table.papeleta {
    font: 0.7em verdana, arial, tahoma, sans-serif;
    border: 0.0015em solid;
    border-collapse: collapse;
    border-spacing: 0px;
}

.papeleta td {
    font: 0.7em verdana, arial, tahoma, sans-serif;
    border: 0.0015em solid;
    padding: 2px;
    border-collapse: collapse;
    border-spacing: 1px;
}


-->
</style>

</head>

<font size="2">

<div align="left">
     <?=$header->get_empresa($PATH_IMAGES)?>
</div>

<?php


echo papeleta_header($diario_id);


if($fl_finalizada == 'f' && $fl_digitada == 'f') {
    $fl_situacao = '<font color="green"><b>Aberto</b></font>';
}
else {
    if($fl_digitada == 't') {
        $fl_situacao = '<font color="blue"><b>Conclu&iacute;do</b></font>';
    }

    if($fl_finalizada == 't') {
        $fl_situacao = '<font color="red"><b>Finalizado</b></font>';
        $fl_encerrado = 1;
    }
}

echo 'Situação: ' . $fl_situacao;

if( $fl_finalizada == 'f') {

	echo '<br /><font color="red" size="-2"><strong>SEM VALOR COMO DOCUMENTO, PASSÍVEL DE ALTERAÇÕES</strong></font>';

}

$sql_quantidade_notas = "SELECT quantidade_notas_diario 
								FROM tipos_curso 
								WHERE id = get_tipo_curso((SELECT ref_curso FROM disciplinas_ofer WHERE id = $diario_id));";
$quantidade_notas_diario = $conn->get_one($sql_quantidade_notas);

?>
</font>
<table cellspacing="0" cellpadding="0" class="papeleta">
	<tr bgcolor="#cccccc">
		<th><b>N&ordm;</b></th>
		<th><b>Matr&iacute;cula</b></th>
		<th><b>Nome</b></th>
        <?php
            for( $i = 1; $i <= $quantidade_notas_diario; $i++ ) :
        ?>
				<th align="center"><b>N<?=$i?></b></th>
        <?php
           endfor;
        ?>

		<th align="center"><b>N. Extra</b></th>
		<th align="center"><b>Total</b></th>
		<th align="center"><b>Faltas</b></th>
	</tr>
<?php


$sql_carga_horaria = "SELECT get_carga_horaria_realizada($diario_id), get_carga_horaria(get_disciplina_de_disciplina_of($diario_id));";

$carga_horaria = $conn->get_row($sql_carga_horaria);

$ch_prevista = $carga_horaria['get_carga_horaria'];
$ch_realizada = $carga_horaria['get_carga_horaria_realizada'];

$FaltaMax = $ch_realizada * 0.25;


$i = 0;
$No = 1;

$r1 = '#FFFFFF';
$r2 = '#FFFFCC';

foreach($matriculas as $row3)
{
    if ($row3['ref_diario_avaliacao'] == 1)
	{
		$nome_f = $row3["nome"];
		$racnec = $row3["ra_cnec"];
		$racnec = str_pad($racnec, 5, "0", STR_PAD_LEFT) ;
		$num = $row3["ordem_chamada"];
   
		if ($row3["num_faltas"] > 0)
			$falta = $row3["num_faltas"];
		else
			$falta = '0';
		
		if($falta > $FaltaMax) $falta = "<font color=\"red\"><b>$falta</b></font>";
		
		if($row3['nota_final'] != 0)
		{    
			$nota = number::numeric2decimal_br($row3['nota_final'],1);
		}
		else 
		{ 
			$nota = $row3['nota_final'];
		}

		if ($nota < 60)
		{
			$nota = "<font color=\"red\"><b>$nota</b></font>";
		}
   
		if ( ($i % 2) == 0)
		{
			$rcolor = $r1;
		}
		else
		{
			$rcolor = $r2;
		}
   	
		print  ("<tr bgcolor=\"$rcolor\">\n");
		print ("<td align=\"center\">".$No++."</td>\n ");
		print (" <td align=\"center\">$racnec</td>\n <td>$nome_f</td>\n "); 

		$total_nota_webdiario = 0;
	}
		
	$N = $row3['nota'];
    
    if($N < 0)
    {
      $N = '-';
    }
   
    if($N > 0) 
	{ 
		$N = number::numeric2decimal_br($N,1);
    }
    //somatorio nota web diario
    $total_nota_webdiario += $N;

    if ($row3['ref_diario_avaliacao'] <= $quantidade_notas_diario || $row3['ref_diario_avaliacao'] == 7)
		print ("<td align=\"center\">$N</td>\n ");
	//else	

    $i++;
    if ($row3['ref_diario_avaliacao'] != 7)
        continue;
	//if ($row3['ref_diario_avaliacao'] == 7) 
//	{
		print ("<td align=\"center\">$nota</td>\n ");
		print ("<td align=\"center\">$falta</td>\n ");
   
		print ("</tr>\n ");
//	}
  //  else
	//	if ($row3['ref_diario_avaliacao'] <= $quantidade_notas_diario)
	//		print ("<td align=\"center\">$N</td>\n ");
   
   	//$i++;
}

?>

</table>

<?=$msg_dispensa?>

<hr width="60%" size="1" align="left" color="#FFFFFF">

<?php

	print("Aulas dadas: <b>$ch_realizada</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
	print("Aulas previstas: <b>$ch_prevista</b> <br />");
	print("<br />ASSINATURA(S):");
	
    echo '<br /><br />';

    $i = 0;

	$sql_notas_distribuidas = 'SELECT nota_distribuida FROM diario_formulas WHERE grupo ilike \'%-'. $diario_id .'\' order by prova;';
	$notas_distribuidas = $conn->get_all($sql_notas_distribuidas);

?>
	<h4>Notas distribu&iacute;das</h4>
<table cellspacing="0" cellpadding="0" class="papeleta">
    <tr bgcolor="#cccccc">
         <?php
            for( $i = 1; $i <= $quantidade_notas_diario; $i++ ) :
        ?>
                <th align="center"><b>N<?=$i?></b></th>
        <?php
           endfor;
        ?>
		<td align="center"><b>Total</b></td>
    </tr>

    <tr bgcolor="#ffffff">
        <?php
			$total_distribuido = 0;
            $count = 1;
            foreach($notas_distribuidas as $nota)
			{
				$nota_d = number::numeric2decimal_br($nota['nota_distribuida'],'1');
				if($nota_d == 0 || empty($nota_d))
					$nota_d = '-';
                if ($count <= $quantidade_notas_diario)
					echo '<td align="center">'. $nota_d .'</td>';
				$total_distribuido += $nota['nota_distribuida'];
				$count++;
			}
			echo '<td align="center">'. number::numeric2decimal_br($total_distribuido,1) .'</td>';
        ?>
    </tr>
</table>
<font size="-1" color="brown"><strong>*</strong> as notas acima s&atilde;o informadas pelo professor.</font>
<br />

<?php

    if (!empty($msg_dispensa)) {
	
		$sql_dispensas = "SELECT 
         b.nome, b.id AS ra_cnec, a.ordem_chamada, a.nota_final, a.num_faltas, a.ref_motivo_matricula 
         FROM matricula a, pessoas b
         WHERE 
            
            (a.dt_cancelamento is null) AND         
            a.ref_disciplina_ofer = $diario_id AND
            a.ref_pessoa = b.id AND 
            a.ref_motivo_matricula IN (2,3,4)
         	ORDER BY lower(to_ascii(nome,'LATIN1'));" ;
	   
		$qry_dispensas = $conn->adodb->getAll($sql_dispensas);

?>

		<h4> Alunos dispensados </h4>
		<table cellspacing="0" cellpadding="0" class="papeleta">
        <tr bgcolor="#cccccc">
			<td align="center"><b>Matr&iacute;cula</b></td>
			<td><b>Nome</b></td>
			<td align="center"><b>Nota</b></td>
			<td align="center"><b>Motivo</b></td>
		</tr>

<?php
	

	foreach($qry_dispensas as $row3)
	{
		$nome_f = $row3['nome'];
		$racnec = $row3['ra_cnec'];
		$racnec = str_pad($racnec, 5, "0", STR_PAD_LEFT) ;
		$num = $row3['ordem_chamada'];
        $motivo_matricula = $row3['ref_motivo_matricula'];

		if($row3['nota_final'] != 0) {
			$nota = number::numeric2decimal_br($row3['nota_final'],1);
		}
		else {
			$nota = $row3['nota_final'];
		}

		if ($nota < 60)
		{
			$nota = "<font color=\"red\"><b>$nota</b></font>";
		}

        // APROVEITAMENTO DE ESTUDOS 2
		// CERTIFICACAO DE EXPERIENCIAS 3
		// EDUCACAO FISICA 4
        switch ($motivo_matricula) {
    		case 2:
        		$motivo_matricula = 'Aproveitamento de estudos';
        		break;
    		case 3:
        		$motivo_matricula = 'Certifica&ccedil;&atilde;o de experi&ecirc;ncia';
        		break;
    		case 4:
        		$motivo_matricula = 'Educa&ccedil;&atilde;o f&iacute;sica';
        		break;
		}

		//<td width=\"10%\">$num</td>\n
		if ( ($i % 2) == 0) { $rcolor = $r1; } else { $rcolor = $r2; }

		print("<tr bgcolor=\"$rcolor\">\n");
		print("<td align=\"center\">$racnec</td>\n <td>$nome_f</td>\n ");
		print ("<td align=\"center\">$nota</td>\n ");
		print ("<td align=\"center\">$motivo_matricula</td>\n ");
		print("</tr>\n ");

		$i++;
	}
} // end if - somente exibe se houver dispensa

?>


</table>
<br /><br />
<div class="nao_imprime">
<input type="button" value="Imprimir" onClick="window.print()">
&nbsp;&nbsp;
<a href="#" onclick="javascript:window.close();">Fechar</a>
</div>
<br /><br />
</body>
</html>
