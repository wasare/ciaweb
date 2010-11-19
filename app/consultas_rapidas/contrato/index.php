<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Pesquisa de Contrato</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css"/>
<link href="../../../public/styles/style.css" rel="stylesheet" type="text/css"/>
<script language="javascript" src="../../../lib/prototype.js"></script>
<script language="javascript" src="../../../consultas_rapidas/contrato/index.js"></script>
<script language="JavaScript">
<!--

function send(id,nome,curso){

    window.opener.document.form1.cod_contrato.value=id;
	window.opener.document.form1.aluno.value=nome;
	window.opener.document.form1.curso.value=curso;
    self.close();
}

-->
</script>
</head>
<body onLoad="pesquisar();">
<h2>Pesquisa de Contrato</h2>
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="60"><div align="center"><a href="javascript:window.close();" class="bar_menu_texto"><img src="../../../public/images/icons/close.png" alt="Voltar" width="24" height="24" /><br />
        Fechar</a></div></td>
  </tr>
</table>
<form id="form1" name="form1" method="post" action="">
<table width="90%" border="0" cellpadding="0" cellspacing="0" bgcolor="#E6E6E6">
<tr>
  <td>&nbsp;</td>
</tr>
<tr>
  <td>Consultar Nome de Pessoa:</td>
</tr>
<tr>
  <td>
<form id="form1" name="form1" method="post" action="">
  <input name="nome" type="text" id="nome" size="50" onkeyup="pesquisar();"/>
</form>
</td>
</tr>
<tr>
  <td>&nbsp;</td>
</tr>
</table>
<span id="listagem"></span>
</body>
</html>
