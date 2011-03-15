<?php

require_once("../app/setup.php");

$conn = new connection_factory($param_conn);


$sql = 'SELECT descricao, data FROM avisos WHERE id = 1;';
$Result1 = $conn->Execute($sql);

$avisos = array();
$avisos[0] = $Result1->fields[0];
		
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SA</title>
<script language="javascript">
<!--
    function fechar(){
        document.getElementById('popup').style.display = 'none';
    }

    function abrir(){
        document.getElementById('popup').style.display = 'block';
        setTimeout ("fechar()", 36000);
    }

    function avisos() {
        window.open("avisos/cadastrar.php",'Avisos','resizable=yes, toolbar=no,width=550,height=350,scrollbars=yes,top=0,left=0');
    }
-->
</script>
<link href="../public/styles/style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
	.style1 {
		font-size: 10px;
	}
	a {
		text-decoration:underline;
		color:#0033CC;
	}
	#popup {
		position: absolute;
		top: 10%;
		left: 1px;
		width: 160px;
		padding: 10px 10px 10px 10px;
		border-width: 2px;
		border-style: solid;
		background: #ffffa0;
		display: none;
	}
</style>
</head>

<body onload="javascript: abrir()">
<div id="popup">
  <strong>Avisos:</strong><br />
  <br />
  <?php echo $avisos[0]; ?>
  <p><small>
     <a href="javascript: fechar();">Fechar</a>
     <a href="javascript:avisos()">Alterar</a>
  </small></p>
</div>
<br />
<center>
  <table width="650" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><div align="center"><img src="../public/images/diagrama.gif" width="650" height="340" border="0" usemap="#Map" /></div></td>
    </tr>
    
    <tr>
      <td align="center"><br />
        <p><a href="../index.php"> Sair do Sistema</a> - <a href="javascript: abrir();">Avisos</a> - <a href="../public/help.php">Ajuda e Documenta&ccedil;&atilde;o</a> </p>
        <p class="texto1 style1"><strong>Sistema Acad&ecirc;mico - revis&atilde;o <a href="../MUDANCAS.TXT" target="_blank"><?=$REVISAO?></a></strong><br />
          &copy;2011  <?=$IEnome?><br />
      </p></td>
    </tr>
  </table>
</center>

<map name="Map" id="Map">
  <area shape="rect" coords="406,40,582,58" href="matricula/matricula_aluno.php" alt="Matr&iacute;cula individual" />
  <area shape="rect" coords="406,85,557,102" href="matricula/remover_matricula/filtro.php" alt="Remover matr&iacute;cula" />
  <area shape="rect" coords="230,37,348,87" href="sagu/academico/consulta_inclui_contratos.php" alt="Contratos" />
  <area shape="rect" coords="16,31,158,89" href="sagu/academico/consulta_inclui_pessoa.php" alt="Pessoa f&iacute;sica" />
  <area shape="rect" coords="229,212,323,246" href="sagu/academico/consulta_inclui_professores.php" alt="professores" />
  <area shape="rect" coords="412,163,567,223" href="sagu/academico/disciplina_ofer.php" alt="Disciplinas oferecidas" />
  <area shape="rect" coords="425,277,546,325" href="sagu/academico/consulta_inclui_cursos_disciplinas.php" alt="cursos / disciplinas" />
  <area shape="rect" coords="227,140,323,175" href="sagu/academico/coordenadores.php" alt="coordenadores" />
  <area shape="rect" coords="585,270,648,330" href="relatorios/menu.php" alt="Listar relat&oacute;rios" />
  <area shape="rect" coords="404,61,583,82" alt="Dispensa de disciplina" href="dispensa_disciplina/dispensa_aluno.php" />
</map>
</body>
</html>
