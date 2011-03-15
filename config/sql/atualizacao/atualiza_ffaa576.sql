CREATE TABLE pessoa_prontuario_campus (
    ref_pessoa integer,
    prontuario character varying(20),
    ref_campus integer
);

ALTER TABLE pessoa_prontuario_campus ADD CONSTRAINT pessoa_prontuario_campus_pkey PRIMARY KEY (ref_pessoa, prontuario, ref_campus);


ALTER TABLE ONLY pessoa_prontuario_campus
    ADD CONSTRAINT pessoa_prontuario_campus_ref_pessoa_fkey FOREIGN KEY (ref_pessoa) REFERENCES pessoas(id) MATCH FULL;

ALTER TABLE ONLY pessoa_prontuario_campus
    ADD CONSTRAINT pessoa_prontuario_campus_ref_campus_fkey FOREIGN KEY (ref_campus) REFERENCES campus(id) MATCH FULL;


ALTER TABLE disciplinas ADD COLUMN abreviatura character varying(20);
ALTER TABLE disciplinas ALTER COLUMN abreviatura SET NOT NULL;
ALTER TABLE disciplinas ADD CONSTRAINT abreviatura_unq UNIQUE (abreviatura);

UPDATE url SET url = '/app/sagu/academico/curso_ins.php'  WHERE url_id = 8;
UPDATE url SET url = '/app/sagu/academico/post/cursos_exclui.php' WHERE url_id = 10;
UPDATE url SET url = '/app/sagu/academico/post/confirm_curso_ins.php' WHERE url_id = 11;
UPDATE url SET url = '/app/sagu/academico/post/curso_altera.php' WHERE url_id = 12;
UPDATE url SET url = '/app/sagu/academico/post/curso_ins.php' WHERE url_id = 13;
UPDATE url SET url = '/app/sagu/academico/disciplinas.php' WHERE url_id = 14;
UPDATE url SET url = '/app/sagu/academico/post/disciplinas.php' WHERE url_id = 15;
UPDATE url SET url = '/app/sagu/academico/post/disciplinas_altera.php' WHERE url_id = 16;
UPDATE url SET url = '/app/sagu/academico/post/disciplinas_exclui.php' WHERE url_id = 17;
UPDATE url SET url = '/app/sagu/academico/cursos_disciplinas.php' WHERE url_id = 18;
UPDATE url SET url = '/app/sagu/academico/post/cursos_disciplinas.php' WHERE url_id = 19;
UPDATE url SET url = '/app/sagu/academico/post/cursos_disciplinas_edita.php' WHERE url_id = 20;
UPDATE url SET url = '/app/sagu/academico/post/cursos_disciplinas_exclui.php' WHERE url_id = 21;
UPDATE url SET url = '/app/sagu/academico/inclui_pre_requisito.php' WHERE url_id = 22;
UPDATE url SET url = '/app/sagu/academico/post/inclui_pre_requisito.php' WHERE url_id = 23;
UPDATE url SET url = '/app/sagu/academico/post/edita_pre_requisito.php' WHERE url_id = 24;
UPDATE url SET url = '/app/sagu/academico/post/pre_requisito_exclui.php' WHERE url_id = 25;
UPDATE url SET url = '/app/sagu/academico/inclui_disciplinas_equivalentes.php' WHERE url_id = 26;
UPDATE url SET url = '/app/sagu/academico/post/altera_disciplinas_equivalentes.php' WHERE url_id = 27;
UPDATE url SET url = '/app/sagu/academico/post/disciplinas_equivalentes_exclui.php' WHERE url_id = 28;
UPDATE url SET url = '/app/sagu/academico/post/inclui_disciplinas_equivalentes.php' WHERE url_id  = 29;

ALTER TABLE periodos RENAME COLUMN media TO nota_maxima;

