<?php

require_once(dirname(__FILE__) .'/../../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/number.php');
require_once($BASE_DIR .'core/situacao_academica.php');

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
                    matricula.ordem_chamada, pessoas.nome, pessoas.id, SUM(d.nota) AS notaparcial, nota_final
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
                     matricula.ordem_chamada, pessoas.nome, pessoas.id, d.id_ref_pessoas, matricula.nota_final
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

$NOTAS = mediaPeriodo($conn->get_one('SELECT periodo_disciplina_ofer('. $diario_id .');'));
$MEDIA_FINAL_APROVACAO = $NOTAS['media_final'];
$NOTA_MAXIMA = $NOTAS['nota_maxima'];

$sql_quantidade_notas = "SELECT quantidade_notas_diario
                                FROM tipos_curso
                                WHERE id = get_tipo_curso(". get_curso($diario_id) .");";
$qtde_notas = $conn->get_one($sql_quantidade_notas);

?>
<html>
<head>
<title><?=$IEnome?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">
<script type="text/javascript" src="<?=$BASE_URL .'lib/prototype.js'?>"> </script>
<script type="text/javascript" language="javascript" src="<?=$BASE_URL .'lib/jquery.min.js'?>"></script>
<script type="text/javascript" language="javascript" src="<?=$BASE_URL .'lib/jquery.floatheader.min.js'?>"></script>
<script type="text/javascript" language="javascript" src="<?=$BASE_URL .'lib/jquery.filter_input.min.js'?>"></script>


<script type="text/javascript">

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
			echo '<font color="blue"> Nota Extra</font> <font color="red" size="2"><br /> Utilize apenas quando houver necessidade de arredondamento ou recupera&ccedil;&atilde;o</font>' ;
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
        <font color="green"><strong>Nota Final ser&aacute; igual (Nota Parcial + Nota Extra)
				</strong></font>
				<br />
		<font color="#330099">
				* A Nota Final do aluno somente será arredondada para cima! <br />
				* A Nota Final n&atilde;o ser&aacute; superior a <strong><?=$NOTA_MAXIMA?> pontos</strong>!<br />
				* Lan&ccedil;ada a "Nota Extra", as notas de 1 a <?=$qtde_notas?> ficarão bloqueadas!<br />
				* Somente eliminando a "Nota Extra" será permitido ajustar as notas de 1 a <?=$qtde_notas?>
		</font>
			</p>
			<input name="valor_avaliacao" type="hidden" id="valor_avaliacao" size="5" maxlength="4" value="<?=$NOTA_MAXIMA?>" />
<?php endif; ?>
<br />
<table cellspacing="0" cellpadding="0" class="papeleta" id="tabela_informa_notas">
  <thead>
  <tr bgcolor="#666666" class="header">

  <td align="center"><font color="#FFFFFF"><strong>Ordem</strong></font></td>
  <td align="center"><font color="#FFFFFF"><strong>Nota <?=($prova) != 7 ? $prova : 'Extra'?></strong></font></td>
      <td><font color="#FFFFFF"><b>&nbsp;Matr&iacute;cula</b></font></td>
  <td><font color="#FFFFFF"><b>&nbsp;Nome</b></font></td>
	<?php if($prova == 7) : ?>
		<td><font color="#FFFFFF"><b>&nbsp;Nota Parcial</b></font></td>
		<td><font color="#FFFFFF"><b>&nbsp;Nota Final</b></font></td>
  <?php endif; ?>
 </tr>
	</thead>
  <tbody>

 <?php
    $st = '';
    $ordem = 1;

   foreach($alunos as $aluno) :

      $notaprova = $aluno['notabanco'];

			$sem_media_parcial = ($aluno['notaparcial'] < $MEDIA_FINAL_APROVACAO && $prova == 7) ? ' color="red"' : '';
			$sem_media_final = ($aluno['nota_final'] < $MEDIA_FINAL_APROVACAO && $prova == 7) ? ' color="red"' : '';

      if($notaprova < 0 )
      {
      		$notaprova = '';
      }
      else {
      		$notaprova = number::numeric2decimal_br($notaprova,1);
      }

      $st = $st == '#F3F3F3' ? '#E3E3E3' : '#F3F3F3';



?>
      <tr bgcolor="<?=$st?>"> <td align="center"><?=$ordem?></td>
		<td align="center">
	   <input name="notas[<?=$aluno['ref_pessoa']?>]"  type="text" onkeyup="validate_nota(this);" value="<?=$notaprova?>" size="4" maxlength="4" tabindex="<?=$ordem + 1?>">
	  <input type="hidden" name="matricula[]" value="<?=$aluno['ref_pessoa']?>"></td>
            <td><?=$aluno['ref_pessoa']?></td>
            <td><?=$aluno['nome']?></td>

						<?php if($prova == 7) : ?>
								<td align="center"><font <?=$sem_media_parcial?>><?=number::numeric2decimal_br($aluno['notaparcial'],1)?></font></td>
								<td align="center"><font <?=$sem_media_final?>><?=number::numeric2decimal_br($aluno['nota_final'],1)?></font></td>
						<?php endif; ?>
            </tr>

<?php
		$ordem++;
		endforeach;
?>
 </tbody>
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
<script type="text/javascript">
	<!--
		jQuery.noConflict();
		jQuery(document).ready(function() {
			jQuery('#tabela_informa_notas').floatHeader({
				fadeIn: 250,
				fadeOut: 250
			});
			jQuery('input[name^="notas"]').filter_input({regex:'[0-9,]', live:true});
		});

	//-->
</script>
</body>
</html>
<?php } ?>

