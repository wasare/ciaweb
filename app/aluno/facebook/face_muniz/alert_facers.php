<?
$AppID          = "338115866266320";
$AppSecret      = "3aeab59509c17ef4dfa32bf87a585211";
 
require "src/facebook.php";
$facebook 		= new Facebook( array( "appId"  => $AppID, "secret" => $AppSecret ) );
$UserLogado 	= $facebook->getUser();
$today = date('d/m/Y');

//Routine enviar msg sobre notícias e sobre dias sem uso do CIAWeb;

//Colocar a verificação pra ver se o aluno está matriculado.
$sql_getFacebookIds = 'SELECT facebook_id FROM acesso_aluno WHERE DATEDIFF(ultimo_acesso, now()) >= 15';
$getFacebookIds = $conn->get_all($sql_getFacebookIds);
if(count($getFacebookIds) > 0)
{
	foreach ($getFacebookIds as $getId)
	{
		$Log = $facebook->api( $getId['facebook_id'] . '/apprequests', 'POST', array('message' => "$today : Aten&ccedil&atildeo já fazem 15 dias ou mais que voce n&atildeo consulta o CIAWeb, por favor entre e verifique se houve alguma mudan&ccedila em seu boletim!") );
	}
}

$sql_getFacebookIds = 'SELECT facebook_id FROM acesso_aluno';
$getFacebookIds = $conn->get_all($sql_getFacebookIds);

if(count($getFacebookIds) > 0)
{
	foreach ($getFacebookIds as $getId)
	{
		$feed = 'http://www.ifspcaraguatatuba.edu.br/feed/';
		foreach ( simplexml_load_file($feed)->channel->item as $item ){
			
			$Log = $facebook->api( $getId['facebook_id'] . '/apprequests', 'POST', array('message' => "$today : Aten&ccedil&atildeo nova notícia no IFSP Car, leia: " $item->title) );
			
			$Data = array(
				'message'       => $item->title,
				'picture'       => '',
				'link'          => $item->link,
				'name'          => 'IFSP Caraguatatuba - CIAWeb',
				'caption'       => '#FicaDica',
				'description'   => $item->description,
				'actions'       => array('name' => 'Compartilhar', 'link' => $item->link),
			);
			$Retorno = $facebook->api('/' . $getId['facebook_id'] . 'me/feed', 'POST', $Data);
			
			break;
		}
	}
}

echo "<script>alert('IFSP CIAWeb - Rotinas diárias realizadas com sucesso!');</script>";
echo '<meta http-equiv="refresh" content="' . 60*60*24 . ';url=?http:ciaweb.ifspcaraguatatuba.edu.br/demo/app/aluno/facebook/alert_facers.php" />'
break;
?>