<?php


trim($formula);
$formula = str_replace(",",".",$formula);
$formula = strtoupper($formula);

$string = $formula;
$nprovas = $numprovas;

$ch1 = substr_count($string, "(");
$ch2 = substr_count($string, ")");

$operador = substr_count($string, "#");
$operador = $operador + substr_count($string, "%");
$operador = $operador + substr_count($string, "x");
$operador = $operador + substr_count($string, "X");
$operador = $operador + substr_count($string, "|");
$operador = $operador + substr_count($string, "~");

$pv=$pv+substr_count($string, ",");
$pv=$pv+substr_count($string, "_");
$pv=$pv+substr_count($string, ":");
$pv=$pv+substr_count($string, ";");

$letra=$letra+substr_count($string, "A");
$letra=$letra+substr_count($string, "B");
$letra=$letra+substr_count($string, "C");
$letra=$letra+substr_count($string, "D");
$letra=$letra+substr_count($string, "E");
$letra=$letra+substr_count($string, "F");
$letra=$letra+substr_count($string, "G");
$letra=$letra+substr_count($string, "H");
$letra=$letra+substr_count($string, "I");
$letra=$letra+substr_count($string, "J");
$letra=$letra+substr_count($string, "K");
$letra=$letra+substr_count($string, "L");
$letra=$letra+substr_count($string, "M");
$letra=$letra+substr_count($string, "N");
$letra=$letra+substr_count($string, "O");
$letra=$letra+substr_count($string, "Q");
$letra=$letra+substr_count($string, "R");
$letra=$letra+substr_count($string, "S");
$letra=$letra+substr_count($string, "T");
$letra=$letra+substr_count($string, "U");
$letra=$letra+substr_count($string, "V");
$letra=$letra+substr_count($string, "Y");
$letra=$letra+substr_count($string, "Z");
$letra=$letra+substr_count($string, "}");
$letra=$letra+substr_count($string, "{");
$letra=$letra+substr_count($string, "&");
$letra=$letra+substr_count($string, "[");
$letra=$letra+substr_count($string, "]");

if(($ch1 > $ch2) OR ($ch1 < $ch2)) 
{
   print '<script language=javascript>                   window.alert("ERRO ! Você abriu '.$ch1.' chave(s) e fechou '.$ch2.'"); javascript:window.history.back(1);               </script>';
}

if($operador != 0) 
{
   print '<script language=javascript>		                   window.alert("ERRO ! Foi detectado um operador não valido use (+)para soma (-)para subtração (/)para divisão e (*)para multiplicação"); javascript:window.history.back(1); </script>';
}

if($letra != 0) 
{
   print '<script language=javascript>                  window.alert("ERRO ! Foi detectado uma letra não válida use somente P1, P2..... para as provas");                        javascript:window.history.back(1); </script>';
}


if($pv != 0) 
{
   print '<script language=javascript> 	                   window.alert("ERRO ! Foi detectado um separador não valido use somente ponto como separador");                               javascript:window.history.back(1);</script>';
}



if($formula == "") 
{
   print '<script language=javascript> 	                   window.alert("ERRO ! Não foi inserido a Fórmula !  (Obs: no caso de uma única nota deve ser colocado P1)");                      javascript:window.history.back(1); </script>';
}    
else  
{

   for($prova = 1; $prova <= 30; $prova++) 
   {
      $numeroprovas = $numeroprovas + substr_count($string, "P".$prova);
   }
   
   if($numeroprovas != $nprovas) 
   {

      $sqlup = "UPDATE diario_formulas SET formula = '$formula' WHERE grupo ILIKE '$grupo_novo';";

   }  
   else 
   {

      $sqlup = "UPDATE diario_formulas SET formula = '$formula' WHERE grupo ILIKE '$grupo_novo';";
      
	  $qry1 = consulta_sql($sqlup);

      if(is_string($qry1))
      {
           envia_erro($qry1);
           exit;
      } 
                               

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
                d.d_ref_disciplina_ofer = ' . $getofer . '
              ) tmp
        ON ( m.ref_pessoa = id_ref_pessoas )
        WHERE
            m.ref_disciplina_ofer = ' . $getofer . ' AND
        id_ref_pessoas IS NULL
        ORDER BY
                id_ref_pessoas;';

		$qry1 = consulta_sql($qryNotas);

      	if(is_string($qry1))
      	{
           envia_erro($qry1);
           exit;
      	}

      /////////////grava nota zerop para os alunos $contador = 0;
	  
	  $NumNotas = $numprovas;

	  $qryDiario = "BEGIN;";

	  	while($registro = pg_fetch_array($qry1))
      	{
			$ref_pessoa = $registro['ref_pessoa'];

			for($i = 1 ; $i <= $NumNotas; $i++)
			{
				$qryDiario .= ' INSERT INTO diario_notas(ra_cnec, ';
	            $qryDiario .= ' ref_diario_avaliacao,nota,peso,id_ref_pessoas,';
		        $qryDiario .= ' id_ref_periodos,id_ref_curso,d_ref_disciplina_ofer,';
			    $qryDiario .= ' rel_diario_formulas_grupo)';
				$qryDiario .= " VALUES($ref_pessoa,'$i','0','0',$ref_pessoa,'$getperiodo',$getcurso,";
	            $qryDiario .= " $getofer,'$grupo_inicial');";
			}

				$qryDiario .= ' INSERT INTO diario_notas(ra_cnec, ';
		        $qryDiario .= ' ref_diario_avaliacao,nota,peso,id_ref_pessoas,';
				$qryDiario .= ' id_ref_periodos,id_ref_curso,d_ref_disciplina_ofer,';
				$qryDiario .= ' rel_diario_formulas_grupo)';
				$qryDiario .= " VALUES($ref_pessoa,'7','-1','0',$ref_pessoa,'$getperiodo',$getcurso,";
				$qryDiario .= " $getofer,'$grupo_inicial');";
	 	}


	  	$qryDiario .= "COMMIT;";

		//  echo '<br /><br />' . $qryDiario;

		$qry1 = consulta_sql($qryDiario);

    	if(is_string($qry1))
    	{
           envia_erro($qry1);
           exit;
    	}
   }
}

?>
