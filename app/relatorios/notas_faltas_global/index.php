<?php
/*
 * Arquivo com as configuracoes iniciais
 */
require_once("../../../app/setup.php");
require_once($BASE_DIR .'core/search.php');

/*
 * Estancia a classe de conexao e abre
 */
$conn = new connection_factory($param_conn);

$arr_campi = $conn->get_all('SELECT id, nome_campus FROM campus ORDER BY nome_campus;');

$turno_sql = "SELECT DISTINCT 
                  oc.turno 
               FROM disciplinas_ofer o LEFT JOIN disciplinas_ofer_compl oc 
               ON (o.id = oc.ref_disciplina_ofer)
               WHERE 
                    o.ref_campus = (SELECT id FROM campus WHERE nome_campus = '". $_SESSION['sa_campus'] ."') AND
                    o.is_cancelada = '0'
              ";

$arr_turno = $conn->get_all("SELECT id, nome FROM turno WHERE id IN ($turno_sql) ORDER BY nome ;");

$busca1  = new search('periodo','periodo_id','periodos_list', 'form1', '../periodo_lista.php');
//$busca2  = new search('curso','curso_id','cursos_list', 'form1', '../curso_lista.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SA</title>
        <link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
	<link href="../../../lib/Spry/widgets/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
	<script src="../../../lib/Spry/widgets/textfieldvalidation/SpryValidationTextField.js" type="text/javascript"></script>
	<script language="javascript" src="../../../lib/prototype.js"></script>
    </head>
    <body>
        <h2>Relat&oacute;rio global de notas e faltas</h2>
        <form action="etapa2.php" method="post" name="form1">
            <div class="btn_action">
                <a href="javascript:history.back();" class="bar_menu_texto">
                    <img src="../../../public/images/icons/back.png" alt="Voltar" width="20" height="20" />
                    <br />Voltar
                </a>
            </div>
            <div class="panel" style="height:300px;">
                <h3>Etapa 1 de 2</h3>
                Per&iacute;odo:<br />
                <span class="comentario">Comece digitando o ano para listar os per&iacute;odos ou informe o c&oacute;digo do per&iacute;odo no primeiro campo.</span>
                <br />
                <span id="sprytextfield0">
                    <?=$busca1->input_text_retorno("5")?>
                    <?=$busca1->input_text_consulta("30")?>
                    <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>
                    <?=$busca1->area_lista()?>
                </span>
                <br />
                Turno:<br />
                <span class="comentario">Selecione o turno ou deixe como está para listar os cursos de todos os turnos.</span>
                <br />
                <select id="turno" name="turno">
                  <option value=""> </option>
                 <?php
                  foreach($arr_turno as $turno):
                 ?>
                  <option value="<?=$turno['id']?>">
                      <?=$turno['nome']?>
                  </option>
                <?php endforeach;?>
                </select>
                <br />
                Campus:<br />
                <select id="campus" name="campus" disabled="disabled">
                 <?php
                  foreach($arr_campi as $campus): 
                    if ($_SESSION['sa_campus'] == $campus['nome_campus']){
                      $selected = ' selected="selected"'; 
                      $ref_campus = $campus['id'];
                    } else
                      $selected = '';
                 ?>
                    <option value="<?=$campus['id']?>" <?=$selected?>>
                      <?=$campus['nome_campus']?>
                    </option>
                <?php endforeach;?>
                </select>
                <input type="hidden" name="campus_id" id="campus_id" value="<?=$ref_campus?>" />
                <br /><br />
                <p>
                    <input type="submit" value="Pr&oacute;ximo" />
                </p>
            </div>
        </form>
        <script type="text/javascript">
            var sprytextfield0 = new Spry.Widget.ValidationTextField("sprytextfield0");
        </script>
    </body>
</html>
