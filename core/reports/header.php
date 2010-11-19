<?php
/**
 * Cabecalho de relatorios
 *
 */
class header{

    private $param_conn;

    function __construct($arr){
        $this->param_conn = $arr;
    }

    function get_empresa($path_images){

		$conn = new connection_factory($this->param_conn);

        $empresa = $conn->get_row("SELECT razao_social, sigla FROM configuracao_empresa WHERE id = 1");

        $cabecalho = '<div width="50" valign="middle" style="float: left; padding-right: 2em;">
                        <img src="'. $path_images .'logo_ifmg.png" alt="Instituto Federal de Minas Gerais" title="Instituto Federal de Minas Gerais" border="0"/>
                      </div>
                      <div width="50" valign="top"  style="float: left;">
                        <img src="'. $path_images .'sa_icon.png" border="0"/>
                      </div>
                      <div width="230" align="middle"  style="float: left;">
                        <span style="font-weight: bold;font-size: 1.6em; padding-top: 3em;">Sistema Acad&ecirc;mico</span>
                        <h3>Campus '. $_SESSION['sa_campus'] .'</h3>
                      </div>
                      <div width="230" valign="middle"  style="clear: both;line-height: .3em;">                        
                        <br /><hr color="#868686" size="2">
                      </div>';

		return  $cabecalho;
    }
}

?>