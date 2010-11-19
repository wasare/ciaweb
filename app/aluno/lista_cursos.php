<?php

require_once('aluno.conf.php');
include_once('includes/topo.htm');


// Recupera a lista de cursos e periodos atuais para o aluno
$sql_curso = '
SELECT DISTINCT
    a.ref_curso, e.abreviatura, a.ref_periodo, d.descricao
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

$aluno = $user;
$data = date("01/01/2006");

$arr_curso = array();
$arr_curso = $conn->get_all(sprintf($sql_curso,$aluno, $data, $aluno));

$sql_aluno = "
SELECT
    p.nome, rua, complemento, bairro, p.cep,
    c.nome || ' - ' || ref_estado AS cidade,
    fone_particular, fone_profissional, fone_celular,
    fone_recado, email, dt_nascimento
FROM pessoas p, cidade c
WHERE 
    p.id = $aluno AND
    p.ref_cidade = c.id ;";

$dados_aluno = $conn->get_all($sql_aluno);
$arr_aluno = $dados_aluno['0'];

$curso = '';
?>
<h2>Minhas informa&ccedil;&otilde;es</h2>
<strong>Registro: </strong>
<?=str_pad($aluno, 5, "0", STR_PAD_LEFT)?>
&nbsp;&nbsp;
<strong>Nome: </strong>
<?=$arr_aluno['nome']?>
&nbsp;&nbsp;
<strong>Data de nascimento:  </strong>
<?=date::convert_date($arr_aluno['dt_nascimento'])?>
<br />
<strong>Endere&ccedil;o: </strong>
<?=$arr_aluno['rua']?>
&nbsp;&nbsp;
<strong>Bairro: </strong>
<?=$arr_aluno['bairro']?>
&nbsp;&nbsp;
<strong>CEP: </strong>
<?=$arr_aluno['cep']?>
&nbsp;&nbsp;
<strong>Cidade: </strong>
<?=$arr_aluno['cidade']?>
<br />
<strong>Telefone: </strong>
<?=$arr_aluno['fone_particular']?>&nbsp;&nbsp;
<strong>E-mail:  </strong>
<?=$arr_aluno['email']?>
<br />
<font color="red">
    <strong>Aten&ccedil;&atilde;o:</strong>
    Para corrigir ou atualizar seus dados procure o setor de
    registros escolares.
</font>
<h2>Meus cursos</h2>
<table>
    <?php
    
    if(!$arr_curso)
        echo '<font color="grey">
        Você não possue vínculo em nenhum curso ou disciplina
        </font>';

    for($i = 0; $i < count($arr_curso) ; $i++) {
        echo '<tr><td><b>';
        echo $arr_curso[$i]["abreviatura"].'</b><br> |  <a href=lista_notas.php?c='.$arr_curso[$i]["ref_curso"].'&p='.$arr_curso[$i]["ref_periodo"].'>'.$arr_curso[$i]["descricao"].'</a>';

        if ($arr_curso[$i]["ref_curso"] == $arr_curso[$i + 1]["ref_curso"] ) {
            echo ' |   <a href=lista_notas.php?c='.$arr_curso[$i]["ref_curso"].'&p='.$arr_curso[$i + 1]["ref_periodo"].'>'.$arr_curso[$i + 1]["descricao"].'</a> | <br />';
            echo ' <br></td></tr> ';
            $i++;
        }
        else {
            echo ' | <br><br></td></tr>';
        }
    }
    ?>
</table>
<?php include_once('includes/rodape.htm'); ?>
