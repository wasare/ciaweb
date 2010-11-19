<?php
/**
 * Pesquisa instantanea
 * @param campo de busca, campo de retorno, area de listagem, nome do formulario
 *
 * Necessita do prototype
 **/
class search{

    private $nome_campo_consulta;
    private $nome_campo_retorno;
    private $nome_area_lista;
    private $nome_formulario;
    private $arq_action;

    /**
    * Construtor cria os scripts
    * @param nome_campo_consulta, nome_campo_retorno, nome_area_lista,
    * 		 nome do formulario, nome do arquivo de acao
    */
    function __construct($nome_campo_consulta, $nome_campo_retorno,
        $nome_area_lista, $nome_formulario, $arq_action){
        $this->nome_campo_consulta = $nome_campo_consulta;
        $this->nome_campo_retorno = $nome_campo_retorno;
        $this->nome_area_lista = $nome_area_lista;
        $this->nome_formulario = $nome_formulario;
        $this->arq_action = $arq_action;

        //Javascript
        $script = "
        <script language=\"javascript\">
	    function ". $nome_campo_consulta ."_pesquisar() {
	        nome = \$F('".$this->nome_campo_consulta."');
		var url = '".$this->arq_action."';
		var parametros = '". $nome_campo_consulta ."=' + nome;
		var myAjax = new Ajax.Request( url, { method: 'post', parameters: parametros, onComplete: ". $nome_campo_consulta  ."_escreve});
	    }
	    function ". $nome_campo_consulta ."_escreve(request){
		if($('".$this->nome_campo_consulta."').value.length<3) {
		    $(\"".$this->nome_area_lista."\").style.display = \"none\";
		}else{
		    $(\"".$this->nome_area_lista."\").style.display = \"block\";
		}
		var saida = unescape(request.responseText);
		$(\"".$this->nome_area_lista."\").innerHTML = saida;
		$(\"msg\").innerHTML = \"\";
	    }
	    function ". $nome_campo_consulta ."_send(id,descricao){
	        document.$this->nome_formulario.$this->nome_campo_retorno.value=id;
		document.$this->nome_formulario.$this->nome_campo_consulta.value=descricao;
		". $nome_campo_consulta ."_fechar();
	    }
	    function ". $nome_campo_consulta ."_fechar(){
		$(\"".$this->nome_area_lista."\").style.display = \"none\";
	    }
    	</script>";
	
	//CSS
	$script .= "
	<style>
	<!--
	    div#$this->nome_area_lista {
		position:absolute;
		border:1px solid #333333;
		width:320px;
		left:100px;
		display:none;
		font-family:Verdana, Arial, Helvetica, sans-serif;
		font-size:11px;
		background-color:#ffffe0;
	    }
	    div#$this->nome_area_lista a {
		float:left;
		width:314px;
		clear:both;
		padding:3px 3px;
		text-decoration:none;
	    }
	    div#$this->nome_area_lista a:hover {
		background-color:#FFE4B5;
		color:#000000;
	    }
	    -->
	</style>";
	
	echo $script;
    }

	function input_text_consulta($width="", $type="text"){
                return '<input type="'. $type .'" id="'. $this->nome_campo_consulta .'"
			name="'. $this->nome_campo_consulta .'" autocomplete="off"
			size="'. $width .'" onkeyup="'. $this->nome_campo_consulta .'_pesquisar();"/>';
	}

	function input_text_retorno($width="", $type="text"){
	    return "<input type=\"$type\" size=\"$width\"
                    id=\"$this->nome_campo_retorno\"
		    name=\"$this->nome_campo_retorno\" /> ";
	}

	function area_lista(){
            return "<div id=\"$this->nome_area_lista\"></div>";
	}
}

?>
