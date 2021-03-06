
--- diarios e prontuarios professores
SELECT DISTINCT
    d.abreviatura AS "Disciplina",
    o.turma AS "Turma",
    p.prontuario As "Prontuário professor",
    u.nome AS "Login",
    s.abreviatura AS "Curso"
FROM
    disciplinas_ofer o,
    disciplinas_ofer_prof op,
    disciplinas d,
    cursos s,
    tipos_curso t,
    campus m,
    pessoa_prontuario_campus p,
    usuario u

WHERE
    o.ref_curso = s.id AND  s.ref_tipo_curso = t.id AND
    o.ref_periodo = '111' AND
    o.is_cancelada = '0' AND
    d.id = o.ref_disciplina AND  o.ref_campus = m.id
    AND op.ref_professor = p.ref_pessoa AND u.ref_pessoa = op.ref_professor AND p.ref_pessoa = u.ref_pessoa AND  o.id = op.ref_disciplina_ofer


-- seleciona pessoas com nomes duplicados
select p1.id, p1.nome, p2.id, p2.nome
  from pessoas p1, pessoas p2
  where p1.nome ilike p2.nome and p1.id != p2.id

select pr1.prontuario, p1.id, p1.nome, pr2.prontuario, p2.id, p2.nome
from pessoas p1, pessoas p2, pessoa_prontuario_campus pr1, pessoa_prontuario_campus pr2
where  p1.id != p2.id and p1.id = pr1.ref_pessoa and p2.id = pr2.ref_pessoa and lower(to_ascii(p1.nome,'LATIN1')) = lower(to_ascii(p2.nome,'LATIN1'))

select pr1.prontuario, p1.id, p1.nome, pr2.prontuario, p2.id, p2.nome
from pessoas p1, pessoas p2, pessoa_prontuario_campus pr1, pessoa_prontuario_campus pr2
where  p1.id != p2.id and p1.id = pr1.ref_pessoa and p2.id = pr2.ref_pessoa and lower(to_ascii(p1.nome,'LATIN1')) = lower(to_ascii(p2.nome,'LATIN1')) and p1.nome = lower(to_ascii('Wanderson Santiago dos Reis','LATIN1'))



-- falas parciais do aluno por dia de ocorrência
SELECT dia, CASE 
                        WHEN faltas IS NULL THEN '0' 
                        ELSE faltas
                    END AS faltas
FROM
(
SELECT DISTINCT
          c.ra_cnec, data_chamada, count(CAST(c.ra_cnec AS INTEGER)) as faltas          
		FROM diario_chamadas c
         WHERE
           c.ref_disciplina_ofer = 82 AND
           CAST(c.ra_cnec AS INTEGER) = 91
        GROUP BY c.ra_cnec, data_chamada
) AS T1
FULL OUTER JOIN
(
SELECT DISTINCT dia FROM diario_seq_faltas WHERE ref_disciplina_ofer = 82 ORDER BY dia
) AS T2 ON (data_chamada = dia)

ORDER BY dia;


-- professor que mais lancou chamadas



--- selecione contratos conforme prontuario
SELECT ppc.prontuario, p.nome, c.id, c.ref_campus, c.ref_pessoa, c.turma, c.ref_periodo_turma, c.ref_curso, c.dt_ativacao FROM contratos c, pessoas p, pessoa_prontuario_campus ppc where c.ref_pessoa = p.id and ppc.ref_pessoa = c.ref_pessoa AND prontuario = '110604X'
SELECT ppc.prontuario, p.nome, c.id, c.ref_campus, c.ref_pessoa, c.turma, c.ref_periodo_turma, c.ref_curso, c.dt_ativacao FROM contratos c, pessoas p, pessoa_prontuario_campus ppc where c.ref_pessoa = p.id and ppc.ref_pessoa = c.ref_pessoa AND turma is null


0854042,
085283X,
0951404,
0951463,
0951293,
0852708,
0950611,
0852104,
0951129,


-- BANCO NAMBEI
ESCOLA_MATCURSO - matricula no curso (contrato)
ESCOLA_EVTDISC - oferta de disciplina
ESCOLA_MODULO - cadastro de módulos de cursos (periodos)
ESCOLA_MODUDIS - oferta de disciplinas para o módulo
ESCOLA_EVTMODU - oferta de módulo de curso
ESCOLA_EVTMODIS - oferta de turmas de disciplinas para o módulo
ESCOLA_CURSOMOD - cadastros de cursos e seus módulos


SELECT AL_PRONT, AL_CURSO FROM ESCOLA_ALUNOS
WHERE AL_PRONT = '1101919';

SELECT MC_ALPRONT, MC_CDCURSO FROM ESCOLA_MATCURSO WHERE MC_ALPRONT = '0950289';

SELECT CM_CDCURSO, CM_MODULO FROM ESCOLA_CURSOMOD WHERE CM_CDCURSO = 7011;

SELECT DM_MODULO,	DM_DISC FROM ESCOLA_MODUDIS WHERE DM_MODULO = 170;

DM_MODULO	DM_DISC
170	ADET1
170	LOGT1
170	IAMT1
170	ISOT1
170	IAAT1
170	LEAT1

SELECT * FROM ESCOLA_MODULO WHERE M_MODULO = 170;

SELECT * FROM ESCOLA_EVTMODU WHERE EM_MODULO = 170;

EM_MODULO	EM_EVENTOM	EM_DTINICIO	EM_DTFINAL	EM_COMENT	EM_PERIODO	EM_SITUACAO
170	1	2009-07-27 00:00:00	2009-12-11 00:00:00	 	N	2
170	2	2010-02-08 00:00:00	2010-07-02 00:00:00	 	N	2
170	3	2009-02-09 00:00:00	2009-07-02 00:00:00	 	N	2


SELECT * FROM ESCOLA_EVTMODIS WHERE EMD_MODULO = 170;

EMD_MODULO	EMD_EVENTOM	EMD_DISC	EMD_EVENTOD
170	1	ADET1	17
170	3	ADET1	21
170	2	ADET1	20
170	2	ADET1	19
170	1	ADET1	18
170	3	IAAT1	16
170	1	IAAT1	13
170	2	IAAT1	15
170	1	IAAT1	14
170	2	IAMT1	19
170	3	IAMT1	21
170	1	IAMT1	18
170	1	IAMT1	17
170	2	IAMT1	20
170	3	ISOT1	12
170	2	ISOT1	11
170	1	ISOT1	10
170	1	ISOT1	9
170	3	LEAT1	15
170	2	LEAT1	13
170	2	LEAT1	14
170	1	LEAT1	11
170	1	LEAT1	12
170	1	LOGT1	10
170	2	LOGT1	12
170	1	LOGT1	11
170	3	LOGT1	13


SELECT * FROM ESCOLA_EVTDISC WHERE ED_DTINICIO > '01/01/2010';

SELECT DISTINCT ED_DISC, ED_EVENTOD FROM ESCOLA_EVTDISC WHERE ED_DTINICIO > '01/01/2010';


-- SQL - Tecnicos
SELECT
    "a" & LCase([MC_ALPRONT]) AS username,
    Year([ED_DTINICIO]) & "-" & getSemestre([ED_DTINICIO]) & "-" & [ABREV_CURSO] & "-" & [CG_MODULOS].[SEMESTRE] & [ED_PERIODO] & "-" & Trim([D_DISC]) AS course1,
    "student" AS role1
FROM
    ESCOLA_EVTMODIS INNER JOIN (((ESCOLA_MATDIS INNER JOIN (((((CG_CURSOS INNER JOIN ESCOLA_MATCURSO ON CG_CURSOS.COD_CURSO = ESCOLA_MATCURSO.MC_CDCURSO) INNER JOIN ESCOLA_MATMOD ON (ESCOLA_MATCURSO.MC_SEQUENC = ESCOLA_MATMOD.MM_SEQUENC) AND (ESCOLA_MATCURSO.MC_ALPRONT = ESCOLA_MATMOD.MM_PRONT)) INNER JOIN ESCOLA_EVTMODU ON (ESCOLA_MATMOD.MM_EVENTOM = ESCOLA_EVTMODU.EM_EVENTOM) AND (ESCOLA_MATMOD.MM_MODULO = ESCOLA_EVTMODU.EM_MODULO)) INNER JOIN ESCOLA_MODULO ON ESCOLA_EVTMODU.EM_MODULO = ESCOLA_MODULO.M_MODULO) INNER JOIN CG_MODULOS ON ESCOLA_MODULO.M_MODULO = CG_MODULOS.COD_MODULO) ON (ESCOLA_MATDIS.MD_PRONT = ESCOLA_MATCURSO.MC_ALPRONT) AND (ESCOLA_MATDIS.MD_SEQUENC = ESCOLA_MATCURSO.MC_SEQUENC)) INNER JOIN ESCOLA_EVTDISC ON (ESCOLA_MATDIS.MD_DISC = ESCOLA_EVTDISC.ED_DISC) AND (ESCOLA_MATDIS.MD_EVENTOD = ESCOLA_EVTDISC.ED_EVENTOD)) INNER JOIN ESCOLA_DISCIPL ON ESCOLA_EVTDISC.ED_DISC = ESCOLA_DISCIPL.D_DISC) ON (ESCOLA_EVTMODIS.EMD_MODULO = ESCOLA_EVTMODU.EM_MODULO) AND (ESCOLA_EVTMODIS.EMD_EVENTOM = ESCOLA_EVTMODU.EM_EVENTOM) AND (ESCOLA_EVTMODIS.EMD_DISC = ESCOLA_EVTDISC.ED_DISC) AND (ESCOLA_EVTMODIS.EMD_EVENTOD = ESCOLA_EVTDISC.ED_EVENTOD)
GROUP BY
    "a" & LCase([MC_ALPRONT]), Year([ED_DTINICIO]) & "-" & getSemestre([ED_DTINICIO]) & "-" & [ABREV_CURSO] & "-" & [CG_MODULOS].[SEMESTRE] & [ED_PERIODO] & "-" & Trim([D_DISC]), "student", ESCOLA_MATMOD.MM_SITUACAO, ESCOLA_MATDIS.MD_SITUACAO
HAVING
    (((ESCOLA_MATMOD.MM_SITUACAO)="1") AND ((ESCOLA_MATDIS.MD_SITUACAO)="1"))
ORDER BY
    "a" & LCase([MC_ALPRONT]);

SELECT
    MC_ALPRONT AS username,
    Year(ED_DTINICIO) || "-" || ED_DTINICIO || "-" || ABREV_CURSO || "-" || CG_MODULOS.SEMESTRE || ED_PERIODO || "-" || Trim(D_DISC) AS course1,
    "student" AS role1
FROM
    ESCOLA_EVTMODIS INNER JOIN (((ESCOLA_MATDIS INNER JOIN (((((CG_CURSOS INNER JOIN ESCOLA_MATCURSO ON CG_CURSOS.COD_CURSO = ESCOLA_MATCURSO.MC_CDCURSO) INNER JOIN ESCOLA_MATMOD ON (ESCOLA_MATCURSO.MC_SEQUENC = ESCOLA_MATMOD.MM_SEQUENC) AND (ESCOLA_MATCURSO.MC_ALPRONT = ESCOLA_MATMOD.MM_PRONT)) INNER JOIN ESCOLA_EVTMODU ON (ESCOLA_MATMOD.MM_EVENTOM = ESCOLA_EVTMODU.EM_EVENTOM) AND (ESCOLA_MATMOD.MM_MODULO = ESCOLA_EVTMODU.EM_MODULO)) INNER JOIN ESCOLA_MODULO ON ESCOLA_EVTMODU.EM_MODULO = ESCOLA_MODULO.M_MODULO) INNER JOIN CG_MODULOS ON ESCOLA_MODULO.M_MODULO = CG_MODULOS.COD_MODULO) ON (ESCOLA_MATDIS.MD_PRONT = ESCOLA_MATCURSO.MC_ALPRONT) AND (ESCOLA_MATDIS.MD_SEQUENC = ESCOLA_MATCURSO.MC_SEQUENC)) INNER JOIN ESCOLA_EVTDISC ON (ESCOLA_MATDIS.MD_DISC = ESCOLA_EVTDISC.ED_DISC) AND (ESCOLA_MATDIS.MD_EVENTOD = ESCOLA_EVTDISC.ED_EVENTOD)) INNER JOIN ESCOLA_DISCIPL ON ESCOLA_EVTDISC.ED_DISC = ESCOLA_DISCIPL.D_DISC) ON (ESCOLA_EVTMODIS.EMD_MODULO = ESCOLA_EVTMODU.EM_MODULO) AND (ESCOLA_EVTMODIS.EMD_EVENTOM = ESCOLA_EVTMODU.EM_EVENTOM) AND (ESCOLA_EVTMODIS.EMD_DISC = ESCOLA_EVTDISC.ED_DISC) AND (ESCOLA_EVTMODIS.EMD_EVENTOD = ESCOLA_EVTDISC.ED_EVENTOD)
GROUP BY
    MC_ALPRONT, Year(ED_DTINICIO) || "-" || ED_DTINICIO || "-" || ABREV_CURSO || "-" || CG_MODULOS.SEMESTRE || ED_PERIODO || "-" || Trim(D_DISC), "student", ESCOLA_MATMOD.MM_SITUACAO, ESCOLA_MATDIS.MD_SITUACAO
HAVING
    (((ESCOLA_MATMOD.MM_SITUACAO) = "1") AND ((ESCOLA_MATDIS.MD_SITUACAO)="1"))
ORDER BY
    MC_ALPRONT;



-- SQL - Superior
SELECT
    "a" & LCase([MC_ALPRONT]) AS username,
    Year([ED_DTINICIO]) & "-" & getSemestre([ED_DTINICIO]) & "-" & [ABREV_CURSO] & "-" & Mid([ESCOLA_DISCIPL].[D_DISC],5) & [ED_PERIODO] & "-" & Trim([D_DISC]) AS course1,
    "student" AS role1,
    Trim([MD_DISC]) AS cod_disciplina,
    Trim([AL_NOME]) AS aluno
FROM
    (((ESCOLA_MATDIS INNER JOIN (CG_CURSOS INNER JOIN ESCOLA_MATCURSO ON CG_CURSOS.COD_CURSO = ESCOLA_MATCURSO.MC_CDCURSO) ON (ESCOLA_MATDIS.MD_PRONT = ESCOLA_MATCURSO.MC_ALPRONT) AND (ESCOLA_MATDIS.MD_SEQUENC = ESCOLA_MATCURSO.MC_SEQUENC)) INNER JOIN ESCOLA_EVTDISC ON (ESCOLA_MATDIS.MD_DISC = ESCOLA_EVTDISC.ED_DISC) AND (ESCOLA_MATDIS.MD_EVENTOD = ESCOLA_EVTDISC.ED_EVENTOD)) INNER JOIN ESCOLA_DISCIPL ON ESCOLA_EVTDISC.ED_DISC = ESCOLA_DISCIPL.D_DISC) INNER JOIN ESCOLA_ALUNOS ON ESCOLA_MATDIS.MD_PRONT = ESCOLA_ALUNOS.AL_PRONT
GROUP BY
    "a" & LCase([MC_ALPRONT]), Year([ED_DTINICIO]) & "-" & getSemestre([ED_DTINICIO]) & "-" & [ABREV_CURSO] & "-" & Mid([ESCOLA_DISCIPL].[D_DISC],5) & [ED_PERIODO] & "-" & Trim([D_DISC]), "student", Trim([MD_DISC]), Trim([AL_NOME]), ESCOLA_MATDIS.MD_SITUACAO, CG_CURSOS.SUPERIOR
HAVING
    (((ESCOLA_MATDIS.MD_SITUACAO)="1") AND ((CG_CURSOS.SUPERIOR)=True))
ORDER BY
    "a" & LCase([MC_ALPRONT]);

