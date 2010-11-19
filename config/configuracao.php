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
$database = 'academico_campuscar';
$user     = 'academico_user';
$password = 'u0sodQFhArqOELlBNKxJ';
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
$param_conn_aluno['user']     = 'aluno';
$param_conn_aluno['password'] = 'eokQdfaBO9M8CY6snUzy';
$param_conn_aluno['port']     = $port;

/**
 * HTML Padrao
 */
$DOC_TYPE       = '<meta http-equiv="Content-Type" content="text/html; charset= UTF-8">';

/**
 * Variaveis do sistema
 */
$BASE_URL       = 'http://'. $_SERVER['HTTP_HOST'] . '/~wasare/dev/netbeans-php/academico/';
$BASE_DIR       = dirname(dirname(realpath(__FILE__))) .'/';
$LOGIN_URL      = $BASE_URL .'index.php';
$LOGIN_LOG_FILE = $BASE_DIR .'app/sagu/logs/login.log';
$PATH_IMAGES    = $BASE_URL."public/images/";
$REVISAO 	= @file_get_contents($BASE_DIR .'VERSAO.TXT');
$SESS_TABLE     = 'sessao';
$PAPEIS_SA = array('secretaria' => 1, 'administrador' => 2);
$PAPEIS_WEB_DIARIO = array('professor' => 3, 'coordenador' => 0);
$PAPEIS_ADMINISTRADOR = array('administrador' => 2);
$EMAIL_ADMIN = 'gti.bambui@ifmg.edu.br';

/*
   ALGUNS PARAMETROS DO SISTEMA ACADEMICO
   ** acima de cada parametro os respectivos arquivos onde sao utilizados **
*/

// app/diagrama.php
// public/help.php
$IEnome     = 'Instituto Federal Minas Gerais';

// app/index.php
// public/help.php
$IEurl      = 'http://www.ifmg.edu.br/bambui';

// app/sagu/academico/cursos_disciplinas_edita.phtm
// app/relatorios/integralizacao_curso/lista_integralizacao_curso.php
$curriculos["M"] = "M&iacute;nimo";
$curriculos["C"] = "Complementar";
$curriculos["O"] = "Optativa";
$curriculos["P"] = "Profici&ecirc;ncia";
$curriculos["A"] = "Atividade complementar";

// app/sagu/academico/cursos_disciplinas_edita.phtm
$historico["S"]  = "Sim";
$historico["N"]  = "N&atilde;o";

// app/sagu/academico/curso_altera.phtml 
// app/sagu/academico/lista_disciplinas_ofer.phtml
$status["1"]     = "Sim";
$status["0"]     = "N&atilde;o";

// app/sagu/academico/pessoaf_edita.phtml
// app/sagu/academico/documentos_edita.phtml
// app/sagu/academico/post/confirm_pessoaf_inclui.phtml
$opcoes["t"]     = "Sim";
$opcoes["f"]     = "N&atilde;o";

// app/sagu/academico/pessoaf_edita.phtml 
// app/sagu/academico/post/confirm_pessoaf_inclui.phtml 
$estados_civis["S"] = "Solteiro";
$estados_civis["C"] = "Casado";
$estados_civis["V"] = "Vi&uacute;vo";
$estados_civis["D"] = "Desquitado";
$estados_civis["U"] = "Uni&atilde;o est&aacute;vel";
$estados_civis["E"] = "Solteiro emancipado";

// app/sagu/generico/post/lista_areas_ensino.php3
// app/sagu/generico/post/lista_cidades.php3
// app/sagu/generico/post/lista_escolas.php3
// app/sagu/generico/post/lista_professores.php3
// app/sagu/generico/post/lista_pessoas.php3
// app/sagu/generico/post/lista_sql.php3
// app/sagu/academico/consulta_disciplinas_equivalentes.phtml
$limite_list        = 25;


// app/sagu/academico/periodos_altera.phtml
// app/sagu/academico/novo_contrato.phtml
// app/sagu/academico/periodos.phtml
// app/sagu/academico/alterar_contrato.phtml
// app/sagu/academico/atualiza_disciplina_ofer.phtml
// app/sagu/academico/disciplina_ofer.phtml
$sql_periodos_academico    = "
SELECT 'Selecione o Periodo',
    '' union all select id||' / '||substr(descricao, 0, 25) as d,
    id
FROM periodos
ORDER BY 1 DESC;";

?>
