<?php
$AppID			= "338115866266320";
$AppSecret		= "3aeab59509c17ef4dfa32bf87a585211";

require_once('AppInfo.php');
require_once('utils.php');
require "src/facebook.php";

$facebook 		= new Facebook( array( "appId"  => $AppID, "secret" => $AppSecret ) );
$UserLogado 	= $facebook->getUser();


if(!$UserLogado)
{
	$expire=time()+60*60*24*30;
	setcookie("user", $_REQUEST["user"] , $expire);
	$Params 	= array	(
						  scope 		=> 'read_stream, status_update, email',
						  redirect_uri	=> 'https://apps.facebook.com/ifspciaweb'
						);

	$LoginUrl = $facebook->getLoginUrl($Params);
	echo '<meta http-equiv="refresh" content="0;url='. $LoginUrl .'" />';
}
else{
	$sql_getRsPessoa = 'SELECT rs_pessoa FROM acesso_aluno WHERE facebook_id='.$UserLogado;
	$getRsPessoa = $conn->get_all($sql_getRsPessoa);
	if(!count($getRsPessoa)>0)
	{
		$sql_updateAcessoAluno = 'UPDATE acesso_aluno 
									set facebook_id = ' . $UserLogado . '
										ultimo_acesso = ' . date('d/m/Y');
		$conn->execute($sql_updateAcessoAluno);
	}
	else
	{
		$sql_updateAcessoAluno = 'UPDATE acesso_aluno 
									set ultimo_acesso = ' . date('d/m/Y');
		$conn->execute($sql_updateAcessoAluno);
	}
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
				<a href='compartilhar.php?titulo=Curta e compartilhe!&mensagem=Além de toda a infra já oferecida, conheça agora o CIAWeb no facebook' class="facebook-button" id="postToWall">
					<span class="plus">Publicar no mural</span>
				</a>
		</td>
		</table>
	<br />
    <?php 
		include('includes/topoFace.htm'); 
		include('includes/menuFace.html');
		echo '<br /><br />';
		switch($_REQUEST["page"])
		{
			case "boletim":
				include("boletim.php");
				echo '<meta http-equiv="refresh" content="360;url=?page=index" />';
				break;
			case "ecad":
				include("ecad.php");
				break;
			case "mensagens":
				include("mensagens.php");
				echo '<meta http-equiv="refresh" content="30;url=?page=mensagens" />';
				break;
			case "compartilhado":
				echo "<script>alert('Compartilhado com sucesso!');</script>";
			default:
				echo '<h1>Seja bem-vindo,'.he(idx($basic, 'name')).'</h1>';
				echo '<br />';
				$feed = 'http://www.ifspcaraguatatuba.edu.br/feed/';
				$i=0;
				foreach ( simplexml_load_file($feed)->channel->item as $item ){
					echo '<a target="_blank" href="' . $item->link .'"> <font color="#419C01">' . $item->title . '</font></a> <br /> ' . $item->pubDate . '<br />' . $item->description .'<p />
					<a href="compartilhar.php?titulo='. $item->title . '&mensagem=' . $item->description .'&link='.$item->link.'"> <font size="0.4em">Compartilhar</font></a>
					
					<br>--------<br><br>';
					$i++;
					if($i==4)
						{ break; }
				}
				echo '<meta http-equiv="refresh" content="120;url=?page=index" />';
				break;
		}
    ?>
		<div align="center">
			<p class="texto1 style1">
				<strong>Controle de Informa&ccedil;&otilde;es Acad&ecirc;micas WEB</strong> - &Aacute;rea do aluno
				<br />
				&copy;2012  Instituto Federal S&atilde;o Paulo - Caraguatatuba
			</p>
		</div>
  </body>
</html>
<?php	
}
?>
