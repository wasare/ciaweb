<?php

if ($aluno_id == 0 || $curso_id == 0 || $contrato_id == 0)
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Dados invalidos!");window.close();</script>');


//  VERIFICA O DIREITO DE ACESSO A FICHA COMO PROFESSOR OU COORDENADOR
if(isset($_SESSION['sa_modulo']) && $_SESSION['sa_modulo'] == 'web_diario_login') {
  if(!acessa_ficha_aluno($aluno_id,$sa_ref_pessoa,$curso_id)) {
    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
  }
  // ^ VERIFICA O DIREITO DE ACESSO A FICHA COMO PROFESSOR OU COORDENADOR ^ //
}

$fl_integralizado = FALSE;

// TODAS AS DISCIPLINAS DA MATRIZ CURRICULAR
$sql_disciplinas_curso = "SELECT ref_disciplina FROM cursos_disciplinas WHERE ref_curso = $curso_id;";

// DISCIPLINAS APROVADAS COM MATRICULA PARA O CONTRATO CONSULTADO
$sql_disciplinas_aprovadas = "
        SELECT DISTINCT
            o.ref_disciplina
        FROM
                matricula m, disciplinas_ofer o
        WHERE
                m.ref_pessoa = $aluno_id AND
                m.ref_disciplina_ofer = o.id AND
                m.ref_contrato = $contrato_id AND
                m.ref_curso = $curso_id AND
                o.is_cancelada = '0' AND
                m.dt_cancelamento IS NULL AND
                ( 
                    ( m.nota_final >= 60 AND 
                      o.fl_finalizada = 't' AND
                      ( m.num_faltas <= ( get_carga_horaria_realizada(o.id) ) * 0.25 ) ) OR
                    ref_motivo_matricula IN (2,3,4)
                ); ";

     
$disciplinas_curso = (array) $conn->get_col($sql_disciplinas_curso);

$disciplinas_aprovadas = (array) $conn->get_col($sql_disciplinas_aprovadas);

$disciplinas_nao_cursadas = array_diff($disciplinas_curso, $disciplinas_aprovadas);
$disciplinas_cursadas_fora_da_matriz = array_diff($disciplinas_aprovadas, $disciplinas_curso);     

/*
$cursadas = array("green", "red", "blue");
$matriz = array("green", "yellow", "red", "blue");
$result = array_diff($matriz, $cursadas);
print_r($result);

 // array_diff       Returns an array containing all the entries from array1  that are not present in any of the other arrays.
// array_intersect Returns an array containing all of the values in array1  whose values exist in all of the parameters.
*/

if (count($disciplinas_nao_cursadas) == 0) {
  $fl_integralizado = TRUE;
}
elseif (count($disciplinas_cursadas_fora_da_matriz) > 0) {

  // DISCIPLINAS REFERENTES AS DISCIPLINAS EQUIVALENTES CURSADAS FORA DA MATRIZ, CASO EXISTA ALGUMA
  $sql_disciplinas_equivalentes = "SELECT
                                          ref_disciplina
                                      FROM
                                          disciplinas_equivalentes
                                      WHERE
                                          ref_curso = $curso_id AND
                                          ref_disciplina_equivalente IN (". implode(",", $disciplinas_cursadas_fora_da_matriz) .");";
        
  $disciplinas_equivalentes_cursadas = (array) $conn->get_col($sql_disciplinas_equivalentes);

  $disciplinas_nao_cursadas_como_equivalentes = array_diff($disciplinas_nao_cursadas, $disciplinas_equivalentes_cursadas);

  // array_diff       Returns an array containing all the entries from array1  that are not present in any of the other arrays.
  // array_intersect Returns an array containing all of the values in array1  whose values exist in all of the parameters.
        
  if (count($disciplinas_nao_cursadas_como_equivalentes) == 0) {
    $fl_integralizado = TRUE;
  }
  elseif (count($disciplinas_equivalentes_cursadas) > 0) {
    //$disciplinas_nao_cursadas = array_diff($disciplinas_nao_cursadas, $disciplinas_equivalentes_cursadas);
    $disciplinas_nao_cursadas =  (array) $disciplinas_nao_cursadas_como_equivalentes;//$disciplinas_equivalentes_cursadas;
  }
}

$disciplinas_nao_integralizadas = array();

if (count($disciplinas_nao_cursadas) > 0) {

  $sql_disciplinas_nao_integralizadas = " SELECT
                                          d.id || ' - ' || descricao_disciplina AS disciplina,
                                          d.carga_horaria, c.semestre_curso,
                                          curriculo_mco AS curriculo
                                       FROM
                                          disciplinas d, cursos_disciplinas c
                                       WHERE
                                          d.id = c.ref_disciplina AND
                                          ref_curso = $curso_id AND
                                          c.ref_disciplina IN (". implode(",", $disciplinas_nao_cursadas) .")
                                       ORDER BY
                                              semestre_curso;";


  $disciplinas_nao_integralizadas = $conn->get_all($sql_disciplinas_nao_integralizadas);

}


?>
    <br />
    <div id="integralizou">
      <h3>Disciplinas n&atilde;o integralizadas</h3>
      <?php
          if ($fl_integralizado) :
      ?>
      <span style="color: green; font-size: 1.1em;font-style: italic;">
        Este curso / contrato foi totalmente integralizado pelo aluno.
      </span>
      <br />
      <?php
          else :
      ?>
            <table cellpadding="0" cellspacing="0" class="relato">
            <tr bgcolor="#666666">
              <th><div align="center"><font color="#FFFFFF"><b>Disciplina</b></font></div></th>
              <th><div align="center"><font color="#FFFFFF"><b>Carga hor&aacute;ria</b></font></div></th>
              <th><div align="center"><font color="#FFFFFF"><b>Per&iacute;odo no curso</b></font></div></th>
              <th><div align="center"><font color="#FFFFFF"><b>Curr&iacute;culo</b></font></div></th>
            </tr>
      <?php

            $carga_nao_integralizada = 0;
            foreach ($disciplinas_nao_integralizadas as $disc) :

                $nome_disciplina  = $disc['disciplina'];
                $carga_prevista   = $disc['carga_horaria'];
                $semestre_curso   = $disc['semestre_curso'];
                $curriculo        = $curriculos[$disc['curriculo']];

                $carga_nao_integralizada += ($disc['curriculo'] == 'M') ? $carga_prevista : 0;

	
                $st = ($st == '#F3F3F3') ? '#FFFFFF' : '#F3F3F3';
      ?>
	
              <tr bgcolor="<?=$st?>">
                <td>&nbsp;&nbsp;<?=$nome_disciplina?></td>
                <td align="center"><?=$carga_prevista?></td>
                <td align="center"><?=$semestre_curso?></td>
                <td align="center"><?=$curriculo?></td>
              </tr>
      <?php
            endforeach;
       ?>
              </table>
            <span style="font-size: 0.7em;">
              * Carga hor&aacute;ria n&atilde;o integralizada no curr&iacute;culo m&iacute;nimo: <strong><?=$carga_nao_integralizada?></strong>
            </span>            
    <?php
        endif; 
     ?>

    <br /><br />
    <span style="font-size: 0.85em;font-weight: bold;">
      Observa&ccedil;&otilde;es sobre as disciplinas n&atilde;o integralizadas:
    </span>
    <br />
    <span style="color: teal; font-size: 0.8em;font-style: italic;">
     * As informa&ccedil;&otilde;es acima estam de acordo com os lan&ccedil;amentos do Sistema Acad&ecirc;mico<br />
     * Somente as disciplinas de di&aacute;rios <strong>finalizados</strong> s&atilde;o consideradas para aprova&ccedil;&atilde;o, ou seja di&aacute;rios com situa&ccedil;&otilde;es diferentes s&atilde;o disciplinas n&atilde;o integralizadas<br />
     * Disciplinas <strong>equivalentes</strong> cursadas s&atilde;o consideradas somente quando matriculadas especificamente para o curso / contrato listado<br />
    </span>
    <br /><br />
    </div>
