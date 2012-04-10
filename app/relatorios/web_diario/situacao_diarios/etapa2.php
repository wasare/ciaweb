<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once('../../../../app/setup.php');

/*
 * Estancia a classe de conexao e abre
 */
$conn = new connection_factory($param_conn);


/*
 * Parametros
 */
$periodo = (string) $_POST['periodo_id'];
$campus = (int) $_POST['campus_id'];
$turno = (string) $_POST['turno'];

$turno_sql = $turno_desc = '';
if (!is_numeric($turno) && !empty($turno)) {
                              
     $turno_sql = " c.id IN (SELECT DISTINCT 
                                  m.ref_curso
                                FROM disciplinas_ofer o 
                                  LEFT JOIN disciplinas_ofer_compl oc 
                                ON (o.id = oc.ref_disciplina_ofer) 
                                  LEFT JOIN matricula m
                                ON (o.id = m.ref_disciplina_ofer)
                                                          
                                WHERE 
                                    oc.turno = '". $turno ."' AND
                                    o.ref_campus = ". $campus ." AND
                                    m.ref_periodo = '". $periodo ."' AND
                                    o.is_cancelada = '0'
                              ) ";
                          
   $turno_desc = $conn->get_one("SELECT get_turno('$turno')");

}

$sql = "SELECT DISTINCT
            c.id, c.descricao
        FROM 
              cursos c  
        WHERE
            $turno_sql
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
        <link href="<?=$BASE_URL?>public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <script src="<?=$BASE_URL?>lib/Spry/widgets/radiovalidation/SpryValidationRadio.js" type="text/javascript"></script>
        <link href=".<?=$BASE_URL?>lib/Spry/widgets/radiovalidation/SpryValidationRadio.css" rel="stylesheet" type="text/css" />
        <script language="javascript" src="<?=$BASE_URL?>lib/prototype.js"></script>
        <script language="javascript">
            //Consulta ajax com prototype
            function consulta_ajax(){
                //Capturando o valor do radio
                var id_curso = Form.getInputs('form1','radio','curso').find(
                    function(radio) {
                        return radio.checked;
                    }
                ).value;
                var turno = $F('turno');
                var periodo = $F('periodo');
                var campus = $F('campus');
                //alert(turno);
                var url = 'lista_turmas.php';
                var pars = 'id_curso=' + id_curso + '&turno=' + turno + '&periodo=' + periodo + '&campus=' + campus;
                var myAjax = new Ajax.Updater('resposta',url, {method: 'get',parameters: pars});
            }
        </script>
    </head>
    <body>
        <h2>Relat&oacute;rio de acompanhamento dos di&aacute;rios</h2>
        <form action="situacao_diarios.php" method="post" name="form1" id="form1" target="_blank">
            <input type="hidden" id="periodo" name="periodo" value="<?=$periodo?>" />
            <input type="hidden" id="campus" name="campus" value="<?=$campus?>" />
            <input type="hidden" id="turno" name="turno" value="<?=$turno?>" />
            <input type="hidden" id="turno_desc" name="turno_desc" value="<?=$turno_desc?>" />
            <div class="btn_action" id="btn_voltar">
                <a href="javascript:history.back();" class="bar_menu_texto">
                    <img src="<?=$BASE_URL?>public/images/icons/back.png" alt="Voltar" width="20" height="20" />
                    <br />Voltar
                </a>
            </div>
            <div class="panel" id="panel">
                <h3>Etapa 2 de 2</h3>
                <br />
                <strong>Per&iacute;odo:</strong>
                <?=$nome_periodo?>
                <br />
                <strong>Campus:</strong>
                <?=$nome_campus?>
                <?php if (isset($turno_desc)) : ?>
                  <br />
                  <strong>Turno:</strong>
                  <?=$turno_desc?>
                <?php endif; ?>
                <br /><br />
                <strong>Selecione um curso:</strong><br />

                <span id="ValidRadio1">
                    
                    <?php
                      $count = 0; 
                      foreach($arr_cursos as $curso): 
                        $checked = ($count === 0) ? 'checked="checked"' : '';
                    ?>
                    <input type="radio" name="curso" id="curso" value="<?=$curso['id']?>" <?=$checked?> />
                    <?=$curso['descricao']?> (<?=$curso['id']?>)<br />
                    <?php 
                      $count++;
                      endforeach; 
                    ?>

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
