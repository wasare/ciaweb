<?php

//ARQUIVO DE CONFIGURACAO E CLASSE ADODB
header("Cache-Control: no-cache");
require_once("../../../app/setup.php");
		
		
$sql = "
SELECT  DISTINCT 
c.id as \"Contrato\", a.id as \"Cód. Pessoa\", a.nome, c.ref_curso, d.abreviatura
FROM 
pessoas a, contratos c, cursos d
WHERE
   a.id IN ( 
	  	SELECT DISTINCT ref_pessoa 
		FROM  contratos 
		ORDER BY ref_pessoa 
	)  AND 
    c.ref_pessoa = a.id AND
    c.ref_curso = d.id  AND
	lower(to_ascii(a.nome)) SIMILAR TO lower(to_ascii('%".$_POST['nome']."%')) 
ORDER BY a.nome LIMIT 20 OFFSET -1;"; 

//Criando a classe de conexão ADODB
$Conexao = NewADOConnection("postgres");

//Setando como conexão persistente
$Conexao->PConnect("host=$host dbname=$database user=$user password=$password");

//Exibindo a descricao do curso caso setado
$RsNome = $Conexao->Execute($sql);

//inicio da tabela
$tabela = '<table width="100%" border="0">';
$tabela.= "  <tr bgcolor='#666666'>";
$tabela.= "    <td width=''><b><font color='#FFFFFF'>Cont.</font></b></td>";
$tabela.= "    <td width=''><b><font color='#FFFFFF'>Aluno</font></b></td>";
$tabela.= "    <td width=''><b><font color='#FFFFFF'>Curso</font></b></td>";
$tabela.= "    <td width=''><b><font color='#FFFFFF'>&nbsp;</font></b></td>";
$tabela.= "  </tr>";


while(!$RsNome->EOF){

	$cod_contrato = $RsNome->fields[0];
	$aluno =  iconv("iso-8859-1", "utf-8", $RsNome->fields[2]) . " - " . $RsNome->fields[1];
	$curso =  iconv("iso-8859-1", "utf-8", $RsNome->fields[4]) . " - " . $RsNome->fields[3];

    $tabela.= "<tr bgcolor='#DDDDDD'>";
	$tabela.= "   <td align=\"left\">" . $cod_contrato . "</td>";
	$tabela.= "   <td align=\"left\">" . $aluno . "</td>";
	$tabela.= "   <td align=\"left\">" . $curso . "</td>";
    $tabela.= "   <td align=\"left\"><a href=\"javascript:send(" .$cod_contrato. ", '". $aluno . "', '". $curso . "')\"><img src=\"../../public/images/icons/bar_menu/apply.png\" alt=\"Enviar\" /></a></td>";
	$tabela.= "</tr>";
	$RsNome->MoveNext();
}

$tabela.= "</table>";

echo $tabela;

//rs2html($RsNome, 'width="90%" cellspacing="0" border="0" class="tabela_relatorio" cellpadding="0"');

?>
