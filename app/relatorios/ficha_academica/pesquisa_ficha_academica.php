<?php

require_once(dirname(__FILE__) .'/../../../app/setup.php');
  
$btnOK = false;
  
$conn = new connection_factory($param_conn);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pesquisa ficha academica</title>
<link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h2>Pesquisa Ficha Acad&ecirc;mica</h2>
<form action="pesquisa_ficha_academica.php" method="post" name="busca">
  <table border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="60"><div align="center">
          <label class="bar_menu_texto"> <a href="#" onclick="history.back(-1)" class="bar_menu_texto"> <img src="../../../public/images/icons/back.png" alt="Voltar" width="20" height="20" /><br />
          Voltar</a> </label>
        </div></td>
    </tr>
  </table>
  <table width="760" cellpadding="0" cellspacing="0" bgcolor="#E6E6E6" class="pesquisa">
    <tr>
      <th colspan="2">&nbsp;</th>
    </tr>
    <tr>
      <td width="50" valign="botton">Matr&iacute;cula:</td>
      <td valign="botton">Nome:</td>
    </tr>
    <tr>
      <td width="50" valign="middle"><input name="ra"  type="text" maxlength="8" size="8" value="<?=$ra?>" /></td>
      <td valign="middle"><input name="nome"type="text" maxlength="50" size="50" value="<?=$nome;?>" />
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

  	$sql1 = 'SELECT  DISTINCT a.nome, a.id, b.ref_curso, d.abreviatura, c.id as contrato, c.turma
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
    
		$sql1 .= " AND a.ra_cnec = '$ra' ";
		$btnOK = true;
    }

	if(isset($nome) && ($nome != "") && strlen($nome) != 2) {

    	$sql1 .= " AND lower(to_ascii(a.nome)) ";
        $sql1 .= " SIMILAR TO lower(to_ascii('$nome%')) ";
        $btnOK = true;
	}

	$sql1 .= " ORDER BY a.nome LIMIT 20 OFFSET -1;"; 

	
	$Result1 = $conn->Execute($sql1);
	
	
	//CONTANTO O NUMERO DE RESULTADOS
  	$num_result = $Result1->RecordCount();
  	
	
	if(is_string($Result1)) {
		echo $Result1;
		exit;
	} 
	else {
			
		if ($num_result > 0) {

?>
				
	   		<table  width="760" border="0" >
              <tr bgcolor="#666666">
                <th height="24"><b><font color="#FFFFFF">Matr&iacute;cula</font></b></th>
                <th><b><font color="#FFFFFF">Nome</font></b></th>
                <th><b><font color="#FFFFFF">Curso</font></b></th>
                <th><b><font color="#FFFFFF">Turma</font></b></th>
      			<th><b><font color="#FFFFFF">Exibir</font></b></th>
    		  </tr>
<?php
           	while(!$Result1->EOF) {
									
      	   		$q3id = str_pad($Result1->fields[1], 5, "0", STR_PAD_LEFT);
				
				if ($st == '#F3F3F3') {
   					$st = '#E3E3E3';
				}
				else {
					$st ='#F3F3F3';
				}
?>
      			<tr bgcolor="<?=$st?>">
                  <td align="center"><?=$q3id?></td>
                  <td><?=$Result1->fields[0]?></td>
                  <td><?=$Result1->fields[3]?></td>
                  <td align="center"><?=$Result1->fields[5]?></td>
                  <td align="center">
                        &nbsp;
						<a target="_blank" href="lista_ficha_academica.php?aluno=<?=$Result1->fields[1]?>&cs=<?=$Result1->fields[2]?>&contrato=<?=$Result1->fields[4]?>">
                          <img src="../../../public/images/icons/report.png" width="20" height="20" title="Ficha acad&ecirc;mica" alt="Ficha acad&ecirc;mica" />
                        </a>
						&nbsp;&nbsp;
                        <a target="_blank" href="../pessoas/lista_pessoa.php?pessoa_id=<?=$Result1->fields[1]?>">
                          <img src="../../../public/images/icons/pessoa.png" width="20" height="20" title="Informa&ccedil;&otilde;es pessoais" alt="Informa&ccedil;&otilde;es pessoais" />
                        </a>
                        &nbsp;&nbsp;
                        <a target="_blank" href="<?=$BASE_URL?>/app/relatorios/integralizacao_curso/lista_integralizacao_curso.php?aluno=<?=$Result1->fields[1]?>&cs=<?=$Result1->fields[2]?>&contrato=<?=$Result1->fields[4]?>">
                          <img src="<?=$BASE_URL?>/public/images/icons/verifica.png" width="20" height="20" border="0" title="Verifica integraliza&ccedil;&atilde;o do curso" alt="Verifica integraliza&ccedil;&atilde;o do curso" />
                        </a>
                        &nbsp;
                  </td>
                </tr>
<?php
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
</body>
</html>
