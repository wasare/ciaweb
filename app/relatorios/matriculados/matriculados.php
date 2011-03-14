<?php

require_once("../../../app/setup.php");
require_once("../../../lib/adodb5/tohtml.inc.php");
require_once("../../../core/reports/header.php");
require_once("../../../core/reports/carimbo.php");


$conn    = new connection_factory($param_conn);

$carimbo = new carimbo($param_conn);
$header  = new header($param_conn);


$novatos    = $_POST['novatos'];
$periodo    = $_POST["periodo1"];
$aluno      = $_POST["aluno"];
$curso      = $_POST["codigo_curso"];
$resp_nome  = $_POST["resp_nome"];
$resp_cargo = $_POST["resp_cargo"];
$turma      = $_POST["turma"];



/**
 * Caso novato seja verdadeiro a tabela c sera contrato
 * caso contrario a tabela c sera matricula
 */
if($novatos == true)
{
	$titulo    = "ALUNOS NOVATOS";
	$tabela_c  = 'contratos';
	$periodo_c = 'c.ref_periodo_turma'; 
}else
{
	$titulo    = "ALUNOS MATRICULADOS";
	$tabela_c  = 'matricula';
	$periodo_c = 'c.ref_periodo';
}


/**
 * Array com o nome das colunas para a planilha (xls)
 */
$colunas[] = "Código";


/**
 * SQL Principal
 */
$sql = " SELECT DISTINCT p.id as \"Código\""; 

if (isset($_POST["nome"]))
{
	$colunas[] = "Nome";
	$sql      .= ', p.nome as "Nome" ';
}

//Turma formulario checkbox
if (isset($_POST["turma2"]))
{ 
	$colunas[]       = 'Turma';
	$sql            .= ', t.turma as "Turma" ';
	$condicao_turma  = 'AND t.ref_curso = c.ref_curso AND t.ref_pessoa = p.id';
	$tabela_contrato = ', contratos t';
}

//Turma formulario text
if ($turma != '')
{ 
	$condicao_turma  = 'AND t.ref_curso = c.ref_curso AND t.ref_pessoa = p.id';
	$condicao_turma .= " AND t.turma = '$turma' ";
	$des_turma       = '<strong>Turma: </strong> '.$turma;

	if($tabela_contrato == ''){
		$tabela_contrato = ", contratos t";
	}
}

if (isset($_POST["pai"]))
{ 
	$colunas[]       = "Pai";
	$sql            .= ', f.pai_nome as "Pai"';
	$tabela_filiacao = "LEFT OUTER JOIN filiacao f ON(p.ref_filiacao = f.id)";
}

if (isset($_POST["mae"]))
{
	$colunas[]       = "Mãe";
	$sql            .= ', f.mae_nome as "Mae" ';
	$tabela_filiacao = "LEFT OUTER JOIN filiacao f ON(p.ref_filiacao = f.id)";
}

if (isset($_POST["endereco"]))
{
	$colunas[] = "Endereço";
	$sql      .= ", p.rua || '  ' || CASE WHEN p.complemento IS NULL THEN ' ' 
	 ELSE p.complemento END AS \"Endereço\"";
}

if (isset($_POST["bairro"]))
{
	$colunas[] = "Bairro";
	$sql      .= ', p.bairro as "Bairro"';
}

//Dados de Cidade
if (isset($_POST["cidade"]))
{
	$colunas[] = "Cidade";
	$sql .= ', m.nome || \'-\' || m.ref_estado as "Cidade"';
	//$condicao_municipio = " p.ref_cidade = m.id AND ";
	$tabela_municipio = " LEFT OUTER JOIN cidade m ON (p.ref_cidade = m.id) ";
}

if (isset($_POST["cep"]))
{
	$colunas[] = "CEP";
	$sql .= ', p.cep as "CEP"';
}

if (isset($_POST["telefone"]))
{
	$colunas[] = "Telefone";
	$sql .= ', p.fone_particular as "Tel. Part."
        , p.fone_profissional as "Tel. Prof."
        , p.fone_celular as "Tel. Cel."
        , p.fone_recado as "Tel. Rec."
      ';
}

if (isset($_POST["rg"]))
{ 
	$colunas[] = "RG";
	$sql .= ', p.rg_numero as "RG"'; 
}
	
if (isset($_POST["cpf"]))
{
	$colunas[] = "CPF";
	$sql .= ', p.cod_cpf_cgc as "CPF"'; 
}

if (isset($_POST["sexo"]))
{ 
	$colunas[] = "Sexo";
	$sql .= ', p.sexo as "Sexo"'; 	
}

if (isset($_POST["data_nascimento"]))
{ 
	$colunas[] = "Data Nascimento";
	$sql .= ', to_char(p.dt_nascimento, \'DD/MM/YYYY\') as "Data de Nascimento"'; 
}

$sql .= "  
	FROM
	     pessoas p $tabela_filiacao $tabela_municipio, $tabela_c c $tabela_contrato 
	WHERE
	     $periodo_c = '$periodo' AND ";

if ($curso != '')
{
	$sql .= " c.ref_curso = '$curso' AND"; 
}

$sql .= $condicao_municipio;

if ($aluno != '')
{
	$sql .= " p.id = '$aluno' AND ";
}

$sql .= " c.ref_pessoa = p.id $condicao_turma ";

if (isset($_POST["nome"]))
{ 
	$sql = 'SELECT * FROM ('. $sql .') AS T1 ORDER BY lower(to_ascii("Nome",\'LATIN1\'));';
}else
{
	$sql .= ' ORDER BY p.id; ';
}

//echo $sql;die();


$Result1 = $conn->Execute($sql);

$num_result = $Result1->RecordCount();

/**
 * Verifica se retornou resultados 
 */
if($num_result < 1)
{
	echo "<script>alert('Nenhum registro foi retornado!'); window.close();</script>";
}


/**
 * Montando informacoes do cabecalho
 */
$RsCurso = $conn->Execute("SELECT descricao ||' (' || id || ') ' as \"Curso\" FROM cursos WHERE id = $curso;");
$info = "<h4>" . $RsCurso->fields[0] . "</h4>";

$RsPeriodo = $conn->Execute("SELECT descricao FROM periodos WHERE id = '$periodo';");
$DescricaoPeriodo = $RsPeriodo->fields[0];

$info .= "<strong>Data: </strong>" . date("d/m/Y") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Hora: </strong>" . date("H:i:s") . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Total de Registros: </strong>" . $num_result . "&nbsp;&nbsp;-&nbsp;&nbsp;";
$info .= "<strong>Período: </strong> <span>$DescricaoPeriodo</span> $des_turma<br><br>";

?>
