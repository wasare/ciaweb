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
	}
	
	function confirma()
	{
		if (confirm('Tem certeza que deseja matricular o aluno nas disciplinas selecionadas?'))
		{
			document.form1.submit();
		} else {
			// se não confirmar, coloque o codigo aqui
	    }
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
