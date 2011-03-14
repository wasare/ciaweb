<?php

require_once("../../../app/setup.php");
require_once("../../../core/reports/header.php");
require_once("../../../core/date.php");

$conn    = new connection_factory($param_conn);
$header  = new header($param_conn);
$data    = new date();

$pessoa_id = (int) $_GET['pessoa_id'];

if($pessoa_id == 0)
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Aluno invalido!");</script>');

$sql = '
SELECT DISTINCT
    p.id,
    p.identificacao,
    p.titulo_academico,
    p.nome,
    p.rua,
    p.complemento,
    p.bairro,
    p.cep,
    c1.nome || \' - \'|| c1.ref_estado as ref_cidade,
    p.fone_particular,
    p.fone_profissional,
    p.fone_celular,
    p.fone_recado,
    p.email,
    p.email_alt,
    p.estado_civil,
    p.dt_cadastro,
    p.tipo_pessoa,
    p.obs,
    p.dt_nascimento,
    p.sexo,
    p.nome_fantasia,
    p.cod_inscricao_estadual,
    p.rg_numero,
    c2.nome || \' - \'|| c2.ref_estado as rg_cidade,
    p.rg_data,
    p.ref_filiacao,
    p.ref_cobranca,
    p.ref_assistmed,
    c3.nome || \' - \'|| c3.ref_estado as ref_naturalidade,
    n1.nacionalidade as ref_nacionalidade,
    p.ref_segurado,
    p.cod_cpf_cgc,
    p.titulo_eleitor,
    p.conta_laboratorio,
    p.conta_provedor,
    p.regc_livro,
    p.regc_folha,
    p.regc_local,
    p.regc_nasc_casam,
    p.ano_1g,
    p.cidade_1g,
    p.ref_curso_1g,
    p.escola_1g,
    p.ano_2g,
    p.cidade_2g,
    p.ref_curso_2g,
    p.escola_2g,
    p.graduacao,
    p.cod_passivo,
    p.senha,
    p.fl_dbfolha,
    p.ref_pessoa_folha,
    p.nome2,
    p.fl_cartao,
    p.deficiencia,
    p.cidade,
    p.nacionalidade,
    p.deficiencia_desc,
    p.dt_responsavel,
    p.rg_orgao,
    p.placa_carro,
    p.fl_dados_pessoais,
    p.tipo_sangue,
    f.pai_nome,
    f.mae_nome
FROM
    pessoas p
LEFT OUTER JOIN cidade c1 ON(p.ref_cidade = c1.id)
LEFT OUTER JOIN cidade c2 ON(p.rg_cidade  = c2.id)
LEFT OUTER JOIN cidade c3 ON(p.ref_naturalidade = c3.id)
LEFT OUTER JOIN pais n1 ON(p.ref_nacionalidade = n1.id)
LEFT OUTER JOIN filiacao f ON(p.ref_filiacao = f.id)
WHERE
    p.id = ' . $pessoa_id . ';';

$pessoa = $conn->get_row($sql);

?>
<html>
    <head>
        <title>SA</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="../../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <link href="<?=$BASE_URL?>public/styles/print.css" rel="stylesheet" type="text/css" media="print" />
    </head>
    <body>
        <div style="width: 760px;" align="center">
            <div align="center" style="text-align:center; font-size:12px;">
                <?=$header->get_empresa($PATH_IMAGES, $IEnome)?>
           </div>
            <h2>Informa&ccedil;&otilde;es pessoais</h2>
            <div class="panel">

              <table cellspacing="15" cellpadding="5" border="0">
                <th rowspan="2">
                    <img src="<?=$BASE_URL?>core/pessoa_foto.php?id=<?=$pessoa['id']?>"
                     border="1"
                     alt="<?=$pessoa['nome'];?>"
                     title="<?=$pessoa['nome'];?>"
                     width="120" />
			    </th>
                <tr>
                    <td>
                      <h2><?=$pessoa['nome'];?></h2>
                        <strong>N&uacute;mero de registro:</strong> <?=str_pad($pessoa['id'], 5, "0", STR_PAD_LEFT);?>
                        <br />
                        <strong>Data de registro:</strong> <?=$data->convert_date($pessoa['dt_cadastro']);?>
                    </td>
                </tr>
              </table>
                <strong>Endere&ccedil;o:</strong>
                <?=$pessoa['rua'];?> <?=$pessoa['complemento'];?>
                <br />
                <strong>Bairro:</strong>
                <?=$pessoa['bairro'];?>
                <strong>Cidade:</strong>
                <?=$pessoa['ref_cidade'];?>
                <strong>CEP:</strong>
                <?=$pessoa['cep'];?>
                <br />
                <br />
                <strong>Sexo:</strong>
                <?php
                if($pessoa['sexo'] == 'M'){
                    echo 'Masculino';
                }elseif($pessoa['sexo'] == 'F'){
                    echo 'Feminino';
                }
                ?>
                <strong>Data de nascimento:</strong>
                <?=$data->convert_date($pessoa['dt_nascimento']);?>
                <br />
                <strong>Estado civil:</strong>
                <?php
                if($pessoa['estado_civil'] == 'C'){
                    echo 'Casado';
                }elseif($pessoa['estado_civil'] == 'S'){
                    echo 'Solteiro';
                }elseif($pessoa['estado_civil'] == null or $pessoa['estado_civil'] == ''){
                    echo 'N/D';
                }
                ?>
                <strong>Credo:</strong>
                <?=$pessoa['credo'];?>
                <br />
                <strong>Naturalidade:</strong>
                <?=$pessoa['ref_naturalidade'];?>
                <strong>Nacionalidade:</strong>
                <?=$pessoa['ref_nacionalidade'];?>
                <br />
                <strong>Tipo sanguineo:</strong>
                <?=$pessoa['tipo_sangue'];?>
                <p>
                    <strong>E-mail:</strong>
                    <?=$pessoa['email'];?>
                    <strong>E-mail alternativo:</strong>
                    <?=$pessoa['email_alt'];?>
                </p>
                <h3>Telefone</h3>
                <strong>Particular:</strong>
                <?=$pessoa['fone_particular'];?>
                <br />
                <strong>Profissional:</strong>
                <?=$pessoa['fone_profissional'];?>
                <br />
                <strong>Celular:</strong>
                <?=$pessoa['fone_celular'];?>
                <br />
                <strong>Recado:</strong>
                <?=$pessoa['fone_recado'];?>
                <h3>Documentos</h3>
                <strong>RG:</strong> 
                <?=$pessoa['rg_numero'];?>
                <?=$pessoa['rg_cidade'];?>
                <?=$data->convert_date($pessoa['rg_data']);?>
                <?=$pessoa['rg_orgao'];?>
                <br />
                <strong>CPF:</strong> 
                <?=$pessoa['cod_cpf_cgc'];?>
                <br />
                <strong>Título de eleitor:</strong>
                <?=$pessoa['titulo_eleitor'];?>
                <br />
                <h3>Filia&ccedil;&atilde;o</h3>
                <strong>Pai:</strong> 
                <?=$pessoa['pai_nome'];?>
                <br />
                <strong>M&atilde;e:</strong>
                <?=$pessoa['mae_nome'];?>
                <h3>Observa&ccedil;&atilde;o</h3>
                <?=$pessoa['obs'];?>
            </div>

            <div class="nao_imprime">
              <input type="button" value="Imprimir" onClick="window.print()">
              &nbsp;&nbsp;&nbsp;
              <a href="#" onclick="javascript:window.close();">Fechar</a>
            </div>
            <div style="clear: both;line-height: .3em;">
              <br /><hr color="#868686" size="2">
            </div>
        </div>
      <br />
    </body>
</html>
