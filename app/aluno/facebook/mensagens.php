<?php
/*-------------------------------Todas as mudan�as neste documento foram realizadas pelo aluno Victor Ulisses Pugliese---------------------------------*/
//viktao@msn.com || (12)81071139
//Quando o usu�rio clicar sobre umas das duas a��es (DelAviso / DelTodos) desta p�gina ele entra nesse switch de verifica��o...
	switch($_REQUEST["Acao"])
	{
//A primeira verifica��o olha se est� a��o � delAviso, se sim...	
		case "1":
//Ele pega o c�digo da requisi��o;
			$Codigo		= $_REQUEST["Codigo"];
//Envia o comando delete app request;
			$facebook->api($Codigo, 'DELETE');
//Mostra mensagem de sucesso, limpa os campos, al�m de atualizar a p�gina;
			echo "<script>alert('ID " . $Codigo . " deletado com sucesso!');</script>";
			echo '<meta http-equiv="refresh" content="0;url=?mensagens=true" />';
			$_GET = array();
			unset($_GET);
			break;
//A segunda verifica��o olha se est� a��o � DelTodos, se sim....
		case "2":
			$Lista = $facebook->api('me/apprequests', 'GET');
//Ele varre todas as requisi��es;
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
//Carrega todas as requisi��es enviadas para o usu�rio
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