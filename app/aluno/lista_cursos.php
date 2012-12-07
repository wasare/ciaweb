<?php
require_once('aluno.conf.php');
include_once('includes/topo.htm');
include("includes/menu.html");

// Recupera a lista de cursos e periodos atuais para o aluno
$sql_curso = '
SELECT DISTINCT
    a.ref_curso, e.descricao AS descricao_curso, a.ref_periodo, d.descricao, f.prontuario
FROM 
    matricula a, pessoas b, disciplinas c,
    periodos d, cursos e, contratos f
WHERE
    a.ref_disciplina IN (
        SELECT DISTINCT a.ref_disciplina
        FROM matricula a, disciplinas b
        WHERE
            a.ref_disciplina = b.id AND
            a.ref_motivo_matricula = 0 AND
            a.ref_pessoa = %s
    ) AND
    d.dt_final >= \'%s\' AND
    a.ref_curso = e.id AND
    a.ref_periodo = d.id AND
    a.ref_disciplina = c.id AND
    a.ref_pessoa = b.id AND
    a.ref_pessoa = %s AND
    f.dt_desativacao is null AND
    f.id = a.ref_contrato
;';


$aluno = $aluno_id;
$data = date("01/01/2006");

$arr_curso = array();
$arr_curso = $conn->get_all(sprintf($sql_curso,$aluno, $data, $aluno));

$sql_aluno = "
SELECT
    p.nome, cod_cpf_cgc, rua, complemento, bairro, p.cep,
    c.nome || ' - ' || ref_estado AS cidade,
    fone_particular, fone_profissional, fone_celular,
    fone_recado, email, dt_nascimento
FROM pessoas p LEFT OUTER JOIN cidade c ON (p.ref_cidade = c.id)
WHERE 
    p.id = $aluno;";

$dados_aluno = $conn->get_all($sql_aluno);
$arr_aluno = $dados_aluno['0'];

$curso = '';
?>
<h2>Minhas informa&ccedil;&otilde;es</h2>
<strong>Nome: </strong>
<?=$arr_aluno['nome']?>
<br />
<!--
<strong>CPF: </strong>
</?=$arr_aluno['cod_cpf_cgc']?>
<br />-->
<strong>Data de nascimento:  </strong>
<?=date::convert_date($arr_aluno['dt_nascimento'])?>
<br />
<strong>E-mail:  </strong>
<?=$arr_aluno['email']?>
<br />
<!--
<font color="red">
    <strong>Aten&ccedil;&atilde;o:</strong>
    Para corrigir ou atualizar seus dados procure a secretaria escolar.
</font>-->
<h2>Meus cursos</h2>
<table>
    <?php
    
    if(!$arr_curso)
        echo '<font color="grey">
        Você não possue vínculo em nenhum curso ou disciplina
        </font>';

    for($i = 0; $i < count($arr_curso) ; $i++) {
        echo '<tr><td><b>';
        echo 'Prontu&aacute;rio: </b>'. $arr_curso[$i]['prontuario'] .'<b><br />';
		$prontuario = $arr_curso[$i]['prontuario'];
        echo $arr_curso[$i]['descricao_curso'].'</b><br> |  <a href=lista_notas.php?c='.$arr_curso[$i]["ref_curso"].'&p='.$arr_curso[$i]["ref_periodo"].'>'.$arr_curso[$i]["descricao"].'</a>';

        if ($arr_curso[$i]["ref_curso"] == $arr_curso[$i + 1]["ref_curso"] ) {
            echo ' |   <a href=lista_notas.php?c='.$arr_curso[$i]["ref_curso"].'&p='.$arr_curso[$i + 1]["ref_periodo"].'>'.$arr_curso[$i + 1]["descricao"].'</a> | <br />';
            echo ' <br></td></tr> ';
            $i++;
        }
        else {
            echo ' | <br><br></td></tr>';
        }
    }
$rs_pessoa = $conn->get_one("SELECT ref_pessoa FROM contratos where prontuario='1201522'");
    ?>
</table>
<div id="buttonprincipal" style="position:fixed;top:90px;right:0px;width:200px;text-align:center;text-indent:inherit;background:white;color:black;_position: absolute;>
	<font color="black">
		<strong>Adicione o CIAWEB no facebook:</strong><br />
		<div style="text-align:center"><a href="https://apps.facebook.com/ifspciaweb/?page=index&user=<?php echo $rs_pessoa; ?>"><img src="facebook/includes/curtir-facebook.png" alt="CIAWEB IFSP Caraguatatuba" height="60" width="100" /></a></div> <br />
		<strong>Ou use o CIAWeb no seu smartphone atraves deste atalho por QRCode:</strong>
		<img src="facebook/includes/URI_ciaweb_ifspcaraguata.png" alt="CIAWEB IFSP Caraguatatuba" height="210" width="210" /> 
	</font>
</div>
<?php include_once('includes/rodape.htm'); ?>