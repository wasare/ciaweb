<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once("../../../app/setup.php");

/*
 * Estancia a classe de conexao e abre
 */
$conn = new connection_factory($param_conn);


/*
 * Parametros
 */
$periodo = (string) $_POST['periodo_id'];
$campus = (int) $_POST['campus_id'];

$sql = "SELECT DISTINCT
            c.id, c.descricao
        FROM cursos c, matricula m
        WHERE
            c.id = m.ref_curso AND
            m.ref_campus = $campus AND
            m.ref_periodo = '$periodo'
        ORDER BY c.descricao;";

$arr_cursos = $conn->get_all($sql);

$nome_campus  = $conn->get_one('SELECT nome_campus FROM campus WHERE id = '.$campus);
$nome_periodo = $conn->get_one("SELECT descricao FROM periodos WHERE id = '$periodo'");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SA</title>
        <link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <script src="../../../lib/Spry/widgets/radiovalidation/SpryValidationRadio.js" type="text/javascript"></script>
        <link href="../../../lib/Spry/widgets/radiovalidation/SpryValidationRadio.css" rel="stylesheet" type="text/css" />
        <script language="javascript" src="../../../lib/prototype.js"></script>
        <script language="javascript">
            //Consulta ajax com prototype
            function consulta_ajax(){
                //Capturando o valor do radio
                var id_curso = Form.getInputs('form1','radio','curso').find(
                    function(radio) {
                        return radio.checked;
                    }
                ).value;
                //Capturando o valor do text
                //var id_curso = $F('curso');
                var url = 'lista_turmas.php';
                var pars = 'id_curso=' + id_curso;
                var myAjax = new Ajax.Updater('resposta',url, {method: 'get',parameters: pars});
            }
        </script>
    </head>
    <body>
        <h2>Relat&oacute;rio global de notas e faltas</h2>
        <form action="notas_faltas_global.php" method="post" name="form1" id="form1" target="_blank">
            <input type="hidden" id="periodo" name="periodo" value="<?=$periodo?>" />
            <input type="hidden" id="campus" name="campus" value="<?=$campus?>" />
            <div class="btn_action" id="btn_voltar">
                <a href="javascript:history.back();" class="bar_menu_texto">
                    <img src="../../../public/images/icons/back.png" alt="Voltar" width="20" height="20" />
                    <br />Voltar
                </a>
            </div>
            <div class="panel" id="panel">
                <h3>Etapa 2 de 2</h3>
                <span class="comentario"><strong>Aten&ccedil;&atilde;o:</strong> O relat&oacute;rio exibir&aacute; somente os di&aacute;rio concluídos.</span>
                <br /><br />
                <strong>Per&iacute;odo:</strong>
                <?=$nome_periodo?>
                <br />
                <strong>Campus:</strong>
                <?=$nome_campus?>
                <br />
                <br />
                <strong>Selecione um curso:</strong><br />

                <span id="ValidRadio1">
                    <span class="radioRequiredMsg">Selecione um curso.</span><br />
                    
                    <?php foreach($arr_cursos as $curso): ?>
                    <input type="radio" name="curso" id="curso" value="<?=$curso['id']?>" onclick="consulta_ajax();" />
                    <?=$curso['descricao']?> (<?=$curso['id']?>)<br />
                    <?php endforeach; ?>

                    <span class="radioRequiredMsg">Selecione um curso.</span>
                </span>

                <br />
                <div id="resposta"></div>
                <p>
                    <input type="submit" value="Exibir" />
                </p>
            </div>
        </form>
        <script type="text/javascript">
            <!--
            var ValidRadio1 = new Spry.Widget.ValidationRadio("ValidRadio1", {validateOn:["change"]});
            //-->
        </script>
    </body>
</html>
