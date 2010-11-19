<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Pesquisa de Institui&ccedil;&otilde;es</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css">
<link href="../../../public/styles/style.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../../../lib/prototype.js"></script>
<script language="javascript" src="index.js"></script>
<script language="JavaScript">
<!--
function send(id,descricao){

    window.opener.document.dispensa_frm.ref_instituicao.value=id;
    window.opener.document.dispensa_frm.instituicao_nome.value=descricao;
    self.close();
}
-->
</script>

</head>
<body onLoad="pesquisar();">
<h2>Pesquisa de Institui&ccedil;&otilde;es </h2>
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="60"><div align="center"><a href="javascript:window.close();" class="bar_menu_texto"><img src="../../../public/images/icons/close.png" alt="Voltar" width="24" height="24" /><br />
      Fechar</a></div></td>
  </tr>
</table>
<form id="form1" name="form1" method="post" action="">
  <table width="101%" border="0" cellpadding="0" cellspacing="0" bgcolor="#E6E6E6">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
  <td>Consultar Descri&ccedil;&atilde;o da Institui&ccedil;&atilde;o:</td>
  </tr>
  <tr>
  <td><input name="nome" type="text" id="nome" size="50" onkeyup="pesquisar();"/></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

  
</form>
<p>
<span id="listagem"></span>
</p>
<br />
<a href="https://sistemas.cefetbambui.edu.br/sa/app/sagu/generico/inclui_instituicao.phtml"> Incluir institui&ccedil;&atilde;o</a>
<br />
</body>
</html>
