<?php

require_once("../setup.php");
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

if($_POST['btnOK'] == 10 AND $_POST['nota1'] <= 100)
{
	print_r($_POST);

	if (is_numeric($_POST['registro_id']))
		$registro_id = $_POST['registro_id'];

    // ATUALIZA NOTAS E FALTAS NO DIARIO
    atualiza_dispensa($_POST['aluno_id'],$_POST['diario_id'],$_POST['dispensa_tipo']);
    if(is_numeric($_POST['nota1']) AND $_POST['nota1'] >= 50 ) {
        echo lanca_nota($_POST['aluno_id'],$_POST['nota1'],$_POST['diario_id']);
        $nota1 = str_replace(",",".",$_POST['nota1']);
		$conn->Execute('UPDATE matricula SET nota_final = '. $nota1 .' WHERE id = '. $registro_id );
    }
    // ^ ATUALIZA NOTAS E FALTAS NO DIARIO ^ //

    $_POST = array();

}


if (is_numeric($_GET['id']))
	$registro_id = $_GET['id'];


if(is_numeric($registro_id))
{

	$sqlDispensas = 'SELECT id, ref_pessoa AS "Matricula", pessoa_nome(ref_pessoa) AS "Nome", ref_curso || \' - \' ||  get_curso_abrv(ref_curso) AS "Curso", descricao_disciplina(get_disciplina_de_disciplina_of(ref_disciplina_ofer)) AS "Disciplina", nota_final AS "Nota", ref_disciplina_ofer, ref_motivo_matricula FROM matricula where id = '. $registro_id .'  ORDER BY 2,1;';

	$dispensa = $conn->get_all($sqlDispensas);

	echo 'Matricula &nbsp;/&nbsp; Nome &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ Curso&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/  Disciplina &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  / Nota&nbsp;&nbsp;&nbsp;  <br />  ';
	
	foreach($dispensa as $linha)
	{
		$ref_pessoa = $linha['Matricula'];
		$nome = $linha['Nome'];
		$curso = $linha['Curso'];
		$disciplina = $linha['Disciplina'];
		$nota = $linha['Nota'];
		$registro_id = $linha['id'];
        $diario_id = $linha['ref_disciplina_ofer'];
        $dispensa_tipo = $linha['ref_motivo_matricula'];
		echo "$ref_pessoa&nbsp;&nbsp;&nbsp;$nome&nbsp;&nbsp;&nbsp;$curso&nbsp;&nbsp;&nbsp;$disciplina&nbsp;&nbsp;&nbsp;$nota";
	}

    echo '<form name="form1" method="post" action="">';

   	echo '<input type="hidden" name="btnOK" id="btnOK" value="10" />';
    echo '<input type="hidden" name="registro_id" id="registro_id" value="'. $registro_id .'" />';
    echo '<input type="hidden" name="aluno_id" id="aluno_id" value="'. $ref_pessoa .'" />';
    echo '<input type="hidden" name="diario_id" id="diario_id" value="'. $diario_id .'" />';
    echo '<input type="hidden" name="dispensa_tipo" id="dispensa_tipo" value="'. $dispensa_tipo .'" />';

  	echo 'Nota da dispensa:&nbsp;<input type="text" name="nota1" id="nota1" size="6" value="" />';
   	echo '<input type="submit" name="enviar" id="enviar" value="Gravar -->" />';

	echo '</form>';
}


echo '<br /><a href="lista_dispensas_nota_zero.php">Voltar</a>';


?>
