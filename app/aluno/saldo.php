<?php

require_once('aluno.conf.php');
include_once('includes/topo.htm');

$aluno = $user;

function getNumeric2Real($nNumeric) {

    setlocale(LC_CTYPE,"pt_BR");

    $Real = explode('.',$nNumeric);

    $Inteiro = $Real[0];
    $Centavo = substr($Real[1], 0, 2);

    if ( strlen($Centavo) < 2 ) {
        $Centavo = str_pad($Centavo, 2, "0", STR_PAD_RIGHT);
    }

    $InteiroComMilhar = number_format($Inteiro, 0, '.', '.');
    $Real = $InteiroComMilhar.','.$Centavo;

    return $Real;
}

$SaldoCA = $conn->get_one('SELECT saldo_usuario FROM financeiro.tb_saldo where "FKid_usuario"= '.$aluno.';');

if ($SaldoCA < 0 ) $cfont = "red";
else $cfont = "green";

$saldo = getNumeric2Real($SaldoCA);
?>

<h2>Saldo Conta Acad&ecirc;mica</h2>
Saldo&nbsp;&nbsp;R$&nbsp;
<font color="<?=$cfont?>"><?=$saldo?></font>
<div align="left">
    <form id="form1" name="form1" method="post" action="extrato.php">
        <input type="hidden" name="id_aluno" id="id_aluno" value="<?=$aluno?>"/>
        <input type="hidden" name="saldo" id="saldo" value="<?=$saldo?>"/>
        <p>
            <strong>Consulta extrato por per&iacute;odo</strong>
        </p>
        Data Inicial:<br />
        <input type="text" name="data_inicial" id="data_inicial" value="01-01-2000" />
        <i>Exemplo: dd-mm-aaaa</i><br />
        Data Final:<br />
        <input type="text" name="data_final" id="data_final" value="01-01-2010" />
        <i>Exemplo: dd-mm-aaaa</i>
        <p>
            <input type="submit" value="Consultar" />
        </p>
    </form>
</div>
<?php include_once('includes/rodape.htm'); ?>