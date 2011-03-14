<?php

require_once(dirname(__FILE__). '/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/reports/header.php');
require_once($BASE_DIR .'core/number.php');

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
}
// ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //

if (!existe_matricula($diario_id)) {
  exit('<script language="javascript">window.alert("Este diário ainda não possue alunos matriculados!"); javascript:window.close(); </script>');
}


$sql3 = "SELECT 
         b.nome, b.id AS ra_cnec, a.ordem_chamada, a.nota_final, a.num_faltas 
         FROM matricula a, pessoas b
         WHERE 
            (a.dt_cancelamento is null) AND
            a.ref_disciplina_ofer = $diario_id AND
            a.ref_pessoa = b.id AND 
            a.ref_motivo_matricula = 0
            
         ORDER BY lower(to_ascii(nome,'LATIN1'));" ;


$qry3 = $conn->get_all($sql3);

$matriculas = count($qry3);

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
        $msg_dispensa .= '<font size="-1" color="brown"><strong>*</strong> ' . $dispensas . ' aluno dispensado, consulte a papeleta completa para exib&iacute;-lo. </font>';
	else
		$msg_dispensa .= '<font size="-1" color="brown"><strong>*</strong> ' . $dispensas . ' alunos dispensados, consulte a papeleta completa para exib&iacute;-los. </font>';
}


?>

<html>
<head>
<title><?=$IEnome?> - papeleta</title>

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

<body>
<font size="2">

<div align="left">
     <?=$header->get_empresa($PATH_IMAGES, $IEnome)?>
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

echo 'Situa&ccedil;&atilde;o: ' . $fl_situacao;

if( $fl_finalizada == 'f') {

    echo '<br /><font color="red" size="-2"><strong>SEM VALOR COMO DOCUMENTO, PASS&Iacute;VEL DE ALTERA&Ccedil;&Otilde;ES</strong></font>';

}

?>
</font>
<table cellspacing="0" cellpadding="0" class="papeleta">
	<tr bgcolor="#cccccc">
		<th  align="center"><b>N&ordm;</b></th>
		<th  align="center"><b>Matr&iacute;cula</b></th>
		<th><b>Nome</b></th>
		<th align="center"><b>Nota</b></th>
		<th align="center"><b>Falta</b></th>
	</tr>

<?php

    
$sql_carga_horaria = "SELECT get_carga_horaria_realizada($diario_id), get_carga_horaria(get_disciplina_de_disciplina_of($diario_id));"; 

$carga_horaria = $conn->get_row($sql_carga_horaria);

$ch_prevista = $carga_horaria['get_carga_horaria'];
$ch_realizada = $carga_horaria['get_carga_horaria_realizada'];

$FaltaMax = $ch_realizada * 0.25;

$i = 0;
$N = 1;

$r1 = '#FFFFFF';
$r2 = '#FFFFCC';


foreach($qry3 as $row3) 
{
   $nome_f = $row3['nome'];
   $racnec = $row3['ra_cnec'];
   $racnec = str_pad($racnec, 5, "0", STR_PAD_LEFT) ;
   $num = $row3['ordem_chamada'];
   
   if ($row3['num_faltas'] > 0){
      $falta = $row3['num_faltas'];
   }
   else{
      $falta = '0';
   }

   if($falta > $FaltaMax) $falta = "<font color=\"red\"><b>$falta</b></font>";
   
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
   
   if ( ($i % 2) == 0)
   {
      $rcolor = $r1;
   }
   else
   {
      $rcolor = $r2;
   }
   print("<tr bgcolor=\"$rcolor\">\n"); 
   print(" <td align=\"center\" >". $N++ ."</td>\n <td align=\"center\" >$racnec</td>\n <td>$nome_f</td>\n "); 
   print ("<td align=\"center\">$nota</td>\n ");
   print ("<td align=\"center\">$falta</td>\n ");
   print("</tr>\n ");
   
   $i++;
}

?>


</table>

<?=$msg_dispensa?>

<hr width="60%" size="1" align="left" color="#FFFFFF">

<?php
	
print("Aulas dadas: <b>$ch_realizada</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
print("Aulas previstas: <b>$ch_prevista</b> <br />");
print("<br />ASSINATURA(S):");


?>
<br /><br />
<div class="nao_imprime">
<input type="button" value="Imprimir" onClick="window.print()">
&nbsp;&nbsp;
<a href="#" onclick="javascript:window.close();">Fechar</a>
</div>
<br /><br />
</body>
</html>
