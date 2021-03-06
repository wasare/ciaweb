<?php

require_once("../common.php");
require_once("../lib/SQLCombo.php");


$nome = $_GET['nome'];

$op_opcoes = SQLArray("select nome_setor, id from setor order by nome_setor");


CheckFormParameters(array("nome"));

$conn = new Connection;

$conn->Open();

$sql = " select nome," .
       "        nome_completo," .
       "        email," .
       "        setor," .
       "        obs," .
       "        grupo" .
       " from usuario " .
       " where nome = '$nome'";

$query = $conn->CreateQuery($sql);

SaguAssert($query && $query->MoveNext(),"Registro não encontrado!");

list ( $nome,
$nome_completo,
$email,
$ref_setor,
$obs,
$grupo) = $query->GetRowValues();

$query->Close();

$conn->Close();


?>
<html>
<head>
<script language="JavaScript">
function _init()
{
  document.myform.descricao.focus();
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
  ChangeOption(document.myform.op,document.myform.ref_setor);
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

  alert(code + ' não é um código válido!');

  field.focus();

  return true;
}
</script>
</head>
<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20"
	onload="_init()">
<FORM action="perfil_edita.post.php" method="post" name=myform>
<TABLE align=center cols=2 width=500>
	<tr>
		<td bgcolor="#000099" colspan="2" height="28"><font size="3"
			face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b>&nbsp;Alteração
		de Usu&aacute;rio </b></font></td>
	</tr>
	<TBODY>
		<TR>
			<TD bgColor=#ccccff width="109"><FONT color=#00009c
				face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;User Name</FONT></TD>
			<TD width="379"><font face="Verdana, Arial, Helvetica, sans-serif"
				size="2" color="#0000FF"><? echo($nome); ?></font> <INPUT
				type="hidden" name="nome" value="<?echo($nome)?>"></TD>
		</TR>
		<TR>
			<TD bgColor=#ccccff width="109"><FONT color=#00009c
				face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;Senha</FONT></TD>
			<TD width="379"><INPUT type="password" name="password1"
				value="<?echo($password1)?>" size="20"></TD>
		</TR>
		<TR>
			<TD bgColor=#ccccff width="109"><FONT color=#00009c
				face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;Confirma
			Senha:</FONT></TD>
			<TD width="379"><INPUT type="password" name="password2"
				value="<?echo($password2)?>" size="20"></TD>
		</TR>
		<TR>
			<TD bgColor=#ccccff width="109"><FONT color=#00009c
				face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;Nome
			Completo</FONT></TD>
			<TD width="379"><INPUT type="text" name="nome_completo"
				value="<?echo($nome_completo)?>" size="50"></TD>
		</TR>
		<TR>
			<TD bgColor=#ccccff width="109"><FONT color=#00009c
				face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;E-mail</FONT></TD>
			<TD width="379"><INPUT type="text" name="email"
				value="<?echo($email)?>" size="50"></TD>
		</TR>
		<tr>
			<td bgcolor="#CCCCFF" width="161"><font
				face="Verdana, Arial, Helvetica, sans-serif" size="2"
				color="#00009C">&nbsp;Grupo</font></td>
			<td colspan="3" width="410"><select name="grupo">
				<option value="access" <?php if($grupo == 'access') echo 'selected="selected"';?>>Acesso</option>
				<option value="admin" <?php if($grupo == 'admin') echo 'selected="selected"';?>>Administração</option>
                <option value="admin_matriz" <?php if($grupo == 'admin_matriz') echo 'selected="selected"';?>>Administração da Matriz Curricular</option>
			</select></td>
		</tr>

		<tr>
			<td bgcolor="#CCCCFF" width="109"><font
				face="Verdana, Arial, Helvetica, sans-serif" size="2" color="00009C">&nbsp;Setor</font></td>
			<td width="379"><font color="#000000"> <input name="ref_setor"
				type=text size="5" onChange="ChangeCode('ref_setor','op')" value="<?echo($ref_setor)?>"> <?php ComboArray("op",$op_opcoes,$ref_setor,"ChangeOp()"); ?>
			</font></td>
		</tr>
		<TR>
			<TD bgColor=#ccccff width="109"><FONT color=#00009c
				face="Verdana, Arial, Helvetica, sans-serif" size=2>&nbsp;Obs.</FONT></TD>
			<TD width="379"><textarea name="obs" cols="40" rows="2"><?echo($obs);?></textarea>
			</TD>
		</TR>
		<TR>
			<TD colSpan="2">
			<HR SIZE="1">
			</TD>
		</TR>
		<TR>
			<TD colspan="2" align="center"><INPUT name="Submit" type="submit"
				value=" Alterar "> <input type="button" name="Button"
				value="  Sair  " onClick="location='consulta_inclui_usuarios.php'">
			</TD>
		</TR>
	</TBODY>
</TABLE>
</FORM>
</body>
</html>
