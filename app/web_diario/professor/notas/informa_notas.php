<?php

require_once(dirname(__FILE__) .'/../../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/number.php');

$conn = new connection_factory($param_conn);

$diario_id = (int) $_POST['diario_id'];
$periodo = $_SESSION['web_diario_periodo_id'];
$operacao = $_POST['operacao'];

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript"> 
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
}
// ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //

$curso = $_POST['curso'];
$prova = $_POST['getprova'];

$grupo = ($sa_ref_pessoa . "-" . $periodo . "-" . get_disciplina($diario_id) . "-" . $diario_id);
$grupo_novo = ("%-" . $periodo_id . "-%-" . $diario_id);


if(empty($prova))
{
     print '<script language="javascript" type="text/javascript">
	window.alert("Você deve selecionar qual a prova que será lançada as notas.");
	javascript:window.history.back(1);
	</script>';
	exit;
}
else
{

$sql12 = 'SELECT * FROM (';
$sql12 .= "SELECT   DISTINCT
                    matricula.ordem_chamada, pessoas.nome, pessoas.id, SUM(d.nota) AS notaparcial
            FROM
                matricula
            INNER JOIN pessoas ON (matricula.ref_pessoa = pessoas.id)
            INNER JOIN diario_notas d ON (d.id_ref_pessoas = pessoas.id AND
                                            d.id_ref_pessoas = matricula.ref_pessoa AND
                                            d.id_ref_periodos = '$periodo' AND
                                            d.d_ref_disciplina_ofer = $diario_id AND
                                            d.ref_diario_avaliacao <> '$prova'  AND
                                            d.ref_diario_avaliacao <> '7')
            WHERE
                (matricula.ref_disciplina_ofer = $diario_id) AND
                (matricula.dt_cancelamento is null) AND
				(matricula.ref_motivo_matricula = 0)

            GROUP BY
                     matricula.ordem_chamada, pessoas.nome, pessoas.id, d.id_ref_pessoas
            ORDER BY pessoas.nome ";

$sql12 .= ') AS T1 INNER JOIN (';

$sql12 .= "SELECT DISTINCT
               pessoas.id, d.nota AS notabanco
            FROM
               matricula INNER JOIN
               pessoas ON (matricula.ref_pessoa = pessoas.id) INNER JOIN
               diario_notas d ON (id_ref_pessoas = pessoas.id AND
                                 d.id_ref_pessoas = matricula.ref_pessoa AND 
							     d.id_ref_periodos = '$periodo' AND 
								 d.d_ref_disciplina_ofer = $diario_id AND 
								 d.ref_diario_avaliacao = '$prova')
            WHERE
				(matricula.ref_disciplina_ofer = $diario_id) AND 
				(matricula.dt_cancelamento is null) AND 
				(matricula.ref_motivo_matricula = 0)";


$sql12 .= ') AS T2 ON (T2.id = T1.id) INNER JOIN (';


$sql12 .= "SELECT DISTINCT
               pessoas.id AS ref_pessoa, d.nota AS notaextra
            FROM
               matricula INNER JOIN
               pessoas ON (matricula.ref_pessoa = pessoas.id) INNER JOIN
               diario_notas d ON (id_ref_pessoas = pessoas.id AND
                                 d.id_ref_pessoas = matricula.ref_pessoa AND 
								d.id_ref_periodos = '$periodo' AND 
								d.d_ref_disciplina_ofer = $diario_id AND 
								d.ref_diario_avaliacao = '7')
            WHERE
				(matricula.ref_disciplina_ofer = $diario_id) AND 
				(matricula.dt_cancelamento is null) AND 
				(matricula.ref_motivo_matricula = 0)";

$sql12 .= ') AS T3 ON (T3.ref_pessoa = T2.id) ORDER BY lower(to_ascii(nome,\'LATIN1\'));';

//die('<pre>'.$sql12.'</pre>');

$sql1 = "SELECT DISTINCT
  m.ordem_chamada,
  p.nome,
  p.id,
  p.ra_cnec
FROM
  matricula m
  INNER JOIN pessoas p ON (m.ref_pessoa = p.id)
WHERE
  (m.ref_periodo = '$periodo') AND
  (m.ref_disciplina_ofer = '$diario_id') AND
  (m.dt_cancelamento isnull) AND
  (m.ref_motivo_matricula = 0)
ORDER BY
  m.ordem_chamada;";

// (matricula.ref_disciplina = '$getdisciplina') AND

$alunos = $conn->get_all($sql12);


if($prova != 7)
{
	/* PROCESSO DE NOTA DISTRIBUIDA */

	$sqlNotaDistribuida = "
		SELECT nota_distribuida 
		FROM diario_formulas 
		WHERE 
		grupo ILIKE '%-$diario_id' AND 
		prova = '$prova'";

    $nota_distribuida = $conn->get_one($sqlNotaDistribuida);
    if($nota_distribuida > 0)
      $nota_distribuida = number::numeric2decimal_br($conn->get_one($sqlNotaDistribuida),1);
    else
       $nota_distribuida = '';
}

?>
<html>
<head>
<title><?=$IEnome?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
<script type="text/javascript" src="<?=$BASE_URL .'lib/prototype.js'?>"> </script>

<script language="javascript" type="text/javascript">

function findNextElement(index) {
 if(Prototype.Browser.IE) {
    elements = new Form.getElements(document.forms[0]);
 }else{
    elements = $$('[tabindex]');
 }

    for(i = 0; i < elements.length; i++) {
        element = elements[i];
        if(parseInt(element.readAttribute("tabindex")) > (parseInt(index))) {
            // alert(element.id+' '+element.readAttribute('tabindex')+' '+element.visible()+' '+element.disabled+' '+element.readOnly);
            return element;
        }
    }
    return elements[0];
}

function auto_tab(field) {
    if (field.value.length < field.getAttribute("maxlength")) return;

    new Field.activate(findNextElement(field.tabIndex));
}

function toNumeric(nStr) {
    var numeric;
    if(!isFinite(nStr)) {
      numeric = nStr.replace(',', '|');
      numeric = numeric.replace('.', '');
      numeric = numeric.replace('|','.');
      x = numeric.split('.');
      return parseFloat(x[0] + '.' + x[1]);
    }
    else
        return parseFloat(nStr);
}

function validate_nota(field) {
	var distribuida = $F('valor_avaliacao');
    if(((toNumeric(field.value) + toNumeric(distribuida) ) / 2) > toNumeric(distribuida)) {
		alert("Você não pode lançar " + field.value + " pontos e distribuir somente " + distribuida + " pontos!");
        field.focus();
    }
}

</script>

</head>

<body>

<br />

  <div align="left" class="titulo1">
		   Lan&ccedil;amento / Altera&ccedil;&atilde;o da 
        <?php
		if($prova == 7) { 
			echo '<font color="blue"> Nota Extra</font> <font color="red" size="2"><br /> Utilize apenas quando houver reavalia&ccedil;&atilde;o e/ou recupera&ccedil;&atilde;o final</font>' ;		  
		}
		else{	
			echo ' Nota<font color="blue"> P'. $prova .'</font>.'; 
		}		
        ?>
</div>
 <br />
 <?=papeleta_header($diario_id)?>
 <br />

<form name="informa_notas" id="informa_notas" method="post" action="<?=$BASE_URL .'app/web_diario/professor/notas/grava_notas.php'?>">

	<input type="hidden" name="codprova" id="codprova" value="<?=$prova?>">
	<input type="hidden" name="diario_id" id="diario_id" value="<?=$diario_id?>">
	<input type="hidden" name="operacao" id="operacao" value="<?=$operacao?>">
    
<?php
		if($prova != 7) :
?>        
            <span class="obrigatorio">Para eliminar todas as notas informe 0 para todas as notas.</span><br />

			<p><strong>Nota distribu&iacute;da:</strong>
			<input name="valor_avaliacao" type="text" id="valor_avaliacao" size="5" maxlength="4" value="<?=$nota_distribuida?>" tabindex="1" />&nbsp;
			<span class="obrigatorio">* obrigatória</span>
			</p>
<?php	else : ?>
			<p>
            <font color="green"><strong>
<?php 
			$curso_tipo = get_curso_tipo($diario_id);
			// TODO: Selecionar método de cálculo da nota final com base em parâmetros do sistema
			if( $curso_tipo == 2 || $curso_tipo == 4 || $curso_tipo == 5 || $curso_tipo == 6 || $curso_tipo == 10 ) : ?>
				Nota Final ser&aacute; igual  (Nota Anterior + Nota Extra) / 2		
<?php		else : ?>
				Nota Final ser&aacute; igual Nota Extra 
<?php		endif; ?>
			</strong></font></p>
			<input name="valor_avaliacao" type="hidden" id="valor_avaliacao" size="5" maxlength="4" value="100" />
<?php endif; ?>
<br />
<table cellspacing="0" cellpadding="0" class="papeleta">
  <tr bgcolor="#666666">

  <td align="center"><font color="#FFFFFF"><strong>Ordem</strong></font></td>
  <td align="center"><font color="#FFFFFF"><strong>Nota <?=($prova) != 7 ? $prova : 'Extra'?></strong></font></td>
      <td><font color="#FFFFFF"><b>&nbsp;Matr&iacute;cula</b></font></td>
  <td><font color="#FFFFFF"><b>&nbsp;Nome</b></font></td>
 </tr>
 <?php
    $st = '';
    $ordem = 1;
  
   foreach($alunos as $aluno) :
   
      $notaprova = $aluno['notabanco'];
      $nota_parcial = $aluno['notaparcial'];

      if($prova == 7 && $nota_parcial > 59.999) {

			continue;
	  }

      if($notaprova < 0 )
      {
      		$notaprova = '';
      }
      else {
      		$notaprova = number::numeric2decimal_br($notaprova,1);
      }

      if($st == '#F3F3F3')
      {
         $st = '#E3E3E3';
      }
      else
      {
         $st ='#F3F3F3';
      }
?>
      <tr bgcolor="<?=$st?>"> <td align="center"><?=$ordem?></td>
		<td align="center">
	   <input name="notas[<?=$aluno['ref_pessoa']?>]"  type="text" onkeyup="validate_nota(this);" value="<?=$notaprova?>" size="4" maxlength="4" tabindex="<?=$ordem + 1?>">
	  <input type="hidden" name="matricula[]" value="<?=$aluno['ref_pessoa']?>"></td>
            <td><?=$aluno['ref_pessoa']?></td>
            <td><?=$aluno['nome']?></td>
            </tr>

<?php   
		$ordem++; 
		endforeach; 
?>
 
 </table><br>
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
 </table>
  <input type="submit" name="Submit" value="Gravar notas" tabindex="<?=$ordem + 1?>">
&nbsp;&nbsp;
<a href="#" onclick="javascript:window.close();">Cancelar</a>
</form>
<br />
<br />
<script language="javascript" type="text/javascript">

$('informa_notas').getInputs('text').each(function(input) {
    input.observe('keypress', function(e) {
            var field = Event.element(e);
            if(parseInt(field.tabIndex) > 1) {
              if (e.keyCode==13) {
                new Field.focus(findNextElement(field.tabIndex));
                Event.stop(e);
                return false;
              }
              else {
                if (parseInt(field.value.length) == parseInt(field.getAttribute('maxlength')))
                  new Field.focus(findNextElement(field.tabIndex));
                else
                  return false;
              }
            }
        }
    );
});
</script>
</body>
</html>
<?php } ?>
