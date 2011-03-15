<?php require_once("../common.php"); ?>

<html>
<head>
<title>Untitled Document</title>
<script language="JavaScript">
function Confirma_Exclui(arg1, arg2)
{
  url = 'post/disciplinas_equivalentes_exclui.php?id=' + arg1;

  if (confirm("Você tem certeza que deseja EXCLUIR a Equivalência da Disciplina: "+arg2+" ?"))
    location=(url)
  else
    alert("Exclusão Cancelada.");
}
</script>
<script language="PHP">
function ListaDisciplinasEquivalentes($ref_curso=null)
{
   
    $conn = new Connection;

    $conn->open();

    $sql = " select id, " .
           "        ref_disciplina, " .
           "        descricao_disciplina(ref_disciplina), " .
           "        ref_disciplina_equivalente, " .
           "        descricao_disciplina(ref_disciplina_equivalente), " .
           "        ref_curso, " .
           "        curso_desc(ref_curso) " .
           " from disciplinas_equivalentes";

    if ( $ref_curso ) 
    {
        $sql .= " where ref_curso = $ref_curso";
    }
          
    $sql .= " order by ref_curso";
   
    $query = $conn->CreateQuery($sql);

    echo("<center><table width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");
    echo ("<tr><td>&nbsp;</td></tr>");
    echo ("<tr>");
    echo ("<td bgcolor=\"#000099\" colspan=\"6\" height=\"28\" align=\"center\"><font size=\"3\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#FFFFFF\"><b>Listagem de Disciplinas Equivalentes Cadastradas</b></font></td>");
    echo ("</tr>"); 

    $i=1;

    // cores fundo
    $bg0 = "#000000";
    $bg1 = "#DDDDFF";
    $bg2 = "#FFFFEE";
 
    // cores fonte
    $fg0 = "#FFFFFF";
    $fg1 = "#000099";
    $fg2 = "#000099";

    $aux_curso = -1;
   
    while( $query->MoveNext() )
    {
     list ( $id, 
	        $ref_disciplina,
	        $disciplina,
            $ref_disciplina_equivalente,
	        $disciplina_equivalente,
            $ref_curso,
            $nome_curso) = $query->GetRowValues();

     if ($aux_curso != $ref_curso)
     {
        echo ("<tr>");
        echo ("<td bgcolor=\"#FFFFFF\" colspan=\"6\" height=\"28\" align=\"center\"><font size=\"3\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#000077\"><b>&nbsp;<br>$ref_curso - $nome_curso<br>&nbsp;</b></font></td>");
        echo ("</tr>"); 

        echo ("<tr bgcolor=\"#000000\">\n");
        echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Cont</b></font></td>");
        echo ("<td width=\"35%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Cód - Disciplina</b></font></td>");
        echo ("<td width=\"40%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Cód - Disciplina Equivalente</b></font></td>");
        echo ("<td width=\"10%\" align=\"right\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Curso&nbsp;&nbsp;</b></font></td>");
        echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>&nbsp;</b></td>");
        echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>&nbsp;</b></td>");
        echo ("</tr>"); 

        $aux_curso = $ref_curso;
     }

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
    
     $href  = "<a href=\"altera_disciplinas_equivalentes.php?id=$id\"><img src=\"../images/update.gif\" title='Alterar Disciplina Equivalente' align='absmiddle' border=0></a>";
     $href1 = "<a href=\"javascript:Confirma_Exclui('$id','$ref_disciplina')\"><img src=\"../images/delete.gif\" title='Excluir Disciplina Equivalente' align='absmiddle' border=0></a>";

     echo("<tr bgcolor=\"$bg\">\n");
     echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$i</td>");
     echo ("<td width=\"35%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$ref_disciplina - $disciplina</td>");
     echo ("<td width=\"40%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$ref_disciplina_equivalente - $disciplina_equivalente</td>");
     echo ("<td width=\"10%\" align=\"right\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$ref_curso&nbsp;</td>");
     echo ("<td width=\"5%\" align=\"right\">$href</td>");
     echo ("<td width=\"5%\" align=\"right\">$href1</td>");
     echo("  </tr>");
     
     $i++;

   }

   echo("<tr><td colspan=\"6\" align=\"center\"><hr></td></tr>");
   echo("</table></center>");

   $query->Close();

//   $conn->Close();
}
</script>

</head>

<body bgcolor="#FFFFFF">
<form method="post" action="">
  <p>
    <? ListaDisciplinasEquivalentes($ref_curso); ?>
  </p>
  <div align="center"> 
    <input type="button" name="Button" value="  Voltar  " onClick="location='consulta_disciplinas_equivalentes.php'">
  </div>
</form>
</body>
</html>
