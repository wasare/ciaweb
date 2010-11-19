<?php

require_once("../../app/setup.php");
require_once(dirname(__FILE__).'/../../core/login/acl.php');

$conn = new connection_factory($param_conn);

// Definindo as permissoes do usuario quanto ao arquivo
$acl = new acl();
if(!$acl->has_access(__FILE__, $conn)) {
    exit ('Você não tem permissão para acessar este formulário!');
}

$sql = '
SELECT
    u.id,
    u.nome,
    u.ref_pessoa,
    u.senha,
    u.ativado,
    s.nome_setor,
    c.nome_campus,
    p.id || \' - \' || p.nome as nome_pessoa
FROM
    usuario u, setor s, pessoas p, campus c
WHERE
    s.id = u.ref_setor AND
    u.ref_pessoa = p.id AND
    c.id = u.ref_campus
ORDER BY lower(u.nome)';

$RsNome = $conn->Execute(iconv("utf-8","iso-8859-1",$sql));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>SA</title>
        <link href="../../public/styles/formularios.css" rel="stylesheet" type="text/css" />
        <script language="javascript" src="../../lib/prototype.js" type="text/js"></script>
        <script language="javascript" src="index.js" type="text/js"></script>
    </head>

    <body onLoad="pesquisar();">
        <h2>Controle de usu&aacute;rios</h2>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="60">
                    <div align="center">
                        <a href="cadastrar.php" class="bar_menu_texto">
                            <img src="../../public/images/icons/new.png" alt="Novo" width="20" height="20" />
                            <br />Novo
                        </a>
                    </div>
                </td>
                <td width="60">
                    <div align="center">
                        <a href="javascript:history.back();" class="bar_menu_texto">
                            <img src="../../public/images/icons/back.png" alt="Voltar" width="20" height="20" />
                            <br />
                            Voltar
                        </a>
                    </div>
                </td>
            </tr>
        </table>

        <table width="80%" border="0">
            <tr  style="font-weight:bold; color: white; background-color: #666666;">
                <th>Usu&aacute;rio</th>
                <th>Campus</td>
                <th>Setor</td>
                <th>Permiss&otilde;es</th>
                <th align="center">Op&ccedil;&otilde;es</th>
            </tr>
            <?php

            while(!$RsNome->EOF) {

                if($RsNome->fields['ativado'] == 't') {
                    $cor_linha = '#F3F3F3';
                    $situacao = ' ';

                } else {
                    $cor_linha = '#DDDDDD';
                    $situacao = ' - <font color="#999999">Usu&aacute;rio desativado</font>';
                }
                ?>
            <tr bgcolor="<?=$cor_linha?>">
                <td align="left">
                    <a href="../relatorios/pessoas/lista_pessoa.php?pessoa_id=<?=$RsNome->fields[2]?>"
                       target="_blank"
                       title="<?=$RsNome->fields['nome_pessoa']?>" >
                               <?=$RsNome->fields[1]?>
                    </a>
                        <?=$situacao?>
                </td>
                <td align="left"><?=$RsNome->fields['nome_campus']?></td>
                <td align="left"><?=$RsNome->fields['nome_setor']?></td>
                <td align="left">
                        <?php

                        //Listagem das permissoes do usuario

                        $sqlPapel = "SELECT nome FROM papel, usuario_papel
							WHERE ref_usuario = ".$RsNome->fields[0].
                            " AND ref_papel = papel_id";

                        $RsPapel = $conn->Execute($sqlPapel);

                        if($RsPapel->RecordCount() == 0) echo '<font color=grey>Nenhuma</font>';

                        while(!$RsPapel->EOF) {
                            echo $RsPapel->fields[0] . '<br />';
                            $RsPapel->MoveNext();
                        }

                        ?>
                </td>
                <td align="center">
                    <a href="alterar.php?id_usuario=<?=$RsNome->fields[0]?>">
                        <img src="../../public/images/icons/edit.png" alt="Alterar Usu&aacute;rio" title="Alterar Usu&aacute;rio" />
                    </a>&nbsp;&nbsp;
                    <a href="excluir_action.php?id_usuario=<?=$RsNome->fields[0]?>" onclick="return confirm('Deseja realmente excluir?')">
                        <img src="../../public/images/icons/delete.png" alt="Excluir" />
                    </a>		
                </td>
            </tr>
                <?php
                $RsNome->MoveNext();
            }
            ?>
        </table>
    </body>
</html>
