ALTER TABLE contratos ADD COLUMN prontuario character varying(20);

ALTER TABLE ONLY contratos
    ADD CONSTRAINT contratos_ref_pessoa_fkey FOREIGN KEY (ref_pessoa) REFERENCES pessoas(id) MATCH FULL;
    
ALTER TABLE ONLY contratos
    ADD CONSTRAINT contratos_ref_campus_fkey FOREIGN KEY (ref_campus) REFERENCES campus(id) MATCH FULL;

ALTER TABLE ONLY contratos
    ADD CONSTRAINT contratos_ref_curso_fkey FOREIGN KEY (ref_curso) REFERENCES cursos(id) MATCH FULL;
    
ALTER TABLE ONLY contratos
    ADD CONSTRAINT contratos_prontuario_ref_pessoa_ref_campus_fkey FOREIGN KEY (prontuario,ref_pessoa,ref_campus) REFERENCES pessoa_prontuario_campus(prontuario,ref_pessoa,ref_campus) MATCH SIMPLE;