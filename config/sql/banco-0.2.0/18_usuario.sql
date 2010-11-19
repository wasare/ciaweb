--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: usuario; Type: TABLE DATA; Schema: public; Owner: usrsa
--

INSERT INTO pessoas (id, nome) VALUES (1, 'Administrador - altere para o cadastro de uma pessoa real');
INSERT INTO documentos (ref_pessoa) VALUES (1);
INSERT INTO usuario VALUES (1, 'admin', 1, 1, 1, '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', true);
