<?php
require_once(dirname(__FILE__).'/../../lib/adodb5/adodb.inc.php');
/**
 * Connection factory
 * usando a bibliote ADODB
 */
class connection_factory {

    private $host;
    private $database;
    private $user;
    private $password;
    private $port;
    private $conn_persistent; //verifica se e conexao persistente
    private $debug;

    public $adodb; //Objeto de ADODB

    /**
     * Construtor da connection factory
     * @param vetor com parametros de configuracao
     * @param default true para conexao persistente
     */
    public function __construct($arr, $conn_persistent = TRUE, $debug = FALSE) {
        $this->host 	= $arr['host'];
        $this->user 	= $arr['user'];
        $this->password = $arr['password'];
        $this->database = $arr['database'];
        $this->port 	= $arr['port'];

        $this->conn_persistent = $conn_persistent;

        $this->debug = $debug;
        $this->open();
    }

	/**
	 * Exibe o erro na tela e interrompe a execução
     */
	public function show_error($error_msg) {

		die('<h2 style="color: red">DB: Erro ao acessar o banco de dados</h2>'.
                        '<div style="background-color: #ffffcc; padding:12px; margin:12px; font-size: 10px; width: 70%;">'.
                        $error_msg .'</div>');
	}

    /**
     * Abre a conexao com o banco de dados
     */
    public function open() {

        $this->adodb = $GLOBALS['ADODB_SESS_CONN'];

        // reaproveita a conexao da sessao, caso exista uma e seja identica a conexao sendo criada
        if(is_object($this->adodb) && $this->adodb->host == $this->host && $this->adodb->port == $this->port && $this->adodb->database == $this->database && $this->adodb->user == $this->user && $this->adodb->password == $this->passowrd) {
            ADOdb_session::Persist($connectMode = $this->conn_persistent);
        }
        else {

            $conn_data = "host=$this->host port=$this->port dbname=$this->database user=$this->user password=$this->password";

            $this->adodb = ADONewConnection("postgres");

            if($this->conn_persistent) {
            // Conexao persistente
                if(!$this->adodb->PConnect($conn_data)) {
					$this->show_error($this->adodb->ErrorMsg());
                }
            }
            else {
            // Conexao nao persistente
                if(!$this->adodb->Connect($conn_data)) {
					$this->show_error($this->adodb->ErrorMsg());
                }
            }
        }

        $this->adodb->debug = $this->debug;
    }

    /**
     * Fecha conexao
     */
    public function close() {
        $this->adodb->Close();
    }

    /**
     * Executa instrucoes no banco de dados
     * @param sql
     * @return ResultSet
     */
    public function Execute($sql) {
        if (!$ResultSet = $this->adodb->Execute($sql)) {
			$this->show_error($this->adodb->ErrorMsg() .'<br />'. $sql);
        }
        return $ResultSet;
    }
    /**
     * Realiza uma consulta retornando um vetor multidimensional
     * @param string $sql
     * @return array
     */
    public function get_all($sql){
		$ret = $this->adodb->GetAll($sql);
        if($ret === FALSE)
			$this->show_error($this->adodb->ErrorMsg() .'<br />'. $sql);
		else
			return $ret;
    }

    /**
     * Realiza uma consulta retornando um vetor unidimensional
     * da primeira linha
     * @param string $sql
     * @return array
     */
    public function get_row($sql){
		$ret = $this->adodb->GetRow($sql);
        if($ret === FALSE)
			$this->show_error($this->adodb->ErrorMsg() .'<br />'. $sql);
		else
			return $ret;
    }

    /**
     * Realiza uma consulta retornando o primeiro valor
     * @param string $sql
     * @return var
     */
    public function get_one($sql){
		$ret = $this->adodb->GetOne($sql);
        if($ret === FALSE)
			$this->show_error($this->adodb->ErrorMsg() .'<br />'. $sql);
		else
			return $ret;
    }

	/**
     * Realiza uma consulta retornando a primeira coluna em um array
     * @param string $sql
     * @return var
     */
    public function get_col($sql){
        $ret = $this->adodb->GetCol($sql);
        if($ret === FALSE)
            $this->show_error($this->adodb->ErrorMsg() .'<br />'. $sql);
        else
            return $ret;
    }
}

?>
