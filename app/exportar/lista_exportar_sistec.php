<?php

require_once("../../app/setup.php");

$conn = new connection_factory($param_conn);

$periodo_id    = (string) $_POST['periodo1'];
$curso_id      = (int) $_POST['codigo_curso'];
$campus_id     = (int) $_POST['campus'];
$turma         = (string) $_POST['turma'];

$campus_sql = $campus_id > 0 ? " AND m.ref_campus = $campus_id " : '';
$turma_sql = empty($turma) ? '' : " AND c.turma = '$turma' ";

$sql = "
SELECT DISTINCT
	p.id, p.nome, p.cod_cpf_cgc, fone_particular,
	fone_profissional, fone_celular, fone_recado, email,
    c.turma
FROM
	pessoas p 
        LEFT OUTER JOIN 
    matricula m ON (m.ref_pessoa = p.id)
		LEFT OUTER JOIN
    contratos c ON (c.id = m.ref_contrato)
    
WHERE
	m.ref_periodo = '". $periodo_id ."' AND
	m.ref_curso = '". $curso_id . "'
    ". $campus_sql ." 
    ". $turma_sql ."
ORDER BY 2; ";

//die($sql);



$Result1 = $conn->Execute($sql);

$total = $Result1->RecordCount();

if($total < 1){
    echo "<script>alert('Nenhum registro foi retornado!');history.back(-1);</script>";
}


$sqlCurso = "SELECT id, descricao FROM cursos WHERE id = ". $curso_id ."; ";

$RsCurso = $conn->Execute($sqlCurso);

$resp_erro_cpf .= "
<table>
<tr>
    <td><strong>CPF</strong></td>
    <td><strong>C&oacute;digo</strong></td>
    <td><strong>Nome</strong></td>
    <td><strong>Tel. Particular</strong></td>
    <td><strong>Tel. Profissional</strong></td>
    <td><strong>Tel. Celular</strong></td>
    <td><strong>Tel. Recado</strong></td>
    <td><strong>E-mail</strong></td>
</tr>";

while(!$Result1->EOF){
    
    if($Result1->fields[2] == "" || strlen($Result1->fields[2]) != 11 || $Result1->fields[2] == "NULL" ){
        $resp_erro_cpf .= "<tr>";
        $resp_erro_cpf .= "<td>".$Result1->fields[2]."</td>";
        $resp_erro_cpf .= "<td>".$Result1->fields[0]."</td>";
        $resp_erro_cpf .= "<td>".$Result1->fields[1]."</td>";
        $resp_erro_cpf .= "<td>".$Result1->fields[3]."</td>";
        $resp_erro_cpf .= "<td>".$Result1->fields[4]."</td>";
        $resp_erro_cpf .= "<td>".$Result1->fields[5]."</td>";
        $resp_erro_cpf .= "<td>".$Result1->fields[6]."</td>";
        $resp_erro_cpf .= "<td>".$Result1->fields[7]."</td></tr>";
        $resp_erro_cpf .= "</tr>";
        
    }else{
        $resp_ok .= $Result1->fields[2].";";
    }

    $Result1->MoveNext();
}
$resp_erro_cpf .= "</table>";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SA</title>
<link href="../../public/styles/formularios.css" rel="stylesheet"	type="text/css" />
</head>
<body>
<div align="center" style="height: 600px;">
<h2>Exportar alunos matriculados para o SISTEC</h2>
<h3>Curso: <?=$RsCurso->fields[0]." - ".$RsCurso->fields[1]?></h3>
<?php
echo "<strong>Per&iacute;odo da matr&iacute;cula: </strong><span>" . $periodo_id . "</span>&nbsp;&nbsp;";

if($campus_id > 0){
    $campus_nome = $conn->get_one("SELECT nome_campus FROM campus WHERE id = $campus_id;");
    echo "&nbsp;&nbsp;<strong>Campus: </strong><span>" . $campus_nome . "</span>&nbsp;&nbsp;";
}

if(!empty($turma)) {
	echo "&nbsp;&nbsp;<strong>Turma: </strong><span>" . $turma . "</span>";
}
?>

<div class="panel">
    Copie o texto do formul&aacute;rio abaixo e cole no respectivo campo do SISTEC.<br />
    Para facilitar dê um clique na caixa de texto do formul&aacute;rio e utilize os 
    atalhos de teclado "Ctrl + A" para selecionar o texto, "Ctrl + C" para copiar e 
    "Ctrl + V" para colar na caixa de texto do SISTEC.<br /><br />
    <textarea name="" cols="100" rows="5"><?=$resp_ok?></textarea>
    <h4><font color="red">Alunos sem CPF cadastrado ou com n&uacute;mero de caracteres diferente de 11</font></h4>
    <?=$resp_erro_cpf?>
</div>
</div>
</body>
</html>
