<? 

require("../../common.php"); 

$id   = $_POST['id'];
$desc = $_POST['desc'];

?>

<html>
    <head>
        <title>Localizar Países</title>
        <script language="JavaScript">
            function _select(id,desc)
            {
                window.opener.setResult(id,desc);
                window.close();
            }
        </script>
    </head>
    <body bgcolor="#FFFFFF">
        <form method="post" action="lista_paises.php">
            <table width="500" border="0" cellspacing="2" cellpadding="0" align="center">
                <tr bgcolor="#0066CC">
                    <td colspan="4" height="28" align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF"><b>Localizar Pa&iacute;ses</b></font></td>
                </tr>
                <tr bgcolor="#CCCCCC">
                    <td width="20">&nbsp;</td>
                    <td width="50"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;C&oacute;digo</font></td>
                    <td width="303"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;Descri&ccedil;&atilde;o</font></td>
                    <td width="50">&nbsp;</td>
                </tr>
                <tr>
                    <td width="20">&nbsp;</td>
                    <td width="50">
                        <input type="text" name="id" size="8" value="<?echo($id);?>">
                    </td>
                    <td width="303">
                        <input type="text" name="desc" value="<?echo($desc);?>" size="40">
                    </td>
                    <td width="50" align="right">
                        <input type="submit" name="Submit" value=" Localizar ">
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
                    &nbsp;C&oacute;digo </font></td>
                    <td colspan="2"> <font face="Arial, Helvetica, sans-serif" size="2" color="#000000">
                        &nbsp;</font><font size="2" face="Arial, Helvetica, sans-serif">Descri&ccedil;&atilde;o</font><font face="Arial, Helvetica, sans-serif" size="2" color="#000000">
                    </font></td>
                </tr>
                <?php
                $conn = new Connection;

                $conn->Open();

                // note the parantheses in the where clause !!!
                $sql = "select id, nome from pais";

                $where = '';

                if ( $id != '' )
                $where .= " and id = '$id'";

                if ( $desc != '' )
                $where .= " and upper(nome) like upper('$desc%')";

                if ( substr($where,0,5) == " and " )
                $where = " where " . substr($where,5);

                $sql .= $where . " order by nome";

                $query = $conn->CreateQuery($sql);

                for ( $i=0; $i<25 && $query->MoveNext(); $i++ )
                {
                    list ( $id, $nome ) = $query->GetRowValues();

                    $href = "<a href=\"javascript:_select($id,'$nome')\"><img src=\"../../images/select.gif\" border=0 title=\"Selecionar\"></a>";

                    if ( $i % 2 == 0)
                    {
                        ?>
                <tr bgcolor="#EEEEFF" valign="top">
                    <td width="20"><font face="Arial, Helvetica, sans-serif" size="2">
                            <?php
                            echo($href);
                            ?>
                    </font></td>
                    <td width="50"> <font face="Arial, Helvetica, sans-serif" size="2">
                            <?php
                            echo($id);
                            ?>
                    </font></td>
                    <td colspan="2"> <font face="Arial, Helvetica, sans-serif" size="2">
                            <script language="PHP">
        echo($nome);
                            </script>
                    </font><font face="Arial, Helvetica, sans-serif" size="2"> </font></td>
                </tr>
                <?php
            } // if 

            else
            {
                ?>
                <tr bgcolor="#FFFFEE" valign="top">
                    <td width="20"><font face="Arial, Helvetica, sans-serif" size="2">
                            <?php
                            echo($href);
                            ?>
                    </font></td>
                    <td width="50"> <font face="Arial, Helvetica, sans-serif" size="2">
                            <?php
                            echo($id);
                            ?>
                    </font></td>
                    <td colspan="2"> <font face="Arial, Helvetica, sans-serif" size="2">
                            <?php
                            echo($nome);
                            ?>
                    </font><font face="Arial, Helvetica, sans-serif" size="2"> </font></td>
                </tr>
                <?php
            } // else
        } // for

        $hasmore = $query->MoveNext();

        $query->Close();
        $conn->Close();

        ?>
                <tr>
                    <td colspan="4" align="center">
                        <?php
                        if ( $hasmore )
                        echo("<br>Resultado maior do que 25 linhas<br>");
                        ?>
                        <hr size="1" width="500">
                        <input type="button" name="Button" value=" Voltar " onClick="javascript:window.close()">
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
