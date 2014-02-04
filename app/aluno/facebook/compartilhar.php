<?
$AppID          = "338115866266320";
$AppSecret      = "3aeab59509c17ef4dfa32bf87a585211";
 
require "src/facebook.php";
echo '<meta http-equiv="refresh" content="1;url=https://apps.facebook.com/ifspciaweb/?page=compartilhado" target="_parent"/>'; 

$facebook       = new Facebook( array( "appId"  => $AppID, "secret" => $AppSecret ) );
$UserLogado     = $facebook->getUser();
$msg = $_REQUEST['mensagem']; 
$titulo = $_REQUEST['titulo'];
$link = $_REQUEST['link'];
if(empty($link))
{
	$link = 'https://apps.facebook.com/ifspciaweb';	
}
$Data = array(
        'message'       => $titulo,
        'picture'       => '',
        'link'          => $link,
        'name'          => 'IFSP Caraguatatuba - CIAWeb',
        'caption'       => '#FicaDica',
        'description'   => $msg,
        'actions'       => array('name' => 'Maiores Info.', 'link' => "$link"),
    );
$Retorno = $facebook->api('/me/feed', 'POST', $Data);
?> 
