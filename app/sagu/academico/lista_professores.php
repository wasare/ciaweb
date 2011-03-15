<?php require_once("../common.php"); ?>

<html>
<head>
<title>Lista Professores</title>
<script language="PHP">
function ListaProfessores()
{
   $conn = new Connection;

   $conn->open();

   $sql = " select id, " .
          "        ref_professor, " .
	      "        pessoa_nome(ref_professor), " .
	      "        ref_departamento, " .
          "        descricao_departamento(ref_departamento)," .
          "        to_char(dt_ingresso,'dd-mm-yyyy') " .
          " from professores " .
          " order by pessoa_nome(ref_professor);";
   
   $query = $conn->CreateQuery($sql);


   echo("<center><table width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");
   echo ("<tr>");
   echo ("<td bgcolor=\"#000099\" colspan=\"4\" height=\"28\" align=\"center\"><font size=\"3\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#FFFFFF\"><b>Listagem de Professores Cadastrados</b></font></td>");
   echo ("</tr>"); 
   
   echo ("<tr bgcolor=\"#000000\">\n");
   echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Código</b></font></td>");
   echo ("<td width=\"60%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Nome</b></font></td>");
   echo ("<td width=\"20%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Departamento</b></font></td>");
   echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Ingresso</b></font></td>");
   echo ("</tr>"); 

   $i=1;

   // cores fundo
   $bg0 = "#000000";
   $bg1 = "#EEEEFF";
   $bg2 = "#FFFFEE";
 
   // cores fonte
   $fg0 = "#FFFFFF";
   $fg1 = "#000099";
   $fg2 = "#000099";

   while( $query->MoveNext() )
   {
     list ( $id, 
            $ref_professor, 
            $nome_professor,
            $ref_departamento,
            $nome_departamento,
            $dt_ingresso) = $query->GetRowValues();
  
     $href  = "<a href=\"professores_edita.php?id=$id\">$ref_professor</a>";
  
     if ( $i % 2 )
     {
        $bg = $bg1;
        $fg = $fg1;
     }
     else
     {
        $bg = $bg2;
        $fg = $fg2;
     }
     
     echo("<tr bgcolor=\"$bg\">\n");
     echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$href</td>");
     echo ("<td width=\"60%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$nome_professor</td>");
     echo ("<td width=\"20%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$nome_departamento</td>");
     echo ("<td width=\"10%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$dt_ingresso</td>");
     echo("  </tr>");
     
     $i++;

   }

   echo("<tr><td colspan=\"4\" align=\"center\"><hr></td></tr>");

   echo("</table></center>");

   $query->Close();

   $conn->Close();
}
</script>
</head>

<body bgcolor="#FFFFFF" marginwidth="20" marginheight="20">
<form method="post" action="">
  <p> 
    <script language="PHP">
       ListaProfessores();
    </script>
  </p>
  <div align="center"> 
    <input type="button" name="Button" value="  Voltar  " onClick="location='consulta_inclui_professores.php'">
  </div>
</form>
</body>
</html>
