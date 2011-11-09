<?php

require_once(dirname(__FILE__) .'/../../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/date.php');
require_once($BASE_DIR .'core/number.php');

$conn = new connection_factory($param_conn);

$diario_id = (int) $_GET['diario_id'];
$chamada_id = (int) $_GET['chamada'];
$num_aulas = $flag = (int) $_GET['flag'];

//  VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR
if(!acessa_diario($diario_id,$sa_ref_pessoa)) {

    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
}
// ^ VERIFICA O DIREITO DE ACESSO AO DIARIO COMO PROFESSOR OU COORDENADOR ^ //

if (is_fechado($diario_id)){

    echo '<script language="javascript" type="text/javascript">';
    echo 'alert("ERRO! Este diário está fechado e não pode ser alterado!");';
    echo 'window.close();';
    echo '</script>';
    exit;
}

$aul_atipo = '';
for($i = 1; $i <= $num_aulas; $i++) { $aula_tipo .= "$i"; }

$flag = $num_aulas;
/*
$data_bd = $selectdia . '/' . $selectmes . '/'.$selectano;
$data_cons = $selectdia . '/' . $selectmes . '/'.$selectano;
$data_ok = $selectdia . "/" . $selectmes . '/'.$selectano;
$data_chamada =  $selectdia . "/" . $selectmes . '/'.$selectano;
$datadehoje = date ("d/m/Y");
*/

if($flag_falta === 'F') {
	require_once($BASE_DIR .'app/web_diario/professor/chamada/registra_faltas.php');
	exit;
}
	
$sql_chamada = "SELECT DISTINCT
              dia
         FROM
          diario_seq_faltas d
        WHERE
          d.id = $chamada_id;";

$data_chamada = $conn->get_one($sql_chamada);


$sql_falta = " SELECT
  a.ra_cnec, count(a.ra_cnec) as faltas
  FROM
    diario_chamadas a
	WHERE
	    (a.ref_disciplina_ofer = $diario_id) AND
		  (a.data_chamada = '$data_chamada')
		  GROUP BY ra_cnec;";

$faltas_chamada = $conn->get_all($sql_falta);

$sql1 ="SELECT DISTINCT
  p.nome,
  p.id,
  p.ra_cnec,
  m.num_faltas
FROM
  matricula m
  INNER JOIN pessoas p ON (m.ref_pessoa = p.id)
WHERE
  (m.ref_disciplina_ofer = $diario_id) AND
  (m.dt_cancelamento is null)
ORDER BY
  p.nome; ";


$alunos = $conn->get_all($sql1);

$sql_carga_horaria = "SELECT get_carga_horaria_realizada($diario_id);";
$carga_horaria_realizada = $conn->get_one($sql_carga_horaria);

?>


<html>
<head>
<title><?=$IEnome?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">

<script language="javascript" type="text/javascript">
<!--
function validate(field) {
	var valid = "0" + "<?=$aula_tipo?>"
	var ok = "yes";

	var temp;
	for (var i=0; i<field.value.length; i++) {
		temp = "" + field.value.substring(i, i+1);
		if (valid.indexOf(temp) == "-1") ok = "no";
	}
	
	if (ok == "no") {
		alert("Você não pode lançar " + field.value + " faltas para uma chamada de " + <?=$num_aulas?> + " aulas !");
		//field.focus();
		field.value = "";
		field.focus();
   }
}

// Functions de mudanca automatica de foco
function autoTab(input,len, e) {
         var isNN = (navigator.appName.indexOf("Netscape")!=-1);

         var keyCode = (isNN) ? e.which : e.keyCode;
         var filter = (isNN) ? [0,8,9] : [0,8,9,16,17,18,37,38,39,40,46];
         if(input.value.length >= len && !containsElement(filter,keyCode)) {
                 input.value = input.value.slice(0, len);
                 input.form[(getIndex(input)+1) % input.form.length].focus();
         }

        function containsElement(arr, ele) {
               var found = false, index = 0;
               while(!found && index < arr.length)
               if(arr[index] == ele)
                  found = true;
               else
               index++;
               return found;
        }

		        function getIndex(input) {
                var index = -1, i = 0, found = false;
                while (i < input.form.length && index == -1)
                if (input.form[i] == input)index = i;
                else i++;
                return index;
        }

        return true;

        /* Usando no formulario
        <input onKeyUp="return autoTab(this, 3, event);" size="4" maxlength="3">
        */
}

//-->
</script>

</head>

<body>
<br />
<div align="left" class="titulo1">
  Lan&ccedil;amento de Faltas - Altera&ccedil;&atilde;o
</div>
<br />
<?=papeleta_header($diario_id)?>
<br />

<form name="altera_faltas" id="altera_faltas" method="post" action="<?=$BASE_URL .'app/web_diario/professor/chamada/registra_alteracao_faltas.php'?>">
	<input type="hidden" name="diario_id" id="diario_id" value="<?=$diario_id?>">
	<input type="hidden" name="num_aulas" id="num_aulas" value="<?=$num_aulas?>">
	<input type="hidden" name="aula_tipo" id="aula_tipo" value="<?=$aula_tipo?>">
    <input type="hidden" name="data_chamada" id="data_chamada" value="<?=$data_chamada?>">

  <h3>
    <?php
        $dt_chamada = date("w", strtotime($data_chamada));
    ?>
    Data da Chamada:&nbsp;<font color="blue"><?=date::convert_date($data_chamada)?></font>&nbsp;->&nbsp;
    <font color="brown"><?=date::dia_semana($dt_chamada)?></font>
    <br />Quantidade de Aulas:&nbsp;<font color="brown"><?=$num_aulas?></font>
  </h3>

<div align="justify">
<font color="#0000CC" size="1,5" face="Verdana, Arial, Helvetica, sans-serif">Informe ou altere a quantidade de faltas para cada aluno:</font>
</div>
<br />
<table cellspacing="0" cellpadding="0" class="papeleta">
  <tr bgcolor="#666666">
	<td align="center"><b>Ordem</b></td>
    <td align="center"><strong>Faltas</strong></td>
    <td align="center"><b>&nbsp;Matr&iacute;cula</b></td>
    <td><b>&nbsp;Nome do aluno</b></td>
    <td><b>&nbsp;Faltas</b></td>
		<td><b>&nbsp;% Faltas atual</b></td>
  </tr>
  
<?php 

$st = '';
$ordem = 1;
	
foreach($alunos as $aluno) :
	$aluno_id = $aluno['ra_cnec'];
	$nome_aluno = $aluno['nome'];
	$total_faltas = (int) $aluno['num_faltas'];

	$faltas = '';

	if(is_array($faltas_chamada) && count($faltas_chamada) > 0) {
      
        reset($faltas_chamada);

        foreach($faltas_chamada as $aluno_chamada) {
          if($aluno_chamada['ra_cnec'] == $aluno_id) {
            $faltas = (int) $aluno_chamada['faltas'];
            break;
          }
        }
  }

  if($st == '#F3F3F3') $st = '#E3E3E3'; else $st ='#F3F3F3';
      
  $percentual_faltas_atual = round($total_faltas / $carga_horaria_realizada * 100, 2);
		
	$destaque_faltas = ($percentual_faltas_atual > 25) ? 'red' : 'black';
      
?>

  <tr bgcolor="<?=$st?>">
    <td align="center"><?=$ordem?></td>
    <td align="center">
      <input type="text" name="faltas[<?=$aluno_id?>]" value="<?=$faltas?>" maxlength="1" size="1" onblur="validate(this);" onkeyup="return autoTab(this, 1, event);"/>
    </td>
    <td align="center"><?=$aluno_id?></td>
    <td><?=$nome_aluno?></td>
    <td align="center">
		  <font color="<?=$destaque_faltas?>"><?=$total_faltas?></font>
		</td>		
		<td align="center">
			<font color="<?=$destaque_faltas?>"><?=number::numeric2decimal_br($percentual_faltas_atual,2);?>
		  </font>
	  </td>
  </tr>
<?php

    $ordem++;
  endforeach;
?>
</table>
<br /><br />

  <input type="submit" name="Submit" value="Salvar faltas" />
  &nbsp;&nbsp;&nbsp;
  <a href="#" onclick="javascript:window.history.back(1);">Voltar</a>
  &nbsp;&nbsp;&nbsp;
  <a href="#" onclick="javascript:window.close();">Cancelar</a>
  

  
  <input type="hidden" name="faltas_ok" value="F" />
	  
</form>
<br />
<br />
      
</body>
</html>
