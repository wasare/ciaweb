<?
$AppID          = "338115866266320";
$AppSecret      = "3aeab59509c17ef4dfa32bf87a585211";
 
require "src/facebook.php";
 
$facebook       = new Facebook( array( "appId"  => $AppID, "secret" => $AppSecret ) );
$UserLogado     = $facebook->getUser();
$msg = $_REQUEST['mensagem']; 
$titulo = $_REQUEST['titulo'];
$link;
if($_REQUEST['link'])
	{	$link = $_REQUEST['link'];	}
else
	{		}

if($UserLogado)
{
 
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
	echo '<meta http-equiv="refresh" content="0;url=http:apps.facebook.com/ifspciaweb/?page=compartilhado" target="_parent"/>';
	break;
}
else
{
	echo '<meta http-equiv="refresh" content="0;url=index" />';
	break;
}
?> 
