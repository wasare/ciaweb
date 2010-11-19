ALTER TABLE ONLY cursos_disciplinas
    ADD CONSTRAINT cursos_disciplinas_cursos_fkey FOREIGN KEY (ref_curso) REFERENCES cursos(id) MATCH FULL;

ALTER TABLE ONLY cursos_disciplinas
    ADD CONSTRAINT cursos_disciplinas_campus_fkey FOREIGN KEY (ref_campus) REFERENCES campus(id) MATCH FULL;

ALTER TABLE ONLY cursos_disciplinas
    ADD CONSTRAINT cursos_disciplinas_disciplinas_fkey FOREIGN KEY (ref_disciplina) REFERENCES disciplinas(id) MATCH FULL;


