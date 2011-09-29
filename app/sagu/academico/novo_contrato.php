<?php

require("../lib/SQLCombo.php");
require("../common.php");

$conn = new connection_factory($param_conn);

// Verifica as permissoes de acesso do usuario quanto ao arquivo
$ACL_FILE = __FILE__;
require_once($BASE_DIR .'core/login/acesso.php');
// ^ Verifica as permissoes de acesso do usuario quanto ao arquivo ^ //



$op_opcoes1 = SQLArray("select nome_campus, id from campus order by nome_campus");
$op_opcoes2 = SQLArray("select id || '  ' || substr(descricao,1,30),id from motivo order by id");
$op_opcoes3 = SQLArray("select id || '  ' || substr(descricao,1,30),id from motivo order by id");
$op_opcoes4 = SQLArray("select id || '  ' || substr(descricao,1,30),id from motivo order by id");
$op_opcoes5 = SQLArray("select id || '  ' || substr(descricao,1,30),id from motivo order by id");
$op_opcoes6 = SQLArray("$sql_periodos_academico");
$op_opcoes7 = SQLArray("$sql_periodos_academico");
$op_opcoes8 = SQLArray("select id || '  ' || substr(descricao,1,30),id from motivo order by id");
$op_opcoes9 = SQLArray("$sql_periodos_academico");

?>

<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="JavaScript">
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
  ChangeOption(document.myform.op1,document.myform.ref_campus);
}

function ChangeOp2()
{
  ChangeOption(document.myform.op2,document.myform.ref_motivo_ativacao);
}

function ChangeOp3()
{
  ChangeOption(document.myform.op3,document.myform.ref_motivo_inicial);
}
function ChangeOp4()
{
  ChangeOption(document.myform.op4,document.myform.status);
}
function ChangeOp5()
{
  ChangeOption(document.myform.op5,document.myform.ref_motivo_desativacao);
}
function ChangeOp6()
{
  ChangeOption(document.myform.op6,document.myform.ref_last_periodo);
}
function ChangeOp7()
{
  ChangeOption(document.myform.op7,document.myform.ref_periodo_formatura);
}
function ChangeOp8()
{
  ChangeOption(document.myform.op8,document.myform.ref_motivo_entrada);
}
function ChangeOp9()
{
  ChangeOption(document.myform.op9,document.myform.ref_periodo_turma);
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
<script language="javascript">
var tipo_busca;

function buscaCurso()
{
  tipo_busca = 1;

  var url = "../generico/post/lista_cursos_nome.php" + 
            "?id=" + escape(document.myform.ref_curso.value) + 
            "&curso=" + escape(document.myform.curso.value);

  var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
}

function _busca()
{
  tipo_busca = 2;

  var url = "../generico/post/lista_pessoas.php" +
            "?pnome=" + escape(document.myform.pessoa.value);

  var wnd = window.open(url,'busca','toolbar=no,width=530,height=350,scrollbars=yes');
}

function setResult(arg1,arg2)
{
  if  (tipo_busca == 1)
  {
    document.myform.ref_curso.value = arg1;
    document.myform.curso.value = arg2;
  }
  else
  {
    document.myform.ref_pessoa.value = arg1;
    document.myform.pessoa.value = arg2;
  }
}
</script>
<script language="JavaScript">
function _init()
{
  document.myform.ref_campus.focus();
}
</script>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20" onload="_init()">
<form method="post" action="post/novo_contrato.php" name="myform">
<table width="90%" align="center">
    <tr> 
      <td bgcolor="#000099" colspan="2" height="35" align="center"> <font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>Inclus&atilde;o de Contrato</b></font></td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="00009C">&nbsp;Campus&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td> <font color="#000000"> 
        <input name="ref_campus" type=text value="1" onChange="ChangeCode('ref_campus','op1')" size="10">
        <?PHP ComboArray("op1",$op_opcoes1,"0","ChangeOp1()"); ?> </font></td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="00009C">&nbsp;Aluno&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td>
        <table width="65%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="0%"><font color="#000000"> 
              <input name="ref_pessoa" type=text size="10" value="<? echo($id); ?>">
              </font></td>
            <td width="100%"><font color="#000000"> 
              <input type="text" name="pessoa" size="35" value="<? echo($nome); ?>">
              </font></td>
            <td width="0%"> 
              <div><font color="#000000"> 
                <input type="button" value="..." onClick="_busca()" name="button2">
                </font></div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font color="#000099"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Curso&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="14%"><font color="#000000"> 
              <input name="ref_curso" type=text size="10">
              </font></td>
            <td width="86%"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="55%"><font color="#000000"> 
                    <input type="text" name="curso" size="35">
                    </font></td>
                  <td width="0%"> 
                    <div> 
                      <input type="button" value="..." onClick="buscaCurso()" name="button22">
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Turma&nbsp;</font></td>
      <td>
        <input name="turma" type="text" size="10">
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;In&iacute;cio da turma&nbsp;&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td colspan="2"> <font color="#000000">
        <input name="ref_periodo_turma" type="text" size="5" onChange="ChangeCode('ref_periodo_turma','op9')">
        <?PHP ComboArray("op9",$op_opcoes9,"0","ChangeOp9()"); ?> </font></td>
       </td>
    </tr>
    <!--<tr>
       <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Inscri&ccedil;&atilde;o Vestibular&nbsp;</font></td>
       <td>-->
           <input name="id_vestibular" type="hidden" size="10" value="" />
       <!--</td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Dia de Vencimento</font></td>
      <td>-->
          <input name="dia_vencimento" type="hidden" value="10" size="4" />
      <!--</td>
    </tr>-->
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="00009C">&nbsp;Data Ativa&ccedil;&atilde;o&nbsp;<span class="required">*</span>&nbsp;<br>&nbsp;(dd-mm-aaaa)</font></td>
      <td> <font color="#000000">
        <input name="dt_ativacao" type=text value="<? echo date("d-m-Y") ?>" size="10">
        </font></td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="00009C">&nbsp;Motivo Inicial Entrada&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td> <font color="#000000"> 
        <input name="ref_motivo_inicial" type="text" size="5" value="1" onChange="ChangeCode('ref_motivo_inicial','op3')">
        <?PHP ComboArray("op3",$op_opcoes3,"0","ChangeOp3()"); ?> </font></td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="00009C">&nbsp;Motivo Ativa&ccedil;&atilde;o&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td> <font color="#000000"> 
        <input name="ref_motivo_ativacao" type=text size="5" value="1" onChange="ChangeCode('ref_motivo_ativacao','op2')">
        <?PHP ComboArray("op2",$op_opcoes2,"0","ChangeOp2()"); ?> </font></td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="00009C">&nbsp;Status Livro Matr&iacute;cula&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td> <font color="#000000"> 
        <input name="status" type=text size="5" value="1" onChange="ChangeCode('status','op4')">
        <?PHP ComboArray("op4",$op_opcoes4,"0","ChangeOp4()"); ?> </font></td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data&nbsp;Conclus&atilde;o&nbsp;</font><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#00009C">(dd-mm-aaaa)</font></td>
      <td> 
        <input name="dt_conclusao" type=text value="" size="10" maxlength="10">
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data&nbsp;Desativa&ccedil;&atilde;o&nbsp;</font><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#00009C">(dd-mm-aaaa)</font></td>
      <td>
        <input name="dt_desativacao" type=text value="" size="10" maxlength="10">
    </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="00009C">&nbsp;Motivo Desativa&ccedil;&atilde;o</font></td>
      <td> <font color="#000000"> 
        <input name="ref_motivo_desativacao" type=text size="5" onChange="ChangeCode('ref_motivo_desativacao','op5')">
        <?PHP ComboArray("op5",$op_opcoes5,"0","ChangeOp5()"); ?> </font></td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Observa&ccedil;&atilde;o&nbsp;Desativa&ccedil;&atilde;o&nbsp;</font></td>
      <td><textarea name="obs_desativacao" rows="3" cols="30"><?echo $obs_desativacao;?></textarea></td>
    </tr>
<!--
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="00009C">&nbsp;Motivo&nbsp;Entrada<br> (Casos de Transfer&eacute;ncias / Portador de Diploma)&nbsp;</font></td>
      <td> <font color="#000000"> 
        <input name="ref_motivo_entrada" type=text size="5" onChange="ChangeCode('ref_motivo_entrada','op8')">
        <?PHP ComboArray("op8",$op_opcoes8,"0","ChangeOp8()"); ?> </font></td>
    </tr>
-->
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr bgcolor="#FFFFFF">
      <td colspan="2" height="28"><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"><font color="#FF0033">&nbsp;Outras Informa&ccedil;&otilde;es</font></font></b></td>
    </tr>
<!--    
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Desconto&nbsp;</font></td>
      <td> 
        <input name="desconto" type=text value="" size="5">
      </td>
    </tr>
-->
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data Formatura&nbsp;</font><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#00009C">(dd-mm-aaaa)</font></td>
      <td> 
        <input name="dt_formatura" type=text value="" size="10" maxlength="10">
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data Prov&atilde;o&nbsp;</font><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#00009C">(dd-mm-aaaa)</font></td>
      <td> 
        <input name="dt_provao" type=text value="" size="10" maxlength="10">
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data Diploma&nbsp;</font><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#00009C">(dd-mm-aaaa)</font></td>
      <td> 
        <input name="dt_diploma" type=text value="" size="10" maxlength="10">
      </td>
    </tr>
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data Apostila&nbsp;</font><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#00009C">(dd-mm-aaaa)</font></td>
      <td> 
        <input name="dt_apostila" type=text value="" size="10" maxlength="10">
      </td>
    </tr>
    
    <tr>
      <td bgcolor="#CCCCFF" width="24%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099">&nbsp;&Uacute;ltimo Per&iacute;odo&nbsp;<span class="required">*</span>&nbsp;</font></td>
      <td colspan="2"> <font color="#000000">
        <input name="ref_last_periodo" type="text" size="5" onChange="ChangeCode('ref_last_periodo','op6')">
        <?PHP ComboArray("op6",$op_opcoes6,"0","ChangeOp6()"); ?> </font></td>
    </tr>

    <tr>
      <td bgcolor="#CCCCFF" width="24%"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099">&nbsp;Per&iacute;odo da Formatura</font></td>
      <td colspan="2"> <font color="#000000">
        <input name="ref_periodo_formatura" type="text" size="5" onChange="ChangeCode('ref_periodo_formatura','op7')">
        <?PHP ComboArray("op7",$op_opcoes7,"0","ChangeOp7()"); ?> </font></td>
    </tr>
    
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;&Eacute; Ouvinte?</font></td>
      <td valign="middle"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="radio" name="is_ouvinte" value="yes">
        sim </font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="radio" name="is_ouvinte" value="no" checked>
        n&atilde;o</font></td>
    </tr>
    <tr> 
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;&Eacute; Formando? </font></td>
      <td valign="middle"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="radio" name="is_formando" value="yes">
        sim </font> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="radio" name="is_formando" value="no" checked>
        n&atilde;o</font></td>
    </tr>
<!--    <tr> 
      <td bgcolor="#CCCCFF"><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>&nbsp;</b></font><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">Percentual 
        Pago </font></td>
      <td> 
        <input name="percentual_pago" type="text" size="10">
      </td>
    </tr>-->
    <tr> 
      <td bgcolor="#CCCCFF"><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>&nbsp;</b></font><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">Observa&ccedil;&otilde;es</font></td>
      <td> 
        <textarea name="obs" cols="40" rows="4"></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <hr size="1">
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center"> 
        <input type="hidden" name="periodo_id" value="<? echo($periodo_id) ?>">
        <input type="submit" name="Submit"  value=" Salvar ">
        <input type="reset"  name="Submit2" value=" Limpar ">
        <input type="button" name="Submit3" value=" Voltar " onClick="location='consulta_inclui_contratos.php'">
      </td>
    </tr>
    </table>
  </div>
</form>
</body>
</html>
