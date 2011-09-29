<?php 

require_once("../common.php");
require("../lib/InvData.php");
require("../lib/VerificaContrato.php"); 

$conn = new connection_factory($param_conn);

// Verifica as permissoes de acesso do usuario quanto ao arquivo
$ACL_FILE = __FILE__;
require_once($BASE_DIR .'core/login/acesso.php');
// ^ Verifica as permissoes de acesso do usuario quanto ao arquivo ^ //


$id = (int) $_GET['id'];

?>

<script language="JavaScript">

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
  ChangeOption(document.myform.op,document.myform.ref_last_periodo);
}

function ChangeOp1()
{
  ChangeOption(document.myform.op1,document.myform.ref_periodo_formatura);
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

<?php
function SQL_Combo($nome,$sql,$default,$onchange)
{
	$conn = new Connection;
	$conn->Open();
	$query = $conn->CreateQuery($sql);

	if ( $onchange != "" )
	echo("<select name=\"$nome\" onchange=\"$onchange\">");
	else
	echo("<select name=\"$nome\">");

	for ( $i=1; $query->MoveNext(); $i++ )
	{
		list ( $text, $value ) = $query->GetRowValues();

		if ( $value == $default )
		echo("  <option value=\"$value\" selected>$text</option>\n");
		else
		echo("  <option value=\"$value\">$text</option>\n");
	}
	echo("</select>");
	$query->Close();
	$conn->Close();
}

?>

<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<?php 

CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();

$sql = "select " .
       "    ref_campus," .
       "    get_campus(ref_campus)," .
       "    ref_pessoa," .
       "    pessoa_nome(ref_pessoa)," .
       "    ref_curso," .
       "    curso_desc(ref_curso)," .
       "    dt_ativacao," .
       "    ref_motivo_entrada," .
       "    get_motivo(ref_motivo_entrada)," .
       "    ref_motivo_ativacao," .
       "    get_motivo(ref_motivo_ativacao)," .
       "    ref_motivo_desativacao," .
       "    get_motivo(ref_motivo_desativacao)," .
       "    ref_motivo_inicial," .
       "    get_motivo(ref_motivo_inicial)," .
       "    dt_desativacao," .
       "    obs_desativacao," .
       "    obs," .
       "    desconto," .
       "    dt_formatura," .
       "    dt_provao," .
       "    dt_diploma," .
       "    dt_apostila," .
       "    ref_last_periodo," .
       "    fl_ouvinte," .
       "    fl_formando," .
       "    percentual_pago," .
       "    dt_conclusao," .
       "    ref_periodo_formatura," . 
       "    id_vestibular," . 
       "    cod_status, " .
       "    dia_vencimento,".
       "    semestre,".
       "    turma,".
       "    ref_periodo_turma".
       "  from contratos" .
       "  where id = $id;";

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro n&atilde;o encontrado!");

list ( $ref_campus,
       $campus_desc,
       $ref_pessoa,
       $pessoa_nome,
       $ref_curso,
       $curso_desc,
       $dt_ativacao,
       $ref_motivo_entrada,
       $motivo_entrada,
       $ref_motivo_ativacao,
       $motivo_at,
       $ref_motivo_desativacao,
       $motivo_des,
       $ref_motivo_inicial,
       $motivo_inicial,
       $dt_desativacao,
       $obs_desativacao,
       $obs,
       $desconto,
       $dt_formatura,
       $dt_provao,
       $dt_diploma,
       $dt_apostila,
       $ref_last_periodo,
       $is_ouvinte,
       $is_formando,
       $percentual_pago,
       $dt_conclusao,
       $ref_periodo_formatura,
       $id_vestibular, 
       $status,
       $dia_vencimento,
       $semestre,
       $turma,
       $ref_periodo_turma) = $query->GetRowValues();

$query->Close();

$conn->Close();

VerificaContrato($ref_motivo_desativacao);

$dt_ativacao = InvData($dt_ativacao);
$dt_desativacao = InvData($dt_desativacao);
$dt_formatura = InvData($dt_formatura);
$dt_provao = InvData($dt_provao);
$dt_diploma = InvData($dt_diploma);
$dt_apostila = InvData($dt_apostila);
$dt_conclusao = InvData($dt_conclusao);

?>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<form method="post" action="post/alterar_contrato.php" name="myform">
<table width="90%" align="center">

	<tr>
		<td bgcolor="#000099" colspan="2" height="35" align="center"><font
			size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>Altera&ccedil;&atilde;o
		de Contrato</b></font></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td colspan="2" height="35">
		<div align="center"><font face="Verdana, Arial, Helvetica, sans-serif"
			color="#000099"><b><font color="#FF0033">Aluno: <?php 
			echo($ref_pessoa) ;
			echo('  -  ');
			echo($pessoa_nome); ?> </font></b></font></div>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="35" align="center"><?
		echo("<a href=\"lista_proficiencias.php?ref_pessoa=$ref_pessoa&ref_contrato=$id&ref_curso=$ref_curso&ref_campus=$ref_campus&ref_periodo=$ref_last_periodo\">Insere Profici&ecirc;ncia</a>");
		?></td>
	</tr>


	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Campus&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input type="hidden" name="id" value="<?echo($id);?>"> <input
			name="ref_campus" type=text value="<?echo($ref_campus);?>" size="4">
		<font face="Verdana, Arial, Helvetica, sans-serif" size="2"><?echo($campus_desc);?></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Aluno&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="ref_pessoa" type=text value="<?echo($ref_pessoa);?>"
			size="8"> <font face="Verdana, Arial, Helvetica, sans-serif" size="2"><?echo($pessoa_nome);?>
		</font></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Curso&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td><input name="ref_curso" type=text value="<?echo($ref_curso);?>"
			size="8"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?echo($curso_desc);?>&nbsp;</font></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Turma&nbsp;</font></td>
		<td><input name="turma" type="text" size="10" value="<?=$turma?>"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;In&iacute;cio
		da turma&nbsp;&nbsp;<span class="required">*</span>&nbsp;</font></td>
		<td colspan="2"><font color="#000000"> <input name="ref_periodo_turma"
			type="text" size="5" value="<?=$ref_periodo_turma?>"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Inscri&ccedil;&atilde;o
		Vestibular</font></td>
		<td><input name="id_vestibular" type=text
			value="<?echo($id_vestibular);?>" size="10"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Dia
		de Vencimento</font></td>
		<td><input name="dia_vencimento" type=text
			value="<?echo($dia_vencimento);?>" size="4"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data&nbsp;Ativa&ccedil;&atilde;o&nbsp;<span class="required">*</span>&nbsp;</font><font
			face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#00009C">(dd-mm-aaaa)</font></td>
		<td><input name="dt_ativacao" type=text
			value="<?echo($dt_ativacao);?>" size="10" maxlength="10"></td>
	</tr>
	<tr>
		<TD bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Motivo&nbsp;Inicial&nbsp;Entrada&nbsp;<span class="required">*</span> </font></td>
		<td><input name="ref_motivo_inicial" type=text
			value="<?echo($ref_motivo_inicial);?>" size="4"> <?echo($motivo_inicial);?>&nbsp;</td>
	</tr>
	<tr>
		<TD bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Motivo&nbsp;Ativa&ccedil;&atilde;o&nbsp;<span class="required">*</span> </font></td>
		<td><input name="ref_motivo_ativacao" type=text
			value="<?echo($ref_motivo_ativacao);?>" size="4"> <?echo($motivo_at);?>&nbsp;</td>
	</tr>
	<TR>
		<TD bgcolor="#CCCCFF"><FONT
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Status
		Livro Matricula&nbsp;&nbsp;<span class="required">*</span> </font></TD>
		<TD><INPUT name="status" type="text" value="<?echo($status);?>"
			size="4"> <?echo($desc_status);?></TD>
	</TR>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data&nbsp;Conclus&atilde;o&nbsp;</font><font
			face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#00009C">(dd-mm-aaaa)</font></td>
		<td><input name="dt_conclusao" type=text
			value="<?echo($dt_conclusao);?>" size="10" maxlength="10"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data&nbsp;Desativa&ccedil;&atilde;o&nbsp;</font><font
			face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#00009C">(dd-mm-aaaa)</font></td>
		<td><input name="dt_desativacao" type=text
			value="<?echo($dt_desativacao);?>" size="10" maxlength="10"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Motivo&nbsp;Desativa&ccedil;&atilde;o&nbsp;</font></td>
		<td><input name="ref_motivo_desativacao" type=text
			value="<?echo($ref_motivo_desativacao);?>" size="4"> <?echo($motivo_des);?></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Observa&ccedil;&atilde;o&nbsp;Desativa&ccedil;&atilde;o&nbsp;</font></td>
		<td><textarea name="obs_desativacao" rows="3" cols="30"><?echo $obs_desativacao;?></textarea></td>
	</tr>
	<!--
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Motivo&nbsp;Entrada<br> (Casos de Transfer&ecirc;ncias / Portador de Diploma)&nbsp;</font></td>
      <td> 
         <input name="ref_motivo_entrada" type=text value="<?echo($ref_motivo_entrada);?>" size="4">
	     <?echo($motivo_entrada);?></td>
    </tr>
-->
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td colspan="2" height="28"><b><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C"><font
			color="#FF0033">&nbsp;Outras Informa&ccedil;&otilde;es</font></font></b></td>
	</tr>
	<!--
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Desconto&nbsp;</font></td>
      <td> 
        <input name="desconto" type=text value="<?echo($desconto);?>" size="4">
      </td>
    </tr>
-->
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data
		Formatura&nbsp;</font><font
			face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#00009C">(dd-mm-aaaa)</font></td>
		<td><input name="dt_formatura" type=text
			value="<?echo($dt_formatura);?>" size="10" maxlength="10"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data
		Prov&atilde;o&nbsp;</font><font
			face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#00009C">(dd-mm-aaaa)</font></td>
		<td><input name="dt_provao" type=text value="<?echo($dt_provao);?>"
			size="10" maxlength="10"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data
		Diploma&nbsp;</font><font face="Verdana, Arial, Helvetica, sans-serif"
			size="1" color="#00009C">(dd-mm-aaaa)</font></td>
		<td><input name="dt_diploma" type=text value="<?echo($dt_diploma);?>"
			size="10" maxlength="10"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data
		Apostila&nbsp;</font><font
			face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#00009C">(dd-mm-aaaa)</font></td>
		<td><input name="dt_apostila" type=text
			value="<?echo($dt_apostila);?>" size="10" maxlength="10"></td>
	</tr>

	<tr>
		<td bgcolor="#CCCCFF" width="24%"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099">&nbsp;&Uacute;ltimo
		Per&iacute;odo&nbsp;<span class="required">*</span> </font></td>
		<td colspan="2"><font color="#000000"> <input type="text"
			name="ref_last_periodo" size="10"
			onChange="ChangeCode('ref_last_periodo','op')"
			value="<?echo($ref_last_periodo);?>"> <script language="PHP">
          SQL_Combo("op",$sql_periodos_academico, $ref_last_periodo,"ChangeOp()");
        </script> </font></td>
	</tr>

	<tr>
		<td bgcolor="#CCCCFF" width="24%"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099">&nbsp;Per&iacute;odo
		da Formatura</font></td>
		<td colspan="2"><font color="#000000"> <input type="text"
			name="ref_periodo_formatura" size="10"
			onChange="ChangeCode('ref_periodo_formatura','op1')"
			value="<?echo($ref_periodo_formatura);?>"> <script language="PHP">
          SQL_Combo("op1",$sql_periodos_academico,$ref_periodo_formatura,"ChangeOp1()");
        </script> </font></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;&Eacute;
		Ouvinte?</font></td>
		<td><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> <?
		if ($is_ouvinte=='1')
		{
			echo("<input type=\"radio\" name=\"fl_ouvinte\" value=\"yes\" checked>sim ");
			echo("<input type=\"radio\" name=\"fl_ouvinte\" value=\"no\">n&atilde;o");
		}
		else
		{
			echo("<input type=\"radio\" name=\"fl_ouvinte\" value=\"yes\">sim ");
			echo("<input type=\"radio\" name=\"fl_ouvinte\" value=\"no\" checked>n&atilde;o");
		}

		?> </font></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;&Eacute;
		Formando?</font></td>
		<td><font face="Verdana, Arial, Helvetica, sans-serif" size="2"> <?
		if ($is_formando=='1')
		{
			echo("<input type=\"radio\" name=\"fl_formando\" value=\"yes\" checked>sim ");
			echo("<input type=\"radio\" name=\"fl_formando\" value=\"no\">n&atilde;o");
		}
		else
		{
			echo("<input type=\"radio\" name=\"fl_formando\" value=\"yes\">sim ");
			echo("<input type=\"radio\" name=\"fl_formando\" value=\"no\" checked>n&atilde;o");
		}
		?> </font></td>
	</tr>
	<!--
    <tr>
      <td bgcolor="#CCCCFF"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Percentual Pago</font></td>
      <td> 
        <input name="percentual_pago" type=text value="<?echo($percentual_pago);?>" size="8">
      </td>
    </tr>
-->
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;&Uacute;ltimo
		semestre</font></td>
		<td><input name="semestre" type=text value="<?echo($semestre);?>"
			size="2"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Observa&ccedil;&atilde;es</font></td>
		<td><textarea name="obs" rows="3" cols="30"><?echo($obs);?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="Submit"
			value=" Salvar "> <input type="button" name="Submit2"
			value=" Voltar " onclick="javascript:history.back(1)"></td>
	</tr>
</table>
</form>
</body>
</html>
