<html>
    <title>Cadastro de Cidade</title>
    <head>
   <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
        <script language="JavaScript">
            var tipo_busca;

            function buscaPais()
            {
                tipo_busca = 1;

                url = 'post/lista_paises.php' +
                    '?id=' + escape(document.myform.ref_pais.value) +
                    '&desc=' + escape(document.myform.nome_pais.value);

                window.open(url,"busca","toolbar=no,width=530,height=320,top=80,left=55,directories=no,menubar=no,scrollbars=yes");
            }

            function buscaEstado()
            {
                tipo_busca = 2;

                url = 'post/lista_estados.php' +
                    '?id=' + escape(document.myform.ref_estado.value) +
                    '&desc=' + escape(document.myform.nome_estado.value) +
                    '&ref_pais=' + escape(document.myform.ref_pais.value);

                window.open(url,"busca","toolbar=no,width=530,height=320,top=80,left=55,directories=no,menubar=no,scrollbars=yes");
            }

            function setResult(arg1,arg2)
            {
                if ( tipo_busca == 1 )
                {
                    document.myform.ref_pais.value = arg1;
                    document.myform.nome_pais.value = arg2;
                }

                else if ( tipo_busca == 2 )
                {
                    document.myform.ref_estado.value = arg1;
                    document.myform.nome_estado.value = arg2;
                }
            }
        </script>
    </head>
    <body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
        <form method="post" action="post/cidades_inclui.php" name="myform">
            <table width="90%" align="center">
                <tr>
                    <td bgcolor="#000099" colspan="2" height="28" align="center"> <font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>&nbsp;Inclus&atilde;o
                    de Cidade</b></font></td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome&nbsp;<span class="required">*</span>&nbsp;</font></td>
                    <td>
                        <input name="nome" type=text size="40">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;CEP&nbsp;<span class="required">*</span>&nbsp;</font></td>
                    <td>
                        <input name="cep" type=text size="10">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Pais&nbsp;<span class="required">*</span>&nbsp;</font></td>
                    <td>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <input type="text" name="ref_pais" size="6">
                                </td>
                                <td width="100%">
                                    <input name="nome_pais" type=text size="35">
                                </td>
                                <td align="right">
                                    <input type="button" name="Submit3" value="..." onclick="buscaPais()">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Estado&nbsp;<span class="required">*</span>&nbsp;</font></td>
                    <td>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <input name="ref_estado" type=text size="6">
                                </td>
                                <td width="100%">
                                    <input name="nome_estado" type=text size="35">
                                </td>
                                <td align="right">
                                    <input type="button" name="Submit32" value="..." onClick="buscaEstado()">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr size="1" width="100%">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="Submit"   value=" Salvar ">
                        <input type="reset"  name="Submit2"  value=" Limpar ">
                        <input type="button" name="Submit22" value=" Voltar " onClick="location='consulta_cidades.php'">
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
