<?php 

require_once("matricula_avulsa.inc.php"); 

unset($_SESSION['sa_diarios_matricula_avulsa']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <script language="javascript" src="../../lib/prototype.js"></script>
        <script language="javascript">
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
            //Ajax que busca os contratos e os cursos
            function BuscaDiarios(){
                var cod_diario = $F('diario_id');
                var cod_contrato = $F('contrato_id');
                var cod_curso = $F('curso_id');
                var url  = 'matricula_avulsa_adicionar.php';
                var pars = 'diario_id=' + cod_diario + '&contrato_id=' + cod_contrato + '&curso_id=' + cod_curso;

                var myAjax = new Ajax.Updater('DiariosMatricular',url, {method: 'post',parameters: pars});
            }

            -->
        </script>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body onload="Oculta('matricular')">
        <div align="center" style="height:600px;">
            <h1>Processo de Matr&iacute;cula Avulsa</h1>
            <h4>Sele&ccedil;&atilde;o das disciplinas: Etapa 2/2</h4>
            <div class="panel"> <strong>Aluno: </strong>
                <?=$aluno_id?> - <?=$aluno_nome?><br />
                <strong>Curso: </strong><?=$curso_id?> - <?=$curso_nome?> <strong>Turma: </strong><?=$turma?><br />
                <strong>Per&iacute;odo: </strong><?=$periodo_id?>
                <strong>Contrato: </strong><?=$contrato_id?>
                <strong>Cidade: </strong><?=$campus_nome?>
            </div>
            <div class="panel"> <strong>Di&aacute;rios matriculados</strong> (Di&aacute;rio / Disciplina / Professor) <br />
                <br />
                <?=$DisciplinasMatriculadas?>
                <br />
                <strong>(*) Disciplina cancelada.</strong>
            </div>

            <form name="form1" method="post" action="matricula.post.php">
                <label>C&oacute;digo do di&aacute;rio:
                    <input type="text" name="diario_id" id="diario_id" />
                </label>
                <input type="button" name="adicionar" id="adicionar" onclick="BuscaDiarios();" value="Adicionar di&aacute;rio" />
                <a href="#" onclick="window.open('matricula_avulsa_pesquisar.php','consulta_diaro','resizable=yes, toolbar=no,width=500,height=500,scrollbars=yes,top=0,left=0');">
                    <img src="../../public/images/icons/lupa.png" alt="" /> Buscar di&aacute;rio
                </a>

                <div class="panel">
                    <strong>Di&aacute;rios para matricular</strong>
                    (Di&aacute;rio / Disciplina / Professor)
                    <br /><br />
                    <div id="DiariosMatricular"></div>
                    <br /><br />
                </div>
                <input type="hidden" name="periodo_id" id="periodo_id" value="<?=$periodo_id?>" />
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
