<?php 

require_once(dirname(__FILE__) .'/../../common.php'); 
require_once('../../lib/InvData.php'); 


$nome                   = addslashes($_POST['nome']);
$rua                    = addslashes($_POST['rua']);
$complemento            = addslashes($_POST['complemento']);
$bairro                 = addslashes($_POST['bairro']);
$cep                    = $_POST['cep'];
$ref_cidade             = $_POST['ref_cidade'];
$fone_particular        = $_POST['fone_particular'];
$fone_profissional      = $_POST['fone_profissional'];
$fone_celular           = $_POST['fone_celular'];
$fone_recado            = $_POST['fone_recado'];
$email                  = addslashes($_POST['email']);
$email_alt              = addslashes($_POST['email_alt']);
$estado_civil           = $_POST['estado_civil'];
$dt_cadastro            = $_POST['dt_cadastro'];
$dt_nascimento          = $_POST['dt_nascimento'];
$rg_data                = $_POST['rg_data'];
$sexo                   = $_POST['sexo'];
$rg_numero              = $_POST['rg_numero'];
$rg_orgao               = $_POST['rg_orgao'];
$rg_cidade              = $_POST['rg_cidade'];
$ref_filiacao           = $_POST['ref_filiacao'];
$ref_naturalidade       = $_POST['ref_naturalidade'];
$ref_nacionalidade      = $_POST['ref_nacionalidade'];
$cod_cpf_cgc            = $_POST['cod_cpf_cgc'];
$titulo_eleitor         = $_POST['titulo_eleitor'];
$placa_carro            = $_POST['placa_carro'];
$fl_dados_pessoais      = $_POST['fl_dados_pessoais'];
$rg_num                 = $_POST['rg_num'];
$cpf                    = $_POST['cpf'];
$titulo_eleitord        = $_POST['titulo_eleitord'];
$quitacao_eleitoral     = $_POST['quitacao_eleitoral'];
$hist_original          = $_POST['hist_original'];
$hist_escolar           = $_POST['hist_escolar'];
$doc_militar            = $_POST['doc_militar'];
$foto                   = $_POST['foto'];
$atestado_medico        = $_POST['atestado_medico'];
$diploma_autenticado    = $_POST['diploma_autenticado'];
$solteiro_emancipado    = $_POST['solteiro_emancipado'];
$ano_2g                 = $_POST['ano_2g'];
$ref_escola_2g          = $_POST['ref_escola_2g'];
$cidade_2g              = $_POST['cidade_2g'];
$ref_curso_2g           = $_POST['ref_curso_2g'];
$cod_passivo            = $_POST['cod_passivo'];
$obs                    = addslashes($_POST['obs']);
$deficiencia            = addslashes($_POST['deficiencia']);
$cod_externo            = $_POST['cod_externo'];
$fl_cartao              = $_POST['fl_cartao'];

CheckFormParameters(array("nome",
                          "rua",
                          "cep",
                          "ref_cidade",
                          "dt_cadastro",
                          "dt_nascimento",
                          "sexo",
                          "deficiencia",
                          "fl_dados_pessoais",
                          "")); // Marca fim da Lista -> é opcional

$dt_cadastro    = InvData($dt_cadastro);
$rg_data        = InvData($rg_data);
$dt_nascimento  = InvData($dt_nascimento);
$cep            = trim(str_replace('-','',$cep));
$cod_cpf_cgc    = str_replace('/','',$cod_cpf_cgc);
$cod_cpf_cgc    = str_replace('-','',$cod_cpf_cgc);
$cod_cpf_cgc    = trim(str_replace('.','',$cod_cpf_cgc));

$conn = new Connection;

$conn->Open();
$conn->Begin();
  
$sql = "select nextval('seq_pessoas')";

$query = $conn->CreateQuery($sql);

$success = false;

if ( $query->MoveNext() )
{
  $id_pessoa = $query->GetValue(1);
  
  $success = true;
}

$query->Close();

SaguAssert($success,"Nao foi possivel obter um numero da pessoa! <br> $sql");

$tipo_pessoa= 'f';

$sql = "
INSERT INTO pessoas (
	id, nome, rua, complemento, bairro, cep, ref_cidade, fone_particular, fone_profissional, 
	fone_celular, fone_recado, email, email_alt, estado_civil, dt_cadastro, tipo_pessoa, 
	dt_nascimento, sexo, deficiencia, deficiencia_desc, rg_numero, rg_orgao, rg_cidade, 
	rg_data, fl_cartao, fl_dados_pessoais, ref_filiacao, ref_naturalidade, ref_nacionalidade,
	cod_cpf_cgc, titulo_eleitor, placa_carro, ano_2g, escola_2g, cidade_2g, ref_curso_2g, 
	cod_passivo, cod_externo, obs
) values ( 
	'$id_pessoa', '$nome', '$rua', '$complemento', '$bairro', '$cep', '$ref_cidade', '$fone_particular', 
    '$fone_profissional', '$fone_celular', '$fone_recado', '$email', '$email_alt', '$estado_civil', 
	'$dt_cadastro', '$tipo_pessoa',";

if ( $dt_nascimento == ''){ 
	$sql .= "  null,"; 
} else{ 
	$sql .= "  '$dt_nascimento',";  
}

$sql .= " '$sexo', '$deficiencia',";

if ( $deficiencia_desc == '' ) { 
	$sql .= " null,"; 
} else{ 
	$sql .= " '$deficiencia_desc',"; 
}

$sql .= " '$rg_numero', '$rg_orgao', '$rg_cidade',";

if ( $rg_data=='') { 
	$sql .= " null,"; 
} else{ 
	$sql .= " '$rg_data',";  
}

if ( ($fl_cartao=='t') || ($fl_cartao=='Sim') ) { 
	$sql .= "  't',"; 
} else{ 
	$sql .= "  'f',";  
}

if ( ($fl_dados_pessoais=='t') || ($fl_dados_pessoais=='Sim') ){ 
	$sql .= "  't',"; 
} else { 
	$sql .= "  'f',";  
}
       
$sql .= " 
	'$ref_filiacao', '$ref_naturalidade', '$ref_nacionalidade', '$cod_cpf_cgc', 
	'$titulo_eleitor', '$placa_carro', '$ano_2g', '$ref_escola_2g', '$cidade_2g', '$ref_curso_2g', 
	'$cod_passivo', '$cod_externo', '$obs'
)";

$ok = $conn->Execute($sql);

SaguAssert($ok,"Não foi possível inserir o registro!<br>$sql");

$sql = " 
INSERT INTO documentos (
	ref_pessoa, rg_num, cpf, titulo_eleitor, quitacao_eleitoral, hist_original, 
	hist_escolar, doc_militar, foto, atestado_medico, diploma_autenticado, 
	solteiro_emancipado
) VALUES (
	'$id_pessoa', "; 

if (( $rg_num=='f') || ($rg_num=='')) { 
	$sql .= "  'f',"; 
}
if ( $rg_num=='t') { 
	$sql .= "  't',";  
}
if (( $cpf=='f') || ($cpf=='')) { 
	$sql .= "  'f',"; 
}
if ( $cpf=='t') { 
	$sql .= "  't',";  
}
if (( $titulo_eleitord=='f') || ($titulo_eleitord=='')) { 
	$sql .= "  'f',"; 
}
if ( $titulo_eleitord=='t') { 
	$sql = $sql . "  't',";  
}
if (( $quitacao_eleitoral=='f') || ($quitacao_eleitoral=='')) { 
	$sql = $sql . "  'f',"; 
}
if ( $quitacao_eleitoral=='t') { 
	$sql = $sql . "  't',";  
}
if (( $hist_original=='f') || ($hist_original=='')) { 
	$sql = $sql . "  'f',"; 
}
if ( $hist_original=='t') { 
	$sql = $sql . "  't',";  
}
if (( $hist_escolar=='f') || ($hist_escolar=='')) { 
	$sql = $sql . "  'f',"; 
}
if ( $hist_escolar=='t') { 
	$sql = $sql . "  't',";  
}
if (( $doc_militar=='f') || ($doc_militar=='')) { 
	$sql = $sql . "  'f',"; 
}
if ( $doc_militar=='t') { 
	$sql = $sql . "  't',";  
}
if (( $foto=='f') || ($foto=='')) { 
	$sql = $sql . "  'f',"; 
}
if ( $foto=='t') { 
	$sql = $sql . "  't',";  
}
if (( $atestado_medico=='f') || ($atestado_medico=='')) { 
	$sql = $sql . "  'f',"; 
}
if ( $atestado_medico=='t') { 
	$sql = $sql . "  't',";  
}
if (( $diploma_autenticado=='f') || ($diploma_autenticado=='')) { 
	$sql = $sql . "  'f',"; 
}
if ( $diploma_autenticado=='t') { 
	$sql = $sql . "  't',";  
}
if (( $solteiro_emancipado=='f') || ($solteiro_emancipado=='')) { 
	$sql = $sql . "  'f');"; 
}
if ( $solteiro_emancipado=='t') { 
	$sql = $sql . "  't');";  
}

$ok = $conn->Execute($sql);

$conn->Finish();
$conn->Close();

SaguAssert($ok,"Não foi possível inserir o registro!<br>$sql");

SuccessPage("Inclusão de Alunos", 
			"location='../pessoaf_inclui.php'",
            "O código do Aluno é $id_pessoa",
            "location='../consulta_inclui_pessoa.php'");

?>
<div align="center">
    <a href="../consulta_inclui_pessoa.php">Consultar pessoa</a> |
    <a href="../pessoaf_inclui.php">Cadastrar nova pessoa</a>
</div>