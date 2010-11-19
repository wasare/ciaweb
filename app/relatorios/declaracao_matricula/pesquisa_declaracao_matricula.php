<?php

require_once("../../../app/setup.php");
require_once("../../../core/reports/carimbo.php");

//Assinatura
$carimbo = new carimbo($param_conn);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="../../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
    <link href="../../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
    <script language="javascript" src="../../../lib/prototype.js"></script>
    <script language="javascript" src="../../../lib/functions.js"></script>
    <script language="javascript">
    <!--		
        //Oculta botao
        function Oculta(id){
            document.getElementById(id).style.display = "none";
        }
        //Exibe botao
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

         //Configuracao do caminho das imagens do tigra calendar
         var caminho_img_tigra = '../../../lib/tigra_calendar/img/';
    -->
    </script>
    <script language="JavaScript" src="../../../lib/tigra_calendar/calendar_br.js"></script>
    <link rel="stylesheet" href="../../../lib/tigra_calendar/calendar.css" />
    <link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    <title>SA</title>
</head>

<body onload="Oculta('confirmar');">
<form method="post" name="form1" target="_blank">
    <div align="center" style="height: 600px;">

        <h1>Declara&ccedil;&atilde;o de matr&iacute;cula</h1>

        <span class="comentario">ATENÇÃO: este relatório não verifica a situação acadêmica do aluno, apenas emite o documento.</span>

        <div class="panel">Data de emiss&atilde;o: <br />

            <input type="text" name="data" id="data" value="<?php echo date("d/m/Y");?>" size="10" />
            <script	language="JavaScript">
            new tcal ({
                'formname': 'form1',
                'controlname': 'data'
            });
            </script>

            <p>
                Assinatura (opcional):<br />
                <?php echo $carimbo->listar();?>
            </p>
            <br />
            Selecione um aluno:<br />
            <span id="sprytextPessoa">
                <input type="text" name="codigo_pessoa" id="codigo_pessoa" size="10" />
                <input type="text" name="nome_pessoa" id="nome_pessoa" size="35" />
                <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>
            </span>
            <a href="javascript:abre_consulta_rapida('../../consultas_rapidas/pessoas/index.php')">
            <img src="../../../public/images/icons/lupa.png" alt="Pesquisar usu&aacute;rio" width="20" height="20" /></a>
            <br />
            <br />
            <input type="button" name="teste" id="teste" value="Exibir cursos" onclick="Exibe('confirmar');ConsultaCursos();" />
            <div id="RespostaCursos"></div>
        </div>
        <br />
        <div id="confirmar">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
		<td width="73" align="center"><label class="bar_menu_texto"> <input
			type="image" name="imageField" id="imageField"
			src="../../../public/images/icons/pdf_icon.jpg"
			onclick="document.form1.action = 'pdf_declaracao_matricula.php';document.form1.submit();" />
		<br />
		Gerar PDF</label></td>
		<td width="63" align="center"><label class="bar_menu_texto"> <a
			href="#" class="bar_menu_texto" onclick="history.back(-1)"> <img
			src="../../../public/images/icons/back.png" alt="Voltar" width="20"
			height="20" /> <br />
		Voltar</a></label></td>
                </tr>
            </table>
        </div>
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
