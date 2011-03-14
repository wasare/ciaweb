<?php

require_once(dirname(__FILE__) .'/../../setup.php');
require_once($BASE_DIR .'core/web_diario.php');
require_once($BASE_DIR .'core/reports/header.php');
require_once($BASE_DIR .'core/date.php');

$conn    = new connection_factory($param_conn);
$header  = new header($param_conn);
$data    = new date();

$aluno_id = (int) $_GET['aluno'];
$curso_id = (int) $_GET['curso'];

if($aluno_id == 0 || $curso_id == 0)
    exit('<script language="javascript" type="text/javascript">window.alert("ERRO! Aluno invalido!");</script>');


//  VERIFICA O DIREITO DE ACESSO A FICHA COMO PROFESSOR OU COORDENADOR
if(isset($_SESSION['sa_modulo']) && $_SESSION['sa_modulo'] == 'web_diario_login') {
  if(!acessa_ficha_aluno($aluno_id,$sa_ref_pessoa,$curso_id)) {
    exit('<script language="javascript" type="text/javascript">
            alert(\'Você não tem direito de acesso a estas informações!\');
            window.close();</script>');
  }
  // ^ VERIFICA O DIREITO DE ACESSO A FICHA COMO PROFESSOR OU COORDENADOR ^ //
}

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
    p.id = ' . $aluno_id . ';';

$aluno = $conn->get_row($sql);

if(count($aluno) == 0)
    exit('<script language="javascript" type="text/javascript">window.alert("Aluno inexistente!"); window.close();</script>');

?>
<html>
    <head>
        <title><?=$IEnome?> - web di&aacute;rio</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="<?=$BASE_URL .'public/styles/formularios.css'?>" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div style="width: 760px;" align="center">
            <div align="center" style="text-align:center; font-size:12px;">
                <?=$header->get_empresa($PATH_IMAGES, $IEnome)?>
                <br /><br />
            </div>
            <h2>Informa&ccedil;&otilde;es pessoais</h2>
            <div class="panel">
              <table cellspacing="15" cellpadding="5" border="0">
                <th rowspan="2">
                    <img src="<?=$BASE_URL?>core/pessoa_foto.php?id=<?=$aluno['id']?>&curso=<?=$curso_id?>"
                     border="1"
                     alt="<?=$aluno['nome'];?>"
                     title="<?=$aluno['nome'];?>"
                     width="120" />
			    </th>
                <tr>
                    <td>
                      <h2><?=$aluno['nome'];?></h2>
                        <strong>N&uacute;mero de registro:</strong> <?=str_pad($aluno['id'], 5, "0", STR_PAD_LEFT);?>
                        <br />
                        <strong>Data de registro:</strong> <?=$data->convert_date($aluno['dt_cadastro']);?>
                    </td>
                </tr>
              </table>

                <strong>Data de nascimento:</strong>
                <?=$data->convert_date($aluno['dt_nascimento']);?>
                &nbsp;&nbsp;&nbsp;<strong>Sexo:</strong>
                <?php
                if($aluno['sexo'] == 'M'){
                    echo 'Masculino';
                }elseif($aluno['sexo'] == 'F'){
                    echo 'Feminino';
                }
                ?>               
                &nbsp;&nbsp;&nbsp;<strong>Estado civil:</strong>
                <?php
                if($aluno['estado_civil'] == 'C'){
                    echo 'Casado';
                }elseif($aluno['estado_civil'] == 'S'){
                    echo 'Solteiro';
                }elseif($aluno['estado_civil'] == null or $aluno['estado_civil'] == ''){
                    echo 'N/D';
                }
                ?>
                <br />
                <strong>Naturalidade:</strong>
                <?=$aluno['ref_naturalidade'];?>
                &nbsp;&nbsp;&nbsp;<strong>Nacionalidade:</strong>
                <?=$aluno['ref_nacionalidade'];?>
                &nbsp;&nbsp;&nbsp;<strong>Tipo sanguineo:</strong>
                <?=$aluno['tipo_sangue'];?>
                <br />
                <strong>M&atilde;e:</strong>
                <?=$aluno['mae_nome'];?>
                &nbsp;&nbsp;&nbsp;<strong>Pai:</strong>
                <?=$aluno['pai_nome'];?>
                <br />
                <br />
                <strong>Endere&ccedil;o:</strong>
                <?=$aluno['rua'];?> <?=$aluno['complemento'];?>
                 &nbsp;&nbsp;&nbsp;<strong>Bairro:</strong>
                <?=$aluno['bairro'];?>
                 <br />
                <strong>Cidade:</strong>
                <?=$aluno['ref_cidade'];?>
                &nbsp;&nbsp;&nbsp;<strong>CEP:</strong>
                <?=$aluno['cep'];?>
                <br /> 
                <strong>Telefone particular:</strong>
                <?=$aluno['fone_particular'];?>
                &nbsp;&nbsp;&nbsp;<strong>Telefone celular:</strong>
                <?=$aluno['fone_celular'];?>
                <br />
                <strong>E-mail:</strong>
                <?=$aluno['email'];?>
                 <br />
                <h3>Observa&ccedil;&atilde;o</h3>
                <?=$aluno['obs'];?>
            </div>
        </div>
      <br />
      <div align="left">
        <a href="#" onclick="javascript:window.close();">Fechar</a>
      </div>
      <br />
    </body>
</html>
