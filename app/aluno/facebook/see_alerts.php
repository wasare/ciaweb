<?php
$AppID          = "338115866266320";
$AppSecret      = "3aeab59509c17ef4dfa32bf87a585211";
require_once('AppInfo.php');
require_once('utils.php');
require "src/facebook.php";
 
$facebook       = new Facebook( array( "appId"  => $AppID, "secret" => $AppSecret ) );
$UserLogado     = $facebook->getUser();

$Lista = $facebook->api($UserLogado . '/apprequests', 'GET');

if(!$UserLogado)
{
	$Params 	= array	(
						  scope 		=> 'read_stream, status_update, email, user_birthday',
						  redirect_uri	=> 'https://apps.facebook.com/ifspciaweb'
						);

	$LoginUrl = $facebook->getLoginUrl($Params);
	//echo '<meta http-equiv="refresh" content="1;url='. $LoginUrl .'" />';
	echo header('Location:'.$LoginUrl);
}
else{
	$basic = $facebook->api('/me');
?>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
  <head>
    <meta charset="utf-8" />
	<script type="text/javascript" src="/javascript/jquery-1.7.1.min.js"></script>
	<link rel="stylesheet" href="stylesheets/screen.css" media="Screen" type="text/css" />
  </head>
  <body>
    <table width="100%">
		<td align="right">
			<p>Compartilhar este aplicativo:</p>
				<a href="publish.php" class="facebook-button" id="postToWall" data-url="<?php echo AppInfo::getUrl(); ?>">
					<span class="plus">Publicar no mural</span>
				</a>
		</td>
		</table>
	<br />
    <?php 
		include('includes/topoFace.htm'); 
		include('includes/menuFace.html');
		echo '<br /><br />';
		
		if( count($Lista["data"]) > 0 )
		{
		?>
 
        <?php 
			for( $i=0; $i<count($Lista["data"]); $i++ ){ ?>
 
				<a href="?Acao=DelAviso&Codigo=<?=$Lista["data"][$i]["id"]?>">
                <?php echo $Lista["data"][$i]["message"]; ?>
				</a>
				<Br />
 
        <?php } ?>
		<?php
			}else{
				echo 'Nenhum aviso encontrado!';
			}
    ?>
  </body>
</html>
<?php	
}
?>