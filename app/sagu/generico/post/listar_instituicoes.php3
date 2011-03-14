<?php 

header("Cache-Control: no-cache");
require("../../common.php");
require("../../lib/InvData.php3");
 

function Lista_Instituicoes()
{
   
   $conn = new Connection;

   $conn->open();

   $total=0;

   $sql = " select id, " .
          "        nome, " .
          "        nome_atual " .
          " from instituicoes " .
          " order by nome; ";
   
   $query = $conn->CreateQuery($sql);

   echo("<center><table width=\"85%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");

   // cores fundo
   $bg0 = "#000000";
   $bg1 = "#EEEEFF";
   $bg2 = "#FFFFEE";
 
   // cores fonte
   $fg0 = "#FFFFFF";
   $fg1 = "#000099";
   $fg2 = "#000099";

   echo ("<tr>");
   echo ("<td bgcolor=\"#000099\" colspan=\"7\" height=\"28\" align=\"center\"><font size=\"3\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#FFFFFF\"><b>Listagem de Instituições Cadastradas</b></font></td>");
   echo ("</tr>");

   echo ("<tr bgcolor=\"#000000\">\n");
   echo ("  <td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Cont</b></font></td>");
   echo ("  <td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Código</b></font></td>");
   echo ("  <td width=\"40%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Nome da Instituição</b></font></td>");
   echo ("  <td width=\"40%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Nome Atual</b></font></td>");
   echo ("</tr>"); 

   $i=1;

   while( $query->MoveNext() )
   {
     list ($id,
           $nome,
	   $nome_atual) = $query->GetRowValues();

     
     
     if ( $i % 2 )
     {
         $fg = $fg1;
         $bg = $bg1;
     }
     else
     {
         $fg = $fg2;
         $bg = $bg2;
     }

     echo("<tr bgcolor=\"$bg\">\n");
     echo("  <td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">&nbsp;$i</td>");
     echo("  <td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">&nbsp;$id</td>");
     echo("  <td width=\"40%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">&nbsp;$nome</td>");
     echo("  <td width=\"40%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg\">&nbsp;$nome_atual</td>");
     echo("</tr>");

     $i++;

   }

   echo("<tr><td colspan=\"4\"><hr></td></tr>");
   echo("</table></center>");

   $query->Close();

   $conn->Close();
}
?>
<html>
<head>
<title>Lista Instituições Cadastradas</title>
</head>
<body bgcolor="#FFFFFF">
<form method="post" action="">
  <p> <?php Lista_Instituicoes(); ?>  </p>
  <div align="center"> 
    <input type="button" name="Button" value="  Voltar  " onclick="location='../consulta_inclui_instituicoes.phtml'">
  </div>
</form>
</body>
</html>
