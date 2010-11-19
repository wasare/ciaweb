<?php 

require("../../common.php"); 

$id               = $_POST['id'];
$pai_nome         = $_POST['pai_nome'];
$pai_fone         = $_POST['pai_fone'];
$pai_profissao    = $_POST['pai_profissao'];
$pai_instrucao    = $_POST['pai_instrucao'];
$pai_loc_trabalho = $_POST['pai_loc_trabalho'];
$mae_nome         = $_POST['mae_nome'];
$mae_fone         = $_POST['mae_fone'];
$mae_profissao    = $_POST['mae_profissao'];
$mae_instrucao    = $_POST['mae_instrucao'];
$mae_loc_trabalho = $_POST['mae_loc_trabalho'];


SaguAssert($pai_nome || $mae_nome, "É necessário informar pelo menos o nome do pai ou da mãe!!!");

$conn = new Connection;

$conn->Open();

$sql = "select nextval('seq_filiacao')";

$query = $conn->CreateQuery($sql);

$success = false;

if ( $query->MoveNext() )
{
    $id_filiacao = $query->GetValue(1);

    $success = true;
}

$query->Close();

SaguAssert($success,"Não foi possível obter o código de filiação!");

$sql = " insert into filiacao ( " .
         "      id," .
         "      pai_nome," .
         "      pai_fone," .
         "      pai_profissao," .
         "      pai_instrucao," .
         "      pai_loc_trabalho," .
         "      mae_nome," .
         "      mae_fone," .
         "      mae_profissao," .
         "      mae_instrucao," .
         "      mae_loc_trabalho " .
         " ) values ( " .
         "      '$id_filiacao'," .
         "      '$pai_nome'," .
         "      '$pai_fone'," .
         "      '$pai_profissao'," .
         "      '$pai_instrucao'," .
         "      '$pai_loc_trabalho'," .
         "      '$mae_nome'," .
         "      '$mae_fone'," .
         "      '$mae_profissao'," .
         "      '$mae_instrucao'," .
         "      '$mae_loc_trabalho')";

$ok = $conn->Execute($sql);  // tire o @ para visualizar mensagens de error do sistema DB

SaguAssert($ok,"Não foi possível incluir a filiação!");

$conn->Close();

?>
<html>
    <head>
        <title>Inclusão de Filiação</title>
        <script language="JavaScript">
            function _select(id,nome_pai,nome_mae)
            {
                window.opener.setResult(id, nome_pai, nome_mae);
                window.close();
            }
        </script>
    </head>
    <body bgcolor="#FFFFFF">
        <table width="500" border="0" cellspacing="0" cellpadding="0" height="40" align="center">
            <tr bgcolor="#000099">
                <td height="35">
                    <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Opera&ccedil;&atilde;o Conclu&iacute;da</font></b></font><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><b></b></font></div>
                </td>
            </tr>
        </table>
        <p align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#FF0000"><b>Filia&ccedil;&atilde;o Cadastrada com sucesso:</b></font></p>
        <p align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">C&oacute;digo de Filia&ccedil;&atilde;o: </font><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">
                    <script language="PHP">
echo("<a href=\"javascript:_select($id_filiacao,'$pai_nome','$mae_nome')\"> $id_filiacao </a>");
                    </script>
        </font></b></p>
        <center>
            <?
            echo"<input type=\"button\" name=\"Button2\" value=\"  Continuar  \" onClick=\"javascript:_select($id_filiacao,'$pai_nome','$mae_nome')\">";
            ?>
        </center>
    </body>
</html>
