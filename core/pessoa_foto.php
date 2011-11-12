<?php

// Informa o curso quando acessando do web diario
$curso = (int) $_GET['curso'];
$pessoa_id = (int) $_GET['id'];

if(is_numeric($pessoa_id) && $pessoa_id != 0) {

  require_once(dirname(__FILE__) .'/../config/configuracao.php');
  require_once($BASE_DIR .'core/data/connection_factory.php');
  require_once($BASE_DIR .'core/login/session.php');
  require_once($BASE_DIR .'core/login/auth.php');
  require_once($BASE_DIR .'core/web_diario.php');
  
  
  $session = new session($param_conn);

  // PROTEGE A FOTO DE ACESSO NÃO AUTORIZADO
  $acessa_foto = FALSE;
  $usuario = (string) @$_SESSION['sa_auth'];
  //echo $usuario;
    
  if (!empty($usuario))
    $acessa_foto = TRUE;
  // ^ PROTEGE A FOTO DE ACESSO NÃO AUTORIZADO ^
    
  //  VERIFICA O DIREITO DE ACESSO A FICHA COMO PROFESSOR OU COORDENADOR
  if(isset($_SESSION['sa_modulo']) && $_SESSION['sa_modulo'] == 'web_diario_login') {
    $acessa_foto = acessa_ficha_aluno($pessoa_id,$sa_ref_pessoa,$curso_id);
  }
  // ^ VERIFICA O DIREITO DE ACESSO A FICHA COMO PROFESSOR OU COORDENADOR ^ //

  // foto padrão
  $image = file_get_contents($BASE_DIR.'public/images/user.jpg');
  
  if ($acessa_foto) {
  
    // CONEXAO ABERTA PARA TRABALHAR COM TRANSACAO (NÃO PERSISTENTE)
    $conn = new connection_factory($param_conn);
  
    $sql = "SELECT encode(foto, 'base64') AS foto FROM pessoas_fotos WHERE ref_pessoa = $pessoa_id ;";
 
    $foto = (string) @$conn->get_one($sql);

    if(!empty($foto)) {
      $image = base64_decode($foto);
    }
  }
    
  // configura o header para o browser
  header('Content-type: image/jpeg');
  echo $image;   
  
}

?>
