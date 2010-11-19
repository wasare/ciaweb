<?php

function pessoa_foto($id,$curso_id=0) {

    require_once(dirname(__FILE__) .'/../config/configuracao.php');
    require_once($BASE_DIR .'core/login/session.php');
    require_once($BASE_DIR .'core/web_diario.php');

    $session = new session($param_conn);

    // PROTEGE A FOTO DE ACESSO NÃO AUTORIZADO
    $acessa_foto = FALSE;
    $usuario = (string) $_SESSION['sa_auth'];
    if (!empty($usuario))
      $acessa_foto = TRUE;     
    // ^ PROTEGE A FOTO DE ACESSO NÃO AUTORIZADO ^

    $image = file_get_contents($BASE_DIR.'/public/images/user.gif');
    
    if(isset($id) && is_numeric($id)) {
        $db = pg_connect(
            ' host     ='.$param_conn['host'].
            ' port     ='.$param_conn['port'].
            ' dbname   ='.$param_conn['database'].
            ' user     ='.$param_conn['user'].
            ' password ='.$param_conn['password']
        );

         //  VERIFICA O DIREITO DE ACESSO A FICHA COMO PROFESSOR OU COORDENADOR
        if(isset($_SESSION['sa_modulo']) && $_SESSION['sa_modulo'] == 'web_diario_login') {
              $acessa_foto = acessa_ficha_aluno($id,$sa_ref_pessoa,$curso_id,$db);
        }
        // ^ VERIFICA O DIREITO DE ACESSO A FICHA COMO PROFESSOR OU COORDENADOR ^ //

        if ($acessa_foto == TRUE) {
            $sql = 'SELECT foto
              FROM  pessoas_fotos
              WHERE ref_pessoa = '.$_GET['id'].'; ';

          $rs = pg_query($db, $sql);
          $numrows = pg_numrows($rs);

          if($numrows != 0) {
              $image = pg_unescape_bytea(pg_fetch_result($rs, 0, 0));
          }
          else {
            $image = file_get_contents($BASE_DIR.'/public/images/user.gif');
          }
          pg_close($db);
        }    
    }
    
    header("Content-type: image/jpeg");
    echo $image;
}

// Informa o curso quando acessando do web diario
$curso = (int) $_GET['curso'];

pessoa_foto($_GET['id'],$curso);

?>
