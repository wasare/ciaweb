<?php

/**
* Captura dados do contrato
* @author Wanderson Santiago dos Reis
* @version 1
* @since 04-02-2009
**/

//ARQUIVO DE CONFIGURACAO E CLASSE ADODB
header ("Cache-Control: no-cache");
require_once('../../app/setup.php');

/**
 * @var string com o codigo do aluno
 */
$id_pessoa = $_GET['codigo_pessoa'];


$sql = "SELECT 
			a.ref_curso,
			b.descricao,
			a.id,
            a.turma
		FROM 
			contratos a,
			cursos b
        WHERE 
			a.ref_pessoa = '$id_pessoa' AND
        	a.dt_desativacao is null AND
        	a.ref_curso = b.id";


//Criando a classe de conex�o ADODB
$Conexao = NewADOConnection("postgres");

//Setando como conex�o persistente
$Conexao->PConnect("host=$host dbname=$database port=$port user=$user password=$password");

//Exibindo a descricao do cuso caso setado
$RsContrato = $Conexao->Execute($sql);

$sqlAluno = 'SELECT nome FROM pessoas WHERE id = '. $id_pessoa .';';
$RsAluno = $Conexao->Execute($sqlAluno);
$aluno_nome = $RsAluno->fields[0];

if ( @$RsContrato->RecordCount() > 0 )
{

$Result1 .= '<div class="panel"><strong>Aluno: </strong>' .  $id_pessoa .' - '. $aluno_nome ;
$Result1 .= '<h4>Selecione o curso: </h4>';

$cont = 0;

while(!$RsContrato->EOF){

    if($cont == 0){
        $Result1.= '<input type="radio" name="id_contrato" id="id_contrato" value="';
        $Result1.= $RsContrato->fields[2].'" checked /> ';
        $Result1.= $RsContrato->fields[0].' - <b>'.$RsContrato->fields[1];
        $Result1.= '</b> - Turma: '.$RsContrato->fields[3].'<br>';
    }
    else{
        $Result1.= '<input type="radio" name="id_contrato" id="id_contrato" value="';
        $Result1.= $RsContrato->fields[2].'" /> ';
        $Result1.= $RsContrato->fields[0].' - <b>'.$RsContrato->fields[1];
        $Result1.= '</b> - Turma: '.$RsContrato->fields[3].'<br>';
    }
    $cont += 1;

	$RsContrato->MoveNext();
}

//$Result1 .= '<br />      <input type="checkbox" name="checar_turma" id="checar_turma" value="1" /> Filtrar disciplinas por turma.';//somente para matricula regular;
$Result1 .= '</div> <br /><input type="hidden" name="first" value="1">';
$Result1 .= '<input type="submit" name="processeguir" id="prosseguir"  value=" >> Prosseguir " />';

echo $Result1;
}
else
   echo '<div class="panel"><div align="center"><b><font color="#CC0000">nenhum curso encontrado.</font></b> </div></div>';
?>
