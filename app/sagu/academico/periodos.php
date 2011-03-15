<?php

require("../common.php");
require("../lib/SQLCombo.php");


$op1_options = SQLArray($sql_periodos_academico);

$sql = "select substr(nome_campus,1,25),id from campus order by nome_campus";


?>
<html>
    <head>
    <?=$DOC_TYPE?>
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

            function ChangeOp1()
            {
                ChangeOption(document.myform.op1,document.myform.ref_anterior);
            }


            function ChangeOp5()
            {
                ChangeOption(document.myform.op5,document.myform.ref_cobranca);
            }


            function ChangeOp6()
            {
                ChangeOption(document.myform.op6,document.myform.ref_local);
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

                alert(code + ' n&atilde;o &eacute; um c&oacute;digo v&aacute;lido!');

                field.focus();

                return true;
            }

        </script>
    </head>
    <body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
          onload="_init()">
        <form method="post" action="post/periodos.php" name="myform">
            <div align="center">
                <table width="90%">
                    <tr>
                        <td bgcolor="#000099" colspan="2" height="35" align="center"><font
                                size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>Inclus&atilde;o de per&iacute;odo letivo</b></font></td>
                    </tr>
                    <tr>
                        <td bgcolor="#CCCCFF"><font
                                face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo&nbsp;<span class="required">*</span>&nbsp;</font></td>
                        <td><input name="id" type=text size="12"></td><td>
                    </tr>

                    <tr>
                        <td bgcolor="#CCCCFF"><font
                                face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Descri&ccedil;&atilde;o&nbsp;<span class="required">*</span>&nbsp;</font></td>
                        <td><input name="descricao" type=text size="40"></td>
                    </tr>
                    <tr>
                        <td bgcolor="#CCCCFF"><font
                                face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Per&iacute;odo
		Anterior </font></td>
                        <td><font color="#000000"> </font>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td><input name="ref_anterior" type=text size="12"
                                               onChange="ChangeCode('ref_anterior','op1')"></td>
                                    <td width="100%"><?php ComboArray("op1",$op1_options,"0","ChangeOp1()") ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#CCCCFF"><font
                                face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data Inicial&nbsp;<span class="required">*</span>&nbsp;<br>
                                &nbsp;(dd/mm/aaaa)</font></td>
                        <td><input name="dt_inicial" type="text" size="12" maxlength="10"> <font
                                face="Verdana, Arial, Helvetica, sans-serif" size="1"> </font></td>
                    </tr>
                    <tr>
                        <td bgcolor="#CCCCFF"><font
                                face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data
		Final&nbsp;<span class="required">*</span>&nbsp;<br>
                                &nbsp;(dd/mm/aaaa)</font></td>
                        <td><input name="dt_final" type=text size="12" maxlength="10"></td>
                    </tr>
                    <tr>
                        <td bgcolor="#CCCCFF"><font
                                face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Nota M&aacute;xima<span class="required">*</span>&nbsp;</font></td>
                        <td><input name="nota_maxima" type="text" size="5" maxlength="5" value="100">
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#CCCCFF"><font
                                face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;M&eacute;dia
		Final Aprova&ccedil;&atilde;o&nbsp;<span class="required">*</span>&nbsp;</font></td>
                        <td><input name="media_final" type="text" size="5" maxlength="5"
                                   value="60"></td>
                    </tr>
                    <tr>
                        <td bgcolor="#CCCCFF"><font
                                face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data
		In&iacute;cio Aulas (Caderno de Chamadas)&nbsp;<span class="required">*</span>&nbsp;<br>
                                &nbsp;(dd/mm/aaaa)</font></font></td>
                        <td><input name="dt_inicio_aula" type=text size="10" maxlength="10"
                                   value=""></font></td>
                    </tr>
                    <input name="tx_acresc" id="tx_acresc" value="" type="hidden" size="5">
                    <input name="tx_cancel" id="tx_cancel" value="" type="hidden" size="5">
                    <input name="ref_status_vest" type=hidden value="1">
                    <tr>
                        <td colspan="2">
                            <hr size="1">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div align="center"><input type="submit" name="Submit"
                                                       value=" Salvar "> <input type="reset" name="Submit2" value=" Limpar ">
                                <input type="button" name="Button" value=" Voltar "
                                       onClick="location='consulta_periodos.php'"></div>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </body>
</html>

