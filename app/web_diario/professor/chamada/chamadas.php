<?php

require_once(dirname(__FILE__) .'/../../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

$diario_id = (int) $_GET['id'];
$operacao  = $_GET['do'];

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
}
// ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //

if (is_fechado($diario_id)) {

    echo '<script language="javascript" type="text/javascript">';
    echo 'alert("ERRO! Este diário está fechado e não pode ser alterado!");';
    echo 'window.close();';
    echo '</script>';
    exit;
}

$curso = get_curso($diario_id);
$disciplina = get_disciplina($diario_id);

$sa_ref_periodo = $_SESSION['web_diario_periodo_id'];

$meses = array("Janeiro","Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title><?=$IEnome?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
        <!-- Adobe Spry Framework -->
        <script src="../../lib/Spry/widgets/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>
        <link href="../../lib/Spry/widgets/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
        <!-- Tigra Calendar -->
        <script language="JavaScript">
            <!--
            //Configuracao do caminho das imagens do tigra calendar
            var caminho_img_tigra = '../../lib/tigra_calendar/img/';
            -->
        </script>
        <script src="../../lib/tigra_calendar/calendar_br.js"  type="text/javascript" language="JavaScript" ></script>
        <link href="../../lib/tigra_calendar/calendar.css" rel="stylesheet" type="text/css" />
        <style type="text/css">@import "<?=$BASE_URL .'public/styles/jquery.maxlength.css'?>";</style>
    </head>
    <body>
        <div align="left" class="titulo1">
            Lan&ccedil;amento de chamadas
        </div>
        <br />
        <?=papeleta_header($diario_id)?>
        <br />
        <form name="envia_chamada" id="envia_chamada" method="post" action="<?=$BASE_URL .'app/web_diario/professor/chamada/lanca_faltas.php'?>">
            <input type="hidden" name="diario_id" id="diario_id" value="<?=$diario_id?>">
            <input type="hidden" name="operacao" id="operacao" value="<?=$operacao?>">
            <p>
                <a href="javascript:void(0);" onmouseover="TagToTip('instrucoes', ABOVE, true,PADDING, 9, TITLE, 'Ajuda - Lan&ccedil;amento de chamadas', CLOSEBTN, true,STICKY, true,FONTSIZE, '0.8em', COPYCONTENT, false, BGCOLOR, '#FFFFFF' )" onmouseout="UnTip()">Ajuda/Instru&ccedil;&otilde;es</a>
            </p>
            Data da chamada:<br />
            <span id="date1">
                <input type="text" name="data_chamada" id="data_chamada" />
                    <script type="text/javascript">
                        new tcal ({
                            'formname': 'envia_chamada',
                            'controlname': 'data_chamada'
                        });
                    </script>
                <span class="textfieldRequiredMsg">Valor obrigat&oacute;rio.</span>
                <span class="textfieldInvalidFormatMsg">Formato inv&aacute;lido.</span>
            </span>
            <br />
            <br />
            Selecione a quantidade de aulas desta chamada:
            <br />
            <select name="aula_tipo" id="aula_tipo" style="width:400px">
                <option>--- quantidade de aulas ---</option>
                <option value="1" <?php if($_SESSION['aula_tipo'] == "1") {
                    echo 'selected="selected"';
                        } ?>>1 Aula</option>
                <option value="12" <?php if($_SESSION['aula_tipo'] == "12") {
                    echo 'selected="selected"';
                        } ?>>2 Aulas  (Aula Dupla)</option>
                <option value="123" <?php if($_SESSION['aula_tipo'] == "123") {
                    echo 'selected="selected"';
                        } ?>>3 Aulas (Aula Tripla)</option>
                <option value="1234" <?php if($_SESSION['aula_tipo'] == "1234") {
                    echo 'selected="selected"';
                        } ?>>4 Aulas (Aula Qu&aacute;drupla)</option>
                <option value="12345" <?php if($_SESSION['aula_tipo'] == "12345") {
                    echo 'selected="selected"';
                        } ?>>5 Aulas</option>
                <option value="123456" <?php if($_SESSION['aula_tipo'] == "123456") {
                    echo 'selected="selected"';
                        } ?>>6 Aulas</option>
            </select>
            <br />
            <br />
            Bases e conhecimentos desenvolvidos na(s) aula(s):<br />
            <textarea name="conteudo" cols="50" rows="6" id="bases_conhecimento"><?=$_SESSION['conteudo']?></textarea>
            <br /><span class="maxlength-feedback" id="targetFeedback1"></span> <br />
            <br />
            <br />
            Atividades e avaliações da(s) aula(s):<br />

            <input type="checkbox" class="checkbox" name="atividades[]" id="atividade1" value="Aula expositiva" /> Aula expositiva
            <br />
            <input type="checkbox" class="checkbox" name="atividades[]" id="atividade2" value="Aula prática / laboratório" /> Aula pr&aacute;tica / laborat&oacute;rio
            <br />
            <input type="checkbox" class="checkbox" name="atividades[]" id="atividade3" value="Exercícios" /> Exerc&iacute;cios
            <br />
            <input type="checkbox" class="checkbox" name="atividades[]" id="atividade4" value="Trabalho em grupos" /> Trabalho em grupos
            <br />
            <input type="checkbox" class="checkbox" name="atividades[]" id="atividade5" value="Pesquisa" /> Pesquisa
            <br />
            <input type="checkbox" class="checkbox" name="atividades[]" id="atividade6" value="Análise de situação problema" /> An&aacute;lise de situa&ccedil;&atilde;o problema
            <br />
            <input type="checkbox" class="checkbox" name="atividades[]" id="atividade7" value="Seminário" /> Semin&aacute;rio
            <br />
            <input type="checkbox" class="checkbox" name="atividades[]" id="atividade8" value="Visita técnica" /> Visita t&eacute;cnica
            <br />
            <input type="checkbox" class="checkbox" name="atividades[]" id="atividade9" value="Avaliação" /> Avalia&ccedil;&atilde;o
            <br />
            <input type="checkbox" class="checkbox" name="atividades[]" id="atividade10" value="Outros" /> Outros (especificar abaixo) <br />
            &nbsp;&nbsp;&nbsp;&nbsp;<textarea name="outros" cols="48" rows="4" id="atividade11"></textarea>
            <br /><span class="maxlength-feedback" id="targetFeedback2"></span> <br />
            <br />
            <br />
            <br />
            <input type="checkbox" class="checkbox" name="flag_falta" id="flag_falta" value="F" />
            <font color="brown">
                <b>N&atilde;o houve faltas neste dia</b>
                <span style="font-size: 0.8em">
                    (marque esta op&ccedil;&atilde;o caso n&atilde;o exista faltas neste dia)
                </span>
            </font>
            <br />
            <br />
            <input type="submit" name="Submit" value="Prosseguir">&nbsp;&nbsp;
            <a href="#" onclick="javascript:window.close();">Cancelar</a>
            <br /><br />
        </form>
        <script type="text/javascript">
            <!--
            var date1 = new Spry.Widget.ValidationTextField("date1", "date", {format:"dd/mm/yyyy", validateOn:["blur", "change"], useCharacterMasking:true});
            //-->
        </script>
        <div id="instrucoes">
            <h3>INSTRU&Ccedil;&Otilde;ES</h3>
            <font color="#330099">
                * As op&ccedil;&otilde;es com mais de uma aula se referem &agrave; aulas seguidas.<br />
                * N&atilde;o pode haver mais de uma chamada no mesmo dia para o mesmo di&aacute;rio.<br />
                * As faltas desta chamada, caso exista alguma, dever&atilde;o ser informadas no pr&oacute;ximo passo.
                <br />
            </font>
        </div>
    <script type="text/javascript" src="<?=$BASE_URL .'lib/wz_tooltip.js'?>"> </script>
    <script type="text/javascript" language="javascript" src="<?=$BASE_URL .'lib/jquery.min.js'?>"></script>
    <script type="text/javascript" language="javascript" src="<?=$BASE_URL .'lib/jquery.maxlength.pack.js'?>"></script>
    <script type="text/javascript">
        $(function() {
            $('#bases_conhecimento').maxlength({max: 300, feedbackText: 'Usando {c} de {m} caracteres.', feedbackTarget: '#targetFeedback1'});
            $('#atividade11').maxlength({max: 300,feedbackText: 'Usando {c} de {m} caracteres.', feedbackTarget: '#targetFeedback2'});
        });
    </script>
</body>
</html>

