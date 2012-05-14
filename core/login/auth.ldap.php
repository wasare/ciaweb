<?php

/**
 * Classe de autenticação no LDAP
 * @filesource
 * @since 2012-02-10
 * @author Wanderson S. Reis
 */

/**
* Auth LDAP class
* 
*/
class authLDAP {

    protected $_base_dn = 'dc=domain,dc=tld';
    protected $_ldap_hosts = array ('ldap.domain.tld');
    protected $_ldap_port = 389;
    protected $_use_ssl = FALSE;
    protected $_use_tls = FALSE;
    protected $_server_type = 'LDAP';
    protected $_account_suffix = "";

	  protected $_conn; // Connection variable
    

    /**
    * Constructor
    * 
    * Tries to bind to LDAP or LDAPs
    * 
    * @param array $options Array of options to pass to the constructor
    * @return bool
    */
    function __construct($options=array()){
        
        // You can specifically overide any of the default configuration options setup above
        if (count($options)>0){
            if (array_key_exists('base_dn',$options)){ $this->_base_dn=$options['base_dn']; }
            if (array_key_exists('ldap_hosts',$options)){ $this->_ldap_hosts=$options['ldap_hosts']; }
            if (array_key_exists('ldap_port',$options)){ $this->_ldap_port=$options['ldap_port']; }
            if (array_key_exists('use_ssl',$options)){ $this->_use_ssl=$options['use_ssl']; }
            if (array_key_exists('use_tls',$options)){ $this->_use_tls=$options['use_tls']; }
            if (array_key_exists('server_type',$options)){ $this->_server_type=$options['server_type']; }
            if (array_key_exists('account_suffix',$options)){ $this->_account_suffix=$options["account_suffix"]; }
        }
        
        if (!function_exists('ldap_connect')) {
            die('No LDAP support for PHP.  See: http://www.php.net/ldap');
        }

        return $this->connect();
    }

    /**
    * Connects and Binds to LDAP
    * 
    * @return bool
    */
    public function connect() {
        // Connect to the AD/LDAP server as the username/password
        $ldap = $this->ldap_server();
        if ($this->_use_ssl){
            $this->_conn = ldap_connect("ldaps://".$ldap, $this->_ldap_port);
        } else {
            $this->_conn = ldap_connect($ldap, $this->_ldap_port);
        }
               
        // Set some ldap options
        ldap_set_option($this->_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->_conn, LDAP_OPT_REFERRALS, 0);
        
        if ($this->_use_tls) {
            ldap_start_tls($this->_conn);
        }
        
        return TRUE;
    }
    

    /**
    * Autentica o usuário na base LDAP
    * 
    * @param string $username
    * @param string $password
    * @return bool
    */
    public function authenticate($username,$password){
        
        $ret = TRUE;

        // Prevent null binding
        if ($username === NULL || $password === NULL) $ret = FALSE; 
        if (empty($username) || empty($password)) $ret = FALSE;
        
        // Bind as the user
        if ($this->_server_type == 'AD')        
          $bind = @ldap_bind($this->_conn,$username . $this->_account_suffix,$password);

        if ($this->_server_type == 'LDAP') 
          if (is_array($this->_base_dn) && count($this->_base_dn) != 0) {
            foreach($this->_base_dn as $base_dn) {
              $bind = @ldap_bind($this->_conn, 'uid='. $username .','. $base_dn, $password);       
              if ($bind) break;
            }
          }
          else
            $bind = @ldap_bind($this->_conn, 'uid='. $username .','. $this->_base_dn, $password);

        if (!$bind) $ret = FALSE;

        @ldap_close($this->_conn);

        return $ret;
    }

    // Return a random server
    public function ldap_server() {
        //select a random ldap server
        mt_srand(doubleval(microtime()) * 100000000); // for older php versions
        return ($this->_ldap_hosts[array_rand($this->_ldap_hosts)]);
    }
}
?>
