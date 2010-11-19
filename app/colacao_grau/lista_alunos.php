<?php

require_once("../../app/setup.php");


$id_curso = $_POST['codigo_curso'];
$id_periodo = $_POST['periodo1'];


$Conexao = NewADOConnection("postgres");
$Conexao->PConnect("host=$host dbname=$database port=$port user=$user password=$password");


/* Nome do curso */
$RsNomeCurso = $Conexao->Execute("SELECT descricao FROM cursos WHERE id = $id_curso;");

if (!$RsNomeCurso) {
    print $Conexao->ErrorMsg();
    die();
}

/* Descricao do periodo */
$RsDescPeriodo = $Conexao->Execute("SELECT descricao FROM periodos WHERE id = '$id_periodo';");

if (!$RsDescPeriodo) {
    print $Conexao->ErrorMsg();
    die();
}

$sqlAlunos = "
SELECT 
contratos.id, contratos.ref_pessoa, pessoas.nome

FROM 
contratos, pessoas

WHERE 
pessoas.id = contratos.ref_pessoa AND
contratos.ref_curso = '$id_curso' AND 
contratos.ref_periodo_turma = '$id_periodo' AND
contratos.dt_formatura IS NULL

ORDER BY
to_ascii(pessoas.nome);";

$RsAlunos = $Conexao->Execute($sqlAlunos);

if (!$RsAlunos) {
    print $Conexao->ErrorMsg();
    die();
}

$respAlunos = "
<table bgcolor='white' width='600'>
<tr>
  <th>&nbsp;</th>
  <th>Contrato</th>
  <th>Aluno (C&oacute;digo/Nome)</th>
</tr>";

$line_color = "white";

while(!$RsAlunos->EOF) {

    if($line_color == "white") {
        $line_color = "#f4f4f4";
    }else {
        $line_color = "white";
    }

    $respAlunos .= "<tr bgcolor='$line_color'>";
    $respAlunos .= '<td><input type="checkbox" name="contrato[]" id="contrato[]" value="'.$RsAlunos->fields[0].'"/></td>';
    $respAlunos .= "<td>".$RsAlunos->fields[0]."</td>";
    $respAlunos .= "<td>".$RsAlunos->fields[1]." - ".$RsAlunos->fields[2]."</td>";
    $respAlunos .= "</tr>";

    $RsAlunos->MoveNext();
}

$respAlunos .= "</table>";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>SA</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../../public/styles/formularios.css" rel="stylesheet"	type="text/css" />
        <script src="../../lib/SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
        <link href="../../lib/SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />

        <script language="JavaScript">
            <!--
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
            //Configuracao do caminho das imagens do tigra calendar
            var caminho_img_tigra = '../../lib/tigra_calendar/img/';
            -->
        </script>
        <script language="JavaScript" src="../../lib/tigra_calendar/calendar_br.js"></script>
        <link rel="stylesheet" href="../../lib/tigra_calendar/calendar.css" />
    </head>
    <body>
        <div align="center" style="height: 600px;">
            <h1>Cola&ccedil;&atilde;o de grau</h1>
            <h4>
                <?php

                echo "<strong>Curso: </strong> ".$id_curso." - ".$RsNomeCurso->fields[0].
                        " - <strong>Per&iacute;odo inicial: </strong>".$RsDescPeriodo->fields[0];

                ?>
            </h4>
            <div class="panel">
                <form action="atualizar_contratos.php" name="form1" method="post">
                    Data da cola&ccedil;&atilde;o:
                    <span id="sprytextfield1">
                        <input type="text" name="data" id="data" size="10" value="<?php echo date("d/m/Y");?>" />
                        <script language="JavaScript">
                            new tcal ({
                                'formname': 'form1',
                                'controlname': 'data'
                            });
                        </script>
                        <span class="textfieldRequiredMsg">Obrigat&oacute;rio.</span>
                    </span>

                    <h4>Selecione os alunos que colaram grau (Contrato/C&oacute;digo/Nome)</h4>

                    <p>
                        <a href="#" onclick="selecionar_tudo();" >Marcar todas</a>&nbsp;&nbsp;
                        <a href="#" onclick="deselecionar_tudo();" >Desmarcar todas</a>
                    </p>
                    <?php

                    echo $respAlunos;

                    ?>
                    <p>
                        <a href="#" onclick="selecionar_tudo();" >Marcar todas</a>&nbsp;&nbsp;
                        <a href="#" onclick="deselecionar_tudo();" >Desmarcar todas</a>
                    </p>
                    <p align="center">
                        <input type="button" value="Voltar" onclick="history.back(0)" />
                        <input type="submit" value="Confirmar" />
                    </p>
                </form>
                <script type="text/javascript">
                    <!--
                    var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
                    //-->
                </script>
            </div>
        </div>
    </body>
</html>
