GRANT SELECT ON TABLE diario_seq_faltas TO "aluno";
GRANT SELECT ON TABLE tipos_curso TO "aluno";

ALTER TABLE disciplinas_ofer RENAME COLUMN fl_digitada TO fl_finalizada;
ALTER TABLE disciplinas_ofer RENAME COLUMN fl_concluida TO fl_digitada;

ALTER TABLE tipos_curso ADD COLUMN quantidade_notas_diario smallint;

UPDATE tipos_curso SET quantidade_notas_diario = 6;


