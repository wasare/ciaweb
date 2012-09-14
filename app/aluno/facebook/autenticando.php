<?
$AppID			= "338115866266320";
$AppSecret		= "3aeab59509c17ef4dfa32bf87a585211";

require "src/facebook.php";

$facebook 		= new Facebook( array( "appId"  => $AppID, "secret" => $AppSecret ) );
$UserLogado 	= $facebook->getUser();

if($UserLogado)
{

	$UID 	= $UserLogado;
	$user 	= $facebook->api('/me');  

	echo "UID: " . $UID . "<br>";
	echo "Nome: " . $user["name"] . "<br>";
	echo "Link: " . $user["link"] . "<br>";
	echo "Username: " . $user["username"] . "<br>";
	echo "Data de Nascimento: " . $user["birthday"] . "<br>";
	echo "Sexo: " . $user["gender"] . "<br>";
	echo "E-mail: " . $user["email"];

}else{

	//https://developers.facebook.com/docs/reference/fql/
	$Params 	= array	(
						  scope 		=> 'read_stream, status_update, email, user_birthday',
						  redirect_uri	=> 'http://apps.facebook.com/ciaweb'
						);

	$LoginUrl 	= $facebook->getLoginUrl($Params);

	header("Location: " . $LoginUrl);

}
?>