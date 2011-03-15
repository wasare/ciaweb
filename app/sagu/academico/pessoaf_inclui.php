<?php require("../common.php"); ?>

<html>
    <head>
    <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
        <script language="JavaScript">

            function _init()
            {
                document.myform.nome.focus();
            }

            var tipo_busca;

            function incluiFiliacao()
            {
                tipo_busca = 6;
                var wnd = window.open("../generico/filiacao_inclui.php",'buscaPessoa','toolbar=no,width=550,height=350,scrollbars=yes');
            }

            function buscaCurso()
            {
                tipo_busca = 7;
                var url = "../generico/post/lista_cursos_externos.php" +
                    "?id=" + escape(document.myform.ref_curso_2g.value) +
                    "&curso=" + escape(document.myform.curso.value);

                var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
            }

            function buscaEscola()
            {
                tipo_busca = 8;
                var url = "../generico/post/lista_escolas.php" +
                    "?id=" + escape(document.myform.ref_escola_2g.value) +
                    "&cnome=" + escape(document.myform.escola_2g.value);

                var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
            }

            function buscaCidade(pf_opcao)
            {
                var url;
                tipo_busca=pf_opcao;

                if (tipo_busca == 1)
                    url = '../generico/post/lista_cidades.php' +
                    '?cnome=' + escape(document.myform.cnome.value);

                else if (tipo_busca == 2)
                    url = '../generico/post/lista_cidades.php' +
                    '?cnome=' + escape(document.myform.cnome_rg.value);

                else if (tipo_busca == 3)
                    url = '../generico/post/lista_cidades.php' +
                    '?cnome=' + escape(document.myform.naturalidade.value);

                else if (tipo_busca == 5)
                    url = '../generico/post/lista_cidades.php' +
                    '?cnome=' + escape(document.myform.d_cidade_2g.value);

                window.open(url,"popWindow","toolbar=no,width=600,height=368,top=5,left=5,directories=no,menubar=no,scrollbars=yes");
            }

            function setResult(id,nome,cep,ref_pais,ref_estado,pais_desc)
            {
                if (tipo_busca == 1)
                {
                    document.myform.ref_cidade.value = id;
                    document.myform.cnome.value = nome;
                    document.myform.cep.value = cep;
                }

                else if (tipo_busca == 2)
                {
                    document.myform.rg_cidade.value = id;
                    document.myform.cnome_rg.value = nome;
                }

                else if (tipo_busca == 3)
                {
                    document.myform.ref_naturalidade.value = id;
                    document.myform.naturalidade.value = nome;
                    document.myform.ref_nacionalidade.value = ref_pais;
                    document.myform.nacionalidade.value = pais_desc;
                }

                else if (tipo_busca == 4)
                {
                    document.myform.cidade_1g.value = id;
                    document.myform.d_cidade_1g.value = nome;
                }

                else if (tipo_busca == 5)
                {
                    document.myform.cidade_2g.value = id;
                    document.myform.d_cidade_2g.value = nome;
                }

                else if (tipo_busca == 6)
                {
                    document.myform.ref_filiacao.value = id;
                    document.myform.nome_filiacao.value = nome + ' ' + cep;
                }

                else if (tipo_busca == 7)
                {
                    document.myform.ref_curso_2g.value = id;
                    document.myform.curso.value = nome;
                }
                else if (tipo_busca == 8)
                {
                    document.myform.ref_escola_2g.value = id;
                    document.myform.escola_2g.value = nome;
                }

            }

        </script>
    </head>
    <body bgcolor="#FFFFFF" marginwidth="20" marginheight="20" onload="_init()">
        <form method="post" action="post/confirm_pessoaf_inclui.php" name="myform">
            <table width="90%" align="center">

                <tr bgcolor="#000099">
                    <td height="35" colspan="2">
                        <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Inclus&atilde;o de Pessoa F&iacute;sica</font></b></font></div>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nome&nbsp;<span class="required">*</span>&nbsp;</font></font></td>
                    <td height="30" colspan="3">
                        <input name="nome" type=text size="40" maxlength="80">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Rua&nbsp;<span class="required">*</span>&nbsp;</font></font></td>
                    <td colspan="3">
                        <input name="rua" type=text size="40" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Complemento</font></td>
                    <td colspan="3">
                        <input name="complemento" type=text size="40" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Bairro</font></td>
                    <td colspan="3">
                        <input name="bairro" type=text size="40" maxlength="40">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cidade&nbsp;<span class="required">*</span>&nbsp;</font></font></td>
                    <td colspan="3">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%">
                                    <input type="text" name="ref_cidade" size="5" maxlength="10">
                                </td>
                                <td width="100%"><font color="#000000">
                                        <input type="text" name="cnome" size="33">
                                </font></td>
                                <td><font color="#000000"><font color="#000000"> <font color="#000000">
                                                <input type="button" value="..." onClick="buscaCidade(1)" name="button">
                                </font></font></font></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cep&nbsp;<span class="required">*</span>&nbsp;</font></font></td>
                    <td colspan="3">
                        <input name="cep" type=text size="15" maxlength="9">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data de Nascimento&nbsp;<span class="required">*</span>&nbsp;</font><br><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;(dd-mm-aaaa)</font></td>
                    <td colspan="3">
                        <input type="text" name="dt_nascimento" size="10" maxlength="10">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Sexo&nbsp;<span class="required">*</span>&nbsp;</font></font></td>
                    <td colspan="3">
                        <select name="sexo">
                            <option value="" selected>----- Selecione -----</option>
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Permite Divulga&ccedil;&atilde;o<br>&nbsp;dos Dados Pessoais&nbsp;</font></td>
                    <td colspan="3">
                        <select name="fl_dados_pessoais">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Fone
                    Particular</font></td>
                    <td colspan="3">
                        <input name="fone_particular" type=text size="15" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Fone
                    Profissional</font></td>
                    <td colspan="3">
                        <input name="fone_profissional" type=text size="15" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Fone
                    Celular</font></td>
                    <td colspan="3">
                        <input name="fone_celular" type=text size="15" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Fone
                    Recado</font></td>
                    <td colspan="3">
                        <input name="fone_recado" type=text size="15" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;E-mail
                    Principal </font></td>
                    <td colspan="3">
                        <input name="email" type=text size="40" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;E-mail
                    Alternativo</font></td>
                    <td colspan="3">
                        <input name="email_alt" type=text size="40" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Estado
                    Civil</font></td>
                    <td colspan="3">
                        <select name="estado_civil" size="1">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="S">Solteiro</option>
                            <option value="C">Casado</option>
                            <option value="V">Vi&uacute;vo</option>
                            <option value="D">Desquitado</option>
                            <option value="U">Uni&atilde;o Est&aacute;vel</option>
                            <option value="E">Solteiro Emancipado</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data de Cadastro<br>&nbsp;(dd-mm-aaaa)</font></td>
                    <td colspan="3">
                        <input type="text" name="dt_cadastro" size="10" maxlength="10" value="<? echo date("d-m-Y") ?>">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Deficiente&nbsp;</font></td>
                    <td>
                        <select name="deficiencia">
                            <option value="0">N&atilde;o</option>
                            <option value="1">Sim</option>
                        </select>
                        <input name="deficiencia_desc" type=text size="40">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;N&uacute;mero
                    RG </font></td>
                    <td colspan="3">
                        <input name="rg_numero" type=text size="20" maxlength="20">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;&oacute;rg&atilde;o Expedidor RG</font></td>
                    <td colspan="3">
                        <input name="rg_orgao" type=text size="6" maxlength="5">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cidade
                    RG </font></td>
                    <td colspan="3">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%">
                                    <input name="rg_cidade" type=text size="6" maxlength="10">
                                </td>
                                <td width="100%"><font color="#000000">
                                        <input type="text" name="cnome_rg" size="35">
                                </font></td>
                                <td><font color="#000000"><font color="#000000"><font color="#000000">
                                                <input type="button" value="..." onClick="buscaCidade(2)" name="button2">
                                </font></font></font></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data do RG<br>&nbsp;(dd-mm-aaaa)</font></td>
                    <td colspan="3">
                        <input type="text" name="rg_data" size="10" maxlength="10">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Filia&ccedil;&atilde;o
                    </font></td>
                    <td colspan="3">
                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td>
                                    <input name="ref_filiacao" type=text size="6" maxlength="8">
                                </td>
                                <td width="100%">
                                    <input type="text" name="nome_filiacao" size="35">
                                </td>
                                <td><font color="#000000"><font color="#000000"><font color="#000000">
                                                <input type="button" value="..." onClick="incluiFiliacao()" name="button223">
                                </font></font></font></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" height="34"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Naturalidade
                    </font></td>
                    <td colspan="3" height="34">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%"><font color="#000000">
                                        <input name="ref_naturalidade" type=text size="6" maxlength="10">
                                </font></td>
                                <td width="100%"><font color="#000000">
                                        <input type="text" name="naturalidade" size="35">
                                </font></td>
                                <td>
                                    <div align="right"><font color="#000000"><font color="#000000"><font color="#000000"><font color="#000000"><font color="#000000">
                                                            <input type="button" value="..." onClick="buscaCidade(3)" name="button22">
                                    </font></font></font></font></font></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" height="32"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nacionalidade
                    </font></td>
                    <td height="32" colspan="3">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <input name="ref_nacionalidade" type=text size="6" maxlength="6">
                                </td>
                                <td width="100%">
                                    <input type="text" name="nacionalidade" size="35">
                                </td>
                                <td align="right"><font color="#000000"><font color="#000000"><font color="#000000">
                                </font></font></font></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;N&ordm;
                    do CPF</font></td>
                    <td colspan="3">
                        <input name="cod_cpf_cgc" type=text size="20" maxlength="11">
                        <font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#000099"><font size="2">Obs.: Somente n&uacute;meros</font></font>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;T&iacute;tulo
                    Eleitor</font></td>
                    <td colspan="3">
                        <input name="titulo_eleitor" type=text size="20" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Placa Carro</font></td>
                    <td colspan="3">
                        <input name="placa_carro" type=text size="10" maxlength="10">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Carteirinha</font></td>
                    <td colspan="3">
                        <select name="fl_cartao">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr bgcolor="#ffeecc">
                    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FF3333">Documenta&ccedil;&atilde;o</font></b></font></td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;pia do RG</font></td>
                    <td colspan="3">
                        <select name="rg_num">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;pia do CPF</font></td>
                    <td colspan="3">
                        <select name="cpf">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;pia do T&iacute;tulo de Eleitor</font></td>
                    <td colspan="3">
                        <select name="titulo_eleitord">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Quita&ccedil;&atilde;o Eleitoral</font></td>
                    <td colspan="3">
                        <select name="quitacao_eleitoral">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Hist&oacute;rico Original</font></td>
                    <td colspan="3">
                        <select name="hist_original">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;pia do Hist&iacute;rico</font></td>
                    <td colspan="3">
                        <select name="hist_escolar">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Documenta&ccedil;&atilde;o Militar</font></td>
                    <td colspan="3">
                        <select name="doc_militar">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Foto</font></td>
                    <td colspan="3">
                        <select name="foto">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Atestado M&eacute;dico</font></td>
                    <td colspan="3">
                        <select name="atestado_medico">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Diploma Autenticado</font></td>
                    <td colspan="3">
                        <select name="diploma_autenticado">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Solteiro Emancipado</font></td>
                    <td colspan="3">
                        <select name="solteiro_emancipado">
                            <option value="" selected>--- Selecione ---</option>
                            <option value="t"> Sim</option>
                            <option value="f"> N&atilde;o</option>
                        </select>
                    </td>
                </tr>
                <tr bgcolor="#ffeecc">
                    <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FF3333">Informa&ccedil;&otilde;es Ensino M&eacute;dio</font></b></font></td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Ano conclus&atilde;o</font></td>
                    <td colspan="3">
                        <input name="ano_2g" type=text size="6" maxlength="4">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Escola</font></td>
                    <td colspan="3">
                        <table width="100%%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%">
                                    <input name="ref_escola_2g" type=text value="<?echo($ref_escola_2g);?>" size="6" maxlength="10">
                                </td>
                                <td width="100%">
                                    <input type="text" name="escola_2g" size="35" value="<?echo($escola);?>">
                                </td>
                                <td width="10%">
                                    <input type="button" value="..." onClick="buscaEscola()" name="button23">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cidade
                    </font></td>
                    <td colspan="3"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b></b></font>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%">
                                    <input name="cidade_2g" type=text size="6" maxlength="10">
                                </td>
                                <td width="100%">
                                    <input type="text" name="d_cidade_2g" size="35">
                                </td>
                                <td><font color="#000000"><font color="#000000"><font color="#000000">
                                                <input type="button" value="..." onClick="buscaCidade(5)" name="button222">
                                </font></font></font></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Curso</font></td>
                    <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b></b></font>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%">
                                    <input name="ref_curso_2g" type=text size="6">
                                </td>
                                <td width="100%">
                                    <input type="text" name="curso" size="35">
                                </td>
                                <td>
                                    <div align="right">
                                        <input type="button" value="..." onClick="buscaCurso()" name="button22">
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFFFFF" height="16">&nbsp;</td>
                    <td colspan="3" valign="middle" height="16">&nbsp;</td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo
                    Passivo</font></td>
                    <td colspan="3">
                        <input name="cod_passivo" type=text size="20" maxlength="20">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo
                    Externo</font></td>
                    <td colspan="3">
                        <input name="cod_externo" type=text size="6" maxlength="6">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Observa&ccedil;&otilde;es</font></td>
                    <td colspan="3">
                        <textarea name="obs" cols="40" rows="5"></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <hr size="1">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" name="op_cidade" value="2">
                    </td>
                    <td colspan="3">
                        <input type="submit" name="Submit"  value=" Prosseguir ">
                        <input type="reset"  name="Submit2" value="   Limpar   ">
                        <input type="button" name="Button2" value="   Voltar   " onClick="location='consulta_inclui_pessoa.php'">
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
