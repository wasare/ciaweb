--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Name: url_url_id_seq; Type: SEQUENCE SET; Schema: public; Owner: usrsa
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('url', 'url_id'), 29, true);


--
-- Data for Name: url; Type: TABLE DATA; Schema: public; Owner: usrsa
--

INSERT INTO url VALUES (1, '/index.php', 'Pagina de autenticação');
INSERT INTO url VALUES (2, '/app/index.php', 'Página inicial do sistema');
INSERT INTO url VALUES (3, '/app/diagrama.php', 'Página com o diagrama de acesso do sistema');
INSERT INTO url VALUES (4, '/app/setup.php', 'Arquivo com as ações iniciais das páginas do sistema (bootstrap).');
INSERT INTO url VALUES (5, '/', 'Raiz do sistema');
INSERT INTO url VALUES (6, '/app/usuarios/', 'Pasta de controle de usuarios');
INSERT INTO url VALUES (7, '/app/usuarios/alterar_senha.php', 'Alterar senha de usuario');
INSERT INTO url VALUES (8, '/app/sagu/academico/curso_ins.php', 'Formulário inserir curso');
INSERT INTO url VALUES (10, '/app/sagu/academico/post/cursos_exclui.php', 'Ação excluir curso');
INSERT INTO url VALUES (11, '/app/sagu/academico/post/confirm_curso_ins.php', 'Confirmar inserir curso');
INSERT INTO url VALUES (12, '/app/sagu/academico/post/curso_altera.php', 'Ação alterar curso');
INSERT INTO url VALUES (13, '/app/sagu/academico/post/curso_ins.php', 'Ação inserir curso');
INSERT INTO url VALUES (14, '/app/sagu/academico/disciplinas.php', 'Formulário inserir disciplina');
INSERT INTO url VALUES (15, '/app/sagu/academico/post/disciplinas.php', 'Ação inserir disciplina');
INSERT INTO url VALUES (16, '/app/sagu/academico/post/disciplinas_altera.php', 'Ação alterar disciplina');
INSERT INTO url VALUES (17, '/app/sagu/academico/post/disciplinas_exclui.php', 'Ação excluir disciplina');
INSERT INTO url VALUES (18, '/app/sagu/academico/cursos_disciplinas.php', 'Formulário inserir matriz');
INSERT INTO url VALUES (19, '/app/sagu/academico/post/cursos_disciplinas.php', 'Ação inserir matriz');
INSERT INTO url VALUES (20, '/app/sagu/academico/post/cursos_disciplinas_edita.php', 'Ação alterar matriz');
INSERT INTO url VALUES (21, '/app/sagu/academico/post/cursos_disciplinas_exclui.php', 'Ação excluir matriz');
INSERT INTO url VALUES (22, '/app/sagu/academico/inclui_pre_requisito.php', 'Formulário inserir pre-requisito');
INSERT INTO url VALUES (23, '/app/sagu/academico/post/inclui_pre_requisito.php', 'Ação inserir pre-requisito');
INSERT INTO url VALUES (24, '/app/sagu/academico/post/edita_pre_requisito.php', 'Ação alterar pre-requisito');
INSERT INTO url VALUES (25, '/app/sagu/academico/post/pre_requisito_exclui.php', 'Ação excluir pre-requisito');
INSERT INTO url VALUES (26, '/app/sagu/academico/inclui_disciplinas_equivalentes.php', 'Formulário disciplinas equivalentes');
INSERT INTO url VALUES (27, '/app/sagu/academico/post/altera_disciplinas_equivalentes.php', 'Ação alterar disciplinas equivalentes');
INSERT INTO url VALUES (28, '/app/sagu/academico/post/disciplinas_equivalentes_exclui.php', 'Ação excluir disciplinas equivalentes');
INSERT INTO url VALUES (29, '/app/sagu/academico/post/inclui_disciplinas_equivalentes.php', 'Ação inserir disciplinas equivalentes');


--
-- PostgreSQL database dump complete
--

