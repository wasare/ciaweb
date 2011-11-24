CREATE OR REPLACE FUNCTION get_num_matriculados(integer) RETURNS bigint
    AS $_$select count(*) 
        from matricula 
        where 
          ref_disciplina_ofer = $1 and 
          dt_cancelamento is null and
          ref_motivo_matricula = 0; $_$
    LANGUAGE sql;
