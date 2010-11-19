<?php

header("Cache-Control: no-cache");
//INCLUSAO DE BIBLIOTECAS
require_once("../../../app/setup.php");
require("../../../lib/adodb5/adodb.inc.php"); 
require("../../../lib/adodb5/tohtml.inc.php");


$pessoa = $_GET["aluno"];
$curso = $_GET["cs"];



//Criando a classe de conexão ADODB
$Conexao = NewADOConnection("postgres");

//Setando como conexão persistente
$Conexao->PConnect("host=$host dbname=$database user=$user password=$password");

//Debug
//$Conexao->debug=true;



$sqlPessoa = "
SELECT id, nome 
FROM public.pessoas
WHERE 
id = $pessoa";

$RsPessoa = $Conexao->Execute($sqlPessoa);



$sqlCurso = "
SELECT id, descricao 
FROM public.cursos
WHERE 
id = $curso";

$RsCurso = $Conexao->Execute($sqlCurso);



$sqlDisciplinas = "
SELECT DISTINCT
d.id, d.descricao_disciplina, c.semestre_curso
FROM 
cursos_disciplinas c, disciplinas d
WHERE
c.ref_curso = $curso AND
d.id = c.ref_disciplina 
ORDER BY 3,2;";

$RsDisciplina = $Conexao->Execute($sqlDisciplinas);
  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="../../../lib/prototype.js"></script>
<script language="javascript" src="../../../matriz/index.js"></script>
<script src="../../../lib/functions.js" type="text/javascript"></script>
</head>
<body>
<center>
  <div style="width:760px; text-align:left;">
    <div align="center">
      <h2>Situa&ccedil;&atilde;o do aluno no curso (Hist&oacute;rico)</h2>
    </div>
    <p> 
   	  <strong>Nome:</strong>&nbsp;<?php echo $RsPessoa->fields[0] . " - " . $RsPessoa->fields[1]; ?><br/> 
   	  <strong>Curso:</strong>&nbsp;<?php echo $RsCurso->fields[0] . " - " . $RsCurso->fields[1]; ?><br/>
    </p>
    <div style="border:#000000 solid 2px; background-color:#ffffff; width:760px;">
<?php


	echo ' <table width="100%" border="0" cellspacing="0" cellpadding="0">';
	
	while(!$RsDisciplina->EOF) {
	
		$sqlNotas = " 
		SELECT DISTINCT
		d.id, d.carga_horaria, m.ref_periodo, m.num_faltas, m.nota_final, m.ref_disciplina_ofer
		FROM 
		disciplinas d, matricula m, disciplinas_ofer o 
		WHERE
			m.ref_pessoa = '$pessoa' AND 
			m.ref_curso = '$curso' AND 
			m.ref_disciplina = '" . $RsDisciplina->fields[0] . "' AND
			m.ref_disciplina_ofer = o.id AND 
			d.id = o.ref_disciplina
		ORDER BY 2, 3";
		
		//echo $sqlNotas;
		//die;
		
		$RsNotas = $Conexao->Execute($sqlNotas);
	
		if($RsDisciplina->fields[2] != $lista_periodo)
		{
			echo "<tr>";
			echo "<td colspan=\"3\">";
			echo '<div style="background-color: #000000; color: #ffffff; padding: 1px;">';
			$lista_periodo = $RsDisciplina->fields[2];
			echo $RsDisciplina->fields[2] . "&deg; Período";
			echo '</div>';
			echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td><div style=\"border:#000000 solid 1px;\">&nbsp;Disciplina</div></td>
			  <td><div style=\"border:#000000 solid 1px;\">&nbsp;Nota</div></td>
			  <td><div style=\"border:#000000 solid 1px;\">&nbsp;Falta</div></td>";
			echo "</tr>";
		}

			echo "<tr>";
			echo "<td>";
			echo $RsDisciplina->fields[0] . " - " . $RsDisciplina->fields[1];
			echo "</td>";
			if($RsNotas->fields[4] >= 60)
			{
				echo "<td>" . $RsNotas->fields[4] . "</td><td>" . $RsNotas->fields[3] . "</td>";
			}else
			{
				echo "<td> - </td><td> - </td>";
			}
			
			echo "</tr>";

		
		$RsDisciplina->MoveNext();
	}

	echo '</table>';

?>
    </div>
    <div style="color:#FF0000;">
      <h3>Reprovadas</h3>
    </div>
    <div style="color:#FF0000; border:#FF0000 solid 2px; background-color:#FFFFFF;">
<?php
	
	/* DISCIPLINAS REPROVADAS--  */
	
    echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
	echo "<tr>";
	echo "<td><div style=\"border:#FF0000 solid 1px;\">&nbsp;Disciplina</div></td>
		  <td><div style=\"border:#FF0000 solid 1px;\">&nbsp;Nota</div></td>
		  <td><div style=\"border:#FF0000 solid 1px;\">&nbsp;Falta</div></td>";
	echo "</tr>";
	
	
	$sqlReprovadas = "
	SELECT DISTINCT
	d.id, 
	s.descricao as periodo, 
	d.descricao_disciplina as descricao, 
	d.carga_horaria, 
	m.ref_periodo, 
	m.num_faltas as faltas, 
	m.nota_final as nota_final, 
	m.nota as nota, 
	m.ref_disciplina_ofer as oferecida
	FROM 
		matricula m, disciplinas d, pessoas p, disciplinas_ofer o, periodos s
	WHERE
		m.ref_pessoa = p.id AND 
		p.ra_cnec = $pessoa AND 
		m.ref_curso = $curso AND 
		m.dt_matricula >= '2004-01-01' AND
		m.ref_disciplina_ofer = o.id AND 
		m.nota_final < 60 AND
		
		d.id = o.ref_disciplina AND
		s.id = o.ref_periodo
	ORDER BY 2, 3";
	
	$RsReprovadas = $Conexao->Execute($sqlReprovadas);
	
	
	while(!$RsReprovadas->EOF) {
		
		echo "<tr>";
		echo "<td>&nbsp;" . $RsReprovadas->fields[0] . " - " . $RsReprovadas->fields[2] . "</td>
			  <td>&nbsp;" . $RsReprovadas->fields[6] . "</td>
			  <td>&nbsp;" . $RsReprovadas->fields[5] . "</td>";
		echo "</tr>";
		
		
		$RsReprovadas->MoveNext();
		
	}
	
	echo '</table>';
	
?>
    </div>
  </div>
</center>
</body>
</html>
