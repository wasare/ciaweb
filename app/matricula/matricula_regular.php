<?php require_once("matricula_regular.inc.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SA</title>
<link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
<script language="JavaScript">
<!--
function confirma()
{
	if (confirm('Tem certeza que deseja matricular o aluno nas disciplinas selecionadas?'))
	{
		document.form1.submit();
	} else {
		// se não confirmar, coloque o codigo aqui
    }
}
function selecionar_tudo()
{
		for (i=0;i<document.form1.elements.length;i++)
  		if(document.form1.elements[i].type == "checkbox")
     		document.form1.elements[i].checked=1
} 

function deselecionar_tudo()
{
		for (i=0;i<document.form1.elements.length;i++)
  		if(document.form1.elements[i].type == "checkbox")
    		document.form1.elements[i].checked=0
}
-->
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body onload="Oculta('matricular')">
<div align="center" style="height:600px;">
  <h1>Processo de Matr&iacute;cula Regular</h1>
  <h4>Sele&ccedil;&atilde;o das disciplinas: Etapa 2/2</h4>
  <div class="panel"> <strong>Aluno: </strong>
    <?=$aluno_id?>
    -
    <?=$aluno_nome?>
    <br />
    <strong>Curso: </strong>
    <?=$curso_id?>
    -
    <?=$curso_nome?>
    <strong>Turma: </strong>
    <?=$turma?>
    <br />
    <strong>Per&iacute;odo: </strong>
    <?=$sa_periodo_id?>
    <strong>Contrato: </strong>
    <?=$contrato_id?>
    <strong>Cidade: </strong>
    <?=$campus_nome?>
  </div>
  <div class="panel"> <strong>Di&aacute;rios matriculados</strong> (Di&aacute;rio / Disciplina / Professor) <br />
    <br />
    <?=$DisciplinasMatriculadas?>
    <br />
    <strong>(*) Disciplina cancelada.</strong>
  </div>
  <form name="form1" method="post" action="matricula.post.php">
    <div class="panel"> <strong>Selecione os di&aacute;rios para matricular</strong> (Di&aacute;rio / Disciplina / Professor)
      <p>
        <?=$MarcaDisciplina?>
      </p>
	  <?=$DiarioMatricular?>
      <p>
        <?=$MarcaDisciplina?>
      </p>
    </div>
    <input type="hidden" name="periodo_id" id="periodo_id" value="<?=$sa_periodo_id?>" />
    <input type="hidden" name="curso_id" id="curso_id" value="<?=$curso_id?>" />
    <input type="hidden" name="aluno_id" id="aluno_id" value="<?=$aluno_id?>" />
    <input type="hidden" name="contrato_id" id="contrato_id" value="<?=$contrato_id?>" />
    <input type="hidden" name="ref_campus" id="ref_campus" value="<?=$ref_campus?>" />
    <p>
      <input type="button" name="matricular" id="matricular" onclick="confirma()" value="Matricular" />
    </p>
  </form>
</div>
</body>
</html>
