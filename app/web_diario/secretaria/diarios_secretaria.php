<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/search.php');

$conn = new connection_factory($param_conn);

$Result1 = $conn->Execute("SELECT descricao, id FROM periodos ORDER BY 1 DESC;");

$busca1  = new search('periodo','periodo_id','periodos_list', 'form1', '../../relatorios/periodo_lista.php');
$busca2  = new search('curso','curso_id','cursos_list', 'form1', '../../relatorios/curso_lista.php');
$busca3 = new search('professor','professor_id','professores_list', 'form1', '../../relatorios/professor_lista.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?=$IEnome?></title>
        <link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <script src="../../../lib/SpryAssets/SpryValidationCheckbox.js" type="text/javascript"></script>
        <link href="../../../lib/SpryAssets/SpryValidationCheckbox.css" rel="stylesheet" type="text/css" />
        <link href="../../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
        <script src="../../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
        <script type="text/javascript" language="javascript" src="../../../lib/prototype.js"></script>
        <script type="text/javascript" src="<?=$BASE_URL .'app/web_diario/web_diario.js'?>"> </script>
    </head>

    <body>
        <h2>Consulta Di&aacute;rios</h2>
        <form name="form1" id="form1" action="lista_diarios_secretaria.php" method="get">
            <input type="image" name="voltar"
                   src="../../../public/images/icons/back.png"
                   alt="Voltar"
                   title="Voltar"
                   id="bt_voltar"
                   name="bt_voltar"
                   class="botao"
                   onclick="history.back(-1);return false;" />
            <div class="panel">
                <h4> Preencha somente um dos grupos de crit&eacute;rios abaixo. </h4>
                C&oacute;digo do di&aacute;rio:<br />
                &nbsp;&nbsp;<span class="comentario">Se preenchido os demais crit&eacute;rios ser&atilde;o ignorados.</span><br />
                <input name="diario_id" type="text" id="diario_id" size="10" />

                <br /><br />
                <h4><hr /></h4>
                <br /> 
		Per&iacute;odo:<br />
                &nbsp;&nbsp;<span class="comentario">Comece digitando o ano para listar os per&iacute;odos ou informe o c&oacute;digo do per&iacute;odo no primeiro campo.</span><br />
                <span id="sprytextfield0">
                    <?php
                    echo $busca1->input_text_retorno("5");
                    echo $busca1->input_text_consulta("30");
                    echo '<span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>';
                    echo $busca1->area_lista();
                    ?>
                </span>

                <br /><br />
                  &nbsp;&nbsp;<span class="comentario">Comece digitando o nome do curso ou do professor para list&aacute;-los ou informe respectivo c&oacute;digo no primeiro campo.</span><br />
		Curso:&nbsp;               
                <span id="sprytextfield1">
                    <?php
                    echo $busca2->input_text_retorno("5");
                    echo $busca2->input_text_consulta("25");
                    echo '<span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>';
                    echo $busca2->area_lista();
                    ?>
                </span>
                &nbsp;
                <strong>e / ou</strong>
                &nbsp;
                Professor:&nbsp;               
                <span id="sprytextfield2">
                    <?php
                    echo $busca3->input_text_retorno("5");
                    echo $busca3->input_text_consulta("25");
                    echo '<span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>';
                    echo $busca3->area_lista();
                    ?>
                </span>    
                <br /><br />
            </div>
            <br />
            &nbsp;&nbsp;&nbsp;&nbsp;<input name="lista_diarios" type="submit" id="lista_diarios" value="Listar di&aacute;rio(s)" />                     
        </form>
    </body>
</html>
