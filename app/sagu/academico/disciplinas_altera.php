<?php

require("../common.php");
require("../lib/GetGrupoDiscipl.php");
require("../lib/GetDepartamento.php");

$id = $_GET['id'];

CheckFormParameters(array("id"));

$conn = new Connection;

$conn->Open();

$sql = "select " .
       "    id," .
       "    ref_grupo," .
       "    ref_departamento," .
       "    descricao_disciplina," .
       "    descricao_extenso," .
       "    num_creditos," .
       "    carga_horaria" .
       "  from disciplinas where id = '$id'";

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro n&aatilde;o encontrado!");

list ( $id,
$ref_grupo,
$ref_departamento,
$descricao_disciplina,
$descricao_extenso,
$num_creditos,
$carga_horaria) = $query->GetRowValues();

$query->Close();

$conn->Close();

$descricao_disciplina = substr($descricao_disciplina, 0, 30);

list ($nome_grupo)=GetGrupoDiscipl($ref_grupo, true);
list ($nome_departamento)=GetDepartamento($ref_departamento, true);

?>
<html>
<head>
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/required.css'?>" type="text/css">
<script language="JavaScript">
function _init()
{
  document.myform.ref_grupo.focus();
}

function buscaGrupos()
{
  $controle = 1;
  url = '/generico/post/lista_grupos_disciplinas.php' +
         '?id=' + escape(document.myform.ref_grupo.value) +
         '&desc=' + escape(document.myform.nome_grupo.value);

  window.open(url,"busca","toolbar=no,width=530,height=320,top=80,left=55,directories=no,menubar=no,scrollbars=yes");
}

function buscaDepartamentos()
{
  $controle = 2;
  url = '/generico/post/lista_departamentos.php' +
         '?id=' + escape(document.myform.ref_departamento.value) +
         '&desc=' + escape(document.myform.nome_departamento.value);

  window.open(url,"busca","toolbar=no,width=530,height=320,top=80,left=55,directories=no,menubar=no,scrollbars=yes");
}

function setResult(arg1,arg2)
{
if ($controle == '1')
{
  document.myform.ref_grupo.value = arg1;
  document.myform.nome_grupo.value = arg2;
}
if ($controle == '2')
{
  document.myform.ref_departamento.value = arg1;
  document.myform.nome_departamento.value = arg2;
}
}
</script>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<form method="post" action="post/disciplinas_altera.php" name="myform">
<table width="90%" align="center">
	<tr bgcolor="#000099" height="35">
		<td colspan="2">
		<div align="center"><font size="3"
			face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#CCCCFF">Altera&ccedil;&atilde;o
		de Disciplina</font></b></font></div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;C&oacute;digo&nbsp;</font></td>
		<td><font face="Verdana, Arial, Helvetica, sans-serif" color="#FF0033">
		<input type="hidden" name="id" value="<? echo($id); ?>"> <?php echo($id); ?></font></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Grupo&nbsp;<span class="required">*</span> </font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="0%"><input name="ref_grupo" type=text size="5"
					onChange="ChangeCode('ref_grupo','op1')"
					value="<?echo($ref_grupo);?>"></td>
				<td width="100%"><input type="text" name="nome_grupo" size="30"
					maxlength="30" value="<?echo($nome_grupo);?>"></td>
				<td width="0%">
				<div align="right"><input type="button" name="Submit3" value="..."
					onClick="buscaGrupos()"></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Departamento&nbsp;<span class="required">*</span> </font></td>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="0%"><input name="ref_departamento" type=text size="5"
					onChange="ChangeCode('ref_departamento','op1')"
					value="<?echo($ref_departamento);?>"></td>
				<td width="100%"><input type="text" name="nome_departamento"
					size="30" maxlength="30" value="<?echo($nome_departamento);?>"></td>
				<td width="0%">
				<div align="right"><input type="button" name="Submit3" value="..."
					onClick="buscaDepartamentos()"></div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Descri&ccedil;&atilde;o
		Breve&nbsp;<span class="required">*</span> </font></td>
		<td><input name="descricao_disciplina" type=text
			value="<?echo($descricao_disciplina);?>" maxlength="160" size="30"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Descri&ccedil;&atilde;o
		Completa&nbsp;</font></td>
		<td><input name="descricao_extenso" type=text
			value="<?echo($descricao_extenso);?>" size="40"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;N&uacute;mero
		de Cr&eacute;ditos</font></td>
		<td><input name="num_creditos" type=text
			value="<?echo($num_creditos);?>" size="10"></td>
	</tr>
	<tr>
		<td bgcolor="#CCCCFF"><font
			face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#00009C">&nbsp;Carga
		Hor&aacute;ria</font></td>
		<td><input name="carga_horaria" type=text
			value="<?echo($carga_horaria);?>" size="10"></td>
	</tr>
	<tr>
		<td colspan="2">
		<hr size="1">
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="Submit"
			value=" Salvar "> <input type="button" name="Button" value=" Voltar "
			onClick="history.go(-1)"></td>
	</tr>
</table>
</form>
</body>
</html>