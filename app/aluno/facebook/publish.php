<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.alert("Publicado com sucesso!")
	// -->
</SCRIPT>
<?
$AppID          = "338115866266320";
$AppSecret      = "3aeab59509c17ef4dfa32bf87a585211";
 
require "src/facebook.php";
 
$facebook       = new Facebook( array( "appId"  => $AppID, "secret" => $AppSecret ) );
$UserLogado     = $facebook->getUser();
 
$Data               = array(
                            'message'       => 'CIAWEB, O portal de informacoes do IFSP, no facebook!',
							//Arrumar imagem com dimensões 198x125;
                            'picture'       => 'https://ciaweb.ifspcaraguatatuba.edu.br/demo/app/aluno/victor/includes/ifspMobile.jpg',
                            'link'          => 'http://apps.facebook.com/ifspciaweb',
                            'name'          => 'IFSP - Caraguatatuba',
                            'caption'       => '#FicaDica',
                            'description'   => 'Alem da infra ja ofericida, agora voce pode acessar o CIAWEB aqui no face.',
                            'actions'       => array('name' => 'CIAWEB - IFSP Caraguatatuba', 'link' => 'http://apps.facebook.com/ifspciaweb'),
                            );
    $Retorno            = $facebook->api('/1614919733/feed', 'POST', $Data);  
	echo header("Location: https://apps.facebook.com/ifspciaweb");

?>