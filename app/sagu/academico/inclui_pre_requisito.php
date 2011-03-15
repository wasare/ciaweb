<?php

require_once("../common.php");
require_once '../../../core/login/acl.php';

$conn = new connection_factory($param_conn);

$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Voc� n�o tem permiss�o para acessar este formul�rio!');
}

?>
<html>
    <head>
    <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
        <script language="JavaScript">
            function buscaOpcao(pf_opcao)
            {
                tipo_busca=pf_opcao;
                if (tipo_busca == 1)
                {
                    var url = "../generico/post/lista_areas_ensino.php" +
                        "?id=" + escape(document.myform.ref_area.value) +
                        "&area=" + escape(document.myform.area.value);
                }
                else if (tipo_busca == 2)
                {
                    var url = "../generico/post/lista_proficiencias.php" +
                        "?id=" + escape(document.myform.ref_proficiencia.value) +
                        "&area=" + escape(document.myform.proficiencia.value);
                }
                else if (tipo_busca == 3)
                {
                    var url = "../generico/post/lista_cursos_nome.php" +
                        "?id=" + escape(document.myform.ref_curso.value) +
                        "&curso=" + escape(document.myform.curso.value);
                }
                else if (tipo_busca == 4)
                {
                    url = "../generico/post/lista_disciplinas_todas.php" +
                        "?desc=" + escape(document.myform.ref_disciplina.value);
                }
                else
                {
                    url = "../generico/post/lista_disciplinas_todas.php" +
                        "?desc=" + escape(document.myform.ref_disciplina_pre.value);
                }


                var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
            }

            function setResult(id,nome)
            {
                if (tipo_busca == 1)
                {
                    document.myform.ref_area.value = id;
                    document.myform.area.value = nome;
                }
                else if (tipo_busca == 2)
                {
                    document.myform.ref_proficiencia.value = id;
                    document.myform.proficiencia.value = nome;
                }
                else if (tipo_busca == 3)
                {
                    document.myform.ref_curso.value = id;
                    document.myform.curso.value = nome;
                }
                else if (tipo_busca == 4)
                {
                    document.myform.ref_disciplina.value = id;
                    document.myform.disciplina.value = nome;
                }
                else
                {
                    document.myform.ref_disciplina_pre.value = id;
                    document.myform.disciplina_pre.value = nome;
                }
            }

        </script>
    <body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
        <form method="post" action="post/inclui_pre_requisito.php" name="myform">
            <table width="90%" align="center">
                <tr>
                    <td bgcolor="#000099" colspan="2" height="28" align="center"> <font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>&nbsp;Inclus&atilde;o
                                de Pr&eacute;-Requisitos</b></font></td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Curso&nbsp;<span class="required">*</span> </font></td>
                    <td colspan="3" width="70%">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%">
                                    <input name="ref_curso" type=text size="6" maxlength="10">
                                </td>
                                <td width="100%">
                                    <input type="text" name="curso" size="30">&nbsp;&nbsp;
                                    <input type="button" value="..." onClick="buscaOpcao(3)" name="button22">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo da Disciplina&nbsp;<span class="required">*</span> </font></td>
                    <td colspan="3" width="70%">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%">
                                    <input name="ref_disciplina" type=text size="6" maxlength="10">
                                </td>
                                <td width="100%">
                                    <input type="text" name="disciplina" size="30">&nbsp;&nbsp;
                                    <input type="button" value="..." onClick="buscaOpcao(4)" name="button22">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo da Disciplina Pr&eacute;-Requisito &nbsp;<span class="required">*</span><!--ou<br>&nbsp;C�digo da Disciplina Co-Requisito ou<br>&nbsp;C�digo da Disciplina Profici�ncia&nbsp;--></font></td>
                    <td colspan="2" width="70%">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="10%">
                                    <input name="ref_disciplina_pre" type=text size="6" maxlength="10">
                                </td>
                                <td width="100%">
                                    <input type="text" name="disciplina_pre" size="30">&nbsp;&nbsp;
                                    <input type="button" value="..." onClick="buscaOpcao(5)" name="button22">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!--<tr>
                   <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Tipo</font></td>
                   <td colspan="3" width="70%">
                     <select name="tipo" size="1">
                         <option value="" selected>--- Selecione ---</option>
                         <option value="P">Pr&eacute;-Requisito</option>
                         <option value="C">Co-Requisito</option>
                     </select>

                   </td>
                </tr>-->
                <input type="hidden" id="tipo" name="tipo" size="1"  value="P" />
                <!--<tr>
                  <td colspan="2">
                    <hr size="1">
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#CCCCFF" width="30%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo da &Aacute;rea Pr&eacute;-Requisito&nbsp;</font></td>
                  <td colspan="3" width="70%">
                   <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                     <td width="10%">
                        <input type="text" name="ref_area" size="6" maxlength="10">
                     </td>
                     <td width="100%"><font color="#000000">
                        <input type="text" name="area" size="30"></font>
	 </td>
                     <td><font color="#000000"><font color="#000000"> <font color="#000000">
                        <input type="button" value="..." onClick="buscaOpcao(1)" name="button">
                     </font></font></font></td>
                    </tr>
                   </table>
                  </td>
                </tr>-->
                <input type="hidden" id="ref_area" name="ref_area" size="6" maxlength="10" />
                <input type="hidden" id="area" name="area" size="30" />
                <input type="hidden" id="horas_area" name="horas_area" size="6" maxlength="6" />

<!--    <tr> 
   <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;N� de Horas Pr&eacute;-Requisito &Aacute;rea&nbsp;</font></td>
   <td width="100%"><INPUT name="horas_area" type="text" size="6" maxlength="6"></td>
 </tr>-->

                <tr>
                    <td colspan="2">
                        <hr size="1">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="Submit"  value=" Salvar ">
                        <input type="reset"  name="Submit2" value=" Limpar ">
                        <input type="button" name="Button2" value=" Voltar " onClick="location='consulta_inclui_pre_requisito.php'">

                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
