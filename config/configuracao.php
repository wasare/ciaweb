<?php
//die('<h3>Sistema em manuten&ccedil;&atilde;o no momento. Voltaremos em breve.</h3>');
/**
 * Forca o fuso horario da aplicacao
 */
date_default_timezone_set('America/Sao_Paulo');

/**
 * Banco de dados
 */
$host     = '127.0.0.1';
$database = 'ciaweb_24042012';
$user     = 'postgres';
$password = '1234';
$port     = 5432;

/**
 * Variaveis de acesso a dados
 */
$param_conn['host']     = $host;
$param_conn['database'] = $database;
$param_conn['user']     = $user;
$param_conn['password'] = $password;
$param_conn['port']     = $port;

/**
 * Variaveis de acesso a dados - Modulo do aluno
 */
$param_conn_aluno['host']     = $param_conn['host'];
$param_conn_aluno['database'] = $param_conn['database'];
$param_conn_aluno['user']     = 'postgres';
$param_conn_aluno['password'] = '1234';
$param_conn_aluno['port']     = $port;

/**
 * Variaveis de acesso a base LDAP (veja core/login/auth.ldap.php)
 */
$param_ldap['base_dn'][]   = 'ou=Users,dc=campus,dc=local';
$param_ldap['base_dn'][]   = 'ou=Users,dc=campus,dc=local';
$param_ldap['ldap_hosts']  = array('127.0.0.1');

$param_ldap_aluno['base_dn'][]  = 'ou=Users,dc=campus,dc=local';
$param_ldap_aluno['ldap_hosts'] = array('127.0.0.1');


/**
 * HTML Padrao
 */
$DOC_TYPE       = '<meta http-equiv="Content-Type" content="text/html; charset= UTF-8">';

/**
 * Variaveis do sistema
 */
$BASE_URL       = 'http://'. $_SERVER['HTTP_HOST'] . '/~wasare/dev/netbeans-php/ciaweb/';
$BASE_DIR       = dirname(dirname(realpath(__FILE__))) .'/';
$LOGIN_URL      = $BASE_URL .'index.php';
$LOGIN_LOG_FILE = $BASE_DIR .'app/sagu/logs/login.log';
$PATH_IMAGES    = $BASE_URL."public/images/";
$REVISAO 	= @file_get_contents($BASE_DIR .'VERSAO.TXT');
$SESS_TABLE     = 'sessao';
$PAPEIS_SA = array('secretaria' => 1, 'administrador' => 2, 'administra_matrizes' => 4, 'relatorios' => 5);
$PAPEIS_WEB_DIARIO = array('professor' => 3, 'coordenador' => 0);
$PAPEIS_ADMINISTRADOR = array('administrador' => 2);
$EMAIL_ADMIN = 'wanderson@ifsp.edu.br';
$USUARIO_ALTERA_SENHA = 0;

/*
   ALGUNS PARAMETROS DO SISTEMA ACADEMICO
   ** acima de cada parametro os respectivos arquivos onde sao utilizados **
*/

// atividades de aula
$ATIVIDADES_AULA[0] = 'Aula expositiva';
$ATIVIDADES_AULA[1] = 'Aula prática / laboratório';
$ATIVIDADES_AULA[2] = 'Exercícios';
$ATIVIDADES_AULA[3] = 'Trabalho em grupos';
$ATIVIDADES_AULA[4] = 'Pesquisa';
$ATIVIDADES_AULA[5] = 'Análise de situação problema';
$ATIVIDADES_AULA[6] = 'Seminário';
$ATIVIDADES_AULA[7] = 'Visita técnica';
$ATIVIDADES_AULA[8] = 'Avaliação';



// app/diagrama.php
// public/help.php
$IEnome     = 'Instituto Federal São Paulo - Caraguatatuba';

// app/index.php
// public/help.php
$IEurl      = 'http://www.ifspcaraguatatuba.edu.br/';

// app/sagu/academico/cursos_disciplinas_edita.php
// app/relatorios/integralizacao_curso/lista_integralizacao_curso.php
$curriculos["M"] = "M&iacute;nimo";
$curriculos["C"] = "Complementar";
$curriculos["O"] = "Optativa";
$curriculos["P"] = "Profici&ecirc;ncia";
$curriculos["A"] = "Atividade complementar";

// app/sagu/academico/cursos_disciplinas_edita.php
$historico["S"]  = "Sim";
$historico["N"]  = "N&atilde;o";

// app/sagu/academico/curso_altera.php
// app/sagu/academico/lista_disciplinas_ofer.php
$status["1"]     = "Sim";
$status["0"]     = "N&atilde;o";

// app/sagu/academico/pessoaf_edita.php
// app/sagu/academico/documentos_edita.php
// app/sagu/academico/post/confirm_pessoaf_inclui.php
$opcoes["t"]     = "Sim";
$opcoes["f"]     = "N&atilde;o";

// app/sagu/academico/pessoaf_edita.php
// app/sagu/academico/post/confirm_pessoaf_inclui.php
$estados_civis["S"] = "Solteiro";
$estados_civis["C"] = "Casado";
$estados_civis["V"] = "Vi&uacute;vo";
$estados_civis["D"] = "Desquitado";
$estados_civis["U"] = "Uni&atilde;o est&aacute;vel";
$estados_civis["E"] = "Solteiro emancipado";

// app/sagu/generico/post/lista_areas_ensino.php
// app/sagu/generico/post/lista_cidades.php
// app/sagu/generico/post/lista_escolas.php
// app/sagu/generico/post/lista_professores.php
// app/sagu/generico/post/lista_pessoas.php
// app/sagu/generico/post/lista_sql.php
// app/sagu/academico/consulta_disciplinas_equivalentes.php
$limite_list        = 25;


// app/sagu/academico/periodos_altera.php
// app/sagu/academico/novo_contrato.php
// app/sagu/academico/periodos.php
// app/sagu/academico/alterar_contrato.php
// app/sagu/academico/atualiza_disciplina_ofer.php
// app/sagu/academico/disciplina_ofer.php
$sql_periodos_academico    = "
SELECT 'Selecione o Periodo',
    '' union all select id||' / '||substr(descricao, 0, 25) as d,
    id
FROM periodos
ORDER BY 1 DESC;";

?>
