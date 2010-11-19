<?php

/**
* Seleciona as disciplinas para matricular
* @author Santiago Silva Pereira, Wanderson S. Reis
* @version 1
* @since 04-02-2009
**/
require_once(dirname(__FILE__) .'/../setup.php');
require_once($BASE_DIR .'core/situacao_academica.php');

$conn = new connection_factory($param_conn);


//
$aluno_id = $_SESSION['sa_aluno_id'];
//
$diario_id = (int) $_POST['diario_id'];
$contrato_id = (int) $_POST['contrato_id'];
$curso_id = (int) $_POST['curso_id'];
//
$msg = '';



if(!is_numeric($diario_id) OR !is_numeric($contrato_id)) {
	
		$msg = '<p><div align="center"><b><font color="#CC0000">'.
	    'Entre com um c&oacute;digo de di&aacute;rio!'.
		'</font></b></div></p>';
	
}
else {

		$sqlDiarioMatricular = "
		SELECT DISTINCT 
  			A.id,
			A.ref_disciplina,
			descricao_disciplina(A.ref_disciplina),
			professor_disciplina_ofer_todos(A.id),
			get_campus(A.ref_campus),
			get_num_matriculados(A.id)
		FROM 
			disciplinas_ofer A, cursos_disciplinas B
		WHERE 
			A.ref_disciplina = B.ref_disciplina AND
			A.ref_periodo = '".$_SESSION['sa_periodo_id']."' AND
			A.id = $diario_id AND
			A.is_cancelada = '0'
		ORDER BY 2";

        //echo '<br />'. $sqlDiarioMatricular;
		$RsDiarioMatricular = $conn->Execute($sqlDiarioMatricular);


		while(!$RsDiarioMatricular->EOF) {

    		$ofer             = $RsDiarioMatricular->fields[0];
		    $id               = $RsDiarioMatricular->fields[1];
		    $nome             = $RsDiarioMatricular->fields[2];
		    $prof             = $RsDiarioMatricular->fields[3];
		    $campus           = $RsDiarioMatricular->fields[4];
		    $num_matriculados = $RsDiarioMatricular->fields[5];

    		// CONFERE SE JA ESTA MATRICULADO
		    $sqlConfereDiario = "
		    SELECT EXISTS(
        		SELECT
            		id
        		FROM
            		matricula
        		WHERE
            		ref_disciplina_ofer = $ofer AND
            		ref_pessoa = $aluno_id
    		);";

		    $RsConfereDiario = $conn->Execute($sqlConfereDiario);

		    if ($RsConfereDiario)
			{
        		$ConfereDiario = $RsConfereDiario->fields[0];
    		}


	        // -- Verifica se o aluno foi aprovado ou dispensado nesta disciplina ou em disciplina equivalente a qualquer tempo
        	$txt_cursada = '';
            //verificaAprovacaoContrato($aluno_id,$curso_id,$contrato_id,$diario_id)
        	$flag_cursada = verificaAprovacaoContrato($aluno_id,$curso_id,$contrato_id,$ofer);
        	if ($flag_cursada)
            	$txt_cursada =  ' - <font color="orange"><strong>[ CURSADA ]</strong></font>';

    		// -- Verifica se o aluno ja eliminou os pre-requisitos
        	$flag_pre_requisito = verificaRequisitos($aluno_id,$curso_id,$ofer);
        	$txt_pre_requisito = '';
        	if ($flag_pre_requisito)
            	$txt_pre_requisito =  ' - <a href="consulta_pre_requisito.php?o='.$ofer.'&c='. $curso_id .'" target="_blank" title="Consultar pr&eacute;-requisito" >[ FALTA PR&Eacute;-REQUISITO ]</a>';


		    if($ConfereDiario == 'f') 
			{				
    	    	if (!$flag_pre_requisito)  
				{
        			$DiarioMatricular .= "<input type=\"checkbox\" name=\"id_diarios[]\" ".
                   "id=\"id_diarios[]\" value=\"$ofer\" onclick=\"Exibe('matricular')\" />";
		   		}
				
				$DiarioMatricular .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        		$DiarioMatricular .= "<strong>$ofer - $nome </strong>($id) $txt_equivalentes".
											" $txt_pre_requisito $txt_cursada - $prof<br />";
											
				$disciplinas_liberadas++;
				
    		}//-- FIM CONFERE MATRICULA


    		$RsDiarioMatricular->MoveNext();

		}//FIM WHILE




		if($DiarioMatricular == '') {
		    $msg = '<p><div align="center"><b><font color="#CC0000">'.
	    	'di&aacute;rio n&atilde;o dispon&iacute;vel ou o aluno j&aacute; est&aacute; matriculado neste di&aacute;rio/disciplina!'.
			'</font></b></div></p>';
		}
		else
		{
			$_SESSION['sa_diarios_matricula_avulsa'][$ofer] = $DiarioMatricular;
		}

}//else


//Retorno
echo $msg;
//echo $_SESSION['sa_diarios_matricula_avulsa'][$ofer];

if (is_array($_SESSION['sa_diarios_matricula_avulsa'])) {
  foreach($_SESSION['sa_diarios_matricula_avulsa'] as $diario) {
	echo $diario;
  }
}

?>
