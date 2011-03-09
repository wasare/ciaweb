<?php
/**
* Lista Diarios
* @author Santiago Silva Pereira
* @version 1
* @since 11-02-2009
**/

//Arquivos de configuracao e biblioteca
header("Cache-Control: no-cache");
require_once("../../app/setup.php");

//Criando a classe de conexao ADODB
$Conexao = NewADOConnection("postgres");

//Setando como conexao persistente
$Conexao->PConnect("host=$host dbname=$database port=$port user=$user password=$password");


/**
 * Retorna uma tabela com os diarios pesquisados
 * @param <string> $value
 * @return <string> $tabela
 */
function lista_diarios($value, $conn)
{
    $sql = "
    SELECT
        o.id,
        d.descricao_disciplina,
        o.ref_curso,
        professor_disciplina_ofer_todos(o.id),
        o.turma
    FROM
        disciplinas_ofer o, disciplinas d
    WHERE
        o.ref_disciplina = d.id AND
        lower(to_ascii(d.descricao_disciplina,'LATIN1')) ILIKE lower(to_ascii('%$value%','LATIN1')) AND
        o.ref_periodo = '".$_SESSION['sa_periodo_id']."'
    ORDER BY to_ascii(d.descricao_disciplina,'LATIN1') LIMIT 40;";

    //echo $sql; die;
    
    $Result1 = $conn->Execute($sql);

    $tabela = '<table class="tabela">';
    $tabela .= '<tr bgcolor="silver"><td></td><td><b>Di&aacute;rio - Disciplina <br /> Curso - Turma(periodo) - Professor</b></td></tr>';
    while (!$Result1->EOF)
    {
        $tabela .= "<tr><td><a href=\"javascript:send(" .$Result1->fields[0]. ")\">Enviar</a></td>";
        $tabela .= "<td> ".$Result1->fields[0]." - <b>".$Result1->fields[1]."</b><br />".
                    $Result1->fields[2]." - ".$Result1->fields[4].'('. $_SESSION['sa_periodo_id'] .") - ".$Result1->fields[3]."</td>";

        $Result1->MoveNext();
    }

    $tabela .= "</tabela>";

    return $tabela;
}

//
$tbl_diarios = '';

if($_POST)
{
    $tbl_diarios = lista_diarios($_POST['disciplina'], $Conexao);
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <style>
        .tabela td{
            font-family:verdana;
            font-size:10px;
        }
        </style>
        <script language="javascript">
            <!--
            function send(codigo)
            {
                window.opener.document.form1.diario_id.value=codigo;
                self.close();
            }
            -->
        </script>
    </head>
    <body>
        <h1>Pesquisar Di&aacute;rio</h1>
        <div class="panel" style="width:300px;">
            <form method="POST" action="matricula_avulsa_pesquisar.php">
                Disciplina:<br />
                <input type="text" name="disciplina" value="" size="30" />
                <input type="submit" value="Pesquisar" />
            </form>
        </div>
        <font color="red">Caso o resultado n&atilde;o apare�a seja mais espec&iacute;fico!</font>
        <?php
        echo $tbl_diarios;
        ?>
    </body>
</html>
