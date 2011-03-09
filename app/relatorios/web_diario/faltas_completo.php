<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/date.php');

$conn = new connection_factory($param_conn);

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

if (!existe_chamada($diario_id)) {
  exit('<script language="javascript" type="text/javascript">window.alert("Nenhuma chamada registrada para este diario!");window.close(); </script>');
}

$sql3 = "SELECT DISTINCT dia FROM diario_seq_faltas WHERE ref_disciplina_ofer = $diario_id ORDER BY dia;"; 


$sql4 = "SELECT 
         b.nome, b.ra_cnec, a.ordem_chamada, a.nota_final, a.num_faltas
         FROM matricula a, pessoas b
         WHERE
            a.ref_disciplina_ofer = $diario_id AND
            a.ref_pessoa = b.id
         ORDER BY lower(to_ascii(nome,'LATIN1'));" ;

$sql5 = "SELECT dia, CASE 
                        WHEN faltas IS NULL THEN '0' 
                        ELSE faltas
                    END AS faltas
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
SELECT DISTINCT dia FROM diario_seq_faltas WHERE ref_disciplina_ofer = $diario_id ORDER BY dia
) AS T2 ON (data_chamada = dia)

ORDER BY dia;";


$alunos_diario = $conn->get_all($sql4);

if($alunos_diario === FALSE)
{
    envia_erro($sql4);
    exit;
}

$num_chamadas = $conn->get_all($sql3);

if($num_chamadas === FALSE)
{
	envia_erro($sql3);
	exit;
} 
else {
	if(count($num_chamadas) == 0) {

		echo '<script language="javascript">window.alert("Nenhuma chamada realizada para este diário!"); javascript:window.close(); </script>';
      exit;
	}
		
}

?>

<html>
<head>
<title><?=$IEnome?> - relat&oacute;rio de faltas</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
</head>

<div align="left" class="titulo1">
   Relat&oacute;rio de Faltas
</div>
<br /><br />

<?=papeleta_header($diario_id)?>

<br />
<table cellspacing="0" cellpadding="0" class="papeleta">
	<tr bgcolor="#cccccc">
		<th><strong>Ordem</strong></th>
		<th><strong>Matr&iacute;cula</strong></th>
		<th><strong>Nome</strong></th>
        <?php

			foreach($num_chamadas as $d) {
				echo '<th align="center"><strong>'. date::convert_date($d['dia']) .'</strong></th>';
			}

		?>
		<td align="center">Total</td>
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
$r2 = '#FAFAFA';

											
foreach($alunos_diario as $row3) {
	
   $nome_f = $row3["nome"];
   $ra = $row3["ra_cnec"];
   $racnec = str_pad($ra, 5, "0", STR_PAD_LEFT) ;
   $falta_total = $row3['num_faltas'];

   
   if($falta_total > $FaltaMax) { 
	   
	   $falta_total = "<font size=\"3\" color=\"red\"><b>$falta_total</b></font>";
   }
   else {
	   if($falta_total > 0) {

		          $falta_total = "<font size=\"3\"><b>$falta_total</b></font>";
	   }
   }

   if ( ($i % 2) == 0) {
	   
      $rcolor = $r1;
   }
   else {

      $rcolor = $r2;
   }
   
   print("<tr bgcolor=\"$rcolor\">\n"); 
   print ("<td align=\"center\">".$No++."</td>\n ");
   print(" <td align=\"center\">$racnec</td>\n <td>$nome_f</td>\n "); 
   

//   -- RECUPERA AS FALTAS PARCIAIS POR ALUNO

	$aluno_faltas = $conn->get_all(sprintf($sql5,$ra));

	if($aluno_faltas === FALSE) {
		
		envia_erro(sprintf($sql5,$ra));
		exit;
	}


	foreach($aluno_faltas as $row) {

		$N = $row['faltas'];

		if ($N != 0 ) {  $N = '<font size=3><b>'.$N.'</b></font>'; }

		print ("<td align=\"center\">$N</td>\n ");
	}

   print ("<td align=\"center\">$falta_total</td>\n ");
   
   print("</tr>\n ");
   
   $i++;
}

?>


</table>
<hr width="60%" size="1" align="left" color="#FFFFFF">

<?php

print("Aulas dadas: <b>$ch_realizada</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
print("Aulas previstas: <b>$ch_prevista</b> <br />");

?>
<br><br>
<input type="button" value="Imprimir" onClick="window.print()">
&nbsp;&nbsp;
<a href="#" onclick="javascript:window.close();">Fechar</a>

</body>
</html>
