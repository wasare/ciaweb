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

