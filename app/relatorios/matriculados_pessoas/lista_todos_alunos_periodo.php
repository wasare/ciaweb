<?php

require_once("../../../app/setup.php");
require_once("../../../core/reports/header.php");
  
$conn = new connection_factory($param_conn);

$header  = new header($param_conn);
  
if ( is_numeric($_POST['cidade']) ) {
	$cidade = ' t.ref_campus = '. $_POST['cidade'] .' AND';
	$RsCidade = $conn->Execute("SELECT cidade_campus FROM campus WHERE id = " . $_POST['cidade'] . ";");
	$txt_cidade = "&nbsp;&nbsp;-&nbsp;&nbsp;<strong>Cidade: </strong>" . $RsCidade->fields[0];
} else{
	$cidade = '';
}

$sql = "
SELECT DISTINCT 
	p.id as \"Código\", 
  	p.nome as \"Nome\" , 
	f.pai_nome as \"Pai\", 
	f.mae_nome as \"Mae\" , 
	p.rua || ' ' || p.complemento as \"Endereço\", 
	p.bairro as \"Bairro\", 
	m.nome || '-' || m.ref_estado as \"Cidade\", 
	p.cep as \"CEP\", 
	
	p.fone_particular as \"Tel. Part.\",
	p.fone_profissional as \"Tel. Prof.\",
	p.fone_celular as \"Tel. Cel.\",
	p.fone_recado as \"Tel. Rec.\", 
	
	p.rg_numero as \"RG\", 	
	p.cod_cpf_cgc as \"CPF\", 
	p.sexo as \"Sexo\", 
	to_char(p.dt_nascimento, 
	'DD/MM/YYYY') as \"Data de Nascimento\" 
	
FROM 
	pessoas p, matricula c , contratos t , cidade m , filiacao f 
	
WHERE 
	c.ref_periodo = '" . $_POST["periodo"] . "' AND 
	p.ref_filiacao = f.id AND 
	p.ref_cidade = m.id AND 
	c.ref_pessoa = p.id AND 
	t.ref_curso = c.ref_curso AND $cidade
	t.ref_pessoa = p.id 
ORDER BY 2";

$sql = 'SELECT * FROM ('. $sql .') AS T1 ORDER BY lower(to_ascii("Nome"));';	
 
$Result1 = $conn->Execute($sql);
  
$num_result = $Result1->RecordCount();
  
$info = "<strong>Data: </strong>" . date("d/m/Y") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Hora: </strong>" . date("H:i:s") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Total de Registros: </strong>" . $num_result . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Período: </strong> <span>".$_POST['periodo']."</span> $txt_cidade <br><br>";
  
?>
<html>
<head>
	<title>SA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="<?=$BASE_URL?>public/styles/style.css" rel="stylesheet" type="text/css">
    <link href="<?=$BASE_URL?>public/styles/print.css" rel="stylesheet" type="text/css" media="print" />
</head>
<body marginwidth="20" marginheight="20">
  	<?php echo $header->get_empresa($PATH_IMAGES); ?>
	<h2>RELAT&Oacute;RIO COM TODOS OS ALUNOS MATRICULADOS POR PER&Iacute;ODO</h2>
	<?=$info?>
	<table cols=16 width="90%" cellspacing="0" border="0" class="tabela_relatorio" cellpadding="0">
		<tr>
			<th>Código</th>
			<th>Nome</ht>
			<th>Pai</th>
			<th>Mae</th>
			<th>Endereço</th>
			<th>Bairro</th>
			<th>Cidade</th>
			<th>CEP</th>
			<th>Tel. Part.</th>
			<th>Tel. Prof.</th>
			<th>Tel. Cel.</th>
			<th>Tel. Rec.</th>
			<th>RG</th>
			<th>CPF</th>
			<th>Sexo</th>
			<th>Data de Nascimento</th>
		</tr>
		<?php while(!$Result1->EOF){ ?>
		<tr valign=top>
			<td align=right> <?php echo $Result1->fields[0]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[1]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[2]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[3]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[4]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[5]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[6]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[7]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[8]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[9]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[10]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[11]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[12]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[13]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[14]; ?></td>
			<td>&nbsp; <?php echo $Result1->fields[15]; ?></td>
		</tr>
		<?php $Result1->MoveNext(); } ?>
	</table>
<br />
<div class="nao_imprime">
  <input type="button" value="Imprimir" onClick="window.print()" />
  &nbsp;&nbsp;&nbsp;
  <a href="#" onclick="javascript:window.close();">Fechar</a>
</div>
<br />
</body>
</html>
