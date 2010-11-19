<?php

require_once(dirname(__FILE__) .'/../../config/configuracao.php');
require_once($BASE_DIR .'core/data/connection_factory.php');
require_once($BASE_DIR .'core/login/session.php');
require_once($BASE_DIR .'core/date.php');
require_once($BASE_DIR .'core/number.php');
require_once($BASE_DIR .'core/situacao_academica.php');

// Inicia a sessao
$sessao = new session($param_conn);
$conn = new connection_factory($param_conn_aluno);

/*
 * Verifica se as variaveis de sessao do usuario foram setadas
 */
if(isset($_SESSION['sa_aluno_user']) and $_SESSION['sa_aluno_user'] != '') {
    $user  = $_SESSION['sa_aluno_user'];
    $senha = $_SESSION['sa_aluno_senha'];
    $nasc  = $_SESSION['sa_aluno_nasc'];
}else {
    /*
     * Verifica se o formulario de autenticacao
     * enviou parametros
     */
    if($_POST['user']
            and $_POST['senha']
            and $_POST['nasc']
    ) {
        $user  = (int) $_POST['user'];
        $senha = md5($_POST['senha']);
        $nasc  = addslashes($_POST['nasc']);

        $_SESSION['sa_aluno_user']  = $user;
        $_SESSION['sa_aluno_senha'] = $senha;
        $_SESSION['sa_aluno_nasc']  = $nasc;
    }else {
        /*
         * Em caso de sessao expirada retorna para
         * o formulario de autenticacao
         */
        header('location: index.php');
    }
}

/*
 * Verifica a autenticacao do usuario na base dados
*/
$qryUsuarioCont = "
SELECT COUNT(*) FROM acesso_aluno a, pessoas b
WHERE
    a.ref_pessoa = $user AND
    b.id = $user AND
    dt_nascimento = '$nasc' AND
    a.senha = '$senha'; ";

$AlunoCont = $conn->get_one($qryUsuarioCont);

if ($AlunoCont != 1) {
    print '<script language=javascript>
           window.alert("Usuário e/ou senha inválido(s)");
           javascript:window.history.back(1);
           </script>';
    exit;
}
/*
else {
    // VERIFICA MATRICULA NO PERIODO CORRENTE
    $m = date("m");

    if($m > 7) {
        $m = '06';
    } else {
        $m = '01';
    }

    $DataInicial = date("01/01/2006");

    $qryPeriodoCont = '
        SELECT DISTINCT COUNT(*)
        FROM matricula a, pessoas b, disciplinas c, periodos d, cursos e
        WHERE
            a.ref_disciplina
            IN (
                SELECT DISTINCT a.ref_disciplina
                FROM matricula a, disciplinas b
                WHERE
                    a.ref_disciplina = b.id AND
                    a.ref_motivo_matricula = 0 AND
                    a.ref_pessoa = %s
            ) AND
            d.dt_final >= \'%s\' AND
            a.ref_curso = e.id AND
        a.ref_periodo = d.id AND
        a.ref_disciplina = c.id AND
        a.ref_pessoa = b.id AND
        a.ref_pessoa = %s;';

    $aluno = $user;
    $data = $DataInicial;

    $MatCont = $conn->get_one(sprintf($qryPeriodoCont,$aluno, $data, $aluno));

    if ($MatCont == 0) {
        print '
            <script language=javascript>
                window.alert("Matrícula para o período corrente não encontrada!");
                javascript:window.history.back(1);
            </script>';
        exit;
    }
}
*/
?>
