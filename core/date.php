<?php 

class date {
	
	/**
	 * Inverte padrao da data de 2009-11-03 para 03/11/2009 e o inverso
	 * @param data
	 * @return data
	 */
	function convert_date($data){
		return implode(!strstr($data, '/') ? "/" : "-", array_reverse(explode(!strstr($data, '/') ? "-" : "/", $data)));
	}
	
	/**
	 * Retorna mes por extenso
	 * @param numero do mes
	 * @return string com o mes
	 */
	function mes($mes_num){
		switch ($mes_num) {
			case 1:
				$mes = "janeiro";
				break;
			case 2:
				$mes = "fevereiro";
				break;
			case 3:
				$mes = "março";
				break;
			case 4:
				$mes = "abril";
				break;
			case 5:
				$mes = "maio";
				break;
			case 6:
				$mes = "junho";
				break;
			case 7:
				$mes = "julho";
				break;
			case 8:
				$mes = "agosto";
				break;
			case 9:
				$mes = "setembro";
				break;
			case 10:
				$mes = "outubro";
				break;
			case 11:
				$mes = "novembro";
				break;
			case 12:
				$mes = "dezembro";
				break;
		}
		
		return $mes;
	}

}
?>