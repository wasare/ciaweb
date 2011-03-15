<?php

require("../common.php");
require("../lib/SQLCombo.php");
require("../lib/InvData.php");


$ref_curso      = $_GET['ref_curso'];
$ref_disciplina = $_GET['ref_disciplina'];
$ref_campus     = $_GET['ref_campus'];

$op1_opcoes = SQLArray("select descricao,id from cursos order by descricao");
$op2_opcoes = SQLArray("select nome_campus, id from campus order by nome_campus");

$conn = new Connection;
$conn->Open();

$sql = " select ref_curso," .
       "        curso_desc(ref_curso), " .
       "        ref_campus," .
       "        ref_disciplina," .
       "        descricao_disciplina(ref_disciplina), " .
       "        semestre_curso," .
       "        curriculo_mco," .
       "        equivalencia_disciplina," .
       "        descricao_disciplina(equivalencia_disciplina), " .
       "        cursa_outra_disciplina," .
       "        esconde_historico," .
       "        dt_inicio_curriculo," .
       "        dt_final_curriculo," .
       "        curso_substituido," .
       "        disciplina_substituida," .
       "        pre_requisito_hora," .
       "        exibe_historico," .
       "        fl_soma_curriculo, " .
       "        ref_area, " .
       "        get_area(ref_area) " .
       " from cursos_disciplinas " .
       " where ref_curso='$ref_curso' and " .
       "       ref_campus='$ref_campus' and " .
       "       ref_disciplina='$ref_disciplina'";
 
$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro n&atilde;o encontrado!");

list ( $ref_curso,
$curso,
$ref_campus,
$ref_disciplina,
$disciplina,
$semestre_curso,
$curriculo_mco,
$equivalencia_disciplina,
$disciplina_equiv,
$cursa_outra_disciplina,
$esconde_historico,
$dt_inicio_curriculo,
$dt_final_curriculo,
$curso_substituido,
$disciplina_substituida,
$pre_requisito_hora,
$exibe_historico,
$fl_soma_curriculo,
$ref_area,
$area) = $query->GetRowValues();
 
$query->Close();

$conn->Close();

$dt_inicio_curriculo = InvData($dt_inicio_curriculo);
$dt_final_curriculo = InvData($dt_final_curriculo);
$cursa_outra_disciplina = trim($cursa_outra_disciplina);

?>
<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="JavaScript">
function _init()
{
document.myform.ref_curso.focus();
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
  ChangeOption(document.myform.op1,document.myform.ref_curso);
}

function ChangeOp2()
{
  ChangeOption(document.myform.op2,document.myform.ref_campus);
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

var tipo_busca;

function buscaOpcao(pf_opcao)
{
  var url;
  tipo_busca=pf_opcao;

 if (tipo_busca == 1)
   url = '../generico/post/lista_disciplinas_todas.php' + 
         '?desc=' + escape(document.myform.ref_disciplina_nome.value);

 else if (tipo_busca == 2)
   url = '../generico/post/lista_disciplinas_todas.php' + 
         '?desc=' + escape(document.myform.equivalencia_disciplina_nome.value);

 else if (tipo_busca == 3)
   url = "../generico/post/lista_cursos_nome.php" + 
         "?id=" + escape(document.myform.ref_curso.value) + 
         "&curso=" + escape(document.myform.curso.value);

 else if (tipo_busca == 4)
   url = "../generico/post/lista_areas_ensino.php" +
         "?id=" + escape(document.myform.ref_area.value) +
         "&area=" + escape(document.myform.area.value);

  var wnd = window.open(url,'busca','toolbar=no,width=550,height=350,scrollbars=yes');
}

function setResult(arg1,arg2)
{
  if (tipo_busca == 1)
  {
    document.myform.ref_disciplina.value = arg1;
    document.myform.ref_disciplina_nome.value = arg2;
  }
  else if  (tipo_busca == 2)
  {
    document.myform.equivalencia_disciplina.value = arg1;
    document.myform.equivalencia_disciplina_nome.value = arg2;
  }
  else if  (tipo_busca == 3)
  {
    document.myform.ref_curso.value = arg1;
    document.myform.curso.value = arg2;
  }
  else if  (tipo_busca == 4)
  {
    document.myform.ref_area.value = arg1;
    document.myform.area.value = arg2;
  }

}
</script>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<form method="post" action="post/cursos_disciplinas_edita.php"
	name="myform">
<table width="90%" align="center">
	<tr bgcolor="#000099">
		<td height="35" colspan="2">
		<div align="center"><font size="3"
			face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Altera&ccedil;&atilde;o
		de Disciplinas nos Cursos</font></b></font></div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Curso&nbsp;<span class="required">*</span> </font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="10%"><input name="ref_curso" type=text
					value="<?echo($ref_curso);?>" size="5"
					onChange="ChangeCode('ref_curso','op1')"> <input name="curso_id"
					type="hidden" value="<?echo($ref_curso);?>"></td>
				<td width="100%"><input type="text" name="curso"
					value="<?echo($curso);?>" size="30"></td>
				<td>
				<div align="right"><input type="button" value="..."
					onClick="buscaOpcao(3)" name="button22"></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Campus&nbsp;<span class="required">*</span> </font></td>
		<td><input name="ref_campus" type="text"
			value="<?echo($ref_campus);?>" size="5"> <input name="campus_id"
			type="hidden" value="<?echo($ref_campus);?>"> <script language="PHP">
ComboArray("op2",$op2_opcoes,"0","ChangeOp2()");
</script></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Disciplina&nbsp;<span class="required">*</span> </font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="9%"><input name="ref_disciplina" type=text
					value="<?echo($ref_disciplina);?>" size="5"
					onChange="ChangeCode('ref_disciplina','op2')"> <input
					name="disciplina_id" type="hidden"
					value="<?echo($ref_disciplina);?>"></td>
				<td width="100%"><font color="#000000"><font color="#000000"> <input
					name="ref_disciplina_nome" type=text value="<?echo($disciplina);?>"
					size="30"> </font></font></td>
				<td width="0%">
				<div align="right"><font color="#000000"><font color="#000000"> <input
					type="button" value="..." onClick="buscaOpcao(1)" name="button"> <font
					color="#000000"> </font> </font></font></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Semestre
		no Curso&nbsp;<span class="required">*</span> </font></td>
		<td><input name="semestre_curso" type=text
			value="<?echo($semestre_curso);?>" size="5"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Curr&iacute;culo
		MCOPA&nbsp;<span class="required">*</span> </font></td>
		<td><select name="curriculo_mco">
			<option value="<?echo($curriculo_mco);?>" selected><?echo($curriculos[$curriculo_mco]); ?></option>
			<option value="M">M&iacute;nimo</option>
			<option value="C">Complementar</option>
			<option value="O">Optativa</option>
			<option value="P">Profici&ecirc;ncia</option>
			<option value="A">Atividade Complementar</option>
		</select></td>
	</tr>

	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data
		In&iacute;cio Curr&iacute;culo<br>
		&nbsp;(dd-mm-aaaa)</font></td>
		<td><input name="dt_inicio_curriculo" type=text
			value="<?echo($dt_inicio_curriculo);?>" size="12"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Data
		Final Curr&iacute;culo<br>
		&nbsp;(dd-mm-aaaa)</font></td>
		<td><input name="dt_final_curriculo" type=text
			value="<?echo($dt_final_curriculo);?>" size="12"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Pr&eacute;-Requisito
		Hora-Aula</font></td>
		<td><input name="pre_requisito_hora" type=text
			value="<?echo($pre_requisito_hora);?>" size="5"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF" width="30%"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;&Aacute;rea
		de Ensino&nbsp;</font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="10%"><input type="text" name="ref_area" size="5"
					maxlength="10" value="<?echo($ref_area);?>"></td>
				<td width="100%"><font color="#000000"> <input type="text"
					name="area" size="30" value="<?echo($area);?>"></font></td>
				<td><font color="#000000"><font color="#000000"> <font
					color="#000000"> <input type="button" value="..."
					onClick="buscaOpcao(4)" name="button"> </font></font></font></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Exibir
		no Hist&oacute;rico</font></td>
		<td><select name="exibe_historico">
			<option selected><?echo($historico[$exibe_historico]); ?></option>
			<?if ($exibe_historico == 'N')
			{ echo "<option value=\"S\">Sim</option>";}
	  if ($exibe_historico == 'S')
	  { echo "<option value=\"N\">N&atilde;o</option>";};?>
		</select></td>
	</tr>
	<tr>
		<td colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<div align="center"><input type="submit" name="Submit"
			value=" Salvar "> <input type="button" name="Submit2"
			value=" Voltar " onClick="history.go(-1)"></div>
		</td>
	</tr>
</table>
</form>
</body>
</html>
