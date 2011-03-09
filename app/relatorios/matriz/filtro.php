<?php

  //ARQUIVO DE CONFIGURACAO E CLASSE ADODB
  header ("Cache-Control: no-cache");
  require("../../../lib/common.php");
  require("../../../app/setup.php");
  require("../../../lib/adodb5/adodb.inc.php");
  require("../../../lib/adodb5/tohtml.inc.php");
  
  $btnOK = false;
  //print_r($_SESSION);
  
  //Criando a classe de conexão ADODB
  $Conexao = NewADOConnection("postgres");
  
  //Setando como conexão persistente
  $Conexao->PConnect("host=$host dbname=$database user=$user password=$password");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Situação do aluno no curso (Histórico)</title>
<link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {
	color: #0099FF;
	font-style: italic;
}
-->
</style>
</head>
<body bgcolor="#FFFFFF">
<h2>Situação do aluno no curso (Histórico)</h2>
<form action="../../../relatorios/matriz/filtro.php" method="post" name="busca">
  <table border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="60"><div align="center">
          <label class="bar_menu_texto"> <a href="../../../relatorios/menu.php" class="bar_menu_texto"> <img src="../../../public/images/icons/back.png" alt="Voltar" width="20" height="20" /><br />
          Voltar</a> </label>
        </div></td>
    </tr>
  </table>
  <table width="700" cellpadding="0" cellspacing="0" bgcolor="#E6E6E6" class="pesquisa">
    <tr>
      <th colspan="2">&nbsp;</th>
    </tr>
    <tr>
      <td width="50" valign="botton">Matr&iacute;cula:</td>
      <td valign="botton">Nome:</td>
    </tr>
    <tr>
      <td width="50" valign="middle"><input name="ra"  type="text" maxlenght="8" size="8" value="<?echo $ra; ?>" /></td>
      <td valign="middle"><input name="nome"type="text" maxlenght="50" size="50" value="<? echo $nome; ?>" />
        <input name="btnOK" type="submit" value=" OK " /></td>
    </tr>
    <tr>
      <td valign="middle">&nbsp;</td>
      <td valign="middle">&nbsp;</td>
    </tr>
  </table>
</form>
<?php


if ($_POST) {
	
	$id = $_SESSION['id'];
	$ra = trim(@$_POST['ra']);
	$nome = trim(@$_POST['nome']);

  	$sql1 = 'SELECT  DISTINCT a.nome, a.id, b.ref_curso, d.abreviatura
	  FROM pessoas a, matricula b, contratos c, cursos d
	  WHERE
      a.id IN ( 
	  	SELECT DISTINCT a.ref_pessoa 
		FROM  matricula a 
		ORDER BY a.ref_pessoa 
		)  AND 
      a.id = b.ref_pessoa AND
      c.ref_pessoa = a.id AND
      c.id = b.ref_contrato AND
      b.ref_curso = d.id AND
      c.ref_curso = d.id ';
 

	if(isset($ra) && is_numeric($ra) && $ra != "") {
    
		$sql1 .= " AND a.ra_cnec LIKE '%$ra%' ";
		$btnOK = true;
    }

	if(isset($nome) && ($nome != "") && strlen($nome) != 2) {

    	$sql1 .= " AND lower(to_ascii(a.nome,'LATIN1')) ";
        $sql1 .= " SIMILAR TO lower(to_ascii('$nome%','LATIN1')) ";
        $btnOK = true;
	}

	$sql1 .= " ORDER BY a.nome LIMIT 20 OFFSET -1;"; 

	
	//EXECUTANDO A SQL COM ADODB
  	$Result1 = $Conexao->Execute($sql1);
	
	
	//CONTANTO O NUMERO DE RESULTADOS
  	$num_result = $Result1->RecordCount();
  	
	
	if(is_string($Result1)) {
			
		echo $Result1;
		//echo $qry1;
		exit;
	} 
	else {
			
		if ($num_result > 0) {
				
	   		echo '<table  width="700" border="0" >
	    			<tr bgcolor="#666666">
    					<td width="12%" height="24"><b><font color="#FFFFFF">Matr&iacute;cula</font></b></td>
    					<td width="45%"><b><font color="#FFFFFF">Nome</font></b></td>
						<td width="35%"><b><font color="#FFFFFF">Curso</font></b></td>
      					<td width="10%"><b><font color="#FFFFFF">Exibir</font></b></td>
    				</tr>';
			
           	while(!$Result1->EOF) {
									
      	   		$q3id = str_pad($Result1->fields[1], 5, "0", STR_PAD_LEFT);
				
				if ($st == '#F3F3F3') {
   					$st = '#E3E3E3';
				}
				else {
					$st ='#F3F3F3';
				}
				
      			echo "<tr bgcolor=\"$st\">";
				echo ' <td align="center">' . $q3id . '</td>';
				echo ' <td>' . $Result1->fields[0] . '</td>';
               	echo ' <td>' . $Result1->fields[3] . '</td>';
				echo ' <td align="center">
						<a href="matriz.php?aluno=' . $q3id . '&nome=' . $Result1->fields[0] . '&curso=' . $Result1->fields[3] . '&cs='.$Result1->fields[2] . '">
						<img src="../../../public/images/icons/print.jpg" width="20" height="20" alt="Exibir dados em HTML" /></a>
						</td>';
				echo '</tr>';
					
           		$Result1->MoveNext();
					
    		}//fim while
				
            	echo '</table>';
 				
    		}//fim se 
			else {
  				
				echo '<script language="javascript">
						window.alert("Não foi encontrado nenhum aluno!"); 
						javascript:window.history.back(1); 
					  </script>';
					  
            	unset($_POST['nomes']);
        		unset($_POST['ras']);
        		unset($_POST['btnOK']);
        		$_POST = array();
				
      			die;
			}// fim else
			
    		unset($_POST['nomes']);
    		unset($_POST['ras']);
        	unset($_POST['btnOK']);
   			
			$_POST = array();
        
		}//fim else	
	}//fim se

?>
</div>
</div>
</body>
</html>
