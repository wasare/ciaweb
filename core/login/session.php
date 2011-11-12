<?php

/**
 * Controle de sessao
 * @author wanderson
 *
 */
require_once(dirname(__FILE__).'/../../lib/adodb5/session/adodb-cryptsession2.php');

class session {

    protected $session_life_time = 900; // 900 segundos = 15 minutos
    protected $session_table;
    protected $persist = TRUE;
    protected $debug = FALSE;

    function __construct($conn_options, $persist = TRUE, $debug = FALSE, $sess_table = 'sessao') {

        $ret = FALSE;

        list($host, $database, $user, $password, $port) = array_values($conn_options);

		$host.=':'.$port;
        $options['table'] = $sess_table;
        $this->session_table = $sess_table;

        ADOdb_Session::config('postgres',$host,$user,$password,$database,$options);
        ADODB_Session::open(false,false,$connectMode = $persist);

        if(isset($GLOBALS['ADODB_SESS_CONN']) && is_object($GLOBALS['ADODB_SESS_CONN'])) {
            ADOdb_session::Persist($connectMode = $persist);
            $GLOBALS['ADODB_SESS_CONN']->debug = $debug;
            // limpa outras sessoes expiradas e inativas por mais de 15 minutos (padr�o)
            $this->clear_expired_sessions();
            @session_start();
        }
    }

    /*
     * Gera um novo ID para sessão
    */
    public static function refresh() {
        $random = rand(1,2);
        if (($random % 2) == 0) adodb_session_regenerate_id();
    }

    public static function destroy() {
        unset($_SESSION);
        session_destroy();
    }

    // forca eliminacao da sessao do usuario no banco
    // TODO: redirecionar o usuario para uma pagina com aviso de sessao expirada
    public static function clear_session($expireref, $sesskey) {

        if(is_object($GLOBALS['ADODB_SESS_CONN'])) {
            $GLOBALS['ADODB_SESS_CONN']->Execute("DELETE FROM sessao WHERE expireref = '". $expireref ."';");
        }
    }

    /*
     * Resume uma sessão criada anteriormente
    */
    public static function resume() {
        global $persist, $debug;

        if(isset($GLOBALS['ADODB_SESS_CONN']) && is_object($GLOBALS['ADODB_SESS_CONN'])) {
            ADOdb_session::Persist($connectMode = $persist);
            $GLOBALS['ADODB_SESS_CONN']->debug = $debug;
            @session_start();
        }
    }

    // forca eliminacao das sessões expiradas de acordo com o tempo definido por  $session_life_time
    protected function clear_expired_sessions() {
        if(is_object($GLOBALS['ADODB_SESS_CONN'])) {
            $time = $GLOBALS['ADODB_SESS_CONN']->OffsetDate(-$this->session_life_time/24/3600,$GLOBALS['ADODB_SESS_CONN']->sysTimeStamp);
            $GLOBALS['ADODB_SESS_CONN']->Execute("DELETE FROM ". $this->session_table ." WHERE expiry < ". $time .";");
        }
    }

    /**
     * Configura o tempo máximo de duração da sessão em segundos
     * @return void
     */
    public function session_life_time($time) {
        $this->session_life_time = $time;
    }

}
?>
