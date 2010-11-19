<?php

/**
* Filtro de periodo, aluno, curso
* @author Santiago Silva Pereira
* @version 1
* @since 23-01-2009
**/

header("Cache-Control: no-cache");

//INCLUSAO DE BIBLIOTECAS
require_once("../../app/setup.php");


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

            //Oculta botoes
            function Oculta(id){
                document.getElementById(id).style.display = "none";
            }
            //Exibe botoes
            function Exibe(id){
                document.getElementById(id).style.display = "inline";
            }
			
			//Ajax que busca os contratos e os cursos
            function ConsultaCursos(){

                var codigo_pessoa = $F('codigo_pessoa');
                var url = 'matricula_contratos.php';
                var pars = 'codigo_pessoa=' + codigo_pessoa;

                var myAjax = new Ajax.Updater('RespostaCursos',url, {method: 'get',parameters: pars});
            }

			//Preenche periodo
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

            function ChangeCode(fld_name,op_name)
            {
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

        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <link href="../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />

        <title>SA</title>
    </head>
    <body onload="Oculta('regular');Oculta('avulsa')">

        <form method="post" name="form1">

            <div align="center" style="height:600px;">
                <h1>Processo de Matr&iacute;cula</h1>
                <h4>Identifica&ccedil;&atilde;o do Per&iacute;odo e do Aluno: Etapa 1/2</h4>

                <div class="panel">

                    <!-- Entrada do Periodo -->
                    Selecione um per&iacute;odo:<br />
                    <span id="sprytextPeriodo">
		    		<input type="text" id="periodo_id" name="periodo_id" value="<?=$sa_periodo_id?>" size="10" onchange="ChangeCode('periodo_id','periodo')" />
                        <?php  print $Result1->GetMenu('periodo',null,true,false,0,'onchange="ChangeOp()"'); ?>
                        <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>
                    </span><br />

                    <!-- Entrada do Aluno-->
                    Selecione um aluno:<br />
                    <span id="sprytextPessoa">
                        <input type="text" name="codigo_pessoa" id="codigo_pessoa" size="10" />
                        <input type="text" name="nome_pessoa" id="nome_pessoa" size="35" />
                    <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span></span>

                    <a href="javascript:abre_consulta_rapida('../consultas_rapidas/pessoas/index.php')">
                        <img src="../../public/images/icons/lupa.png" alt="Pesquisar usu&aacute;rio" width="20" height="20" />
                    </a>
                    <br /><br />
                    <input type="button" name="teste" id="teste" value="Exibir cursos" onclick="Exibe('regular');Exibe('avulsa');ConsultaCursos();" />
                    <div id="RespostaCursos"></div>
                </div>
                <br />

                <!--<input type="button" value="  Voltar  " onclick="javascript:history.back(-1)" name="Button" />-->

                <input type="hidden" name="first" value="1" />

                <input type="button" name="regular" id="regular"  value=" Matr&iacute;cula regular " onclick="document.form1.action = 'matricula_regular.php';document.form1.submit();" />&nbsp;
                <input type="button" name="avulsa" id="avulsa"  value=" Matr&iacute;cula avulsa " onclick="document.form1.action = 'matricula_avulsa.php';document.form1.submit();" />
            </div>

        </form>

        <script type="text/javascript">
            <!--
            var sprytextPeriodo = new Spry.Widget.ValidationTextField("sprytextPeriodo");
            var sprytextPessoa = new Spry.Widget.ValidationTextField("sprytextPessoa");
            //-->
        </script>

    </body>
</html>
