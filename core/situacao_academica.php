<?php

/**
* Funcoes que verificam a situacao do aluno
* @author Wanderson S. Reis
* @version 2
* @since 15-05-2009
**/

// Arquivos de configuracao e biblioteca
// depende do arquivo app/setup.php

$conn = new connection_factory($param_conn);

function mediaPeriodo($periodo_id)
{
    global $conn;
    // -- Busca média final para aprovação e nota máxima no período
    $sqlMedia = "SELECT
                          media_final, nota_maxima
                        FROM
                             periodos
                        WHERE

                             id = '$periodo_id';";

    $media_periodo = $conn->get_row($sqlMedia);

    return $media_periodo;
}


function verificaReprovacaoPorFaltas($aluno_id,$diarios) {
	global $conn;

	$diarios_matriculados = count($diarios);
    $diarios_reprovados = 0;

	if($diarios_matriculados > 0)
	{
		foreach($diarios as $id)
		{
			$diario_id = $id['diario'];

			// -- Verifica se foi reprovado por faltas
			$sqlDisciplina = "
				SELECT DISTINCT
					COUNT(o.id)
				FROM
					matricula m, disciplinas d, pessoas p, disciplinas_ofer o, periodos s
				WHERE
                m.ref_pessoa = p.id AND
                p.id = '$aluno_id' AND
                m.ref_disciplina_ofer = o.id AND
                d.id = o.ref_disciplina AND
                o.is_cancelada = '0' AND
                s.id = o.ref_periodo AND
                ( m.num_faltas > (
                                  	( SELECT
                                        	SUM(CAST(flag AS INTEGER)) AS carga
                                    	FROM
                                        	diario_seq_faltas
                                    	WHERE
                                        	ref_disciplina_ofer = $diario_id ) * 0.25
									)

				) AND
				o.id = $diario_id; ";

			$diarios_reprovados += $conn->get_one($sqlDisciplina);//$RsDisciplina->fields[0];
		}
	}

    if ($diarios_reprovados >= $diarios_matriculados )
         return TRUE;
    else
         return FALSE;
}

function verificaAprovacao($aluno_id,$curso_id,$diario_id)
{
    global $conn;

    $NOTAS = mediaPeriodo($conn->get_one('SELECT periodo_disciplina_ofer('. $diario_id .');'));
    $MEDIA_FINAL_APROVACAO = $NOTAS['media_final'];

      // -- Verifica se foi aprovado neste diário / disciplina
        $sqlDisciplina = "
        SELECT DISTINCT
            o.id AS diario
        FROM
                matricula m, disciplinas_ofer o
        WHERE
                m.ref_pessoa = $aluno_id AND
                m.ref_disciplina_ofer = $diario_id AND
                m.ref_curso = $curso_id AND
                m.ref_disciplina_ofer = o.id AND
                o.is_cancelada = '0' AND
                ( m.nota_final >= $MEDIA_FINAL_APROVACAO AND ref_motivo_matricula = 0 ); ";

        $diarios_matriculados = $conn->get_all($sqlDisciplina);

        if (count($diarios_matriculados) > 0 )
        {
            if (verificaReprovacaoPorFaltas($aluno_id,$diarios_matriculados))
                    return FALSE;
            else
                    return TRUE;
        }
        else
            return FALSE;


   // ^ Verifica se o aluno ja aprovado neste diário / disciplina ^ //
}

function verificaAprovacaoContrato($aluno_id,$curso_id,$contrato_id,$diario_id)
{
    global $conn;

    $NOTAS = mediaPeriodo($conn->get_one('SELECT periodo_disciplina_ofer('. $diario_id .');'));
    $MEDIA_FINAL_APROVACAO = $NOTAS['media_final'];

      // -- Verifica se foi aprovado ou dispensado nesta disciplina ou em disciplina equivalente a qualquer tempo
        $sqlDisciplina = "
        SELECT DISTINCT
            o.id AS diario
        FROM
                matricula m, disciplinas d, pessoas p, disciplinas_ofer o, periodos s, contratos c
        WHERE
                m.ref_pessoa = p.id AND
                p.id = '$aluno_id' AND
                m.ref_disciplina_ofer = o.id AND
                m.ref_contrato = c.id AND
                m.ref_contrato = $contrato_id AND
                c.id = $contrato_id AND
                d.id = o.ref_disciplina AND
                o.is_cancelada = '0' AND
                s.id = o.ref_periodo AND
                ( d.id = get_disciplina_de_disciplina_of('$diario_id') OR
                            d.id IN (
                                        select
                                                distinct ref_disciplina_equivalente
                                        from disciplinas_equivalentes
                                        where ref_disciplina = get_disciplina_de_disciplina_of('$diario_id') and ref_curso = '$curso_id'
                                    )
                ) AND
                ( m.nota_final >= $MEDIA_FINAL_APROVACAO OR ref_motivo_matricula IN (2,3,4) ); ";

        $diarios_matriculados = $conn->get_all($sqlDisciplina);

        if (count($diarios_matriculados) > 0 )
        {
            if (verificaReprovacaoPorFaltas($aluno_id,$diarios_matriculados))
                    return FALSE;
            else
                    return TRUE;
        }
        else
            return FALSE;


   // ^ Verifica se o aluno ja foi aprovado ou dispensado nesta mesma disciplina a qualquer tempo ^ //
}


function verificaAprovacaoContratoDisciplina($aluno_id,$curso_id,$contrato_id,$diario_id)
{
    global $conn;

    $NOTAS = mediaPeriodo($conn->get_one('SELECT periodo_disciplina_ofer('. $diario_id .');'));
    $MEDIA_FINAL_APROVACAO = $NOTAS['media_final'];

      // -- Verifica se foi aprovado na disciplina em questão
        $sqlDisciplina = "
        SELECT DISTINCT
            o.id AS diario
        FROM
                matricula m, disciplinas d, pessoas p, disciplinas_ofer o, periodos s, contratos c
        WHERE
                m.ref_pessoa = p.id AND
                p.id = '$aluno_id' AND
                m.ref_disciplina_ofer = o.id AND
                m.ref_contrato = c.id AND
                m.ref_contrato = $contrato_id AND
                c.ref_curso = $curso_id AND
                c.id = $contrato_id AND
                d.id = o.ref_disciplina AND
                o.is_cancelada = '0' AND
                s.id = o.ref_periodo AND
                m.ref_disciplina_ofer = $diario_id AND
                ( m.nota_final >= $MEDIA_FINAL_APROVACAO OR ref_motivo_matricula IN (2,3,4) ); ";

        $diarios_matriculados = $conn->get_all($sqlDisciplina);

        if (count($diarios_matriculados) > 0 )
        {
            if (verificaReprovacaoPorFaltas($aluno_id,$diarios_matriculados))
                    return FALSE;
            else
                    return TRUE;
        }
        else
            return FALSE;


   // ^ Verifica se o aluno foi aprovado na disciplina em questao ^ //
}

function verificaEquivalencia($curso_id,$diario_id)
{
    global $conn;
    // -- Verifica se a disciplina é equivalente para o curso matriculado
    $sqlDisciplina = "
                    SELECT
                          DISTINCT
                                ref_disciplina_equivalente, ref_disciplina, ref_curso
                        FROM
                                disciplinas_equivalentes
                        WHERE
                             ref_disciplina = get_disciplina_de_disciplina_of('$diario_id') AND ref_curso = '$curso_id';";

    $equivalentes = $conn->get_all($sqlDisciplina);

    if (count($equivalentes) > 0 )
        return TRUE;
    else
        return FALSE;
}


function verificaRequisitos($aluno_id,$curso_id,$diario_id)
{
  global $conn;

  $NOTAS = mediaPeriodo($conn->get_one('SELECT periodo_disciplina_ofer('. $diario_id .');'));
  $MEDIA_FINAL_APROVACAO = $NOTAS['media_final'];

    // -- Verifica se o aluno ja eliminou os pre-requisitos
    // existe  pre-requisito? considera somente os pré-requisito para o curso do aluno

    $disciplinas = " SELECT get_disciplina_de_disciplina_of('$diario_id') ";

    // se é uma disciplina equivalente verifica os pré-requisitos da disciplina "original" da matriz curricular
    if (verificaEquivalencia($curso_id,$diario_id))
    {
      	// a disciplina é equivalente, recupera a disciplina "original" da matriz curricular
      	$sqlEquivalente = "
                    SELECT
                          DISTINCT
                                ref_disciplina
                        FROM
                                disciplinas_equivalentes
                        WHERE
                             ref_disciplina_equivalente = get_disciplina_de_disciplina_of('$diario_id') AND
                             ref_curso = '$curso_id';";

		$disc_original = $conn->get_one($sqlEquivalente);
//        print_r($equivalentes); if ($diario_id = '5354') die;
        if (!empty($disc_original) && is_numeric($disc_original))
            $disciplinas =  "'". $disc_original ."'";
    }

    $sqlPreRequisito = "
            SELECT DISTINCT
                ref_disciplina_pre
            FROM
                pre_requisitos
            WHERE
                ref_disciplina IN ( $disciplinas ) AND ref_curso = $curso_id;";

    $pre_requisitos = $conn->get_all($sqlPreRequisito);

    $total_requisitos = count($pre_requisitos);
    $requisitos_matriculados = array();
    if (count($total_requisitos) > 0)
	{
		foreach($pre_requisitos as $req)
		{
			$disc_req = $req['ref_disciplina_pre'];
        	// foi aprovado ou dispensado do pre-requisito? considera disciplina equivalente também
			// CONSIDERA SOMATORIO FINAL DE NOTA E DISPENSA
        	$sqlPreRequisito1 = "
        			SELECT DISTINCT
        				o.id AS diario
        			FROM
            			matricula m, disciplinas d, pessoas p, disciplinas_ofer o, periodos s
        			WHERE
             			m.ref_pessoa = p.id AND
                		p.id = '$aluno_id' AND
                		m.ref_disciplina_ofer = o.id AND
                		d.id = o.ref_disciplina AND
                		o.is_cancelada = '0' AND
                		s.id = o.ref_periodo AND
                		( d.id = '$disc_req' OR d.id IN ( select distinct ref_disciplina_equivalente
															from disciplinas_equivalentes
														where ref_disciplina = '$disc_req' and ref_curso = '$curso_id'  ) ) AND
        	       		( m.nota_final >= $MEDIA_FINAL_APROVACAO OR ref_motivo_matricula IN (2,3,4) ); ";
        				$requisitos_matriculados = array_merge($requisitos_matriculados,$conn->adodb->getAll($sqlPreRequisito1));
		 }
    }

    $ret = FALSE;
	if (count($requisitos_matriculados) > 0)
    {
        // VERIFICA SE HOUVE REPROVAÇÃO POR FALTAS EM ALGUM PRÉ-REQUISITO
    	if (verificaReprovacaoPorFaltas($aluno_id,$requisitos_matriculados))
            $ret = TRUE;
      	else
            $ret = FALSE;
    }

	// VERIFICA SE A QUANTIDADE DE REQUISITOS MATRICULADOS APROVADOS É MAIOR OU IGUAL
    // AOS REQUISITOS  EXIGIDOS PELA DISCIPLINA, NESTE CASO OS REQUISITOS FORAM SATISFEITOS
    if (count($requisitos_matriculados) >= $total_requisitos)
    	$ret = FALSE;
    else
        $ret = TRUE;

    return $ret;
}


function verificaPeriodo($periodo_id)
{
    global $conn;
    // -- Verifica é um periodo em andamento
    $sqlPeriodo = "
                    SELECT
                          dt_final
                        FROM
                             periodos
                        WHERE
                             id = '$periodo_id';";

    $data_final_periodo = strtotime($conn->get_one($sqlPeriodo));
    $data_atual = strtotime(date('Y-m-d'));

    if ( $data_atual > $data_final_periodo )
        return TRUE;
    else
        return FALSE;
}


?>
