<?php
	switch($_REQUEST["Acao"])
	{
		case "DelAviso":

			$Codigo		= $_REQUEST["Codigo"];

			$facebook->api($Codigo, 'DELETE');

			echo "<script>alert('ID " . $Codigo . " deletado com sucesso!');</script>";
			echo '<meta http-equiv="refresh" content="0;url=?page=mensagens" />';
			break;
		default :
			echo "";
	}
	if( count($Lista["data"]) > 0 )
	{
		echo 'Data : Aviso -------- Excluir<br />';
		for( $i=0; $i<count($Lista["data"]); $i++ ){
			echo $Lista["data"][$i]["message"] . ' - '; ?>
				<a href="?page=mensagens&Acao=DelAviso&Codigo=<?=$Lista["data"][$i]["id"]?>">
					<? echo '<font color="red">[x]</font>'; ?>
				</a>
				<Br />
		<?php
		}
	}else{
		echo 'Nenhum aviso encontrado!';
	}
	echo '<br /><br />';
?>  