//fecha a conexao e limpa o resultado

mysql_close($conexao);
mysql_free_result($resultado);

}else{
mysql_close($conexao) or die ("Não foi possível fechar a conexão");
}