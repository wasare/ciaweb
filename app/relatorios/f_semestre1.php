<?php

include_once('../../conf/webdiario.conf.php');

?>
<html>
    <head>
        <title></title>
    </head>

    <body>
        <form id="f_periodos" name="f_periodos" method="post" action="">

            <select name="ano" id="ano" class="select" >
                <?php
                $sql_ano = 'SELECT DISTINCT DATE_PART(\'YEAR\', dt_inicial) AS ano from periodos ORDER BY ano DESC;';
                $anos = consulta_sql($sql_ano);

                while($row = pg_fetch_array($anos))
                {
                    echo '<option value="'.$row['ano'].'">'.$row['ano'].'</option>';
                }
                ?>
            </select>
            <input type="radio" name="semestre" id="semestre" value="01/01|30/06|01/07" checked="checked" />1º Semestre
            <input type="radio" name="semestre" id="semestre" value="01/07|31/12|01/07" />2º Semestre
            <input type="hidden" name="envio" id="envio" value="1" />
            <br />
            <input type="submit" name="enviar" id="enviar" value="Filtar períodos" />

        </form>

        <?php

        if ($_POST['envio'] == 1) {

            $datas = explode("|", $_POST['semestre']);
            $ano = $_POST['ano'];
            $dt1 = $datas['0'] .'/' . $ano;
            $dt2 = $datas['1'] . '/' . $ano;
            $dt3 = $datas['2'] . '/' . $ano;

            $sql_periodos = "SELECT id FROM periodos WHERE dt_inicial >= '$dt1'";
            $sql_periodos .= " AND dt_final <= '$dt2' OR ";
            $sql_periodos .= " (dt_final >= '$dt3' AND dt_inicial <= '$dt3');";

            $periodos = consulta_sql($sql_periodos);

            while($p = pg_fetch_array($periodos))
            {
                $filtro .= '\''.$p['id'].'\' ,';
            }

            echo 'filtro: <font color="red"> ref_periodo IN ( ' . $filtro . '\'0\') </font>';
            
        }

        ?>

        <br /><br /><br />
        <form id="f_cursos" name="f_cursos" method="post" action="">

            <select name="nivel" id="nivel" class="select" >
                <?php
                $sql_tipos = 'SELECT * from tipos_curso ORDER BY id;';
                $tipos = consulta_sql($sql_tipos);

                while($row = pg_fetch_array($tipos))
                {
                    echo '<option value="'.$row['id'].'">'.$row['descricao'].'</option>';
                }
                ?>
            </select>


            <input type="hidden" name="envia" id="envia" value="1" />
            <br />
            <input type="submit" name="enviar" id="enviar" value="Filtar cursos" />

        </form>

        <?php

        if ($_POST['envia'] == 1) {

            $id_nivel = $_POST['nivel'];

            $sql_cursos = "SELECT id FROM cursos WHERE ref_tipo_curso = $id_nivel;";

            $cursos = consulta_sql($sql_cursos);

            while($c = pg_fetch_array($cursos))
            {
                $filtro .= '\''.$c['id'].'\' ,';
            }

            echo 'filtro: <font color="red"> ref_curso IN ( ' . $filtro . '\'0\') </font>';

        }

        ?>
    </body>
</html>