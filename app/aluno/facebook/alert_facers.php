<?php
/*-------------------------------Todas as mudanças neste documento foram realizadas pelo aluno Victor Ulisses Pugliese----------------------*/
//viktao@msn.com || (12)81071139
//Este arquivo envia as famosas requisições do facebook para os usuários do CIAWEB, cujo acesso esteja desatualizado ou apenas para informar a
//atualização de uma notícia.

//chama a classe de conexão do facebook sdk;
require "src/facebook.php";
//instacia as variáveis que identificam a app;
$AppID = "338115866266320";
$AppSecret = "3aeab59509c17ef4dfa32bf87a585211";
$facebook 		= new Facebook( array( "appId"  => $AppID, "secret" => $AppSecret ) );

//Instancia a conexao com o banco de dados postgres
$str_conexao="host=localhost port=5432 user=ciaweb_demo_user password=aMwygpeGStWzuKxsTBIdEkl2Xq1f4r dbname=ciaweb_demo"; //Conexão ao Demo
//$str_conexao="host=localhost port=5432 user=postgres password=1234 dbname=ciaweb_24042012"; //Conexão no meu micro;
$conexao=pg_connect($str_conexao);

$today = date('d/m/Y');            

$msg_erros_possiveis = "\n----------------Histórico do dia : $today-------------- \n";

//Verifica se a conexão obteve sucesso ou não;
if (!$conexao){
    echo "Houve erro ao conectar ao banco" . pg_last_error();
    echo "<br><br>Informe a mensagem acima ao suporte pelo fone 9999 ou pelo e-mail suporte@suporte.com.br <br>" . $conexao;    
	$msg_erros_possiveis .= 'Houve erro ao conectar ao banco:' . pg_last_error() . '\n';
}
else
{
	$msg_erros_possiveis .= 'Conexão com o banco de dados efetuada com sucesso!';
//Sql para captura dos facebook ids e dias que se passaram desde o último acesso do usuário ao programa;
	$sql_getFacebookIds = 
	'SELECT distinct(aa.facebook_id), age(aa.ultimo_acesso)
		FROM matricula m, acesso_aluno as aa inner join diario_notas as dn on aa.ref_pessoa = dn.id_ref_pessoas 
			inner join periodos p on dn.id_ref_periodos = p.id 
				WHERE facebook_id IS NOT NULL  AND m.ref_motivo_matricula=0';
	$result = pg_query($sql_getFacebookIds);
	//Verifica se houve erros durante a consulta;
	if(!$result)
	{
		$msg_erros_possiveis .= 'Ocorreu um erro na consulta sql:\n
		SELECT distinct(aa.facebook_id), age(aa.ultimo_acesso)\n
			FROM matricula m, acesso_aluno as aa inner join diario_notas as dn on aa.ref_pessoa = dn.id_ref_pessoas\n
				inner join periodos p on dn.id_ref_periodos = p.id\n 
					WHERE facebook_id IS NOT NULL  AND m.ref_motivo_matricula=0\n';
	}
	else
	{
		$msg_erros_possiveis .= 'Consulta sql para captura de facebook ids e dias que se passaram desde o último acesso executada com sucesso!\n';
		//Verifica se ela retornou mais de uma linha;
		if(count($result) > 0)
		{
		//Varre a tabela retornada da consulta;
			while ($row = pg_fetch_array($result)) {
				$split = array();
				//Separa a string com o resultado do tempo já passado desde o último acesso ao boletim
				$split = explode(' ',$row[1]);
				//Se o vetor com o resultado do tempo tiver mais de duas posições significa que ele possui algo além do (<número de dias> days)...
				if(count($split) > 2)
				{
				//Logo obrigatoriamente já se passaram mais que 15 dias e ele envia um alerta para o aluno;
					$Log = $facebook->api( $row[0] . '/apprequests', 'POST', 
								array('message' => "$today : Aten&ccedil&atildeo j&aacute faz mais de 15 dias 
								que voce n&atildeo consulta o CIAWEB, por favor entre e verifique se houve alguma 
								mudan&ccedila em seu boletim!") );
				}
				else
				{
					//Quando não, ele verifica se a primeira posição que é referente apenas aos dias que se passaram é maior que 15.
					if($split[0] > 15)
					{
						//Se sim, ele envia o alerta.
						$Log = $facebook->api( $row[0] . '/apprequests', 'POST', 
									array('message' => "$today : Aten&ccedil&atildeo j&aacute faz mais de 15 dias 
									que voce n&atildeo consulta o CIAWEB, por favor entre e verifique se houve alguma 
									mudan&ccedila em seu boletim!") );
					}
				}
			}
		}
	}
	//Sql para capturar todos os usuários do facebook
	$sql_getFacebookIds = 'SELECT distinct(facebook_id) FROM acesso_aluno WHERE facebook_id IS NOT NULL;';
	$result = pg_query($sql_getFacebookIds);
	//Verifica se houve erro na consulta;
	if(!$result)
	{
		$msg_erros_possiveis .= 'Ocorreu um erro na consulta sql:\n
		SELECT distinct(facebook_id) FROM acesso_aluno WHERE facebook_id IS NOT NULL;\n';
	}
	else
	{
		if(count($result) > 0)
		{
			$msg_erros_possiveis .= 'Consulta sql para capturar todos os usuários do facebook executada com sucesso!\n';
			//Se não, varre a tabela atrás dos valores retornados
			while ($row = pg_fetch_array($result)) {
				//Le o feed de notícias do ifsp;
				$feed = 'http://www.ifspcaraguatatuba.edu.br/feed/';
				foreach ( simplexml_load_file($feed)->channel->item as $item ){
					//Sql para verificar se o feed já foi cadastrado no banco de dados;
					$sql_getNews = 
						"SELECT id FROM avisos where id = 2 and descricao='$item->link'";
					$res = pg_query($sql_getNews);
					if(count($res) > 0)
					{
						$msg_erros_possiveis .= 'Consulta sql para verificar se o feed já foi cadastrado no banco de dados executada com sucesso!\n';
						if($r[0] == "")
						{
							//Se não foi, ele envia uma msg para todos os usuários cadastrados na base de dados;
							$Log = $facebook->api( $row[0] . '/apprequests', 'POST', 
							array('message' => "$today : Aten&ccedil&atildeo nova not&iacutecia no IFSP Caraguatatuba, veja: <a href='$item->link'>". $item->title ."</a>") );				
							pg_query("UPDATE avisos SET descricao = '$item->link', data = '$item->pubDate' WHERE id = 2;");
						}
						break;
					}
					else
					{
						$msg_erros_possiveis .= 'Ocorreu um erro na consulta sql:\n
						SELECT id FROM avisos where id = 2 and descricao=' . $item->link;
						break;
					}
				}
			}
		}
	}
}
pg_close($str_conexao);

//Salva as informações no histórico da app;
$nome_arquivo = 'historico.txt';

if ($fp = fopen($nome_arquivo,'a') ) {
fwrite($fp, $msg_erros_possiveis);
fclose($fp);
}
//Atualiza a cada 24horas a página;
echo '<meta http-equiv="refresh" content="' . 60*60*24 . ';url=?http:ciaweb.ifspcaraguatatuba.edu.br/demo/app/aluno/facebook/alert_facers.php" />';

?>