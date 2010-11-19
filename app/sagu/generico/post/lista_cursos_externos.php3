<? 

require("../../common.php"); 


$id    = $_POST['id'];
$curso = $_POST['curso'];

?>

<html>
    <head>
        <title>Localizar Cursos Externos</title>
        <script language="JavaScript">
            function _select(curso_id,curso_nome)
            {
                if ( window.callSetResult )
                    window.callSetResult(curso_id,curso_nome);
                else
                    window.opener.setResult(curso_id,curso_nome);
                window.close();
            }
        </script>
    </head>
    <body bgcolor="#FFFFFF">
        <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000">
        </font></b></font></font> <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000">
        </font></b></font></font> <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000">
        </font></b></font></font> <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000">
        </font></b></font></font> <font face="Verdana, Arial, Helvetica, sans-serif"><font size="2"><b><font color="#FF0000">
        </font></b></font></font>
        <form method="post" action="lista_cursos_externos.php3">
            <table width="500" border="0" cellspacing="2" cellpadding="0" align="center">
                <tr bgcolor="#0066CC">
                    <td colspan="4" height="28" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><b>Localizar Cursos Externos</b></font></td>
                </tr>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td><font size="2" face="Arial, Helvetica, sans-serif">C&oacute;digo:</font></td>
                    <td><font size="2" face="Arial, Helvetica, sans-serif">Descricao:</font></td>
                    <td width="50">&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="text" name="id" size="8" value="<?echo($id);?>">
                    </td>
                    <td>
                        <input type="text" name="curso" value="<?echo($curso);?>" size="40">
                    </td>
                    <td width="50">
                        <input type="submit" name="Submit" value="Localizar">
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <hr size="1" width="500">
                    </td>
                </tr>
                <tr bgcolor="#CCCCCC">
                    <td width="20" align="left">&nbsp;</td>
                    <td width="50"> <font face="Arial, Helvetica, sans-serif" size="2" color="#000000">
                    C&oacute;digo </font></td>
                    <td colspan="2"> <font face="Arial, Helvetica, sans-serif" size="2" color="#000000">
                    Curso</font></td>
                </tr>
<?php 

if ( $id != '' || $curso != '' )
{
    $conn = new Connection;
    $conn->Open();

    // note the parantheses in the where clause !!!
    $sql = "select id, nome from cursos_externos";
    $where = '';

    if ( $id != '' )
    $where .= " id = $id";

    if ( $curso != '' )
    if ( $where != '' )
    $where .= " and upper(nome) like upper('%$curso%')";
    else
    $where .= " upper(nome) like upper('%$curso%')";

    $sql .= " where" . $where . " order by id";

    $query = $conn->CreateQuery($sql);

    for ( $i=0; $i<25 && $query->MoveNext(); $i++ )
    {
        list ( $id, $curso ) = $query->GetRowValues();
        $href = "<a href=\"javascript:_select($id, '$curso')\"><img src=\"../../images/select.gif\" title='Selecionar' border=0></a>";

        if ( $i % 2 == 0)
        {
                ?>
                <tr bgcolor="#EEEEFF" valign="top">
                <td width="20"><font face="Arial, Helvetica, sans-serif" size="2">
                <script language="PHP">
                echo($href);
                </script>
                </font></td>
                <td width="50"> <font face="Arial, Helvetica, sans-serif" size="2">
                <script language="PHP">
                echo($id);
                </script>
                </font></td>
                <td colspan="2"> <font face="Arial, Helvetica, sans-serif" size="2">
                <script language="PHP">
                echo($curso);
                </script></font></td>
                </tr>
                <script language="PHP">
        } // if

        else
        {
                </script>
                <tr bgcolor="#FFFFEE" valign="top">
                <td width="20"><font face="Arial, Helvetica, sans-serif" size="2">
                <script language="PHP">
                echo($href);
                </script>
                </font></td>
                <td> <font face="Arial, Helvetica, sans-serif" size="2">
                        <script language="PHP">
            echo($id);
                        </script>
                </font></td>
                <td colspan="2"> <font face="Arial, Helvetica, sans-serif" size="2">
                        <script language="PHP">
            echo($curso);
                        </script>
                </font></td>
                </tr>
                <script language="PHP">
        } // else
    } // for

    $hasmore = $query->MoveNext();

    $query->Close();
    $conn->Close();
} // if
                </script>
                <tr>
                    <td colspan="4" align="center">
                        <script language="PHP">
if ( $hasmore )
echo("<center>Se a curso externo não estiver listado, seja mais específico.</center>");
                        </script>
                        <hr size="1" width="500">
                        <input type="button" name="Button" value=" Voltar " onClick="javascript:window.close()">
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
