<?php

require_once('aluno.conf.php');
include_once('includes/topo.htm');

$codigo      = $_POST['id_aluno'];
$datainicial = $_POST['data_inicial'];
$datafinal   = $_POST['data_final'];
$saldo       = $_POST['saldo'];
$usuario     = 'Silas';

$SQL = '
SELECT
    to_char(T.datahora_transacao,\'DD/MM/YYYY HH24:MI\') as momento, 
    O.des_operacao,
    O.tipo_operacao,
    T.valor_transacao
FROM
    financeiro.tb_transacao as T, financeiro.tb_operacao as O
WHERE
    T."FKid_usuario"='.$codigo.' AND
    DATE(T.datahora_transacao) BETWEEN \''.$datainicial.'\' AND
    \''.$datafinal.'\' AND
    T."FKcod_operacao" = O.cod_operacao
ORDER
    BY T.datahora_transacao;';

$tbl = $conn->get_all($SQL);

$tabela = '';
$color = '#ffffff';

foreach($tbl as $chave => $linha) {
    $d = substr($linha['momento'],0,19);
    $ano = $d[6] .$d[7] .$d[8] .$d[9];
    $data_hora = $d[0] .$d[1] .$d[2] .$d[3] .$d[4] .$d[10] .$d[11] .$d[12] .$d[13] .$d[14] .$d[15];

    if($ano == $ult_ano) {
        $ano = "";
    }

    $lin='<tr bgcolor="#cccccc">
            <td colspan="3"><div align="center"><strong>Ano '.$ano.'</strong></div></td>
	<tr>';

    if($ano=="") {
        $lin = "";
    }

    $lin .= '<tr bgcolor="'.$color.'">';
    $lin .= '<td>'.$data_hora.'</td>';
    $lin .= '<td>'.$linha['des_operacao'].'</td>';
    $lin .= '<td>'.number_format($linha['valor_transacao'], 2, ',', '').$linha['tipo_operacao'].'</td>';
    $lin .= '</tr>';

    $tabela = $tabela . $lin;

    if ($ano<>"") {
        $ult_ano = $ano;
    }

    if($color == '#ffffff') $color = '#f5f5f5';
    else $color = '#ffffff';
}
?>
<h2>Extrato para simples confer&ecirc;ncia</h2>
Data: <?=date("d/m/Y")?> Hora: <?=date("H:i:s")?><br />
Usu&aacute;rio: <?=$usuario?><br />
Saldo atual: R$ <?=$saldo?>
<p>
    Per&iacute;odo:
    <?=str_replace("-","/",$datainicial)?> at&eacute; <?=str_replace("-","/",$datafinal)?>
</p>
<table width="300" border="0" cellspacing="0" cellpadding="0">
    <tr bgcolor="#000000">
        <th><font color="#ffffff">Data/hora</font></th>
        <th><font color="#ffffff">Opera&ccedil;&atilde;o</font></th>
        <th><font color="#ffffff">Valor</font></th>
    </tr>
    <tr>
    <?=$tabela?>
</table>

<?php include_once('includes/rodape.htm'); ?>
