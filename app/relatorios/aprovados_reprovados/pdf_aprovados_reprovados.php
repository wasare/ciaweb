<?php

require_once("aprovados_reprovados.php");
ob_start();  

?>
<link href="../../../public/styles/style.css" rel="stylesheet" type="text/css">
<link href="../../../public/styles/relatorio_pdf.css" rel="stylesheet" type="text/css">
<page backtop="10mm" backbottom="10mm" >
	<page_header></page_header>
	<page_footer>	
		<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
  			<tr>
    			<td style="text-align: right; width: 100%">
    				página [[page_cu]]/[[page_nb]]
    			</td>
  			</tr>
		</table>
	</page_footer>
	<table border="0" cellpadding="0" cellspacing="0">
  		<tr>
		<td style="text-align:center; width: 100%;">
			<?php echo $header->get_empresa($PATH_IMAGES, $IEnome);  ?>
		</td>
		</tr>
	</table>
	<h2 style="font-size:16px;">RELAT&Oacute;RIO DE SITUA&Ccedil;&Atilde;O DE APROVA&Ccedil;&Atilde;O DE ALUNO(S)</h2>
	<?php	
  	echo $info;  
  	rs2html($Result1, 'width="90%" cellspacing="0" border="0" class="tabela_relatorio" cellpadding="0"'); 
	?>
	<br /><br />
	<table border="0">
  		<tr>
    		<td width="200" style="text-align:center;">
			<span class="carimbo_box">
           		_______________________________<br />
				<span class="carimbo_nome">
		   			<?php echo $carimbo->get_nome($_POST['carimbo']);?>
				</span>
				<br />
				<span class="carimbo_funcao">
		   			<?php echo $carimbo->get_funcao($_POST['carimbo']);?>
				</span>
			</span>
		</td>
  		</tr>
	</table>
</page>
<?php
$content = ob_get_clean();
require_once('../../../lib/html2pdf/html2pdf.class.php');
$pdf = new HTML2PDF('P','A4','en');
$pdf->WriteHTML($content, isset($_GET['vuehtml']));
$pdf->Output(); 
?>
