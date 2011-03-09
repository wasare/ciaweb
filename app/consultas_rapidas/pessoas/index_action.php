<?php

//ARQUIVO DE CONFIGURACAO E CLASSE ADODB
header("Cache-Control: no-cache");
require("../../../app/setup.php");


$sql = "SELECT id, nome 
		FROM public.pessoas
		WHERE 
		lower(to_ascii(nome,'LATIN1')) ilike lower(to_ascii('".$_POST['nome']."%','LATIN1')) 
		ORDER BY to_ascii(nome,'LATIN1') LIMIT 50;";


//Criando a classe de conexão ADODB
$Conexao = NewADOConnection("postgres");

//Setando como conexão persistente
$Conexao->PConnect("host=$host dbname=$database user=$user password=$password");

//Exibindo a descricao do curso caso setado
$RsNome = $Conexao->Execute($sql);

//inicio da tabela
$tabela = '<table width="90%" border="0">';
$tabela.= "  <tr bgcolor='#666666'>";
$tabela.= "    <td width=''><b><font color='#FFFFFF'>Nome</font></b></td>";
$tabela.= "    <td width=''><b><font color='#FFFFFF'>Enviar</font></b></td>";
$tabela.= "  </tr>";


while(!$RsNome->EOF){

    $tabela.= "<tr bgcolor='#DDDDDD'>";
	$tabela.= "   <td align=\"left\">" . $RsNome->fields[1] . "</td>";
    $tabela.= "   <td align=\"left\"><a href=\"javascript:send(" .$RsNome->fields[0]. ", '". $RsNome->fields[1] . "')\"><img src=\"../../../public/images/icons/apply.png\" alt=\"Enviar\" /></a></td>";
	$tabela.= "</tr>";
	$RsNome->MoveNext();
}

$tabela.= "</table>";

echo $tabela;

//rs2html($RsNome, 'width="90%" cellspacing="0" border="0" class="tabela_relatorio" cellpadding="0"');

?>
