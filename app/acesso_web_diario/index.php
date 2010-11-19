<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SA</title>
<link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css">
<link href="../../public/styles/style.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../../lib/prototype.js"></script>
<script language="javascript" src="index.js"></script>
</head>

<body onLoad="pesquisar();">
<h2>Acesso ao Web Di&aacute;rio</h2>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="60"><div align="center"><a href="cadastrar.php" class="bar_menu_texto"><img src="../../public/images/icons/new.png" alt="Novo" width="20" height="20" /><br />
        Novo</a></div></td>
    <td width="60"><div align="center"><a href="javascript:history.back();" class="bar_menu_texto"><img src="../../public/images/icons/back.png" alt="Voltar" width="20" height="20" /><br />
        Voltar</a></div></td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#E6E6E6">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><form id="form1" name="form1" method="post" action="">
      <label>Pesquisa por nome de pessoa:<br />
      <input name="nome" type="text" id="nome" size="60" onkeyup="pesquisar();"/>
      </label>
    </form>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<div style="height:500px;">
  <p><span id="listagem"></span></p>
    
    <p class="alerta">Se n&atilde;o obteve resultado seja mais espec&iacute;fico na consulta.</p>
</div>
</body>
</html>
