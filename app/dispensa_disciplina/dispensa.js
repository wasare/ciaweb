// JavaScript Document

	//Oculta 
	function Oculta(id)
	{
		document.getElementById(id).style.display = "none";
	}
	//Exibe
	function Exibe(id)
	{
		document.getElementById(id).style.display = "inline";
        if ($F('dispensa_tipo') == -1)
				Oculta(id);
	}


    function pesquisar() {
		nome = $F('nome');
		var url = 'index_action.php';
		var parametros = 'nome=' + nome ;
		var myAjax = new Ajax.Request( url, { method: 'post', parameters: parametros});
	}


	function confirma(frm)
    {
        if (confirm('Tem certeza que deseja dispensar o aluno desta disciplina?'))
        {
            $(frm).submit();
        } else {
            return null;
        }
    }

	function valida(frm){

	   var params = Form.serialize(frm);

	   objAjax = new Ajax.Request('dispensa_valida.php', {
        	method: 'post', parameters: params,
        	onComplete: function(req) {
                var resp = unescape(req.responseText);
				if( resp != 0)
				{
					alert(resp);
				}
				else
				{
					confirma(frm);
				} 
        	}
    	});
	}	


	
	function _confirma()
	{
		if (confirm('Tem certeza que deseja dispensar o aluno desta disciplina?'))
		{
			document.form1.submit();
		} else {
			return null;
	    }
	}

    function envia()
    {
                document.form1.submit();
    } 	

	function selecionar_tudo(){
   		for (i=0;i<document.form1.elements.length;i++)
      		if(document.form1.elements[i].type == "checkbox")
         		document.form1.elements[i].checked=1
	} 
	
	function deselecionar_tudo(){
   		for (i=0;i<document.form1.elements.length;i++)
      		if(document.form1.elements[i].type == "checkbox")
        		document.form1.elements[i].checked=0
	} 
