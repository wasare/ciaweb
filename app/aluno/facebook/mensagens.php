<?php
/*-------------------------------Todas as mudanças neste documento foram realizadas pelo aluno Victor Ulisses Pugliese---------------------------------*/
//viktao@msn.com || (12)81071139
//Quando o usuário clicar sobre umas das duas ações (DelAviso / DelTodos) desta página ele entra nesse switch de verificação...
	switch($_REQUEST["Acao"])
	{
//A primeira verificação olha se está ação é delAviso, se sim...	
		case "1":
//Ele pega o código da requisição;
			$Codigo		= $_REQUEST["Codigo"];
//Envia o comando delete app request;
			$facebook->api($Codigo, 'DELETE');
//Mostra mensagem de sucesso, limpa os campos, além de atualizar a página;
			echo "<script>alert('ID " . $Codigo . " deletado com sucesso!');</script>";
			echo '<meta http-equiv="refresh" content="0;url=?mensagens=true" />';
			$_GET = array();
			unset($_GET);
			break;
//A segunda verificação olha se está ação é DelTodos, se sim....
		case "2":
			$Lista = $facebook->api('me/apprequests', 'GET');
//Ele varre todas as requisições;
			for( $i=0; $i<count($Lista["data"]); $i++ )
			{
//Exclui uma por uma;
				$facebook->api($Lista["data"][$i]["id"], 'DELETE');
			}
//Mostra mensagem de sucesso;
			echo "<script>alert('Todos avisos deletados com sucesso!');</script>";
			echo '<meta http-equiv="refresh" content="0;url=?mensagens=true" />';
			$_GET = array();
			unset($_GET);
			break;
		default :
			echo "";
	}
//Carrega todas as requisições enviadas para o usuário
	if( count($Lista["data"]) > 0 )
	{
		echo '
		<h1>Caixa de mensagens</h1>
		<br />
		<table>
		<tr />
		  <td><b><font color="#419C01">Data &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp: Aviso</font></b></td><td><a href="?mensagens=true&Acao=2"><font color="#419C01">Excluir</font></a></td>
		<tr />';
		for( $i=0; $i<count($Lista["data"]); $i++ ){
			echo '<td>' . $Lista["data"][$i]["message"] . ' </td>'; ?>
				<td><a href="?mensagens=true&Acao=1&Codigo=<?=$Lista["data"][$i]["id"]?>">
					<? echo '<center><font color="red">[x]</font></center>'; ?>
				</a></td><tr />
		<?php
		}
		echo '</table>';
	}else{
		echo 'Nenhum aviso encontrado!';
	}
	echo '<br /><br />';
?>  