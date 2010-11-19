<?php

class sql2excel extends GeraExcel {

	function sql2excel($tit, $sql, $Conexao){
			
		$this->GeraExcel();
		for ($i=0; $i<count($tit); $i++)
		{
			$this->MontaConteudo(0,$i,$tit[$i]);
		}
		$qr=$Conexao->execute($sql);
		$j=1;
		while ($reg=$qr->fetchrow()){
			for ($i=0; $i<count($reg); $i++)
			{
				$this->MontaConteudo($j,$i,$reg[$i]);
			}
			$j++;
		}
		$this->GeraArquivo();
	}
}


class  GeraExcel {

	// define parametros(init)
	function  GeraExcel(){
		$this->armazena_dados   = "";
		$this->ExcelStart();
	}

	// Monta cabecario do arquivo(tipo xls)
	function ExcelStart(){
		$this->armazena_dados = pack( "vvvvvv", 0x809, 0x08, 0x00,0x10, 0x0, 0x0 );
	}

	// Fim do arquivo excel
	function FechaArquivo(){
		$this->armazena_dados .= pack( "vv", 0x0A, 0x00);
	}

	// monta conteudo
	function MontaConteudo( $excel_linha, $excel_coluna, $value){
		$tamanho = strlen( $value );
		$this->armazena_dados .= pack( "v*", 0x0204, 8 + $tamanho, $excel_linha, $excel_coluna, 0x00, $tamanho );
		$this->armazena_dados .= $value;
	}

	// Gera arquivo(xls)
	function GeraArquivo(){
		$this->FechaArquivo();
		header("Content-type: application/msexcel");
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header("Content-disposition: inline; filename=excel.xls");
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Pragma: public");
		print  ( $this->armazena_dados);
	}

}

?>