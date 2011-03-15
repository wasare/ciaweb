<?php

require_once("../common.php");
require_once '../../../core/login/acl.php';

$conn = new connection_factory($param_conn);

$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar este formul&aacute;rio!');
}


require_once("../lib/SQLCombo.php");

$op_opcoes = SQLArray("select id||' - '||descricao, id from tipos_curso order by id");
$op_opcoes2 = SQLArray("select nome, id from turno order by nome");

?>

<html>
    <head>
    <link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
        <script language="JavaScript">
            function _init()
            {
                document.myform.id.focus();
            }
            function ChangeOption(opt,fld)
            {
                var i = opt.selectedIndex;

                if ( i != -1 )
                    fld.value = opt.options[i].value;
                else
                    fld.value = '';
            }

            function ChangeOp()
            {
                ChangeOption(document.myform.op,document.myform.ref_tipo_curso);
            }
            function ChangeOp2()
            {
                ChangeOption(document.myform.op2,document.myform.turno);
            }

            function ChangeCode(fld_name,op_name)
            {
                var field = eval('document.myform.' + fld_name);
                var combo = eval('document.myform.' + op_name);
                var code  = field.value;
                var n     = combo.options.length;

                for ( var i=0; i<n; i++ )
                {
                    if ( combo.options[i].value == code )
                    {
                        combo.selectedIndex = i;
                        return;
                    }
                }

                field.focus();

                return true;

            }

            function buscaOpcao()
            {
                var url = "../generico/post/lista_areas_ensino.php" +
                    "?id=" + escape(document.myform.ref_area.value) +
                    "&area=" + escape(document.myform.area.value);

                var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
            }

            function setResult(arg1,arg2)
            {
                document.myform.ref_area.value = arg1;
                document.myform.area.value = arg2;
            }
        </script>
    </head>
    <body bgcolor="#FFFFFF" marginwidth="20" marginheight="20" onload="_init()">
        <form method="post" action="post/confirm_curso_ins.php" name="myform">
            <table width="90%" align="center">
                <tr bgcolor="#000099">
                    <td colspan="2" height="35">
                        <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Inclus&atilde;o de Curso</font></b></font></div>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo&nbsp;<span class="required">*</span> </font></td>
                    <td>
                        <input name="id" type=text size="10" maxlength="10">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Descri&ccedil;&atilde;o&nbsp;<span class="required">*</span> </font></td>
                    <td>
                        <input name="descricao" type=text size="40" maxlength="278">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Abreviatura&nbsp;<span class="required">*</span> </font></td>
                    <td>
                        <input name="abreviatura" type=text size="20" maxlength="40">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Sigla</font></td>
                    <td>
                        <input name="sigla" type=text size="10" maxlength="10">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Total Cr&eacute;ditos&nbsp;</font></td>
                    <td>
                        <input name="total_creditos" type="text" size="10">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Total Carga Hor&aacute;ria&nbsp;</font></td>
                    <td>
                        <input name="total_carga_horaria" type="text" size="10">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Total Semestres</font></td>
                    <td>
                        <input name="total_semestres" type="text" size="10">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="00009C">&nbsp;Tipo do Curso&nbsp;<span class="required">*</span> </font></td>
                    <td> <font color="#000000">
                            <input name="ref_tipo_curso" type="text" size="5" onChange="ChangeCode('ref_tipo_curso','op')">
                            <? ComboArray("op",$op_opcoes,"0","ChangeOp()"); ?> </font>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Grau Acad&ecirc;mico</font></td>
                    <td>
                        <input name="grau_academico" type="text" size="10">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Exig&ecirc;ncias&nbsp;</font></td>
                    <td>
                        <input name="exigencias" type="text" size="40">
                    </td>
                </tr>


                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="00009C">&nbsp;Turno&nbsp;<span class="required">*</span> </font></td>
                    <td> <font color="#000000">
                            <input name="turno" type="text" size="2" onChange="ChangeCode('turno','op2')">
                            <? ComboArray("op2",$op_opcoes2,"2","ChangeOp2()"); ?> </font>
                    </td>
                </tr>

                <input type="hidden" name="agrupo_curso" id="agrupo_curso" value="0" />

<!--    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;&Aacute;rea de Ensino</font></td>
      <td> 
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="9%"> 
              <input name="ref_area" type=text size="5">
            </td>
            <td width="100%">
              <input name="area" type=text size="35">
            </td>
            <td width="0%"> 
              <div align="right">
                <input type="button" value="..." onClick="buscaOpcao()" name="button">
              </div>
            </td>
          </tr>
        </table>
      </td>
                -->
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Reconhecimento&nbsp;</font></td>
                    <td>
                        <textarea name="reconhecimento" cols="40" rows="2"></textarea>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Autoriza&ccedil;&atilde;o&nbsp;</font></td>
                    <td>
                        <textarea name="autorizacao" cols="40" rows="2"></textarea>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Cabe&ccedil;alho do Hist&oacute;rico&nbsp;</font></td>
                    <td>
                        <textarea name="cabecalho_historico" cols="40" rows="2"></textarea>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <hr size="1">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="Submit"  value=" Prosseguir ">
                        <input type="reset"  name="Submit2" value="   Limpar   ">
                        <input type="button" name="Button2" value="   Voltar   " onClick="location='consulta_cursos.php'">
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
