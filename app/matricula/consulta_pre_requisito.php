<?php 

require_once("../../app/setup.php");
require_once('../../core/situacao_academica.php');


//Criando a classe de conexao ADODB
$Conexao = NewADOConnection("postgres");

//Setando como conexao persistente
$Conexao->PConnect("host=$host dbname=$database port=$port user=$user password=$password");


function ListaPreRequisitos( $diario_id, $curso_id )
{
	global $Conexao;
  
    $disciplinas = " SELECT get_disciplina_de_disciplina_of('$diario_id') ";

    // se � uma disciplina equivalente pesquisa os pr�-requisitos da disciplina "original" da matriz curricular
    if (verificaEquivalencia($curso_id,$diario_id))
    {
        // a disciplina � equivalente, recupera a disciplina "original" da matriz curricular
        $sqlEquivalente = "
                    SELECT 
                          DISTINCT 
                                ref_disciplina
                        FROM
                                disciplinas_equivalentes 
                        WHERE 
                             ref_disciplina_equivalente = get_disciplina_de_disciplina_of('$diario_id') AND 
                             ref_curso = '$curso_id';";

        $RsEquivalente = $Conexao->Execute($sqlEquivalente);
        $equivalentes = $RsEquivalente->GetAll();

        if (count($equivalentes) > 0)
            $disciplinas =  $sqlEquivalente;
    }
 
	$sql = " SELECT id, " .
		   "        ref_curso, " .
		   "        curso_desc(curso_disciplina_ofer($diario_id)), " .
           "        ref_disciplina, " .
           "        descricao_disciplina(ref_disciplina), " .
           "        ref_disciplina_pre, " .
           "        descricao_disciplina(ref_disciplina_pre) " .
	       " FROM pre_requisitos ";
          
   if ( is_numeric($diario_id) AND is_numeric($curso_id) ) 
   {
      $sql .= " WHERE ref_disciplina IN ( $disciplinas ) AND ref_curso = $curso_id ";
   }
          
   $sql .= " ORDER BY ref_curso ;";

   $query = $Conexao->Execute($sql);

   echo("<center><table width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">");
   echo ("<tr><td>&nbsp;</td></tr>");
   echo ("<tr>");
   echo ("<td bgcolor=\"#000099\" colspan=\"8\" height=\"28\" align=\"center\"><font size=\"3\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#FFFFFF\"><b>Pr&eacute;-requisitos Cadastrados</b></font></td>");
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
  

   while( !$query->EOF )
   {
      $id					= $query->fields[0];
      $ref_curso			= $query->fields[1];
      $nome_curso			= $query->fields[2];
	  $ref_disciplina		= $query->fields[3];
	  $disciplina			= $query->fields[4];
      $ref_disciplina_pre	= $query->fields[5];
	  $disciplina_pre		= $query->fields[6];

   
     if ($aux_curso != $ref_curso)
     {
        echo ("<tr>");
        echo ("<td bgcolor=\"#FFFFFF\" colspan=\"7\" height=\"28\" align=\"center\"><font size=\"3\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#000077\"><b>&nbsp;<br>$ref_curso - $nome_curso<br>&nbsp;</b></font></td>");
        echo ("</tr>"); 

        echo ("<tr bgcolor=\"#000000\">\n");
        echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Cont</b></font></td>");
        echo ("<td width=\"28%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>C�d - Disciplina</b></font></td>");
        echo ("<td width=\"28%\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>C�d - Disciplina Pr�-requisito</b></font></td>");
        echo ("<td width=\"34%\" align=\"right\"><Font face=\"Verdana\" size=\"2\" color=\"#ffffff\"><b>Curso&nbsp;&nbsp;</b></font></td>");
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
     
     echo("<tr bgcolor=\"$bg\">\n");
     echo ("<td width=\"5%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$i</td>");
     echo ("<td width=\"28%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$ref_disciplina - $disciplina</td>");
     echo ("<td width=\"28%\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$ref_disciplina_pre - $disciplina_pre</td>");
     echo ("<td width=\"34%\" align=\"right\"><Font face=\"Verdana\" size=\"2\" color=\"$fg1\">$ref_curso&nbsp;&nbsp;</td>");
     echo("  </tr>");
     
     $i++;
	 $query->MoveNext();

   }

   echo("<tr><td colspan=\"8\" align=\"center\"><hr></td></tr>");
   echo("</table></center>");

}
?> 
<html> 
<head> 
<title>Consulta pr&eacute;-requisito</title> 
</head>

<body bgcolor="#FFFFFF">
  <p>
    <?php ListaPreRequisitos($_GET['o'], $_GET['c']); ?>
  </p>
</body>
</html>
