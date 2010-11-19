<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');

$conn = new connection_factory($param_conn);

$campo_aluno = $_GET['aluno'];

// validação simples
if (empty($campo_aluno))
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Aluno ou matrícula inválida! '. $campo_aluno .'");window.close();</script>');

?>

<html>
<head>
<title><?=$IEnome?> - web di&aacute;rio</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<?=$BASE_URL .'public/styles/web_diario.css'?>" type="text/css">

<script type="text/javascript" src="<?=$BASE_URL .'lib/prototype.js'?>"> </script>
<script type="text/javascript" src="<?=$BASE_URL .'app/web_diario/web_diario.js'?>"> </script>
<script type="text/javascript" src="<?=$BASE_URL .'lib/event.simulate.js'?>"> </script>

</head>

<body>

<div align="left">

  <h3>Consultar alunos</h3>

 <br />
<form name="pesquisa_aluno" id="pesquisa_aluno" method="post" action="">
  <strong> Matr&iacute;cula ou nome do aluno:</strong> &nbsp;<input name="campo_aluno" id="campo_aluno" type="text" maxlength="30" size="15" />
   <input type="button" name="envia_pesquisa" id="envia_pesquisa_aluno" value="Consultar" onclick="enviar_diario('pesquisa_aluno',null,null,'<?=$BASE_URL?>','<?=$IEnome?>');" />
    &nbsp;&nbsp;&nbsp;
    <a href="#" onclick="javascript:window.close();">Fechar</a>
 </form>

<?php

// filtro se coordenador
if (is_array($_SESSION['web_diario_cursos_coordenacao'])) {

  $sql_coordena = 'SELECT DISTINCT ref_curso
                    FROM coordenador
                    WHERE
                    ref_professor = '. $sa_ref_pessoa;
}
// ^ filtro se for coordenador ^

// filtro se professor
if (isset($_SESSION['web_diario_periodo_id'])) {
  
  $sql_professor = 'SELECT DISTINCT
                                    o.id
                              FROM
                                    disciplinas_ofer o, disciplinas_ofer_prof dp
                              WHERE
                                    dp.ref_professor = '. $sa_ref_pessoa .' AND
                                    o.id = dp.ref_disciplina_ofer ';
}
// ^ filtro se for professor ^

$sql = 'SELECT  DISTINCT
                  a.nome, a.id, b.ref_curso, d.abreviatura, c.id as contrato, c.turma
            FROM
                  pessoas a, matricula b, contratos c, cursos d
          WHERE
                a.id IN (
                            SELECT DISTINCT
                                    a.ref_pessoa
                               FROM  matricula a ';
$sql .= ' WHERE ';

// aplica filtros
$sql .= isset($sql_coordena) ? ' a.ref_curso IN ('. $sql_coordena .') ' :  '';

$sql .= (isset($sql_coordena) && isset($sql_professor)) ? 'OR' : '';

$sql .= isset($sql_professor) ? ' a.ref_disciplina_ofer IN ('. $sql_professor .') ' : '';

$sql .= '                   ORDER BY a.ref_pessoa
                        )  AND
                a.id = b.ref_pessoa AND
                c.ref_pessoa = a.id AND
                c.id = b.ref_contrato AND
                b.ref_curso = d.id AND
                c.ref_curso = d.id AND
                c.dt_desativacao IS NULL ';

	if(is_numeric($campo_aluno)) {
		$sql .= " AND a.ra_cnec = '$campo_aluno' ";
    }
	if(!is_numeric($campo_aluno)) {
    	$sql .= " AND lower(to_ascii(a.nome)) ";
        $sql .= " SIMILAR TO lower(to_ascii('$campo_aluno%')) ";
	}

    $qtde_alunos = $conn->get_one('SELECT COUNT(*)  FROM (' .$sql .') AS T1 ;');
    
	$sql .= " ORDER BY a.nome LIMIT 15 OFFSET -1;";

	$alunos = $conn->get_all($sql);

   if (count($alunos) > 0 ) :

?>
    <?php if ($qtde_alunos > 15) : ?>
       <br />
       <span class="obrigatorio" style="background: yellow;">Exibindo somente os 15 primeiros resultados, seja mais específico para visualizar os demais.</span>
       <br />
    <?php endif; ?>
    <br />
    <table  border="0" class="papeleta">
      <tr bgcolor="#666666">
        <th><b><font color="#FFFFFF">Matr&iacute;cula</font></b></th>
    	<th><b><font color="#FFFFFF">Nome</font></b></th>
		<th><b><font color="#FFFFFF">Curso</font></b></th>
        <th><b><font color="#FFFFFF">Turma</font></b></th>
        <th><b><font color="#FFFFFF">Op&ccedil;&otilde;es</font></b></th>
      </tr>
<?php
      foreach ($alunos as $aluno) :
        if (acessa_ficha_aluno($aluno['id'],$sa_ref_pessoa,$aluno['ref_curso'])) :
?>
        <tr>
          <td align="center"><?= str_pad($aluno['id'], 5, "0", STR_PAD_LEFT)?></td>
          <td><?=$aluno['nome']?></td>
          <td><?=$aluno['abreviatura']?></td>
          <td align="center"><?=$aluno['turma']?></td>
          <td align="center">
            <a  href="#" onclick="abrir('<?=$IEnome?> - web diário','<?=$BASE_URL .'app/relatorios/ficha_academica/lista_ficha_academica.php?aluno='. $aluno['id'] .'&contrato='. $aluno['contrato'] .'&cs='. $aluno['ref_curso']?>');">
              <img src="<?=$BASE_URL .'public/images/icons/report.png'?>" width="20" height="20" border="0" title="Ficha acad&ecirc;mica" alt="Ficha acad&ecirc;mica" /></a>
              &nbsp;&nbsp;
              <a  href="#" onclick="abrir('<?=$IEnome?> - web diário','<?=$BASE_URL .'app/web_diario/consultas/cadastro_aluno.php?aluno='. $aluno['id'] .'&curso='. $aluno['ref_curso']?>');">
              <img src="<?=$BASE_URL .'public/images/icons/pessoa.png'?>" width="20" height="20" border="0" title="Informa&ccedil;&otilde;es pessoais" alt="Informa&ccedil;&otilde;es pessoais" /></a>
          </td>
        </tr>
<?php
        endif;
      endforeach;
?>
    </table>
<?php
   else:
     echo '<br /><span class="obrigatorio" style="background: yellow;">Nenhum aluno encontrado para o crit&eacute;rio informado!</span>';
  endif;
	
?>
<br />
<br />
<br />
</div>

<script language="javascript" type="text/javascript">
    $('campo_aluno').observe('keydown', function (e) {
        if ( e.keyCode == 13 ) {
            $('envia_pesquisa_aluno').simulate('click');
            e.stop();
        }
    });
</script>

</body>
</html>
