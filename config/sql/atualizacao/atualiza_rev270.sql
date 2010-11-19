ALTER TABLE disciplinas_ofer_compl ADD CONSTRAINT ref_disciplina_ofer_unq UNIQUE (ref_disciplina_ofer);

ALTER TABLE ONLY disciplinas_ofer_compl
    ADD CONSTRAINT disciplinas_ofer_compl_ref_disciplina_ofer_fkey FOREIGN KEY (ref_disciplina_ofer) REFERENCES disciplinas_ofer(id) MATCH FULL;

ALTER TABLE disciplinas_ofer_compl ADD CONSTRAINT disciplinas_ofer_compl_pkey PRIMARY KEY (id);

ALTER TABLE professores ADD CONSTRAINT ref_professor_pkey PRIMARY KEY (ref_professor);

ALTER TABLE ONLY disciplinas_ofer_prof
    ADD CONSTRAINT ref_professor_fkey FOREIGN KEY (ref_professor) REFERENCES professores(ref_professor) MATCH FULL;

ALTER TABLE ONLY disciplinas_ofer_prof
    ADD CONSTRAINT disciplinas_ofer_compl_disciplinas_ofer_prof_fkey FOREIGN KEY (ref_disciplina_compl) REFERENCES disciplinas_ofer_compl(id) MATCH FULL;

DELETE FROM disciplinas_ofer_prof WHERE ref_professor IS NULL;

ALTER TABLE disciplinas_ofer_prof ALTER COLUMN ref_professor SET NOT NULL;
