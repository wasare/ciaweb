<?php
/*-------------------------------Todas as mudanças neste documento foram realizadas pelo aluno Victor Ulisses Pugliese----------------------*/
//viktao@msn.com || (12)81071139
//Este arquivo serve como index para o CIAWEB no facebook, ele tem por funções efetuar login no Facebook, listar o feed de notícias do Campus,
//e em certos momentos quando ação acionada compartilhar novidades com seus contatos na rede do Facebook.

//Códigos de identificação da app necessários para conexão ao aplicativo.
$AppID			= "338115866266320";
$AppSecret		= "3aeab59509c17ef4dfa32bf87a585211";
//Chama as .dll do facebook + a classe de configuração do banco de dados;
require_once('AppInfo.php');
require_once('utils.php');
require "src/facebook.php";
require_once('../../../config/configuracao.php');
//Instancie objeto facebook e tenta efetuar conexão com o método $facebook->getUser() na app;
$facebook 		= new Facebook( array( "appId"  => $AppID, "secret" => $AppSecret ) );
$UserLogado 	= $facebook->getUser();

$today = date('d/m/Y');

if(empty($_COOKIE['user']))
{
	$expire=time()+60*60;
	setcookie("user", null , $expire);
}

if(isset($_GET) && !empty($_GET['user']))
{
	$expire=time()+60*60;
	setcookie("user", $_GET["user"] , $expire);
	$_GET['user'] = null;
	$_GET = array();
	unset($_GET);
}

//Se o usuário não tiver conectado na app, então oferece-lhe a possibilidade de egressar nesta;
if(!$UserLogado)
{
	$Params 	= array	(
						  scope 		=> 'read_stream, status_update, email',
						  redirect_uri	=> 'https://apps.facebook.com/ifspciaweb'
						);

	$LoginUrl = $facebook->getLoginUrl($Params);
	echo '<meta http-equiv="refresh" content="0;url='. $LoginUrl .'" />';
}
//Caso já esteja conectado, continue a execução da app;
else{
//Conecta a base de dados do CIAWEB;
	$str_conexao="host=localhost port=5432 user=ciaweb_demo_user password=aMwygpeGStWzuKxsTBIdEkl2Xq1f4r dbname=ciaweb_demo"; //Conexão ao Demo
	//$str_conexao="host=localhost port=5432 user=postgres password=1234 dbname=ciaweb_24042012"; //Conexão no meu micro;
	$conexao=pg_connect($str_conexao);

	$today = date('d/m/Y');            

	//Verifica se a conexão obteve sucesso ou não;
	if (!$conexao){
		echo "Houve erro ao tentar conectar na base de dados correspondente ao número:" . pg_last_error();
		echo "<br><br>Informe a mensagem acima ao suporte no IFSP suporte@suporte.com.br <br>";    
		exit;
	}
	else
	{
		//Verifica se há um usuário já cadastrado a base com o facebook id correspondente;
		$sql_getUser = 'SELECT * FROM acesso_aluno WHERE facebook_id='.$UserLogado;
		$res = pg_query($sql_getUser);
		if(count($res) > 0)
		{
			while ($row = pg_fetch_array($res)) {
				if(!empty($row[0]))
				{
					//echo "Você está conectado como aluno do Campus Caraguatatuba";
				}
				else
				{
					//Se a consulta não trouxer resultados...
					if($_COOKIE['user'] == null)
					{
						//E não tiver nada armazenado no cookie, usuário não entrou pelo portal CIAWEB e sem o código ref_pessoa não da pra vincular sua conta aluno ao facebook;
						echo "Você add a app ao seu perfil como convidado, caso você seja um aluno do ifsp conecte-se pelo portal CIAWEB ao menos uma vez para atualizarmos sua conta!";
					}
					else
					{
						//Senão, tentar fazer update na base para adicionar o facebook id a sua conta aluno.
						$sql_updateAcessoAluno = 'UPDATE acesso_aluno 
													set facebook_id = ' . $UserLogado . ', ultimo_acesso = NOW() 
														where ref_pessoa=' . $_COOKIE['user'] . ';';
						pg_query($sql_updateAcessoAluno) or die('Erro ao executar comando de atualização da tabela!');
					}
				}
				break;
			}
		}
	}
	pg_close($str_conexao);
//Pega as informações básicas sobre o usuário no facebook;
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
<!-- Este link serve para o compartilhamento da novidade do CIAWEB no facebook, 
qual a lógica dele? R: São atribuidos por meio do método GET os valores do título e da mensagem e a página é redirecionada para ela mesma -->
			<p>Compartilhar este aplicativo:</p>
			<a href='?titulo=Curta e compartilhe!&mensagem=Al&eacute;m de toda a infra j&aacute; oferecida, conhe&ccedil;a agora o CIAWEB no facebook' class="facebook-button" id="postToWall">
				<span class="plus">Publicar no mural</span>
			</a>
		</td>
		</table>
	<br />
    <?php
		include('includes/topoFace.htm'); 
		include('includes/menuFace.html');
		echo '<br /><br />';
//Faz a verificação se o usuário clicou no link "mensagens" dentro do menuFace.html que é exibido na index, se sim ele chama a página por meio de um include;
		if(isset($_GET) && !empty($_GET['mensagens']))
		{
			include("mensagens.php");
			echo '<meta http-equiv="refresh" content="60;url=?mensagens=true" />';
			$_GET = array();
			unset($_GET);
			$_GET['mensagens'] = '';
		}
//Se não, ele continua a app executando o código da index;
		else 
		{
//Exibe a mensagem de Bem-vindo com o nome do usuário que adicionou a app;
			echo '<h1>Seja bem-vindo '.he(idx($basic, 'name')).'!</h1>';
			echo '<br />';
			$feed = 'http://www.ifspcaraguatatuba.edu.br/feed/';
			$i=0;
//Mostra os últimos 4 posts do site do IFSP Caraguatatuba;
			foreach ( simplexml_load_file($feed)->channel->item as $item ){
/*Este link serve para o compartilhamento das notícias do CIAWEB no facebook, 
qual a lógica dele? R: São atribuidos por meio do método GET os valores do título e da mensagem e a página é redirecionada para ela mesma*/
				echo '<a target="_blank" href="' . $item->link .'"> <font color="#419C01">' . $item->title . '</font></a> <br /> ' . $item->pubDate . '<br />' . $item->description .'<p />
				<a href="?titulo='. $item->title . '&mensagem=' . $item->description .'&link='.$item->link.'"> <font size="0.4em">Compartilhar</font></a>	
				<br>--------<br><br>';
				$i++;
				if($i==4)
				{ break; }
			}
			echo '<meta http-equiv="refresh" content="120;url=?" />';
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
//Caso ele tenha clicado em um dos links de compartilhamento...
if(isset($_GET) && !empty($_GET['titulo']))
{
	//Ele instancia as variáveis necessárias, $msg, $titulo, $link...
	$msg = $_REQUEST['mensagem']; 
	$titulo = $_REQUEST['titulo'];
	$link = $_REQUEST['link'];
	if(empty($link))
	{
		$link = 'https://apps.facebook.com/ifspciaweb';	
	}
	//Monta a array para ser compartilhada...
	$Data = array(
		'message'       => $titulo,
		'picture'       => 'http://189.108.236.229/srt/modules/mod_minifrontpage/images/default_thumb.gif',
		'link'          => $link,
		'name'          => 'IFSP Caraguatatuba - CIAWEB',
		'caption'       => '#FicaDica',
		'description'   => $msg,
		'actions'       => array('name' => 'veja aqui!', 'link' => "$link"),
	);
	//Publica a mensagem no facebook;
	$Retorno = $facebook->api('/me/feed', 'POST', $Data);
	//Depois de feito o processo, limpa as variáveis e mostra a mensagem de sucesso!;
	$_GET = array();
	unset($_GET);
	echo "<script>alert('Compartilhado com sucesso!');</script>";
}
?>