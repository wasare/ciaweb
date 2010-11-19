--
-- PostgreSQL database dump
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

--
-- Name: dblink_pkey_results; Type: TYPE; Schema: public; 
--

CREATE TYPE dblink_pkey_results AS (
	"position" integer,
	colname text
);



--
-- Name: campus_disciplina_ofer(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION campus_disciplina_ofer(integer) RETURNS integer
    AS $_$select cast(ref_campus as integer) from disciplinas_ofer where id = $1$_$
    LANGUAGE sql;



--
-- Name: check_matricula_pessoa(integer, integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION check_matricula_pessoa(integer, integer) RETURNS integer
    AS $_$select 1 from matricula where ref_disciplina_ofer = $1 and ref_pessoa = $2 and dt_cancelamento is null$_$
    LANGUAGE sql;



--
-- Name: cria_ra(); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION cria_ra() RETURNS "trigger"
    AS $$begin
 UPDATE pessoas SET ra_cnec = NEW.id
 WHERE NEW.id = id;
 return NEW;
end;
$$
    LANGUAGE plpgsql;



--
-- Name: curso_desc(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION curso_desc(integer) RETURNS character varying
    AS $_$select descricao from cursos where id = $1;$_$
    LANGUAGE sql;



--
-- Name: curso_disciplina_ofer(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION curso_disciplina_ofer(integer) RETURNS integer
    AS $_$select ref_curso from disciplinas_ofer where id = $1$_$
    LANGUAGE sql;



--
-- Name: descricao_departamento(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION descricao_departamento(integer) RETURNS character varying
    AS $_$select descricao from departamentos where id = $1$_$
    LANGUAGE sql;



--
-- Name: descricao_disciplina(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION descricao_disciplina(integer) RETURNS text
    AS $_$select descricao_extenso from disciplinas where id = $1$_$
    LANGUAGE sql;



--
-- Name: descricao_disciplina_sucinto(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION descricao_disciplina_sucinto(integer) RETURNS character varying
    AS $_$select descricao_disciplina from disciplinas where id = $1$_$
    LANGUAGE sql;



--
-- Name: descricao_periodo(character varying); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION descricao_periodo(character varying) RETURNS character varying
    AS $_$select descricao from periodos where id = $1$_$
    LANGUAGE sql;



--
-- Name: dia_disciplina_ofer_todos(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION dia_disciplina_ofer_todos(integer) RETURNS character varying
    AS $_$
DECLARE
    row disciplinas_ofer_compl%ROWTYPE;
    dias varchar := '';
BEGIN
	FOR row IN SELECT * FROM disciplinas_ofer_compl where ref_disciplina_ofer = $1 ORDER BY dia_semana LOOP
            dias := dias ||'/' || row.dia_semana;
	END LOOP;
	RETURN trim(substr(dias, 2, length(dias)));
END;
$_$
    LANGUAGE plpgsql;



--
-- Name: get_area(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_area(integer) RETURNS character varying
    AS $_$select area from areas_ensino where id
= $1$_$
    LANGUAGE sql;



--
-- Name: get_campus(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_campus(integer) RETURNS character varying
    AS $_$select
nome_campus from campus where id = $1$_$
    LANGUAGE sql;



--
-- Name: get_carga_horaria(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_carga_horaria(integer) RETURNS double precision
    AS $_$select carga_horaria from disciplinas
where id = $1$_$
    LANGUAGE sql;



--
-- Name: get_carga_horaria_realizada(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_carga_horaria_realizada(integer) RETURNS integer
    AS $_$DECLARE
    row RECORD;
    carga_horaria integer := 0;
BEGIN
        FOR row IN 
            SELECT 
                CASE 
                    WHEN SUM(CAST(flag AS INTEGER)) IS NULL THEN 0 
                    ELSE SUM(CAST(flag AS INTEGER)) END AS carga 
                FROM 
                    diario_seq_faltas 
                WHERE   
                    ref_disciplina_ofer = $1 LOOP
            carga_horaria := row.carga;
        END LOOP;
        RETURN carga_horaria;
END;
$_$
    LANGUAGE plpgsql;



--
-- Name: get_ccusto(integer, integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_ccusto(integer, integer) RETURNS character varying
    AS $_$select cod_ccusto FROM rel_curso_cc WHERE ref_curso = $1 AND ref_campus = $2$_$
    LANGUAGE sql;



--
-- Name: get_cidade(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_cidade(integer) RETURNS character varying
    AS $_$select nome from cidade where id = $1; $_$
    LANGUAGE sql;



--
-- Name: get_color_campus(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_color_campus(integer) RETURNS character varying
    AS $_$select color from campus where id = $1$_$
    LANGUAGE sql;



--
-- Name: get_complemento_ofer(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_complemento_ofer(integer) RETURNS text
    AS $_$select trim(conteudo) from disciplinas_ofer where id = $1$_$
    LANGUAGE sql;



--
-- Name: get_creditos(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_creditos(integer) RETURNS numeric
    AS $_$select num_creditos from disciplinas where id = $1$_$
    LANGUAGE sql;



--
-- Name: get_curriculo_mco(integer, integer, integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_curriculo_mco(integer, integer, integer) RETURNS character
    AS $_$select curriculo_mco from cursos_disciplinas where ref_curso = $1 and ref_campus = $2 and ref_disciplina = $3$_$
    LANGUAGE sql;



--
-- Name: get_curso_abrv(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_curso_abrv(integer) RETURNS character varying
    AS $_$select abreviatura from cursos where id = $1$_$
    LANGUAGE sql;



--
-- Name: get_departamento(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_departamento(integer) RETURNS character varying
    AS $_$select A.descricao from departamentos A, disciplinas B where B.id= $1 and B.ref_departamento=A.id$_$
    LANGUAGE sql;



--
-- Name: get_dia(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_dia(integer) RETURNS character
    AS $_$select dia_semana from disciplinas_ofer_compl where ref_disciplina_ofer = $1$_$
    LANGUAGE sql;



--
-- Name: get_dia_semana(character varying); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_dia_semana(character varying) RETURNS character varying
    AS $_$select case when strpos($1,'/') > 0 then trim(get_dia_semana_(substr($1,0,strpos($1,'/'))) || '/' || get_dia_semana(substr($1,strpos($1,'/')+1))) else trim(get_dia_semana_($1)) end;$_$
    LANGUAGE sql;



--
-- Name: get_dia_semana_(character varying); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_dia_semana_(character varying) RETURNS character varying
    AS $_$select nome from dias where id = $1$_$
    LANGUAGE sql;



--
-- Name: get_dia_semana_abrv(character varying); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_dia_semana_abrv(character varying) RETURNS character varying
    AS $_$select case when strpos($1,'/') > 0 then trim(get_dia_semana_abrv_(substr($1,0,strpos($1,'/'))) || '/' || get_dia_semana_abrv(substr($1,strpos($1,'/')+1))) else trim(get_dia_semana_abrv_($1)) end;$_$
    LANGUAGE sql;



--
-- Name: get_dia_semana_abrv_(character varying); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_dia_semana_abrv_(character varying) RETURNS character varying
    AS $_$select abrv from dias where id = $1$_$
    LANGUAGE sql;



--
-- Name: get_disciplina_de_disciplina_of(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_disciplina_de_disciplina_of(integer) RETURNS integer
    AS $_$select ref_disciplina from disciplinas_ofer where id = $1$_$
    LANGUAGE sql;



--
-- Name: get_dt_inicio_aula(character varying); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_dt_inicio_aula(character varying) RETURNS date
    AS $_$select dt_inicio_aula from periodos where id = $1$_$
    LANGUAGE sql;



--
-- Name: get_motivo(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_motivo(integer) RETURNS character varying
    AS $_$select descricao from motivo where id = $1$_$
    LANGUAGE sql;



--
-- Name: get_num_matriculados(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_num_matriculados(integer) RETURNS bigint
    AS $_$select count(*) from matricula where ref_disciplina_ofer = $1 and dt_cancelamento is null$_$
    LANGUAGE sql;



--
-- Name: get_ref_professor(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_ref_professor(integer) RETURNS integer
    AS $_$select ref_professor FROM disciplinas_ofer_prof WHERE ref_disciplina_ofer = $1$_$
    LANGUAGE sql;



--
-- Name: get_status(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_status(integer) RETURNS character varying
    AS $_$select descricao from status_matricula where id= $1$_$
    LANGUAGE sql;



--
-- Name: get_status_disciplina(integer, integer, integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_status_disciplina(integer, integer, integer) RETURNS integer
    AS $_$select status from disciplinas_todos_alunos where ref_pessoa = $1 and ref_curso = $2 and ref_disciplina = $3$_$
    LANGUAGE sql;



--
-- Name: get_tipo_curso(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_tipo_curso(integer) RETURNS integer
    AS $_$select ref_tipo_curso from cursos where id = $1;$_$
    LANGUAGE sql;



--
-- Name: get_turno(character varying); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_turno(character varying) RETURNS character varying
    AS $_$select case when strpos($1,'/') > 0 then trim(get_turno_(substr($1,0,strpos($1,'/'))) || '/' || get_turno(substr($1,strpos($1,'/')+1))) else trim(get_turno_($1)) end;$_$
    LANGUAGE sql;



--
-- Name: get_turno_(character varying); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION get_turno_(character varying) RETURNS character varying
    AS $_$select nome from turno where id = $1$_$
    LANGUAGE sql;



--
-- Name: instituicao_nome(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION instituicao_nome(integer) RETURNS character varying
    AS $_$select nome_atual from instituicoes where id = $1$_$
    LANGUAGE sql;



--
-- Name: is_ouvinte(integer, integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION is_ouvinte(integer, integer) RETURNS character
    AS $_$select fl_ouvinte from contratos where ref_pessoa = $1 and ref_curso = $2 order by id desc$_$
    LANGUAGE sql;



--
-- Name: nota_distribuida(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION nota_distribuida(integer) RETURNS integer
    AS $_$select cast(sum(nota_distribuida) as integer) from diario_formulas where grupo ilike '%-' || $1$_$
    LANGUAGE sql;



--
-- Name: num_alunos(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION num_alunos(integer) RETURNS integer
    AS $_$select num_alunos from disciplinas_ofer where id= $1$_$
    LANGUAGE sql;



--
-- Name: num_sala_disciplina_ofer_todos(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION num_sala_disciplina_ofer_todos(integer) RETURNS character varying
    AS $_$
DECLARE
	row disciplinas_ofer_compl%ROWTYPE;
    salas varchar := '';
BEGIN
	FOR row IN SELECT * FROM disciplinas_ofer_compl where ref_disciplina_ofer = $1 ORDER BY dia_semana LOOP
            salas := salas ||'/'|| (row.num_sala::varchar);
	END LOOP;
	RETURN trim(substr(salas, 2, length(salas)));
END;
$_$
    LANGUAGE plpgsql;



--
-- Name: periodo_disciplina_ofer(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION periodo_disciplina_ofer(integer) RETURNS character varying
    AS $_$select ref_periodo from disciplinas_ofer where id = $1$_$
    LANGUAGE sql;



--
-- Name: pessoa_idade(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION pessoa_idade(integer) RETURNS integer
    AS $_$select (date(now())-pessoa_dtnasc($1))/365; $_$
    LANGUAGE sql;



--
-- Name: pessoa_nome(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION pessoa_nome(integer) RETURNS character varying
    AS $_$select nome from pessoas where id = $1$_$
    LANGUAGE sql;



--
-- Name: professor_disciplina_ofer(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION professor_disciplina_ofer(integer) RETURNS character varying
    AS $_$select pessoa_nome(ref_professor) from disciplinas_ofer_prof where ref_disciplina_ofer = $1$_$
    LANGUAGE sql;



--
-- Name: professor_disciplina_ofer_todos(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION professor_disciplina_ofer_todos(integer) RETURNS character varying
    AS $_$DECLARE
	row pessoas%ROWTYPE;
    professores varchar := '';
BEGIN
	FOR row IN  
             SELECT * FROM 
	        pessoas A 
		    FULL OUTER JOIN 
	        disciplinas_ofer_prof B ON (A.id = B.ref_professor)
		    FULL OUTER JOIN 
	        disciplinas_ofer_compl C ON (B.ref_disciplina_compl = C.id)
	    WHERE  B.ref_disciplina_ofer = $1 AND A.id IS NOT NULL ORDER BY A.nome, C.dia_semana LOOP
            professores := professores || ' / ' || SPLIT_PART( (row.nome::varchar), ' ', 1) || ' ' || SPLIT_PART( (row.nome::varchar), ' ', 2);
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



--
-- Name: turno_disciplina_ofer_todos(integer); Type: FUNCTION; Schema: public; 
--

CREATE FUNCTION turno_disciplina_ofer_todos(integer) RETURNS character varying
    AS $_$
DECLARE
    row disciplinas_ofer_compl%ROWTYPE;
    turnos varchar := '';
    t varchar := '';
BEGIN
	FOR row IN SELECT * FROM disciplinas_ofer_compl where ref_disciplina_ofer = $1 ORDER BY dia_semana LOOP
            turnos := turnos ||'/' || (row.turno::varchar);
	END LOOP;
	RETURN trim(substr(turnos, 2, length(turnos)));
END;
$_$
    LANGUAGE plpgsql;



SET default_tablespace = '';

SET default_with_oids = true;

--
-- Name: pessoas; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE pessoas (
    id integer DEFAULT nextval(('seq_pessoas'::text)::regclass) NOT NULL,
    identificacao character(1) DEFAULT ''::bpchar,
    titulo_academico character varying(50) DEFAULT ''::character varying,
    nome character varying(80) DEFAULT ''::character varying,
    rua character varying(80) DEFAULT ''::character varying,
    complemento character varying(50) DEFAULT ''::character varying,
    bairro character varying(60) DEFAULT ''::character varying,
    cep character varying(9) DEFAULT ''::character varying,
    ref_cidade integer,
    fone_particular character varying(50) DEFAULT ''::character varying,
    fone_profissional character varying(50) DEFAULT ''::character varying,
    fone_celular character varying(50) DEFAULT ''::character varying,
    fone_recado character varying(50) DEFAULT ''::character varying,
    email character varying(80) DEFAULT ''::character varying,
    email_alt character varying(80) DEFAULT ''::character varying,
    estado_civil character varying(20) DEFAULT ''::character varying,
    dt_cadastro date DEFAULT date(now()),
    tipo_pessoa character(1) DEFAULT 'f'::bpchar,
    obs text,
    dt_nascimento date,
    sexo character(1) DEFAULT ''::bpchar,
    credo character varying(50),
    nome_fantasia character varying(100),
    cod_inscricao_estadual character varying(50),
    rg_numero character varying(20),
    rg_cidade integer,
    rg_data date,
    ref_filiacao integer,
    ref_cobranca integer DEFAULT 0,
    ref_assistmed integer,
    ref_naturalidade integer,
    ref_nacionalidade integer,
    ref_segurado integer,
    cod_cpf_cgc character varying(18),
    titulo_eleitor character varying(50),
    conta_laboratorio character varying(50),
    conta_provedor character varying(50),
    regc_livro character varying(20),
    regc_folha character varying(20),
    regc_local character varying(20),
    regc_nasc_casam character varying(20),
    ano_1g smallint,
    cidade_1g integer,
    ref_curso_1g integer,
    escola_1g integer,
    ano_2g smallint,
    cidade_2g integer,
    ref_curso_2g integer,
    escola_2g integer,
    graduacao integer,
    cod_passivo character varying(50),
    senha integer DEFAULT 0,
    fl_dbfolha boolean,
    ref_pessoa_folha integer,
    fl_documentos boolean,
    fl_documentos_fora boolean,
    fl_quitacao_eleitoral integer,
    fl_segurado boolean DEFAULT false,
    nome2 character varying(100),
    fl_cartao boolean,
    deficiencia integer DEFAULT 0,
    cidade character varying,
    nacionalidade character varying,
    in_sagu boolean,
    cod_externo character varying(10),
    deficiencia_desc text,
    dt_responsavel date,
    rg_orgao character varying(15),
    placa_carro character varying(40),
    fl_dados_pessoais boolean DEFAULT true,
    seguro_meses text,
    ra_cnec character varying(8),
    tipo_sangue character varying(4),
    CONSTRAINT pessoas_tipo_pessoa CHECK (((tipo_pessoa = 'f'::bpchar) OR (tipo_pessoa = 'j'::bpchar)))
);



SET default_with_oids = false;

--
-- Name: acesso_aluno; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE acesso_aluno (
    ref_pessoa integer NOT NULL,
    senha character varying(40) NOT NULL
);



SET default_with_oids = true;

--
-- Name: contratos; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE contratos (
    id integer DEFAULT nextval(('seq_contratos'::text)::regclass) NOT NULL,
    ref_campus integer NOT NULL,
    ref_pessoa integer NOT NULL,
    ref_curso integer,
    dt_ativacao date NOT NULL,
    ref_motivo_ativacao integer NOT NULL,
    ref_motivo_desativacao integer DEFAULT 0,
    dt_desativacao date,
    obs text,
    desconto double precision DEFAULT 0,
    dt_formatura date,
    dt_provao date,
    dt_diploma date,
    dt_apostila date,
    ref_last_periodo character varying(10),
    cod_status integer,
    fl_ouvinte character(1),
    fl_formando character(1),
    percentual_pago double precision DEFAULT 0,
    dt_conclusao date,
    ref_motivo_entrada integer,
    id_vestibular integer,
    ref_periodo_formatura character varying(10),
    fl_debito_automatico boolean DEFAULT false,
    num_conta character varying,
    dv_conta character varying(10),
    agencia character varying,
    dv_agencia character varying(10),
    ref_banco integer,
    ref_motivo_inicial integer,
    num_contrato character varying(20),
    dia_vencimento integer DEFAULT 10,
    obs_desativacao text,
    semestre character varying(5) DEFAULT '0'::character varying,
    turma text,
    ref_periodo_turma character varying(10) NOT NULL
);



--
-- Name: matricula; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE matricula (
    id integer DEFAULT nextval(('seq_matricula'::text)::regclass) NOT NULL,
    ref_contrato integer NOT NULL,
    ref_pessoa integer NOT NULL,
    ref_campus integer,
    ref_curso integer NOT NULL,
    ref_periodo character varying(10) NOT NULL,
    ref_disciplina integer,
    ref_curso_subst integer,
    ref_disciplina_subst integer,
    ref_disciplina_ofer integer NOT NULL,
    nota double precision DEFAULT 0 NOT NULL,
    nota_exame double precision DEFAULT 0 NOT NULL,
    nota_final double precision DEFAULT 0 NOT NULL,
    conceito character varying(5) DEFAULT ''::character varying,
    conceito_exame character varying(5) DEFAULT ''::character varying,
    conceito_final character varying(5) DEFAULT ''::character varying,
    num_faltas double precision DEFAULT 0,
    obs_aproveitamento text DEFAULT ''::text,
    ref_motivo_matricula integer DEFAULT 0,
    dt_matricula date,
    hora_matricula timestamp with time zone,
    fl_liberado character(1) DEFAULT ''::bpchar,
    ref_liberacao_ed_fisica integer DEFAULT 0,
    ref_motivo_cancelamento integer DEFAULT 0,
    dt_cancelamento date,
    carga_horaria_aprov double precision DEFAULT 0,
    fl_exibe_displ_hist character(1),
    creditos_aprov double precision DEFAULT 0,
    ref_instituicao integer DEFAULT 0,
    nota1 double precision DEFAULT 0,
    nota2 double precision DEFAULT 0,
    nota3 double precision DEFAULT 0,
    num_faltas1 integer,
    num_faltas2 integer,
    num_faltas3 integer,
    obs1 text,
    obs2 text,
    obs3 text,
    obs_final text,
    notap1 double precision,
    notap2 double precision,
    num_faltasp1 integer,
    num_faltasp2 integer,
    obsp1 text,
    obsp2 text,
    nota4 double precision,
    num_faltas4 integer,
    complemento_disc text,
    turma character(1) DEFAULT 'A'::bpchar,
    status_disciplina boolean DEFAULT false,
    fl_internet boolean DEFAULT false,
    ip inet,
    processo text DEFAULT ''::text,
    fl_nota1nc boolean DEFAULT false,
    fl_nota2nc boolean DEFAULT false,
    fl_nota_examenc boolean DEFAULT false,
    ordem_chamada character varying(3)
);



--
-- Name: alunos_curso_periodo; Type: VIEW; Schema: public; 
--

CREATE VIEW alunos_curso_periodo AS
    SELECT DISTINCT a.ref_pessoa, b.ref_curso, b.ref_campus, a.ref_periodo, b.dt_desativacao FROM matricula a, contratos b WHERE ((a.ref_contrato = b.id) AND (a.dt_cancelamento IS NULL)) ORDER BY a.ref_pessoa, b.ref_curso, b.ref_campus, a.ref_periodo, b.dt_desativacao;



--
-- Name: areas_ensino; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE areas_ensino (
    id integer DEFAULT nextval(('seq_areas_ensino'::text)::regclass) NOT NULL,
    area character varying(100)
);



SET default_with_oids = false;

--
-- Name: avisos; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE avisos (
    id serial NOT NULL,
    descricao text NOT NULL,
    data date
);



SET default_with_oids = true;

--
-- Name: campus; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE campus (
    id integer NOT NULL,
    ref_empresa integer,
    nome_campus character varying(50),
    cidade_campus character varying(50),
    color character varying(20),
    ref_campus_sede integer
);



SET default_with_oids = false;

--
-- Name: cargo; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE cargo (
    id character varying(12) NOT NULL,
    descricao character varying(120) NOT NULL,
    descricao_breve character varying(80)
);



SET default_with_oids = true;

--
-- Name: carimbos; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE carimbos (
    id integer DEFAULT nextval(('seq_carimbos'::text)::regclass) NOT NULL,
    nome character varying(80),
    texto character varying(150),
    ref_setor integer
);



--
-- Name: cidade_id_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE cidade_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: cidade; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE cidade (
    id integer DEFAULT nextval('cidade_id_seq'::regclass) NOT NULL,
    nome character varying(80) NOT NULL,
    cep character varying(9) DEFAULT ''::character varying,
    ref_pais integer,
    ref_estado character(2) DEFAULT ''::bpchar,
    praca character varying(6),
    praca_old character varying(6)
);



--
-- Name: configuracao_empresa; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE configuracao_empresa (
    id integer DEFAULT nextval(('seq_configuracao_empresa'::text)::regclass) NOT NULL,
    razao_social character varying(200),
    sigla character varying(30),
    logotipo character varying(25),
    rua character varying(50) DEFAULT ''::character varying,
    complemento character varying(50) DEFAULT ''::character varying,
    bairro character varying(40) DEFAULT ''::character varying,
    cep character varying(9) DEFAULT ''::character varying,
    ref_cidade integer DEFAULT 0,
    cgc character varying(14)
);



--
-- Name: coordenador; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE coordenador (
    ref_professor integer,
    ref_curso integer,
    ref_campus integer
);



--
-- Name: cursos; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE cursos (
    id integer DEFAULT nextval(('seq_cursos_id'::text)::regclass) NOT NULL,
    descricao character varying(280),
    abreviatura character varying(50),
    total_creditos double precision,
    total_semestres integer,
    grau_academico character(18),
    exigencias text,
    agrupo_curso integer DEFAULT 0,
    ccusto character varying(9),
    ref_end_of_conta character varying(20),
    ref_ini_of_conta character varying(10),
    reconhecimento text,
    autorizacao text,
    turno character varying(1) DEFAULT 'N'::character varying,
    ref_tipo_curso integer DEFAULT 1,
    historico text,
    sequencia integer DEFAULT 0,
    total_carga_horaria double precision,
    sigla character varying(10),
    ref_area integer DEFAULT 0
);



--
-- Name: cursos_disciplinas; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE cursos_disciplinas (
    ref_curso integer NOT NULL,
    ref_campus integer NOT NULL,
    ref_disciplina integer NOT NULL,
    semestre_curso integer,
    curriculo_mco character(1),
    equivalencia_disciplina integer DEFAULT 0,
    cursa_outra_disciplina character(1) DEFAULT ''::bpchar,
    esconde_historico character(1),
    dt_inicio_curriculo date,
    dt_final_curriculo date,
    curso_substituido integer DEFAULT 0,
    disciplina_substituida integer DEFAULT 0,
    pre_requisito_hora integer,
    exibe_historico character(1),
    fl_soma_curriculo boolean DEFAULT true,
    ref_area integer
);



--
-- Name: cursos_externos; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE cursos_externos (
    id integer DEFAULT nextval(('seq_cursos_externos'::text)::regclass) NOT NULL,
    nome character varying(100),
    sucinto character varying(40),
    obs text
);



--
-- Name: departamentos; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE departamentos (
    id integer DEFAULT nextval(('seq_departamentos'::text)::regclass) NOT NULL,
    descricao character varying(50)
);



--
-- Name: diario_chamadas; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE diario_chamadas (
    id integer DEFAULT nextval(('"diario_chamadas_id_seq"'::text)::regclass) NOT NULL,
    data_chamada date NOT NULL,
    ref_professor integer NOT NULL,
    ref_periodo character(10) NOT NULL,
    ref_curso integer NOT NULL,
    ref_disciplina integer NOT NULL,
    encerramento integer,
    aula character(2) NOT NULL,
    abono character varying(1),
    ra_cnec character varying(15),
    ref_disciplina_ofer integer
);



--
-- Name: diario_chamadas_id_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE diario_chamadas_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



--
-- Name: diario_formulas; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE diario_formulas (
    id integer DEFAULT nextval(('"diario_formulas_id_seq"'::text)::regclass) NOT NULL,
    ref_prof integer,
    ref_periodo character varying(10),
    ref_disciplina integer,
    prova character varying(4),
    descricao character varying(60),
    grupo character varying(80),
    formula character varying(250),
    nota_distribuida double precision DEFAULT 0 NOT NULL
);



--
-- Name: diario_formulas_id_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE diario_formulas_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



--
-- Name: diario_log; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE diario_log (
    usuario character varying(40),
    data date,
    hora time without time zone,
    ip_acesso character varying(40),
    pagina_acesso text,
    status character varying(40),
    senha_acesso character varying(80)
);



--
-- Name: diario_notas; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE diario_notas (
    id integer DEFAULT nextval(('"diario_seq_faltas_id_seq"'::text)::regclass) NOT NULL,
    ra_cnec character varying(15),
    ref_diario_avaliacao integer,
    nota double precision,
    peso double precision,
    id_ref_pessoas integer,
    id_ref_periodos character varying(10),
    id_ref_curso integer,
    d_ref_disciplina_ofer integer,
    exame character varying(1),
    rel_diario_formulas_grupo character varying(40)
);



--
-- Name: diario_seq_faltas; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE diario_seq_faltas (
    id serial NOT NULL,
    id_prof integer NOT NULL,
    periodo character varying(10),
    curso integer,
    disciplina integer,
    dia date,
    conteudo text,
    flag character varying(2),
    ref_disciplina_ofer integer
);



--
-- Name: dias; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE dias (
    id character varying(2) NOT NULL,
    nome character varying(20),
    abrv character varying(3)
);



--
-- Name: disciplinas; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE disciplinas (
    id integer DEFAULT nextval(('seq_disciplina'::text)::regclass) NOT NULL,
    ref_grupo integer,
    descricao_disciplina character varying(100) DEFAULT ''::character varying,
    descricao_extenso text,
    num_creditos numeric(5,2),
    carga_horaria double precision,
    ref_departamento integer
);



--
-- Name: disciplinas_equivalentes; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE disciplinas_equivalentes (
    id integer DEFAULT nextval(('seq_disciplinas_equivalentes_id'::text)::regclass) NOT NULL,
    ref_disciplina integer NOT NULL,
    ref_disciplina_equivalente integer NOT NULL,
    ref_curso integer NOT NULL
);



--
-- Name: disciplinas_ofer; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE disciplinas_ofer (
    id integer DEFAULT nextval(('seq_disciplinas_ofer_id'::text)::regclass) NOT NULL,
    ref_campus integer DEFAULT 0,
    ref_curso integer DEFAULT 0,
    ref_periodo character varying(10) DEFAULT 0,
    ref_disciplina integer DEFAULT 0,
    num_alunos integer DEFAULT 0,
    fixar_num_sala character(1),
    is_cancelada character(1),
    conteudo character varying(25),
    num_matriculados integer,
    fl_finalizada boolean DEFAULT false,
    turma text,
    ref_periodo_turma character varying(10),
    fl_digitada boolean DEFAULT false
);



--
-- Name: disciplinas_ofer_compl; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE disciplinas_ofer_compl (
    id integer DEFAULT nextval(('seq_disciplinas_ofer_compl_id'::text)::regclass) NOT NULL,
    ref_disciplina_ofer integer,
    turno character(1) DEFAULT 0,
    desconto double precision,
    num_creditos_desconto numeric(5,2),
    observacao text,
    num_sala character(10) DEFAULT 1,
    ref_professor_aux integer,
    num_sala_aux character(10),
    turno_aux character(1) DEFAULT ''::bpchar,
    ref_horario integer,
    ref_horario_aux integer,
    dia_semana character varying(2) DEFAULT (-1),
    dia_semana_aux character varying(2) DEFAULT ''::character varying,
    dt_exame date,
    ref_regime integer
);



--
-- Name: disciplinas_ofer_prof; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE disciplinas_ofer_prof (
    id integer DEFAULT nextval(('seq_disciplinas_ofer_prof'::text)::regclass) NOT NULL,
    ref_disciplina_ofer integer NOT NULL,
    ref_disciplina_compl integer NOT NULL,
    ref_professor integer
);



--
-- Name: documentos; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE documentos (
    ref_pessoa integer NOT NULL,
    rg_num boolean DEFAULT false,
    cpf boolean DEFAULT false,
    hist_escolar boolean DEFAULT false,
    titulo_eleitor boolean DEFAULT false,
    quitacao_eleitoral boolean DEFAULT false,
    doc_militar boolean DEFAULT false,
    foto boolean DEFAULT false,
    hist_original boolean DEFAULT false,
    atestado_medico boolean DEFAULT false,
    diploma_autenticado boolean DEFAULT false,
    solteiro_emancipado boolean DEFAULT false,
    obs_documentos text,
    anotacoes text
);



--
-- Name: estado; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE estado (
    id character(2) NOT NULL,
    nome character varying(80) NOT NULL,
    ref_pais integer DEFAULT 0
);



--
-- Name: filiacao; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE filiacao (
    id integer DEFAULT nextval(('seq_filiacao'::text)::regclass) NOT NULL,
    pai_nome character varying(50),
    pai_fone character varying(50),
    pai_profissao character varying(50),
    pai_instrucao character varying(50),
    pai_loc_trabalho character varying(50),
    mae_nome character varying(50),
    mae_fone character varying(50),
    mae_profissao character varying(50),
    mae_instrucao character varying(50),
    mae_loc_trabalho character varying(50)
);



SET default_with_oids = false;

--
-- Name: funcionario; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE funcionario (
    ref_pessoa integer NOT NULL,
    siape character varying(12) NOT NULL,
    ref_cargo character varying(12) NOT NULL
);



SET default_with_oids = true;

--
-- Name: grupos_disciplinas; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE grupos_disciplinas (
    id integer DEFAULT nextval(('seq_grupos_disciplinas'::text)::regclass) NOT NULL,
    descricao character varying(100)
);



--
-- Name: horarios_id_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE horarios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



--
-- Name: instituicoes; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE instituicoes (
    id integer DEFAULT nextval(('seq_instituicoes_id'::text)::regclass) NOT NULL,
    nome character varying(100),
    sucinto character varying(50),
    nome_atual character varying(100)
);



--
-- Name: motivo; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE motivo (
    id integer DEFAULT nextval(('seq_motivo'::text)::regclass) NOT NULL,
    descricao character varying(100),
    ref_tipo_motivo integer
);



--
-- Name: pais_id_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE pais_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: pais; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE pais (
    id integer DEFAULT nextval('pais_id_seq'::regclass) NOT NULL,
    nome character varying(80) NOT NULL,
    nacionalidade character varying(20)
);



SET default_with_oids = false;

--
-- Name: papel; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE papel (
    papel_id serial NOT NULL,
    descricao character varying(150),
    nome character varying(45) NOT NULL
);



--
-- Name: papel_url; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE papel_url (
    ref_papel integer NOT NULL,
    ref_url integer NOT NULL
);



SET default_with_oids = true;

--
-- Name: periodos; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE periodos (
    id character varying(10) NOT NULL,
    ref_anterior character varying(10),
    ref_cobranca integer,
    ref_origem integer,
    ref_historico integer NOT NULL,
    ref_historico_bolsa integer,
    ref_historico_dce integer NOT NULL,
    descricao character varying(100),
    dt_inicial date,
    dt_final date,
    tx_dce_normal double precision,
    tx_dce_vest double precision,
    tx_acresc double precision,
    tx_cancel double precision,
    ref_status_vest integer NOT NULL,
    ref_local integer,
    ref_ocorrencia integer,
    ref_historico_cancel integer,
    ref_historico_acresc integer,
    tx_banco double precision,
    tipo integer,
    fl_livro_matricula character(1) DEFAULT '0'::bpchar,
    media_final double precision DEFAULT (5)::double precision,
    ref_historico_taxa integer,
    dt_inicio_aula date,
    media double precision DEFAULT (8)::double precision,
    label character varying(10) DEFAULT ''::character varying,
    dt_livro_matricula date,
    fl_gera_financeiro boolean DEFAULT true
);



SET default_with_oids = false;

--
-- Name: pessoas_fotos; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE pessoas_fotos (
    ref_pessoa integer NOT NULL,
    foto bytea NOT NULL
);



SET default_with_oids = true;

--
-- Name: pre_requisitos; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE pre_requisitos (
    id integer DEFAULT nextval(('seq_pre_requisitos'::text)::regclass) NOT NULL,
    ref_curso integer NOT NULL,
    ref_disciplina integer NOT NULL,
    ref_disciplina_pre integer NOT NULL,
    ref_area integer DEFAULT 0,
    horas_area integer,
    tipo character(1) DEFAULT 'P'::bpchar
);



--
-- Name: professores; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE professores (
    id integer DEFAULT nextval(('"professores_id_seq"'::text)::regclass) NOT NULL,
    ref_professor integer NOT NULL,
    ref_departamento integer,
    dt_ingresso date
);



--
-- Name: professores_id_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE professores_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



--
-- Name: salas; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE salas (
    id integer DEFAULT nextval(('seq_salas_id'::text)::regclass) NOT NULL,
    ref_campus integer,
    numero character varying(10),
    capacidade integer
);



--
-- Name: seq_areas_ensino; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_areas_ensino
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_campus; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_campus
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_carimbos; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_carimbos
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_configuracao_empresa; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_configuracao_empresa
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_contratos; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_contratos
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_cursos_externos; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_cursos_externos
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_departamentos; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_departamentos
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_disciplina; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_disciplina
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_disciplinas_equivalentes_id; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_disciplinas_equivalentes_id
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_disciplinas_ofer; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_disciplinas_ofer
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_disciplinas_ofer_compl_id; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_disciplinas_ofer_compl_id
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_disciplinas_ofer_id; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_disciplinas_ofer_id
    START WITH 1
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_disciplinas_ofer_prof; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_disciplinas_ofer_prof
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_filiacao; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_filiacao
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_grupos_disciplinas; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_grupos_disciplinas
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_instituicoes_id; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_instituicoes_id
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_matricula; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_matricula
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_pessoas; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_pessoas
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_pre_requisitos; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_pre_requisitos
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_sagu_setores; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_sagu_setores
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_salas_id; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_salas_id
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



--
-- Name: seq_tipos_curso; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE seq_tipos_curso
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



SET default_with_oids = false;

--
-- Name: sessao; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE sessao (
    sesskey character varying(64) DEFAULT ''::character varying NOT NULL,
    expiry timestamp without time zone NOT NULL,
    expireref character varying(250) DEFAULT ''::character varying,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    sessdata text DEFAULT ''::text
);



SET default_with_oids = true;

--
-- Name: setor; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE setor (
    id integer DEFAULT nextval(('seq_sagu_setores'::text)::regclass) NOT NULL,
    nome_setor character varying(50) DEFAULT ''::character varying,
    email character varying(50) DEFAULT ''::character varying
);



--
-- Name: tipos_curso; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE tipos_curso (
    id integer DEFAULT nextval(('seq_tipos_curso'::text)::regclass) NOT NULL,
    descricao character varying(30),
    quantidade_notas_diario smallint
);



--
-- Name: turno; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE turno (
    id character varying(1) NOT NULL,
    nome character varying(20),
    abrv character varying(3)
);



SET default_with_oids = false;

--
-- Name: url; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE url (
    url_id serial NOT NULL,
    url text NOT NULL,
    descricao character varying(150)
);



--
-- Name: usuario_id_seq; Type: SEQUENCE; Schema: public; 
--

CREATE SEQUENCE usuario_id_seq
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;



SET default_with_oids = true;

--
-- Name: usuario; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE usuario (
    id integer DEFAULT nextval('usuario_id_seq'::regclass) NOT NULL,
    nome character varying(20) NOT NULL,
    ref_campus integer,
    ref_pessoa integer,
    ref_setor integer,
    senha character varying(80),
    ativado boolean
);



SET default_with_oids = false;

--
-- Name: usuario_papel; Type: TABLE; Schema: public; ; Tablespace: 
--

CREATE TABLE usuario_papel (
    ref_usuario integer NOT NULL,
    ref_papel integer NOT NULL
);



--
-- Name: acesso_aluno_ref_pessoa_unq; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY acesso_aluno
    ADD CONSTRAINT acesso_aluno_ref_pessoa_unq UNIQUE (ref_pessoa);


--
-- Name: aux_cargos_pkey; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY cargo
    ADD CONSTRAINT aux_cargos_pkey PRIMARY KEY (id);


--
-- Name: avisos_pkey; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY avisos
    ADD CONSTRAINT avisos_pkey PRIMARY KEY (id);


--
-- Name: diario_chamadas_pkey; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY diario_chamadas
    ADD CONSTRAINT diario_chamadas_pkey PRIMARY KEY (id);


--
-- Name: diario_seq_faltas_pkey; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY diario_seq_faltas
    ADD CONSTRAINT diario_seq_faltas_pkey PRIMARY KEY (id);


--
-- Name: dias_pkey; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY dias
    ADD CONSTRAINT dias_pkey PRIMARY KEY (id);


--
-- Name: disciplinas_equivalentes_key; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY disciplinas_equivalentes
    ADD CONSTRAINT disciplinas_equivalentes_key PRIMARY KEY (ref_disciplina, ref_disciplina_equivalente, ref_curso);


--
-- Name: disciplinas_equivalentes_unq; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY disciplinas_equivalentes
    ADD CONSTRAINT disciplinas_equivalentes_unq UNIQUE (id);


--
-- Name: funcionario_siape_unq; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY funcionario
    ADD CONSTRAINT funcionario_siape_unq UNIQUE (siape);


--
-- Name: periodos_pkey; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY periodos
    ADD CONSTRAINT periodos_pkey PRIMARY KEY (id);


--
-- Name: pessoas_fotos_pkey; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY pessoas_fotos
    ADD CONSTRAINT pessoas_fotos_pkey PRIMARY KEY (ref_pessoa);


--
-- Name: pk_papel_id; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY papel
    ADD CONSTRAINT pk_papel_id PRIMARY KEY (papel_id);


--
-- Name: pk_ref_papel_ref_url; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY papel_url
    ADD CONSTRAINT pk_ref_papel_ref_url PRIMARY KEY (ref_papel, ref_url);


--
-- Name: pk_url_id; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY url
    ADD CONSTRAINT pk_url_id PRIMARY KEY (url_id);


--
-- Name: pkey_campus; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY campus
    ADD CONSTRAINT pkey_campus PRIMARY KEY (id);


--
-- Name: sagu_usuarios_nome_uniq; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT sagu_usuarios_nome_uniq UNIQUE (nome);


--
-- Name: sessao_pkey; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY sessao
    ADD CONSTRAINT sessao_pkey PRIMARY KEY (sesskey);


--
-- Name: setor_pkey; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY setor
    ADD CONSTRAINT setor_pkey PRIMARY KEY (id);


--
-- Name: turnos_pkey; Type: CONSTRAINT; Schema: public; ; Tablespace: 
--

ALTER TABLE ONLY turno
    ADD CONSTRAINT turnos_pkey PRIMARY KEY (id);


--
-- Name: aux_cidades_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX aux_cidades_pkey ON cidade USING btree (id);


--
-- Name: aux_estados_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX aux_estados_pkey ON estado USING btree (id);


--
-- Name: aux_pais_nome_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX aux_pais_nome_key ON pais USING btree (nome);


--
-- Name: aux_paises_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX aux_paises_pkey ON pais USING btree (id);


--
-- Name: campus_id; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX campus_id ON campus USING btree (id);


--
-- Name: carimbos_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX carimbos_pkey ON carimbos USING btree (id);


--
-- Name: cidades_id; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX cidades_id ON cidade USING btree (id);


--
-- Name: configuracao_empresa_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX configuracao_empresa_pkey ON configuracao_empresa USING btree (id);


--
-- Name: contrato_id; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX contrato_id ON contratos USING btree (id);


--
-- Name: contrato_id_dt_desativacao; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contrato_id_dt_desativacao ON contratos USING btree (id, dt_desativacao);


--
-- Name: contrato_pessoa_curso; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contrato_pessoa_curso ON contratos USING btree (ref_pessoa, ref_curso);


--
-- Name: contrato_pessoa_curso_data; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contrato_pessoa_curso_data ON contratos USING btree (ref_pessoa, ref_curso, dt_desativacao);


--
-- Name: contrato_ref_pessoa; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contrato_ref_pessoa ON contratos USING btree (ref_pessoa);


--
-- Name: contratons_ref_curso_ref_campus; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contratons_ref_curso_ref_campus ON contratos USING btree (ref_curso, ref_campus);


--
-- Name: contratos_dt_cancelamento; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contratos_dt_cancelamento ON contratos USING btree (dt_desativacao);


--
-- Name: contratos_dt_pess_curs; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contratos_dt_pess_curs ON contratos USING btree (dt_desativacao, ref_pessoa, ref_curso);


--
-- Name: contratos_id; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contratos_id ON contratos USING btree (id);


--
-- Name: contratos_id_dt_des; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contratos_id_dt_des ON contratos USING btree (id, dt_desativacao);


--
-- Name: contratos_ref_campus_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX contratos_ref_campus_key ON contratos USING btree (ref_campus, ref_pessoa, ref_curso, dt_ativacao);


--
-- Name: contratos_ref_curso_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contratos_ref_curso_key ON contratos USING btree (ref_curso);


--
-- Name: contratos_ref_last_periodo; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contratos_ref_last_periodo ON contratos USING btree (ref_last_periodo);


--
-- Name: contratos_ref_pessoa; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contratos_ref_pessoa ON contratos USING btree (ref_pessoa);


--
-- Name: contratos_ref_pessoa_ref_curso_; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX contratos_ref_pessoa_ref_curso_ ON contratos USING btree (ref_pessoa, ref_curso, ref_campus);


--
-- Name: cursos_disciplin_ref_campus_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX cursos_disciplin_ref_campus_key ON cursos_disciplinas USING btree (ref_campus, ref_curso, ref_disciplina);


--
-- Name: cursos_disciplinas_equivalencia; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX cursos_disciplinas_equivalencia ON cursos_disciplinas USING btree (equivalencia_disciplina);


--
-- Name: cursos_disciplinas_ref_curso; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX cursos_disciplinas_ref_curso ON cursos_disciplinas USING btree (ref_curso);


--
-- Name: cursos_externos_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX cursos_externos_pkey ON cursos_externos USING btree (id);


--
-- Name: cursos_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX cursos_pkey ON cursos USING btree (id);


--
-- Name: departamentos_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX departamentos_pkey ON departamentos USING btree (id);


--
-- Name: diario_chamadas_id_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX diario_chamadas_id_key ON diario_chamadas USING btree (id);


--
-- Name: diario_formulas_id_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX diario_formulas_id_key ON diario_formulas USING btree (id);


--
-- Name: disciplinas_equivalentes_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX disciplinas_equivalentes_pkey ON disciplinas_equivalentes USING btree (id);


--
-- Name: disciplinas_ofer_compl_desconto; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX disciplinas_ofer_compl_desconto ON disciplinas_ofer_compl USING btree (desconto);


--
-- Name: disciplinas_ofer_compl_num_cred; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX disciplinas_ofer_compl_num_cred ON disciplinas_ofer_compl USING btree (num_creditos_desconto);


--
-- Name: disciplinas_ofer_compl_num_sala; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX disciplinas_ofer_compl_num_sala ON disciplinas_ofer_compl USING btree (num_sala);


--
-- Name: disciplinas_ofer_compl_observac; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX disciplinas_ofer_compl_observac ON disciplinas_ofer_compl USING btree (observacao);


--
-- Name: disciplinas_ofer_compl_ref_disc; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX disciplinas_ofer_compl_ref_disc ON disciplinas_ofer_compl USING btree (ref_disciplina_ofer);


--
-- Name: disciplinas_ofer_compl_turno; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX disciplinas_ofer_compl_turno ON disciplinas_ofer_compl USING btree (turno);


--
-- Name: disciplinas_ofer_idkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX disciplinas_ofer_idkey ON disciplinas_ofer USING btree (id);


--
-- Name: disciplinas_ofer_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX disciplinas_ofer_pkey ON disciplinas_ofer USING btree (id);


--
-- Name: disciplinas_oferecidas_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX disciplinas_oferecidas_key ON disciplinas_ofer USING btree (id, ref_campus, ref_curso, ref_periodo, ref_disciplina);


--
-- Name: disciplinas_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX disciplinas_pkey ON disciplinas USING btree (id);


--
-- Name: documentos_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX documentos_pkey ON documentos USING btree (ref_pessoa);


--
-- Name: filiacao_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX filiacao_pkey ON filiacao USING btree (id);


--
-- Name: grupos_disciplinas_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX grupos_disciplinas_pkey ON grupos_disciplinas USING btree (id);


--
-- Name: id_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX id_key ON pessoas USING btree (id);


--
-- Name: instituicoes_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX instituicoes_pkey ON instituicoes USING btree (id);


--
-- Name: matricula_curso_ch_cr; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_curso_ch_cr ON matricula USING btree (ref_curso, carga_horaria_aprov, creditos_aprov);


--
-- Name: matricula_disc_curso_data; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_disc_curso_data ON matricula USING btree (ref_disciplina, ref_curso, dt_cancelamento);


--
-- Name: matricula_dt_cancelamento; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_dt_cancelamento ON matricula USING btree (dt_cancelamento);


--
-- Name: matricula_fl_internet; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_fl_internet ON matricula USING btree (fl_internet);


--
-- Name: matricula_nota_final; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_nota_final ON matricula USING btree (nota_final);


--
-- Name: matricula_periodo; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_periodo ON matricula USING btree (ref_periodo);


--
-- Name: matricula_periodo_aprov; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_periodo_aprov ON matricula USING btree (ref_periodo, obs_aproveitamento);


--
-- Name: matricula_periodo_dt; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_periodo_dt ON matricula USING btree (ref_periodo, dt_cancelamento);


--
-- Name: matricula_periodo_fl_internet; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_periodo_fl_internet ON matricula USING btree (ref_periodo, fl_internet);


--
-- Name: matricula_pessoa_disciplina; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_pessoa_disciplina ON matricula USING btree (ref_pessoa, ref_disciplina);


--
-- Name: matricula_pessoa_disciplina_not; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_pessoa_disciplina_not ON matricula USING btree (ref_pessoa, ref_disciplina, nota_final);


--
-- Name: matricula_pessoa_periodo; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_pessoa_periodo ON matricula USING btree (ref_pessoa, ref_periodo);


--
-- Name: matricula_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX matricula_pkey ON matricula USING btree (id);


--
-- Name: matricula_ref_contrato_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_ref_contrato_key ON matricula USING btree (ref_contrato);


--
-- Name: matricula_ref_curso_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_ref_curso_key ON matricula USING btree (ref_curso);


--
-- Name: matricula_ref_disciplina_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_ref_disciplina_key ON matricula USING btree (ref_disciplina);


--
-- Name: matricula_ref_disciplina_ofer_k; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_ref_disciplina_ofer_k ON matricula USING btree (ref_disciplina_ofer);


--
-- Name: matricula_ref_periodo; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_ref_periodo ON matricula USING btree (ref_periodo);


--
-- Name: matricula_ref_pessoa; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_ref_pessoa ON matricula USING btree (ref_pessoa);


--
-- Name: matricula_ref_pessoa_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX matricula_ref_pessoa_key ON matricula USING btree (ref_pessoa);


--
-- Name: motivos_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX motivos_pkey ON motivo USING btree (id);


--
-- Name: periodos_datas; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX periodos_datas ON periodos USING btree (dt_inicial, dt_final);


--
-- Name: pessoas_10; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX pessoas_10 ON pessoas USING btree (fl_segurado, id);


--
-- Name: pessoas_11; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX pessoas_11 ON pessoas USING btree (fl_segurado);


--
-- Name: pessoas_id; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX pessoas_id ON pessoas USING btree (id);


--
-- Name: pessoas_nome_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX pessoas_nome_key ON pessoas USING btree (nome);


--
-- Name: pessoas_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX pessoas_pkey ON pessoas USING btree (id);


--
-- Name: pessoas_ref_cidade; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX pessoas_ref_cidade ON pessoas USING btree (ref_cidade);


--
-- Name: pre_requisitos_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX pre_requisitos_pkey ON pre_requisitos USING btree (id);


--
-- Name: pre_requisitos_ref_curso_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX pre_requisitos_ref_curso_key ON pre_requisitos USING btree (ref_curso, ref_disciplina, ref_disciplina_pre);


--
-- Name: professores_id_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX professores_id_key ON professores USING btree (id);


--
-- Name: ref_campus_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX ref_campus_key ON disciplinas_ofer USING btree (ref_campus);


--
-- Name: ref_curso_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX ref_curso_key ON disciplinas_ofer USING btree (ref_curso);


--
-- Name: ref_disciplina_compl_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX ref_disciplina_compl_key ON disciplinas_ofer_prof USING btree (ref_disciplina_compl);


--
-- Name: ref_disciplina_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX ref_disciplina_key ON disciplinas_ofer USING btree (ref_disciplina);


--
-- Name: ref_disciplina_ofer_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX ref_disciplina_ofer_key ON disciplinas_ofer_prof USING btree (ref_disciplina_ofer);


--
-- Name: ref_periodo_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX ref_periodo_key ON disciplinas_ofer USING btree (ref_periodo);


--
-- Name: ref_professor_ofer_key; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX ref_professor_ofer_key ON disciplinas_ofer_prof USING btree (ref_professor);


--
-- Name: sagu_usuarios_nome; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX sagu_usuarios_nome ON usuario USING btree (nome);


--
-- Name: sagu_usuarios_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX sagu_usuarios_pkey ON usuario USING btree (id);


--
-- Name: salas_pkey; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE UNIQUE INDEX salas_pkey ON salas USING btree (id);


--
-- Name: sessao_expireref_idx; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX sessao_expireref_idx ON sessao USING btree (expireref);


--
-- Name: sessao_expiry_idx; Type: INDEX; Schema: public; ; Tablespace: 
--

CREATE INDEX sessao_expiry_idx ON sessao USING btree (expiry);


--
-- Name: trg_cria_ra; Type: TRIGGER; Schema: public; 
--

CREATE TRIGGER trg_cria_ra
    AFTER INSERT ON pessoas
    FOR EACH ROW
    EXECUTE PROCEDURE cria_ra();


--
-- Name: campus_sede_fkey; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY campus
    ADD CONSTRAINT campus_sede_fkey FOREIGN KEY (ref_campus_sede) REFERENCES campus(id) MATCH FULL;


--
-- Name: cursos_disciplinas_cursos_fkey; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY cursos_disciplinas
    ADD CONSTRAINT cursos_disciplinas_cursos_fkey FOREIGN KEY (ref_curso) REFERENCES cursos(id) MATCH FULL;

--
-- Name: cursos_disciplinas_campus_fkey; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY cursos_disciplinas
    ADD CONSTRAINT cursos_disciplinas_campus_fkey FOREIGN KEY (ref_campus) REFERENCES campus(id) MATCH FULL;

--
-- Name: cursos_disciplinas_disciplinas_fkey; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY cursos_disciplinas
    ADD CONSTRAINT cursos_disciplinas_disciplinas_fkey FOREIGN KEY (ref_disciplina) REFERENCES disciplinas(id) MATCH FULL;



--
-- Name: campus_usuario_fkey; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT campus_usuario_fkey FOREIGN KEY (ref_campus) REFERENCES campus(id) MATCH FULL;


--
-- Name: fkey_campus_matricula; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY matricula
    ADD CONSTRAINT fkey_campus_matricula FOREIGN KEY (ref_campus) REFERENCES campus(id);


--
-- Name: fkey_contratos_matricula; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY matricula
    ADD CONSTRAINT fkey_contratos_matricula FOREIGN KEY (ref_contrato) REFERENCES contratos(id);


--
-- Name: fkey_cursos_matricula; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY matricula
    ADD CONSTRAINT fkey_cursos_matricula FOREIGN KEY (ref_curso) REFERENCES cursos(id);


--
-- Name: fkey_disciplinas_ofer_matricula; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY matricula
    ADD CONSTRAINT fkey_disciplinas_ofer_matricula FOREIGN KEY (ref_disciplina_ofer) REFERENCES disciplinas_ofer(id);


--
-- Name: fkey_periodos_matricula; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY matricula
    ADD CONSTRAINT fkey_periodos_matricula FOREIGN KEY (ref_periodo) REFERENCES periodos(id);


--
-- Name: fkey_pessoas_matricula; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY matricula
    ADD CONSTRAINT fkey_pessoas_matricula FOREIGN KEY (ref_pessoa) REFERENCES pessoas(id);


--
-- Name: papel_papel_url_fkey; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY papel_url
    ADD CONSTRAINT papel_papel_url_fkey FOREIGN KEY (ref_papel) REFERENCES papel(papel_id) MATCH FULL;


--
-- Name: papel_usuario_papel_fkey; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY usuario_papel
    ADD CONSTRAINT papel_usuario_papel_fkey FOREIGN KEY (ref_usuario) REFERENCES usuario(id) MATCH FULL;


--
-- Name: periodo_fkey; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY diario_seq_faltas
    ADD CONSTRAINT periodo_fkey FOREIGN KEY (periodo) REFERENCES periodos(id) MATCH FULL;


--
-- Name: pessoas_usuario_fkey; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT pessoas_usuario_fkey FOREIGN KEY (ref_pessoa) REFERENCES pessoas(id) MATCH FULL;


--
-- Name: setor_usuario_fkey; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY usuario
    ADD CONSTRAINT setor_usuario_fkey FOREIGN KEY (ref_setor) REFERENCES setor(id) MATCH FULL;


--
-- Name: url_papel_url_fkey; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY papel_url
    ADD CONSTRAINT url_papel_url_fkey FOREIGN KEY (ref_url) REFERENCES url(url_id) MATCH FULL;


--
-- Name: usuario_usuario_papel_fkey; Type: FK CONSTRAINT; Schema: public; 
--

ALTER TABLE ONLY usuario_papel
    ADD CONSTRAINT usuario_usuario_papel_fkey FOREIGN KEY (ref_papel) REFERENCES papel(papel_id) MATCH FULL;


--
-- PostgreSQL database dump complete
--


---
--- ATUALIZACOES
---

-- rev270

ALTER TABLE disciplinas_ofer_compl ADD CONSTRAINT ref_disciplina_ofer_unq UNIQUE (ref_disciplina_ofer);

ALTER TABLE ONLY disciplinas_ofer_compl
    ADD CONSTRAINT disciplinas_ofer_compl_ref_disciplina_ofer_fkey FOREIGN KEY (ref_disciplina_ofer) REFERENCES disciplinas_ofer(id) MATCH FULL;

ALTER TABLE disciplinas_ofer_compl ADD CONSTRAINT disciplinas_ofer_compl_pkey PRIMARY KEY (id);

ALTER TABLE professores ADD CONSTRAINT ref_professor_pkey PRIMARY KEY (ref_professor);

ALTER TABLE ONLY disciplinas_ofer_prof
    ADD CONSTRAINT ref_professor_fkey FOREIGN KEY (ref_professor) REFERENCES professores(ref_professor) MATCH FULL;

ALTER TABLE ONLY disciplinas_ofer_prof
    ADD CONSTRAINT disciplinas_ofer_compl_disciplinas_ofer_prof_fkey FOREIGN KEY (ref_disciplina_compl) REFERENCES disciplinas_ofer_compl(id) MATCH FULL;


ALTER TABLE disciplinas_ofer_prof ALTER COLUMN ref_professor SET NOT NULL;




-- rev271




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


