<?php

/**
* Filtro de periodo, aluno, curso
* @author Wanderson Santiago dos Reis
* @version 1
* @since 04-01-2009
**/

header("Cache-Control: no-cache");
require_once('../../app/setup.php');


//Criando a classe de conex�o
$Conexao = NewADOConnection("postgres");

//Setando como conex�o persistente
$Conexao->PConnect("host=$host dbname=$database port=$port user=$user password=$password");

//EXECUTANDO SQL COM ADODB
$Result1 = $Conexao->Execute("SELECT descricao, id FROM periodos ORDER BY 1 DESC;");

//Se Result1 falhar	
if (!$Result1){
    print $Conexao->ErrorMsg();
    die();
}	

$sa_periodo_id = $_SESSION['sa_periodo_id'];

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <script src="../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
        <script language="javascript" src="../../lib/prototype.js"></script>
        <script language="javascript" src="../../lib/functions.js"></script>
        <script language="javascript">
            <!--

            //Oculta
            function Oculta(id){
                document.getElementById(id).style.display = "none";
            }
            //Exibe
            function Exibe(id){
                document.getElementById(id).style.display = "inline";
            }

            function ConsultaCursos(){

                var codigo_pessoa = $F('codigo_pessoa');
                var url = 'busca_contratos.php';
                var pars = 'codigo_pessoa=' + codigo_pessoa;

                var myAjax = new Ajax.Request(url, { method: 'get', parameters: pars, onComplete: ajax_response });
            }
            //mostra o carregamento
	    function carregando(){
        	$("msg").innerHTML = "<img src='images/carregando.gif'>";
	    }

            function ajax_response(request){
        	$("RespostaCursos").innerHTML = unescape(request.responseText);
        	//$("msg").innerHTML = "";
            }


            function ChangeOption(opt,fld){
                var i = opt.selectedIndex;
                if ( i != -1 ){
                    fld.value = opt.options[i].value;
                }else{
                    fld.value = '';
                }
            }

            function ChangeOp() {
                ChangeOption(document.form1.periodo,document.form1.periodo_id);
            }

            function ChangeCode(fld_name,op_name){
                var field = eval('document.form1.' + fld_name);
                var combo = eval('document.form1.' + op_name);
                var code  = field.value;
                var n     = combo.options.length;
                for ( var i=0; i<n; i++ ){
                    if ( combo.options[i].value == code ){
                        combo.selectedIndex = i;
                        return;
                    }
                }

                alert(code + ' n�o � um c�digo v�lido!');
                field.focus();
                return true;
            }
            -->
        </script>

        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css">
        <link href="../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />

        <title>SA</title>
    </head>
    <body>

        <form method="post" action="dispensa_disciplina.php" name="form1">

            <div align="center" style="height:600px;">
                <h1>Processo de Dispensa de Disciplina</h1>
                <h4>Identifica&ccedil;&atilde;o do Aluno e do Curso: Etapa 1/3</h4>

                <div class="panel">

                    <!-- Entrada do Aluno-->
                    Selecione um aluno:<br>
                    <span id="sprytextPessoa">
                        <input type="text" name="codigo_pessoa" id="codigo_pessoa" size="10" />
                        <input type="text" name="nome_pessoa" id="nome_pessoa" size="35" >
                    <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span></span>

                    <a href="javascript:abre_consulta_rapida('../consultas_rapidas/pessoas/index.php')">
                        <img src="../../public/images/icons/lupa.png" alt="Pesquisar usu&aacute;rio" width="20" height="20" />
                    </a>
                    <br /><br />
                    <input type="button" name="teste" id="teste" value="Exibir cursos" onclick="ConsultaCursos();Exibe('prosseguir');" />
               </div>

                 <span id="RespostaCursos"></span>
               </div>
                <!--</div>
                <br />
                <input type="hidden" name="first" value="1">
                <input type="submit" name="processeguir" id="prosseguir"  value=" >> Prosseguir " />
            </div>-->

        </form>

        <script type="text/javascript">
            <!--
            var sprytextPeriodo = new Spry.Widget.ValidationTextField("sprytextPeriodo");
            var sprytextPessoa = new Spry.Widget.ValidationTextField("sprytextPessoa");
            //-->
        </script>

    </body>
</html>
