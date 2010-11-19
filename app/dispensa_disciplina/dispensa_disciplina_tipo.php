<?php

/**
* Seleciona a disciplina para dispensar
* @author Wanderson Santiago dos Reis
* @version 1
* @since 05-02-2009
**/

//Arquivos de configuracao e biblioteca
header("Cache-Control: no-cache");
require_once('../../app/setup.php');

$conn = new connection_factory($param_conn);


$diario_id = $_POST['id_diario'];
$curso_id = $_POST['curso_id'];
$aluno_id = $_POST['aluno_id'];
$id_contrato = $_POST['id_contrato'];
$ref_campus = $_POST['ref_campus'];

$sa_periodo_id = $_POST['periodo_id'];
//$first = $_POST['first'];
//$second = $_POST['second'];
$checar_turma = $_POST['checar_turma'];

$msg_erro = '';


$_SESSION['sa_periodo_id'] = $sa_periodo_id;

$sqlCurso = "
SELECT 
  cursos.id,
  cursos.descricao,
  contratos.ref_campus,
  contratos.turma
FROM
  contratos, cursos
WHERE
  cursos.id = contratos.ref_curso AND
  contratos.id = $id_contrato;";

//Exibindo a descricao do curso caso setado
$curso = $conn->get_row($sqlCurso);


/**
 * @var integer
 */
$curso_id   = $curso['id'];
/**
 * @var string   
 */
$curso_nome = $curso['descricao'];
/**
 * @var integer   
 */
$ref_campus = $curso['ref_campus'];
/**
 * @var string   
 */
$turma = $curso['turma'];


$sqlCampus = "SELECT get_campus($ref_campus)";
/**
 * @var string Descricao no campus
 */
$campus_nome = $conn->get_one($sqlCampus);

$sqlAluno = "SELECT nome FROM pessoas WHERE id = $aluno_id;";
/**
 * @var string Nome do aluno
 */
$aluno_nome = $conn->get_one($sqlAluno);

$disciplinas_liberadas = 0;

$sqlDisciplina = "SELECT o.id || ' - ' || d.descricao_disciplina || ' (' || o.ref_disciplina || ')' as disciplina,
                  o.turma, '(' || o.ref_periodo || ')'  as periodo_oferta, o.ref_periodo
        FROM
                disciplinas d, disciplinas_ofer o
        WHERE
                d.id = o.ref_disciplina AND
                d.id = o.ref_disciplina AND
                o.is_cancelada = '0' AND
                o.id = $diario_id;";




$disciplina = $conn->get_row($sqlDisciplina);
/**
 * @var string Nome da Disciplina
 */
$nome_disciplina = $disciplina['disciplina'] .' - '. $disciplina['turma'] . $disciplina['periodo_oferta'];
$periodo_id = $disciplina['ref_periodo'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SA</title>
<link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../../lib/prototype.js"></script>
<script language="javascript" src="../../lib/functions.js"></script>


<script language="javascript">


function info() {
    var id = $F("dispensa_tipo");
    var url = 'dispensa_info.php';
    var parametros = 'op=' + id ;
    var myAjax = new Ajax.Request( url, { method: 'post', parameters: parametros, onLoading: carregando, onComplete: escreve});
}

//mostra o carregamento
function carregando(){
    $("msg").innerHTML = "<h2>Carregando...</h2>";
}

// Escreve a tabela de listagem de clientes
function escreve(request){
    //trata caracteres especiais para sair em formato correto para o browser
    $("dispensa_info").innerHTML = unescape(request.responseText);
    $("msg").innerHTML = "";
}


</script>

<script language="JavaScript" src="dispensa.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body onload="Oculta('processa')">
<div align="center" style="height:600px;">
  <h1>Processo de Dispensa de Disciplina</h1>
  <h4>Tipo e informa&ccedil;&otilde;es sobre a dispensa: Etapa 3/3</h4>
  <!--<strong>Identifica&ccedil;&atilde;o do aluno</strong>-->
  <div class="panel"> <strong>Aluno: </strong>
    <?=$aluno_id?>
    -
    <?=$aluno_nome?>
    <br />
    <strong>Curso: </strong>
    <?=$curso_id?>
    -
    <?=$curso_nome?>
    <strong>Turma: </strong>
    <?=$turma?>
    <br />
    <strong>Contrato: </strong>
    <?=$id_contrato?>
    <strong>Cidade: </strong>
    <?=$campus_nome?>
  </div>
  <form name="dispensa_frm" id="dispensa_frm" method="post" action="dispensa_disciplina.post.php">
  <div class="panel">
        <!-- FIXME: exibir informações da disciplina sendo dispensada -->
				&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; ( Di&aacute;rio - Disciplina  - Turma(Per&iacute;odo de oferta)) <br />
                 <strong>Disciplina a dispensar: </strong>&nbsp; <?=$nome_disciplina?> <br /> <br />
                    Selecione o tipo / motivo da dispensa:<br />
                        <select id="dispensa_tipo" name="dispensa_tipo" onchange="info();Exibe('processa')">
			<option value="-1"></option>
			<option value="4">Educa&ccedil;&atilde;o F&iacute;sica (Decreto Lei 1.044 de 21/10/1969)</option>
			<option value="2">Aproveitamento de Estudos (Portaria 216/2009 - Campus Bambu&iacute;)</option>
			<option value="3">Certifica&ccedil;&atilde;o de Experi&ecirc;ncia (Portaria 216/2009 - Campus Bambu&iacute;)</option>
	</select>

    <br />
  </div>

    <span id="msg"></span>
	<span id="dispensa_info"></span>

    <input type="hidden" name="diario_id"  value="<?=$diario_id?>">
    <input type="hidden" name="curso_id" value="<?=$curso_id?>">
    <input type="hidden" name="aluno_id" value="<?=$aluno_id?>">
    <input type="hidden" name="id_contrato" value="<?=$id_contrato?>">
    <input type="hidden" name="ref_campus" value="<?=$ref_campus?>">
    <input type="hidden" name="periodo_id" value="<?=$periodo_id?>">
    <p>
      <input type="button" value="  Voltar  " onclick="javascript:history.back(-1)" name="Button" />
      <input type="button" name="processa" id="processa" onclick="valida('dispensa_frm');" value=">> Processar dispensa" />
    </p>
  </form>


</div>
</body>
</html>
