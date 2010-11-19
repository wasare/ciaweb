// Função de crição da tabela de listagem de ramais

function pesquisar() {
	nome = $F('nome');
	var url = 'index_action.php';
	var parametros = 'nome=' + nome;
	var myAjax = new Ajax.Request( url, { method: 'post', parameters: parametros, onLoading: carregando, onComplete: escreve});
}

//mostra o carregamento
function carregando(){
	$("msg").innerHTML = "<h2>Carregando...</h2>"; //<img src='../../acesso_web_diario/images/carregando.gif'>";
}

// Escreve a tabela de listagem de clientes
function escreve(request){
	//trata caracteres especiais para sair em formato correto para o browser
	var saida = unescape(request.responseText);
	$("listagem").innerHTML = saida;
	$("msg").innerHTML = "";
}
