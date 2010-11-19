CREATE UNIQUE INDEX cidade_pkey ON cidade USING btree (id);


CREATE OR REPLACE FUNCTION get_cidade(integer) RETURNS character varying
    AS $_$select nome from cidade where id = $1; $_$
    LANGUAGE sql;


CREATE OR REPLACE FUNCTION professor_disciplina_ofer_todos(integer) RETURNS character varying
    AS $_$DECLARE
	registropessoas RECORD;
    professores varchar := '';
BEGIN
	FOR registropessoas IN  
		SELECT SPLIT_PART( (pessoa_nome(B.ref_professor)::varchar), ' ', 1) || ' ' || 
			SPLIT_PART( (pessoa_nome(B.ref_professor)::varchar), ' ', 2) as nome
		FROM disciplinas_ofer_compl A, disciplinas_ofer_prof B
		WHERE A.ref_disciplina_ofer = B.ref_disciplina_ofer and
			A.id = B.ref_disciplina_compl and
			B.ref_disciplina_ofer = $1 ORDER BY nome, A.dia_semana LOOP
		professores := professores || ' / ' || registropessoas.nome;
	END LOOP;
        
        BEGIN
            IF (trim(substr(professores, 3, length(professores))) is NULL ) THEN
              RETURN 'sem professor';
            ELSE
              RETURN trim(substr(professores, 3, length(professores)));
            END IF;
        END;


END;
$_$
    LANGUAGE plpgsql;

