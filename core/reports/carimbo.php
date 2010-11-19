<?php
/**
 * CLASSE CARIMBO PARA ASSINATURA
 * 
 */
class carimbo{

    private $param_conn;

    function __construct($arr){
        $this->param_conn = $arr;
    }

    function listar(){
        $conn = new connection_factory($this->param_conn);
        $sql = "
            SELECT
               id, nome, texto, ref_setor
            FROM
               carimbos
            ORDER BY 1 DESC;";

        $RsCarimbo = $conn->Execute($sql);

		$resp = '<select size="5" name="carimbo">
               	<option value="" selected> Sem carimbo </option>';

        while(!$RsCarimbo->EOF){
            $resp .= '<option value="'.$RsCarimbo->fields[0].'"> ';
            $resp .= $RsCarimbo->fields[1];
            $resp .= ' </option>';
            $RsCarimbo->MoveNext();
        }
        $resp .= '</select>';
        $conn->Close();

        return $resp;

    }


    function get_nome($id){
        
        if($id == null){
            return null;
		}else{
            $conn = new connection_factory($this->param_conn);
            $sqlCarimbo = "
                SELECT
                    id, nome, texto, ref_setor
                FROM
                    carimbos
                WHERE	id = $id;";

            $RsCarimbo = $conn->Execute($sqlCarimbo);
            $resp = $RsCarimbo->fields[1];
            $conn->Close();

            return $resp;
		}
    }

    /**
     * Funcao que retorna a funcao do carimbo de acordo com o codigo
     * @param codigo do carimbo
     * @return string
     */
    function get_funcao($id){

		if($id == null){
			return null;
		}else{
            $conn = new connection_factory($this->param_conn);
            $sqlCarimbo = "
            SELECT
                id, nome, texto, ref_setor
            FROM
                carimbos
            WHERE	id = $id;";

            $RsCarimbo = $conn->Execute($sqlCarimbo);
            $resp = $RsCarimbo->fields[2];
            $conn->Close();

            return $resp;
        }
    }
}

?>
