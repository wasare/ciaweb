<?php

require_once("../../../app/setup.php");
require_once("../../../lib/adodb5/tohtml.inc.php");
require_once("../../../core/reports/header.php");
require_once("../../../core/reports/carimbo.php");


$conn = new connection_factory($param_conn);

$header  = new header($param_conn);
$carimbo = new carimbo($param_conn);


//Selecionando o campo periodo
if($_POST["periodo1"] != ''){  
	$periodo = $_POST["periodo1"];
}
else{
	$periodo = $_POST["periodo"];
}
  
$curso 		= $_POST["codigo_curso"];
$aluno 		= $_POST["aluno"];
$situacao 	= $_POST["aprovacao"]; //1 = aprovado, 2 = reprovado, 3 = aprovado e reprovado
$turma 		= $_POST["turma"]; 
  
$sql = "
  SELECT 
  	t.turma as \"Turma\", 
  	p.nome || ' (' || m.ref_pessoa || ') ' as \"Nome (Cód)\", 
  	d.descricao_disciplina || ' (' || o.ref_disciplina || '/' || m.ref_disciplina_ofer || ') ' as \"Disciplina (Cód Disc/Diário) \",
  	m.nota_final as \"Nota\", 
  	m.num_faltas || ' (' || d.carga_horaria || ') ' as \"Falta (Carga Horaria)\"
 	 
  FROM 
  	matricula m, pessoas p, disciplinas_ofer o, disciplinas d, public.contratos t
 	 
  WHERE
  	m.ref_periodo = '$periodo' AND 
  	m.ref_curso = '$curso' AND 
  	t.ref_curso = m.ref_curso AND 
  	t.ref_pessoa = p.id AND ";
 	 
if ($turma != '') 
	$sql .= " t.turma = '$turma' AND ";
  
if ($aluno != '') 
	$sql .= "m.ref_pessoa = '$aluno' AND ";

$sql .= "p.id = m.ref_pessoa AND m.ref_disciplina_ofer = o.id AND o.ref_disciplina = d.id	";

if ($situacao == '1') 
	$sql .= " AND (m.nota_final >= 60 and m.num_faltas < (d.carga_horaria/100)*25) ";
  
if ($situacao == '2') 
	$sql .= " AND (m.nota_final < 60 or m.num_faltas > (d.carga_horaria/100)*25) ";

$sql .= " ORDER BY 1, 2";


$sql = 'SELECT * FROM ('. $sql .') AS T1 ORDER BY lower(to_ascii("Nome (Cód)"));';	
 
$RsCurso = $conn->Execute("SELECT descricao ||' (' || id || ') ' as \"Curso\" FROM cursos WHERE id = $curso;");
  
$info = "<h4>".$RsCurso->fields[0]."</h4>";	

$RsPeriodo = $conn->Execute("SELECT descricao FROM periodos WHERE id = '$periodo';");
$DescricaoPeriodo = $RsPeriodo->fields[0];
  
$Result1 = $conn->Execute($sql);
  
$total = $Result1->RecordCount();
    
if($total < 1){
  echo "<script>alert('Nenhum registro foi retornado!'); window.close();</script>";
}
  
$info .= "<strong>Data: </strong>" . date("d/m/Y") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Hora: </strong>" . date("H:i:s") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Total de Registros: </strong>" . $total . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Período: </strong> <span>$DescricaoPeriodo</span> <br><br>";
  
