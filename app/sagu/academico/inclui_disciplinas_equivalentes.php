<?php

require_once("../common.php");
require_once '../../../core/login/acl.php';

$conn = new connection_factory($param_conn);

$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Você não tem permissão para acessar este formulário!');
}

?>
<html>
    <head>
        <script language="JavaScript">
            function _init()
            {
                document.myform.ref_curso.focus();
            }

            function buscaOpcao(pf_opcao)
            {
                tipo_busca=pf_opcao;
                if (tipo_busca == 1)
                {
                    var url = "../generico/post/lista_cursos_nome.php" +
                        "?id=" + escape(document.myform.ref_curso.value) +
                        "&curso=" + escape(document.myform.curso.value);
                }
                else if (tipo_busca == 2)
                {
                    url = "../generico/post/lista_disciplinas_todas.php" +
                        "?desc=" + escape(document.myform.disciplina.value);
                }
                else if (tipo_busca == 3)
                {
                    url = "../generico/post/lista_disciplinas_todas.php" +
                        "?desc=" + escape(document.myform.disciplina_equivalente.value);
                }
                var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
            }

            function setResult(id,nome)
            {
                if (tipo_busca == 1)
                {
                    document.myform.ref_curso.value = id;
                    document.myform.curso.value = nome;
                }
                else if (tipo_busca == 2)
                {
                    document.myform.ref_disciplina.value = id;
                    document.myform.disciplina.value = nome;
                }
                else if (tipo_busca == 3)
                {
                    document.myform.ref_disciplina_equivalente.value = id;
                    document.myform.disciplina_equivalente.value = nome;
                }
            }
        </script>
    </head>
    <body bgcolor="#FFFFFF" marginwidth="20" marginheight="20" onload="_init()">
        <form method="post" action="post/inclui_disciplinas_equivalentes.php" name="myform">
            <table width="90%" align="center">
                <tr>
                    <td bgcolor="#000099" colspan="2" height="28" align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>Inclusão de Disciplinas Equivalentes</b></font></td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Curso</font></td>
                    <td colspan="2" width="70%">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%">
                                    <input name="ref_curso" type=text size="6" maxlength="10">
                                </td>
                                <td width="100%">
                                    <input type="text" name="curso" size="30">&nbsp;&nbsp;
                                    <input type="button" value="..." onClick="buscaOpcao(1)" name="button22">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Código da Disciplina&nbsp;</font></td>
                    <td colspan="3" width="70%">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%">
                                    <input name="ref_disciplina" type=text size="6" maxlength="10">
                                </td>
                                <td width="100%">
                                    <input type="text" name="disciplina" size="30">&nbsp;&nbsp;
                                    <input type="button" value="..." onClick="buscaOpcao(2)" name="button22">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Código da Disciplina Equivalente&nbsp;</font></td>
                    <td colspan="3" width="70%">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%">
                                    <input name="ref_disciplina_equivalente" type=text size="6" maxlength="10">
                                </td>
                                <td width="100%">
                                    <input type="text" name="disciplina_equivalente" size="30">&nbsp;&nbsp;
                                    <input type="button" value="..." onClick="buscaOpcao(3)" name="button22">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr size="1">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="Submit"  value=" Salvar ">
                        <input type="reset"  name="Submit2" value=" Limpar ">
                        <input type="button" name="Button2" value=" Voltar " onClick="location='consulta_disciplinas_equivalentes.php'">
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
