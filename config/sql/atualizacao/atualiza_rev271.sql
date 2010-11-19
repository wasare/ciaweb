
CREATE TABLE desempenho_docente_levantamento (
ref_periodo character varying(10) NOT NULL ,
descricao text DEFAULT ''::character varying NOT NULL,
nota_maxima double precision DEFAULT 0 NOT NULL NOT NULL 
);

ALTER TABLE desempenho_docente_levantamento ADD CONSTRAINT desempenho_docente_levantamento_ref_periodo_pkey PRIMARY KEY (ref_periodo);

ALTER TABLE ONLY desempenho_docente_levantamento
    ADD CONSTRAINT desempenho_docente_levantamento_ref_periodo_fkey FOREIGN KEY (ref_periodo) REFERENCES periodos(id) MATCH FULL;

CREATE TABLE desempenho_docente_criterio (
criterio_id serial,
descricao character varying(100) NOT NULL 
);

ALTER TABLE desempenho_docente_criterio ADD CONSTRAINT desempenho_docente_criterio_criterio_id_pkey PRIMARY KEY (criterio_id);



CREATE TABLE desempenho_docente_nota (
ref_disciplina_ofer integer NOT NULL ,
ref_professor integer NOT NULL ,
ref_criterio integer NOT NULL ,
ref_periodo character varying(10) NOT NULL ,
nota_media double precision DEFAULT 0 NOT NULL NOT NULL 
);

ALTER TABLE desempenho_docente_nota ADD CONSTRAINT desempenho_docente_nota_pkey PRIMARY KEY (ref_disciplina_ofer, ref_professor, ref_criterio);

ALTER TABLE ONLY desempenho_docente_nota
    ADD CONSTRAINT desempenho_docente_nota_ref_disciplina_ofer_fkey FOREIGN KEY (ref_disciplina_ofer) REFERENCES disciplinas_ofer(id) MATCH FULL;

ALTER TABLE ONLY desempenho_docente_nota
    ADD CONSTRAINT desempenho_docente_nota_ref_professor_fkey FOREIGN KEY (ref_professor) REFERENCES professores(ref_professor) MATCH FULL;

ALTER TABLE ONLY desempenho_docente_nota
    ADD CONSTRAINT desempenho_docente_nota_ref_criterio_fkey FOREIGN KEY (ref_criterio) REFERENCES desempenho_docente_criterio(criterio_id) MATCH FULL;

ALTER TABLE ONLY desempenho_docente_nota
    ADD CONSTRAINT desempenho_docente_nota_ref_periodo_fkey FOREIGN KEY (ref_periodo) REFERENCES desempenho_docente_levantamento(ref_periodo) MATCH FULL;



INSERT INTO desempenho_docente_criterio VALUES (1, 'Domínio do conteúdo');
INSERT INTO desempenho_docente_criterio VALUES (2, 'Dinamismo');
INSERT INTO desempenho_docente_criterio VALUES (3, 'Esclarecimento das dúvidas');
INSERT INTO desempenho_docente_criterio VALUES (4, 'Controle sobre a turma');
INSERT INTO desempenho_docente_criterio VALUES (5, 'Assiduidade e pontualidade');
INSERT INTO desempenho_docente_criterio VALUES (6, 'Retorno comentado das provas');

