<?php

if ( !$exito_goto )
    $exito_goto = 'history.go(-1)';
?>
<html>
<head>
<title>&nbsp;</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body bgcolor="#FFFFFF" leftmargin="20" topmargin="20" marginwidth="20" marginheight="20">
<div align="center">
  <table cols=2 width="500" align="center">
    <tr> 
      <td bgcolor="#000099" height="32" colspan="2"> 
        <div align="center"><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCFF"><b> 
          <?php echo($exito_titulo?$exito_titulo:"Informação"); ?> </b></font></div>
      </td>
    </tr>
    <tr> 
      <td height="80" width="90"> 
        <div align="center"><img src="../../images/information.gif" width="50" height="49" vspace="20" hspace="20"></div>
      </td>
      <td width="398" height="80" align="center"> 
        <p align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b><font color="#000099">A operação foi bem sucedida.</font></b></font></p>
        <p align="center"> <font color="#0099CC"><i><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><? echo($exito_info); ?></font></i></font></p>
      </td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <hr size="1" align="center" width="100%">
      </td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <form method="post" action="">
          <div align="center"> 
            <input type="button" name="Button" value="   Continuar   " onClick="<?echo($exito_goto)?>">
            <? if ($exito_button) { ?>
            <input type="button" name="Button2" value="    Voltar    " onClick="<?echo($exito_button)?>">
            <?}?>
          </div>
        </form>
      </td>
    </tr>
  </table>
</div>
</body>
</html>
