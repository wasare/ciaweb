<?php

require_once("../../../app/setup.php");
require_once("../../../core/reports/carimbo.php");
require_once("../../../core/search.php");

$conn = new connection_factory($param_conn);

$Result1 = $conn->Execute("SELECT descricao, id FROM periodos ORDER BY 1 DESC;");

$carimbo = new carimbo($param_conn);
$busca   = new search('search','codigo_curso','searchlist', 'form1', '../curso_lista.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Lista alunos matriculados</title>
    <link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
    <script src="pesquisa_alunos.js" language="javascript" type="text/javascript"></script>
    <script src="../../../lib/functions.js" language="javascript" type="text/javascript"></script>
    <script src="../../../lib/prototype.js" language="javascript" type="text/javascript" ></script>
    <script src="../../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
    <script src="../../../lib/SpryAssets/SpryValidationCheckbox.js" type="text/javascript"></script>
    <link href="../../../lib/SpryAssets/SpryValidationCheckbox.css" rel="stylesheet" type="text/css" />
    <link href="../../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h2>Relat&oacute;rio de  Alunos Matriculados</h2>
<form method="post" name="form1" id="form1" target="_blank">

    <input type="image" name="input" 
	src="../../../public/images/icons/print.jpg" 
	alt="Exibir relat&oacute;rio" 
	title="Exibir relat&oacute;rio"
	id="bt_exibir" 
	name="bt_exibir" 
	class="botao" 
	onclick="document.form1.action = 'lista_alunos.php'" />
    <input type="image" name="input" 
	src="../../../public/images/icons/pdf_icon.jpg" 
	alt="Gerar PDF" 
	title="Gerar PDF"
	id="bt_pdf" 
	name="bt_pdf" 
	class="botao" 
	onclick="document.form1.action = 'pdf_alunos.php'" />
    <input type="image" name="input" 
	src="../../../public/images/icons/excel.jpg" 
	alt="Gerar planilha" 
	title="Gerar planilha"
	id="bt_excel" 
	name="bt_excel" 
	class="botao" 
	onclick="document.form1.action = 'xls_alunos.php'" />
    <input type="image" name="voltar" 
	src="../../../public/images/icons/back.png" 
	alt="Voltar" 
	title="Voltar" 
	id="bt_voltar" 
	name="bt_voltar" 
	class="botao" 
	onclick="history.back(-1);return false;" />
	
    <div class="panel">
	Per&iacute;odo:<br />
        <span id="sprytextfield1">
	    <input name="periodo1" type="text" id="periodo1" size="10" onchange="ChangeCode('periodo1','periodo'); setPeriodo();" />
            <?php  print $Result1->GetMenu('periodo',null,true,false,0,'onchange="ChangeOp()"; setPeriodo();'); ?>
            <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>
        </span>
	<br />
	Curso:<br />
	<span id="sprytextfield2">
	    <?php 
	        echo $busca->input_text_retorno("5"); 
	        echo $busca->input_text_consulta("40");
	        echo $busca->area_lista();
	    ?>
	    <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>
        </span>
    	<br />
	C&oacute;digo do Aluno:<br />
        <input name="aluno" type="text" id="aluno" size="10" />
        <span class="comentario">Caso n&atilde;o preenchido exibir&aacute; todos.</span>
        <br />
    	Turma:<br />
        <input name="turma" type="text" id="turma" size="10" />
        <br />
	<input type="checkbox" name="novatos" id="novatos" value="true" /> Somente alunos novatos. 
	<span class="comentario">Lista todos os alunos com v&iacute;nculo no curso relacionando o per&iacute;odo com o in&iacute;cio do curso.</span>
	<br />
        <div style="background-color:#CCCCCC; margin-top: 8px;margin-bottom: 6px;padding: 4px;">
	    <strong>Exibir colunas:</strong>
	    <br />
            <span id="sprycheckbox1">
                <input type="checkbox" name="nome" id="nome" value="nome" />Nome 
                <input type="checkbox" name="pai" id="pai" value="pai" />Nome do Pai 
                <input type="checkbox" name="mae" id="mae" value="mae" />Nome da M&atilde;e 
                <input type="checkbox" name="endereco" id="endereco" value="endereco" />Endere&ccedil;o 
                <input type="checkbox" name="bairro" id="bairro" value="bairro" />Bairro 
                <input type="checkbox" name="cidade" id="cidade" value="cidade" />Cidade 
                <input type="checkbox" name="cep" id="cep" value="cep" />CEP
                <br />
                <input type="checkbox" name="telefone" id="telefone" value="telefone" />Telefone(s) 
                <input type="checkbox" name="rg" id="rg" value="rg" />RG                 
                <input type="checkbox" name="cpf" id="cpf" value="cpf" />CPF 
                <input type="checkbox" name="sexo" id="sexo" value="sexo" />Sexo 
                <input type="checkbox" name="data_nascimento" id="data_nascimento" value="data_nascimento" />Data de Nascimento 
                <input type="checkbox" name="turma2" id="turma2" value="turma2" />Turma 
                <br/>                
                <span class="checkboxMinSelectionsMsg">Selecione no m&iacute;nimo uma coluna.</span>
             </span>
	</div>
	Assinatura:<br />
	<?php echo $carimbo->listar();?>
    </div>
</form>
<script type="text/javascript">
    <!--
    var sprycheckbox1 = new Spry.Widget.ValidationCheckbox("sprycheckbox1", {isRequired:false, minSelections:1});
    var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
    var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
    //-->
</script>
</body>
</html>