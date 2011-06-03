ALTER TABLE disciplinas_ofer ADD CONSTRAINT ref_disciplina_unq UNIQUE (ref_disciplina, turma);

ALTER TABLE pessoas ADD CONSTRAINT cod_cpf_cgc_unq UNIQUE (cod_cpf_cgc);