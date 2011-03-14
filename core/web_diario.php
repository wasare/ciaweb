<?php
/**
 * Funcoes usadas pelo web diario
 * @author Wanderson S. Reis
 * @version 1
 * @since 30-09-2009
 **/

require_once(dirname(__FILE__) .'/../app/setup.php');

// CONEXAO ABERTA PARA TRABALHAR COM TRANSACAO (NÃO PERSISTENTE)
$conn = new connection_factory($param_conn);

function envia_erro($msg) {

    // ENVIA EMAIL PARA O ADMINISTRADOR
    $mail_header = 'FROM: '. $EMAIL_ADMIN;
    @mail($EMAIL_ADMIN, '[ Erro ao atualizar diario ] ', $msg, $mail_header);

}

function papeleta_header($diario_id) {
    global $conn;

    $sql9 = "SELECT 
      DISTINCT 
          curso_disciplina_ofer(id) || ' - ' || curso_desc(curso_disciplina_ofer(id)) AS curso, get_campus(ref_campus) AS campus, descricao_periodo(ref_periodo), get_disciplina_de_disciplina_of(id) || ' - ' || descricao_disciplina(get_disciplina_de_disciplina_of(id)) || ' (' || id || ')' AS disciplina
    FROM 
      disciplinas_ofer
   WHERE id = $diario_id AND is_cancelada = '0';";

    $diario_info = $conn->get_row($sql9);

    $profs = $conn->get_col("select pessoa_nome(ref_professor) from disciplinas_ofer_prof where ref_disciplina_ofer = $diario_id;");

    $ret = '';
    $ret .= '<input type="hidden" name="curso" id="curso" value="'. $ref_curso .'" />';

    $ret = 'Curso: <b>'. $diario_info['curso'] .'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />';
    $ret .= 'Disciplina: <b>'. $diario_info['disciplina'] .'</b><br>';
    $ret .= 'Per&iacute;odo: <b>'. $diario_info['descricao_periodo'] .'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />';
    $ret .= 'Campus: <b>'. $diario_info['campus'] .'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />';
    $ret .= 'Professor(a): ';

    $i = 1;
    $count = count($profs);
    foreach($profs as $p) {

        $ret .= '<b>'. $p .'</b><br />';

        if($count != $i) {
            $ret .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

        }
        $i++;
    }
    return $ret;
} 

function get_curso($diario_id) {

    global $conn;

    $sql9 = "SELECT
             d.ref_curso
             FROM
              disciplinas_ofer d  where
              d.id = $diario_id;";

    return $conn->get_one($sql9);
}

function get_curso_tipo($diario_id) {

    global $conn;

    // CONSULTA O NIVEL DO CURSO
    $sql = 'SELECT
                ref_tipo_curso
                        FROM
                            cursos c, disciplinas_ofer d
                        WHERE
                             c.id = ref_curso AND
                         d.id = '. $diario_id .';';

    return $conn->get_one($sql);

}

function get_disciplina($diario_id) {

    global $conn;

    $sql9 = "SELECT
             d.ref_disciplina
             FROM
              disciplinas_ofer d  where
              d.id = $diario_id;";

    return $conn->get_one($sql9);
}

function get_ano_periodo($periodo) {

    global $conn;

    $qry1 = "SELECT
					 to_char(dt_inicial, 'YYYY'), to_char(dt_final, 'YYYY')
                FROM 
					periodos WHERE id = '". $periodo ."';";

    return array_unique($conn->get_row($qry1));
}


function is_inicializado($diario_id) {

    global $conn;

    $grupo = ("%-%-%-" . $diario_id);

    $sql1 = "SELECT
         COUNT(grupo)
         FROM diario_formulas
         WHERE
         grupo ILIKE '$grupo';";

    $num_reg = $conn->get_one($sql1);

    if ($num_reg == 6)
        return TRUE;
    else
        return FALSE;
}

function is_finalizado($diario_id) {

    global $conn;
    $sql = 'SELECT
                  fl_finalizada
                     FROM
                        disciplinas_ofer d
                     WHERE
                        d.id = '. $diario_id .';';

    $diario = $conn->get_one($sql);

    if ($diario == 't')
        return TRUE;
    else
        return FALSE;
}

function is_diario($diario_id) {

    global $conn;
    $sql = 'SELECT
                  COUNT(d.id)
                     FROM
                        disciplinas_ofer d
                     WHERE
                        d.id = '. $diario_id .' AND
                        d.is_cancelada = \'0\';';

    $diario = $conn->get_one($sql);

    if ($diario == 1)
        return TRUE;
    else
        return FALSE;
}


function ini_diario($ofer) {

    global $conn;

    // RECUPERA INFORMACOES DO DIARIO
    $qryDisc = " SELECT DISTINCT
                prof.ref_professor, o.ref_disciplina, o.ref_periodo 
                FROM 
                disciplinas_ofer o, disciplinas_ofer_prof prof
                WHERE
                 o.id = " . $ofer . " AND 
                 o.is_cancelada = '0' AND
                 o.id = prof.ref_disciplina_ofer LIMIT 1;";


    $diario_info = $conn->get_row($qryDisc);

    if($diario_info === FALSE) {

        envia_erro($qryDisc);
        exit;
    }
    else {

        // A DISCIPLINA EXISTE^M

        if(count($diario_info) > 0) {

            $disc = $diario_info['ref_disciplina'];
            $periodo = $diario_info['ref_periodo'];
            $prof = (int) $diario_info['ref_professor'];

        } // ^ A DISCIPLINA EXISTE
    }

    $grupo = ($prof . "-" . $periodo . "-" . $disc . "-" . $ofer);
    $grupo_novo = ("%-%-%-" . $ofer);

    $curso = get_curso($ofer);

    $ret = TRUE;

    // PASSO 1
    $numprovas = 6;

    // PASSO 2
    $formula = '';
    for ($cont = 1; $cont <= $numprovas; $cont++) {
        $prova[] = 'Nota '.$cont;

        if($cont == 1) {
            $formula .= 'P'.$cont;
        }
        else {
            $formula .= '+P'.$cont;
        }
    }

    // PASSO 3
    $sqldel = "BEGIN; DELETE FROM diario_formulas WHERE grupo ILIKE '$grupo_novo';";
    $sqldel .= "DELETE FROM diario_notas WHERE rel_diario_formulas_grupo ILIKE '$grupo_novo'; COMMIT;";

    $ret =  $conn->Execute($sqldel);

    reset($prova);

    $sql1 = 'BEGIN;';

    while(list($index,$value) = each($prova)) {
        $descricao_prova = $prova[$index];
        $num_prova = ($index+1);
        $sql1 .= "INSERT INTO diario_formulas (ref_prof, ref_periodo, ref_disciplina, prova, descricao, formula, grupo) values($prof,'$periodo','$disc',$num_prova,'$descricao_prova','$formula','$grupo');";

    }

    $sql1 .= 'COMMIT;';

    $ret = $conn->Execute($sql1);

    // PASSO 4 - PROCESSA CRIA REGISTROS DE ACORDO COM A FORMULA
    $qryNotas = 'SELECT
        m.ref_pessoa, id_ref_pessoas
        FROM
            matricula m
        LEFT JOIN (
                SELECT DISTINCT
                d.id_ref_pessoas
            FROM
                diario_notas d
            WHERE
                d.d_ref_disciplina_ofer = ' . $ofer . '
              ) tmp
        ON ( m.ref_pessoa = id_ref_pessoas )
        WHERE
            m.ref_disciplina_ofer = ' . $ofer . ' AND
            id_ref_pessoas IS NULL AND
            (m.dt_cancelamento is null) AND
            (m.ref_motivo_matricula = 0)
        ORDER BY
                id_ref_pessoas;';

    $qry = $conn->get_all($qryNotas);

    $qryDiario = "BEGIN;";

    foreach($qry as $registro) {
        $ref_pessoa = $registro['ref_pessoa'];

        for($i = 1 ; $i <= $numprovas; $i++) {
            $qryDiario .= ' INSERT INTO diario_notas(ra_cnec, ';
            $qryDiario .= ' ref_diario_avaliacao,nota,peso,id_ref_pessoas,';
            $qryDiario .= ' id_ref_periodos,id_ref_curso,d_ref_disciplina_ofer,';
            $qryDiario .= ' rel_diario_formulas_grupo)';
            $qryDiario .= " VALUES($ref_pessoa,$i,0,0,$ref_pessoa,'$periodo',$curso,";
            $qryDiario .= " $ofer,'$grupo');";
        }

        $qryDiario .= ' INSERT INTO diario_notas(ra_cnec, ';
        $qryDiario .= ' ref_diario_avaliacao,nota,peso,id_ref_pessoas,';
        $qryDiario .= ' id_ref_periodos,id_ref_curso,d_ref_disciplina_ofer,';
        $qryDiario .= ' rel_diario_formulas_grupo)';
        $qryDiario .= " VALUES($ref_pessoa,7,-1,0,$ref_pessoa,'$periodo',$curso,";
        $qryDiario .= " $ofer,'$grupo');";
    }


    $qryDiario .= "COMMIT;";

    $ret = $conn->Execute($qryDiario);

    return $ret;
}

// function registra as faltas da chamada e alterações destas faltas
function registra_faltas($ref_aluno, $diario_id, $num_faltas, $data_chamada, $professor,$altera=FALSE) {
    global $conn;

    $sql_falta = 'BEGIN;';

    $consulta_faltas = 'INSERT INTO
                              diario_chamadas (ra_cnec, data_chamada,
                                               ref_professor, ref_periodo,
                                               ref_curso, ref_disciplina, aula,
                                               abono, ref_disciplina_ofer) VALUES ';
    $sql_faltas_update = "SELECT
                    count(ra_cnec) AS num_faltas
                FROM
                    diario_chamadas a
                WHERE
                    (a.ref_disciplina_ofer = $diario_id) AND
                    (ra_cnec = '$ref_aluno')";

    if($altera == TRUE) {
        // EXCLUI TODAS AS FALTAS ANTERIORES PARA A CHAMADA
        $sql_falta .= "DELETE FROM diario_chamadas
                                      WHERE
                                          (ref_disciplina_ofer = $diario_id) AND
                                          (data_chamada = '$data_chamada') AND
                                          (ra_cnec = '$ref_aluno');";
        // ^ EXCLUI TODAS AS FALTAS ANTERIORES PARA A CHAMADA ^ //
    }

    // INCLUI AS FALTAS NA CHAMADA (tabela diario_chamadas)
    for ($i = 1; $i <= abs($num_faltas); $i++) {
        $sql_falta .= $consulta_faltas." ('$ref_aluno','$data_chamada','$professor',";
        $sql_falta .= " periodo_disciplina_ofer($diario_id), curso_disciplina_ofer($diario_id),";
        $sql_falta .= " get_disciplina_de_disciplina_of($diario_id),'$i','N',$diario_id);";
    }

    // ATUALIZA O TOTAL DE FALTA (tabela matricula)
    $sql_falta .=  "UPDATE
                  matricula
               SET
                  num_faltas = ( $sql_faltas_update )
               WHERE
                  ref_pessoa = $ref_aluno AND
                  ref_disciplina_ofer = $diario_id;";

    $sql_falta .= 'COMMIT;';

    $conn->Execute($sql_falta);

    return TRUE;

}

// VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR 
function acessa_diario($diario_id,$sa_ref_pessoa) {

    global $conn;

    $sql = 'SELECT
				(
					SELECT count(*) 
							FROM coordenador 
							WHERE ref_professor = '. $sa_ref_pessoa .' AND
								  ref_curso = '. get_curso($diario_id) .'
				) + (
					  SELECT COUNT(*) 
							FROM disciplinas_ofer_prof 
							WHERE ref_disciplina_ofer = '. $diario_id .' AND
								  ref_professor = '. $sa_ref_pessoa .'
					) AS acesso;';						

    $acesso = $conn->get_one($sql);

    if($acesso > 0)
        return TRUE;
    else
        return FALSE;
}

// VERIFICA SE EXISTE CHAMADA
function existe_chamada($diario_id,$data_chamada='') {

    global $conn;

    $sql = "SELECT COUNT(*)
	  FROM
      diario_seq_faltas
      WHERE
      ref_disciplina_ofer = ". $diario_id;

    if (!empty($data_chamada)) {
        $sql .= " AND  dia = '". $data_chamada ."';";
    }
    else {
        $sql .= ';';
    }

    $chamadas = $conn->get_one($sql);

    if($chamadas > 0)
        return TRUE;
    else
        return FALSE;
}

// VERIFICA SE EXISTE MATRICULAS NO DIARIO
function existe_matricula($diario_id) {

    global $conn;

    $sql = 'SELECT
			COUNT(a.id)
		FROM
			matricula a
		WHERE
			(a.dt_cancelamento is null) AND
			a.ref_disciplina_ofer = '. $diario_id .' AND
			a.ref_motivo_matricula = \'0\'
        GROUP BY
            a.id;';

    $matriculas = $conn->get_one($sql);

    if($matriculas > 0)
        return TRUE;
    else
        return FALSE;
}

// GRAVA LOG NO BANCO DE DADOS
function reg_log($pagina,$status) {

    global $conn, $sa_usuario,$sa_senha;

    $ip = $_SERVER["REMOTE_ADDR"];
    $sql_store = htmlspecialchars("$sa_usuario");
    $sql_log = 'BEGIN; INSERT INTO diario_log (usuario, data, hora, ip_acesso, pagina_acesso, status, senha_acesso) VALUES ';
    $sql_log .= '(\''.$sql_store.'\',\''. date("Y-m-d") .'\',\''. date("H:i:s") .'\','."'$ip','$pagina','$status','$sa_senha')";

    $conn->Execute($sql_log);

}



// VERIFICA O DIREITO DE ACESSO AO DADOS DO ALUNO PELO PROFESSOR OU COORDENADOR
function acessa_ficha_aluno($aluno_id,$sa_ref_pessoa,$curso_id,$conexao=FALSE) {

    global $conn;

    $sql = 'SELECT
				(
					SELECT count(*)
							FROM contratos
							WHERE ref_pessoa = '. $aluno_id .' AND
								  ref_curso IN ( 
                                                  SELECT DISTINCT 
                                                            ref_curso
                                                       FROM 
                                                            coordenador
                                                       WHERE
                                                            ref_professor = '. $sa_ref_pessoa .' AND
                                                            ref_curso = '. $curso_id .'
                                               )
				) + (
					  SELECT COUNT(*)
							FROM matricula
							WHERE ref_pessoa = '. $aluno_id .' AND
								  ref_disciplina_ofer IN (
                                                            SELECT DISTINCT
                                                                  o.id
                                                              FROM
                                                                  disciplinas_ofer o, disciplinas_ofer_prof dp
                                                              WHERE
                                                                   dp.ref_professor = '. $sa_ref_pessoa .' AND
                                                                   o.ref_curso = '. $curso_id .' AND
                                                                   o.id = dp.ref_disciplina_ofer AND
                                                                   o.is_cancelada = \'0\'
                                                           )
					) AS acesso;';

    if ($conexao == TRUE) {
        $rs = pg_query($conexao, $sql);
        $acesso = pg_fetch_result($rs, 0, 0);
    }
    else {
        $acesso = $conn->get_one($sql);
    }

    if($acesso > 0)
        return TRUE;
    else
        return FALSE;
}





function calcula_nota_reavaliacao($diario_id,$nota_parcial,$nota_extra) {

	global $conn;

    $curso_tipo = get_curso_tipo($diario_id);


	/*
	 1     Tecnico
	 2     Graduacao Tecnologica
	 4     Pos-Graduacao Latu-Sensu
	 5     Qualificacao
	 6     Bacharelado
	 7     Tecnico Integrado
	 8     Tecnico - EJA
	 9     Tecnico Integrado - EJA
	 10    Licenciatura
	 */

    // TODO: Selecionar método de cálculo da nota final com base em parâmetros do sistema
    // SE FOR NOTA DE RECUPERACAO / REAVALIACAO CALCULA CONFORME CRITERIOS DE CADA CURSO
	if( $curso_tipo == 2 || $curso_tipo == 4 || $curso_tipo == 5 || $curso_tipo == 6 || $curso_tipo == 10 ) {

        return (($nota_parcial + $nota_extra) / 2);
   	}
    else
    {
     	return $nota_extra;
    }
}


function atualiza_diario($aluno,$diario_id,$motivo_matricula=0){

	global $conn;

	// RECUPERA INFORMACOES DO DIARIO
	$qryDisc = " SELECT DISTINCT
				prof.ref_professor, o.ref_disciplina, o.ref_periodo 
				FROM 
				disciplinas_ofer o, disciplinas_ofer_prof prof
            	WHERE
                 o.id = " . $diario_id . " AND 
				 o.is_cancelada = '0' AND
				 o.id = prof.ref_disciplina_ofer ;";


	$diario_info = $conn->get_all($qryDisc);

	// A DISCIPLINA EXISTE

	if(count($diario_info) > 0) {

		foreach($diario_info as $linha)
		{
			$getdisciplina = @$linha['ref_disciplina'];
			$getperiodo = @$linha['ref_periodo'];
			$id = @$linha['ref_professor'];
		}

	} // ^ A DISCIPLINA EXISTE

	$grupo = ($id . "-" . $getperiodo . "-" . $getdisciplina . "-" . $diario_id);

	$grupo_novo = ("%-" . $getperiodo . "-%-" . $diario_id);


	$flag_pendencia = 0;

	$qryDiario = 'BEGIN;';

	$getcurso = get_curso($diario_id);


	// VERIFICA PENDENCIAS RELACIONADAS AO LANCAMENTO DE NOTAS
	$sql1 = "SELECT
	COUNT(grupo)
	FROM diario_formulas
	WHERE
	grupo ILIKE '$grupo_novo';";


	$num_formula = $conn->get_one($sql1);

	if($num_formula == 6) {

		$qryNotas = 'SELECT
		    m.ref_pessoa, id_ref_pessoas 
	        FROM 
	    	matricula m 
	        LEFT JOIN (
		    	SELECT DISTINCT 
				d.id_ref_pessoas 
				FROM 
				diario_notas d 
				WHERE 
				d.d_ref_disciplina_ofer = ' . $diario_id . ' AND
                id_ref_pessoas = ' . $aluno . '
		      ) tmp 
			ON ( m.ref_pessoa = id_ref_pessoas ) 
	    	WHERE 
		        m.ref_disciplina_ofer = ' . $diario_id . ' AND 
		        m.ref_pessoa = ' . $aluno . ' AND
		        id_ref_pessoas IS NULL  AND
			    (m.dt_cancelamento is null) AND
			    (m.ref_motivo_matricula = '. $motivo_matricula .')
	        ORDER BY id_ref_pessoas;';

		$alunos_sem_registro_notas = $conn->get_all($qryNotas);

		$num_registros = count($alunos_sem_registro_notas);

		$num_notas = 6;

		if ($num_registros > 0)
		{
			foreach($alunos_sem_registro_notas as $registro)
			{
				$ref_pessoa = $registro['ref_pessoa'];

				for($i = 1 ; $i <= $num_notas; $i++)
				{
					$qryDiario .= ' INSERT INTO diario_notas(ra_cnec, ';
					$qryDiario .= ' ref_diario_avaliacao,nota,peso,id_ref_pessoas,';
					$qryDiario .= ' id_ref_periodos,id_ref_curso,d_ref_disciplina_ofer,';
					$qryDiario .= ' rel_diario_formulas_grupo)';
					$qryDiario .= " VALUES($ref_pessoa,$i,0,0,$ref_pessoa,'$getperiodo',$getcurso,";
					$qryDiario .= " $diario_id, '$grupo');";
				}

				$qryDiario .= ' INSERT INTO diario_notas(ra_cnec, ';
				$qryDiario .= ' ref_diario_avaliacao,nota,peso,id_ref_pessoas,';
				$qryDiario .= ' id_ref_periodos,id_ref_curso,d_ref_disciplina_ofer,';
				$qryDiario .= ' rel_diario_formulas_grupo)';
				$qryDiario .= " VALUES($ref_pessoa,7,-1,0,$ref_pessoa,'$getperiodo',$getcurso,";
				$qryDiario .= " $diario_id ,'$grupo');";
			}

			$flag_pendencia = 1;
			// $msg .= $num_registros .' alunos com problemas no lancamento de notas\n';

		}

		// ^ VERIFICA PENDENCIAS RELACIONADAS AO LANCAMENTO DE NOTAS ^


		// VERIFICA PENDENCIAS RELACIONADAS AO SOMATORIO DE FALTAS
		$sqlDiarioFaltas = "
		SELECT * FROM
		(
		SELECT DISTINCT
		registro_id,
		CASE
		WHEN num_faltas IS NULL THEN '0'
		ELSE num_faltas
		END AS num_faltas,
		CASE
		WHEN faltas_diario IS NULL THEN '0'
		ELSE faltas_diario
		END AS faltas_diario
		FROM
		(
		SELECT DISTINCT
		CAST(a.ref_pessoa AS INTEGER) AS registro_id, a.num_faltas
		FROM
		matricula a
		WHERE
		a.ref_periodo = '$getperiodo' AND
		a.ref_disciplina_ofer = $diario_id AND
		a.ref_pessoa = $aluno
		) AS T1
		FULL OUTER JOIN
		(
		SELECT
		CAST(a.ra_cnec AS INTEGER) AS registro_id, count(CAST(a.ra_cnec AS INTEGER)) AS faltas_diario
		FROM
		diario_chamadas a
		WHERE
		(a.ref_periodo = '$getperiodo') AND
		(a.ref_disciplina_ofer = $diario_id) AND
		a.ra_cnec = '$aluno'
		GROUP BY ra_cnec
		) AS T4

		USING (registro_id)

		) AS TB

		WHERE
		(num_faltas <> faltas_diario);";

		$diario_faltas = $conn->get_all($sqlDiarioFaltas);

		$numFalta = count($diario_faltas);


		if ($numFalta != 0) {

			foreach($diario_faltas as $registro)
			{
				$ref_pessoa = $registro['registro_id'];
				$faltas = $registro['faltas_diario'];
				$num_faltas = $registro['num_faltas'];

				$qryDiario .= ' UPDATE matricula SET num_faltas = '. $faltas;
				$qryDiario .= ' WHERE ref_pessoa = '. $ref_pessoa .' AND';
				$qryDiario .= " ref_periodo = '$getperiodo' AND ";
				$qryDiario .= ' ref_disciplina_ofer = '. $diario_id .';';
			}

			$flag_pendencia = 1;
			$msg_atualiza .= 'Atualizado somat&oacute;rio de faltas\n';
			// $msg .= $numFalta . ' alunos com problemas no somatorio de faltas\n';

		}
		// ^ VERIFICA PENDENCIAS RELACIONADAS AO SOMATORIO DE FALTAS ^


		// VERIFICA PENDENCIAS RELACIONADAS AO SOMATORIO DE NOTAS *** CONSIDERA NOTA EXTRA ***
		$sqlNotas = "
		SELECT DISTINCT
		registro_id, nota_diario, nota_extra, nota_final
		FROM
		(
		SELECT
		DISTINCT
		CAST(b.id AS INTEGER) AS registro_id, CAST(SUM(c.nota) AS NUMERIC) AS nota_diario
		FROM
		matricula a, pessoas b, diario_notas c
		WHERE
		a.ref_periodo = '$getperiodo' AND
		a.ref_disciplina_ofer = $diario_id AND
		b.ra_cnec = c.ra_cnec AND
		c.d_ref_disciplina_ofer = $diario_id AND
		a.ref_pessoa = b.id AND
		b.ra_cnec = '$aluno'  AND
		ref_diario_avaliacao < 7
		GROUP BY b.id
		) AS T1

		INNER JOIN (
		SELECT DISTINCT
		CAST(b.id AS INTEGER) AS registro_id, CAST(c.nota AS NUMERIC) AS nota_extra
		FROM
		matricula a, pessoas b, diario_notas c
		WHERE
		a.ref_periodo = '$getperiodo' AND
		a.ref_disciplina_ofer = $diario_id AND
		b.ra_cnec = c.ra_cnec AND
		c.d_ref_disciplina_ofer = $diario_id AND
		a.ref_pessoa = b.id AND
		b.ra_cnec = '$aluno'  AND
		ref_diario_avaliacao = 7
		) AS T2

		USING (registro_id)
		INNER JOIN

		(
		SELECT DISTINCT
		CAST(a.ref_pessoa AS INTEGER) AS registro_id, CAST(a.nota_final AS NUMERIC)
		FROM
		matricula a
		WHERE
		a.ref_periodo = '$getperiodo' AND
		a.ref_disciplina_ofer = $diario_id AND
		a.ref_pessoa = $aluno
		) AS T3

		USING (registro_id)

		WHERE
		nota_diario <> nota_final;";

		$diario_notas = $conn->get_all($sqlNotas);

		$numNotas = count($diario_notas);

        //print_r($diario_notas);
		if ($numNotas != 0) {

			$numNotas = 0;

			foreach($diario_notas as $registro)
			{
			
				$ref_pessoa = $registro['registro_id'];
				$nota_diario = $registro['nota_diario'];
				$nota_final = $registro['nota_final'];
				$nota_extra = $registro['nota_extra'];

			    //print_r($registro);		
				if($nota_extra == -1 && $nota_diario != $nota_final) {
					// NOTA EXTRA NAO LANCADA E SOMATORIO ERRADO
					$qryDiario .= ' UPDATE matricula SET nota_final = '. $nota_diario;
					$qryDiario .= ' WHERE ref_pessoa = '. $ref_pessoa .' AND';
					$qryDiario .= " ref_periodo = '$getperiodo' AND ";
					$qryDiario .= ' ref_disciplina_ofer = '. $diario_id .';';

					$numNotas++;
				}
				else {
					// NOTA EXTRA LANCADA
					if($nota_diario < 60 || $nota_final < 60) {

						// CALCULA NOTA FINAL E VERIFICA NOTA EXTRA SOMENTE COM NOTA < 60
						// NOTA < 60 RATIFICA O LANCAMENTO DA NOTA EXTRA

						$nota_final_calculada = calcula_nota_reavaliacao($diario_id,$nota_diario,$nota_extra);

						if($nota_final_calculada != $nota_final) {

							// NOTA EXTRA LANCADA E SOMATORIO ERRADO
							$qryDiario .= ' UPDATE matricula SET nota_final = '. $nota_final_calculada;
							$qryDiario .= ' WHERE ref_pessoa = '. $ref_pessoa .' AND';
							$qryDiario .= " ref_periodo = '$getperiodo' AND ";
							$qryDiario .= ' ref_disciplina_ofer = '. $diario_id .';';

							$numNotas++;
						}
					}
				}
			}

			if($numNotas > 0) {

				$flag_pendencia = 1;
				$msg_atualiza .= 'Atualizado somat&oacute;rio de faltas\n';
				// $msg .= $numNotas . ' alunos com problemas no somatorio de notas\n';
			}

		}

		// VERIFICA PENDENCIAS RELACIONADAS AO SOMATORIO DE NOTAS *** CONSIDERA NOTA EXTRA ***

		// APLICA A RESOLUCAO DE PENDENCIAS CASO EXISTA ALGUMA
		if($flag_pendencia == 1) {

			$qryDiario .= "COMMIT;";

			// GRAVA AS ALTERACOES
			$conn->Execute($qryDiario);
/*
			if($res === FALSE) {

				// MENSAGEM DE ERRO AO GRAVAR AS ALTERACOES OU ENVIA EMAIL AVISANDO ALGUEM
				$msg_erro = "";
                envia_erro($res ."\n\n". $qryDiario);

				//^ MENSAGEM DE ERRO AO GRAVAR AS ALTERACOES OU ENVIA EMAIL AVISANDO ALGUEM

			}
			else {
				// MENSAGEM PENDENCIAS RESOLVIDAS COM SUCESSO
				$msg_sucesso = "";
				//^ MENSAGEM PENDENCIAS RESOLVIDAS COM SUCESSO
			}
*/
		}
		else {

			// MENSAGEM NENHUMA PENDENCIA A RESOLVER
			$msg_sem_pendencias = "";
			//^  MENSAGEM NENHUMA PENDENCIA A RESOLVER
		}
		// ^ APLICA A RESOLUCAO DE PENDENCIAS CASO EXISTA ALGUMA

	}

}


function atualiza_dispensa($aluno,$diario_id,$dispensa_tipo) {

	global $conn;

	// EXCLUI FALTAS E NOTAS ANTERIORES 
    $sql_dispensa = 'BEGIN;';
    $sql_faltas = "DELETE FROM diario_chamadas WHERE ra_cnec = '$aluno' AND ref_disciplina_ofer = $diario_id;";
    $sql_notas = "DELETE FROM diario_notas WHERE ra_cnec = '$aluno' AND d_ref_disciplina_ofer = $diario_id;";
    $sql_dispensa .= $sql_faltas . $sql_notas .'COMMIT;';
    $rs_dispensa = $conn->Execute($sql_dispensa);
    // ^ EXCLUI FALTAS E NOTAS ANTERIORES ^ //

    // INICIALIZA O DIARIO CASO NECESSÁRIO
    if(!is_inicializado($diario_id)) {    
		if (ini_diario($diario_id)) {
			atualiza_diario($aluno, $diario_id,$dispensa_tipo);
		}
        else 
			echo '<script language=javascript> window.alert("Falha ao inicializar o diario!"); window.close();</script>';
	}
    else
		atualiza_diario($aluno, $diario_id,$dispensa_tipo);
}


function lanca_nota($aluno,$nota_final,$diario_id,$codprova=1) {
    
	global $conn;

    // FIXME: antes de gravar a nota verificar:
    //     - se nota não é > 100
    //     - lançamento de nota extra, não lançar nota caso exista a extra
    $msg = '';

    $nota = str_replace(",",".",$nota_final);

    $sql_nota .= "UPDATE 
                     diario_notas 
                  SET 
                     nota = $nota 
                  WHERE 
                     d_ref_disciplina_ofer = $diario_id AND
                     ref_diario_avaliacao = $codprova AND 
                     ra_cnec = '$aluno';";

    $rs_nota = $conn->Execute($sql_nota);

    if($rs_nota == FALSE) {
        envia_erro($sql_nota);
        $msg = 'p>>> <b><font color="#FF0000">Falha ao atualizar Nota '. $codprova .' do aluno '. $aluno .' Di&aacute;rio '. $diario_id .'</font></b></p>';
    }

    return $msg;
}


/*
 // FIXME  -- para as faltas construir a chamada a partir de uma data inicial
function lanca_chamada($aluno,$num_faltas,$getofer,$data_inicial) {

}

// FIXME  -- gravar o conteúdo de aula na primeira chamada e anexar uma observação
function lanca_conteudo($getofer,$data_inicial,$conteudo) {


}
*/

?>
