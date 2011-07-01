<?php 

class number {
	
	/**
	 * Converte do formato numeric para decimal brasileiro
	 * @nNumeric numeric
	 * @return decimal brasileiro
	 */
	function numeric2decimal_br($numeric,$decimals=2) {
		return number_format($numeric,$decimals,',','.');
	}
	
	/**
     * Converte do formato decimal brasileiro para numeric
     * @rValor decimal brasileiro
     * @return numeric
     */
	function decimal_br2numeric($decimal,$decimals=2) {

        if(!is_numeric($decimal)) {

          $val_numeric =  str_replace(',', '+', $decimal);
          $val_numeric =  str_replace('.', '', $val_numeric);
          $val_numeric =  str_replace('+', '.', $val_numeric);

          list($parte_inteira,$parte_decimal) = explode('.',$val_numeric);
        
          $val_numeric = (double) $parte_inteira.'.'.$parte_decimal;
        
          return number_format($val_numeric,$decimals,'.',',');
        }
        else
          return $decimal;

    }

}
?>
