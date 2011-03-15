<?php

require_once(dirname(__FILE__) . '/../../app/setup.php');

$LoginHost   = $host; //  nome do host ;
$LoginDB     = $database; // nome do banco;
$LoginUID    = $user;
$LoginPWD    = $password;
$LoginPort   = $port;

$ErrorURL   	  = $BASE_DIR . 'app/sagu/fatalerror.php';
$SuccessURL 	  = $BASE_DIR . 'app/sagu/modelos/modelo_exito.php';
$PATH_SAGU_IMAGES = $BASE_URL .'app/sagu/images/';
$LoginACL  		  = $BASE_DIR .'app/sagu/users.acl';
$SQL_LogFile 	  = $BASE_DIR .'app/sagu/logs/sql.log';

/**
 * LOG DO SISTEMA
 * 1 para gravar os comandos SQL no arquivo $SLQLogFile, 0 para n�o fazer
 */
$SQL_Debug   = 1;

/**
 * Classe de abstracao de dados do SAGU
 */


class Query {

	var $conn;     // the connection id
	var $sql;      // the SQL command string
	var $result;   // the SQL command result set
	var $row;      // the current row index

	function Open() {

		LogSQL($this->sql);

		$this->result = pg_exec($this->conn->id,$this->sql);
		$this->row    = -1;

		return $this->result != null;
	}

	function Close(){

		if ( $this->result != null ){
			pg_freeresult($this->result);
			$this->result = null;
		}
	}

	function MovePrev()
	{
		if ( $this->row >= 0 )
		{
			$this->row--;

			return true;
		}

		return false;
	}

	function MoveNext()
	{
		if ( $this->row + 1 < $this->GetRowCount() )
		{
			$this->row++;

			return true;
		}

		return false;
	}

	function GetRowCount()
	{
		return pg_numrows($this->result);
	}

	function GetColumnCount()
	{
		return pg_numfields($this->result);
	}

	function GetColumnName($col)
	{
		return pg_fieldname($this->result,$col-1);
	}

	function GetValue($col)
	{
		return pg_result($this->result,$this->row,$col-1);
	}

	function GetRowValues()
	{
		return pg_fetch_row($this->result,$this->row);
	}

	function GetAllValues()
	{
		return pg_fetch_all($this->result);
	}

	function SetConnection($c)
	{
		$this->conn = $c;
	}
}


/**
 * Classe de conexao do SAGU
 */

class Connection {


	var $id;         // the connection identifier
	var $traceback;  // a list of transaction errors
	var $level;      // a counter for the transaction level


	// opens a connection to the specified data source
	function Open($no_SaguAssert=false)
	{
		global $LoginUID,$LoginPWD,$LoginDB,$LoginHost, $LoginPort;


		$arg = "host=$LoginHost dbname=$LoginDB port=$LoginPort user=$LoginUID password=$LoginPWD";
		$this->id = @pg_Connect($arg);
		$this->level = 0;

		if ( empty($no_SaguAssert) || !$no_SaguAssert )
		{
			$err = @$this->GetError();

			SaguAssert($this->id,"Connection : Open(\"user=$LoginUID\") : Connection refused!<br><br>$err");
		}

		return empty($this->id) ? 0 : $this->id;
	}

	// closes a previously opened connection
	function Close(){
		 
		if ( $this->id )
		{
			SaguAssert($this->level==0,"Transactions not finished!");

			pg_close($this->id);

			$this->id = 0;
		}
	}

	function Begin()
	{
		$this->Execute("begin transaction");

		$this->level++;
	}

	function Finish()
	{
		SaguAssert($this->level>0,"Transaction level underrun!");

		$success = $this->GetErrorCount() == 0;

		if ( $success )
		$this->Execute("commit");
		else
		$this->Execute("rollback");

		$this->level--;

		return $success;
	}

	function GetError()
	{
		return pg_errormessage($this->id);
	}

	function GetErrorCount()
	{
		return empty($this->traceback) ? 0 : count($this->traceback);
	}

	function CheckError()
	{
		if ( empty($this->traceback) )
		return;

		$n = count($this->traceback);

		if ( $n > 0 )
		{
			$msg = "";

			for ( $i=0; $i<$n; $i++ )
			$msg .= $this->traceback[$i] . "<br>";

			FatalExit("Transaction Error",$msg);
		}
	}

	function Execute($sql)
	{

		$sql = str_replace("''",'NULL',$sql);

		LogSQL($sql);

		$rs = pg_exec($this->id,$sql);

		$success = false;

		if ( $rs )
		{
			$success = true;
			pg_freeresult($rs);
		}

		else
		$this->traceback[] = $this->GetError();

		return $success;
	}

	function CreateQuery($sql="")
	{
		SaguAssert($this->id,"Connection: CreateQuery: Connection ID");

		$q = new Query;

		$q->conn   = $this;
		$q->sql    = $sql;
		$q->result = 0;
		$q->row    = -1;

		if ( $sql != "" )
		$q->Open();

		return $q;
	}

}


/**
 * Use esta fun��o para pr�-visualizar um comando sq
 */

function LogSQL($sql,$force=false){

	global $SQL_Debug, $SQL_LogFile, $REMOTE_ADDR, $LoginUID;

	if ( ! $SQL_Debug )
	return;

	$sql = ereg_replace("\n+ *"," ",$sql);
	$sql = ereg_replace(" +"," ",$sql);
	$sql = ereg_replace("^ +| +$","",$sql);
	$sql = ereg_replace("\"","\"\"",$sql);
	$dts = date("Y/m/d:H:i:s");

	$cmd = "^\*\*\*|" .
         "^ *INSERT|^ *DELETE|^ *UPDATE|^ *ALTER|^ *CREATE|" . 
         "^ *BEGIN|^ *COMMIT|^ *ROLLBACK|^ *GRANT|^ *REVOKE";

	$ip  = sprintf("%15s",$REMOTE_ADDR);
	$uid = sprintf("%-10s",$LoginUID);

	if ( $force || eregi($cmd,$sql) )
	error_log("$ip - $uid - [$dts] \"$sql\"\n",3,$SQL_LogFile);

}


/**
 * Purpose: The exit function is used in order to provide a
 * consistent manner of error handling. This function
 * does not return from execution.
 */

function FatalExit($msg="",$info="",$href=""){

	global $ErrorURL, $PATH_SAGU_IMAGES;

	if ( $msg == "" )
	$msg = "Erro inesperado ou acesso proibido";

	if ( $info == "" )
	$info = "Causa desconhecida";

	if ( $href == "" )
	$href = "javascript:history.go(-1)";

	if ( $ErrorURL )
	{
		require_once($ErrorURL);
		die;
	}

	echo("<html>");
	echo("<head>");
	echo("<title>Untitled Document</title>");
	echo("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">");
	echo("</head>");
	echo("");
	echo("<body bgcolor=\"#FFFFFF\">");
	echo("<table width=\"80%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" height=\"90%\">");
	echo("  <tr> ");
	echo("    <td width=\"33%\"> ");
	echo("      <div align=\"center\"><img src=\"../images/univates.gif\" width=\"104\" height=\"94\" align=\"middle\"></div>");
	echo("    </td>");
	echo("    <td width=\"67%\">");
	echo("      <div align=\"center\"><b><font color=\"#000000\" size=\"4\" face=\"Verdana, Arial, Helvetica, sans-serif\">Aten&ccedil;&atilde;o</font></b></div>");
	echo("    </td>");
	echo("  </tr>");
	echo("  <tr> ");
	echo("    <td colspan=\"2\"> ");
	echo("      <div align=\"center\">");
	echo("        <p><b><font size=\"5\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#FF0000\">$msg");
	echo("</font></b><br><br><font size=\"3\" face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#000000\">Causa: $info</font></p>");
	echo("        <p>&nbsp;</p>");
	echo("      </div>");
	echo("    </td>");
	echo("  </tr>");
	echo("  <tr> ");
	echo("    <td colspan=\"2\"> ");
	echo("      <div align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\"><a href=\"$href\"><b>Voltar</b></a></font></div>");
	echo("    </td>");
	echo("  </tr>");
	echo("</table>");
	echo("</body>");
	echo("</html>");

	die();
}


/**
 * Purpose: Calls page with information about successful completion
 */

function SuccessPage($titulo,$goto="history.go(-1)",$info="",$button=""){

	global $SuccessURL, $exito_titulo, $exito_goto, $exito_info, $exito_button;

	$exito_titulo = $titulo;
	$exito_goto   = $goto;
	$exito_info   = $info;
	$exito_button = $button;

	if ( substr($exito_goto,0,8) != "history." && substr($exito_goto,0,9) != "location=" )
	$exito_goto = "location='" . $exito_goto . "'";

	LogSQL("\$exito_goto = $exito_goto");

	require_once($SuccessURL);
}


/**
 * Purpose: Aborts program execution if a condition fails.
 */

function SaguAssert($cond,$msg=""){

	if ( $cond == false )
	FatalExit("Erro inesperado ou acesso proibido!",$msg);
}


/**
 * Purpose: Checks a list of required input fields. The
 * argument passed, is expected to be an associative
 * array, whose key is the field name and the value
 * contains the input field's value.
 *
 * When consistent manner of error handling. This function
 * does not return from execution.
 */

function CheckInputFields($fields,$stop=true,$rname=null){

	reset($fields);

	$n = count($fields);

	for ($i=0; $i<$n; $i++ )
	{
		list($key,$val) = each($fields);

		$val = trim($val);

		if ( $val == "" )
		{
			if ( $stop )
			FatalExit("Input Error","Missing value for field (" . ($i + 1) . ") <i>" . $key . "</i>");

			else
			{
				if ( $rname != null )
				$rname = $key;

				return false;
			}
		}
	}
}


/**
 * Purpose: Checks a field value for valid content. This
 * function is mainly for convenience in order to
 * generate a standardized message for an invalid field input.
 * When $cond is false, the function generates an
 * error message and does not return from execution.
 */

function CheckInputValue($name,$cond,$hint=""){

	if ( !$cond )
	{
		$msg = "Valor informado para o campo <b><i>$name</b></i> � inv�lido.";

		if ( $hint != "" )
		$msg .= "<br><br><b>Restri��o:</b> " . $hint;

		FatalExit("Erro de Digita��o!",$msg);
	}
}


/**
 * Purpose: Checks a field value for valid content. This
 * function is mainly for convenience in order to
 * generate a standardized message for an invalid
 * field input.
 *
 * When $cond is false, the function generates an
 * error message and does not return from execution.
 */

function CheckFormParameters($list,$href=""){

	$n = count($list);
	//print_r($list);
	for ( $i=0; $i<$n; $i++ )
	{
		$name  = $list[$i];

		if ( !$name )
			continue;

		if(array_key_exists($name, $GLOBALS['_GET']))
		{
			$value = trim($GLOBALS['_GET']["$name"]);
		}
		else
		{
			if (array_key_exists($name, $GLOBALS['_POST'])) 
			{
                $value = trim($GLOBALS['_POST']["$name"]);
			}
		}
		
		if (!is_numeric($value) AND empty($value))
		{
			$msg = "Campo obrigat&oacute;rio [<b><i>$name</i></b>] n&atilde;o informado!";

			FatalExit("Erro de Digita&ccedil;&atilde;o!",$msg,$href);
		}
	}
}


/**
 * Purpose: Checks if a specified keyword matches the list
 * of valid values. If not FatalExit will be called
 * with an appropriate error message.
 */

function CheckKeyword($name,$kword,$values) {

	if ( empty($kword) || $kword == "" )
	FatalExit("Parameter Error","Required keyword <b>$name</b> is not specified!");

	else
	{
		$n = count($values);

		for ( $i=0; $i<$n; $i++ )
		{
			if ( $kword == $values[$i] )
			return;
		}

		$msg = "Keyword [<b>$name</b>] contains the unupported value [<b>$kword</b>]!<br><br>" .
           "Supported values are: [";

		for ( $i=0; $i<$n; $i++ )
		{
			if ( $i > 0 )
			$msg .= ", ";

			$msg .= "<i>" . $values[$i] . "</i>";
		}

		$msg .= "].";

		FatalExit("Parameter Error",$msg);
	}
}


/**
 * Purpose: Prints a debugging message as preformatted text
 */

function debug($msg){

	echo("<pre>$msg</pre>");
}


/**
 * Purpose: Retorna a data do dia no formato D/M/AAAA
 */

function Today() {

	$dt = getdate();
	return sprintf("%0.2d/%0.2d/%0.4d",$dt["mday"],$dt["mon"],$dt["year"]);
}


function Today_usa(){

	$dt = getdate();
	return sprintf("%0.4d/%0.2d/%0.2d",$dt["year"], $dt["mon"],$dt["mday"]);
}


/**
 * Purpose: Converte a data de formato D/M/AAAA para AAAA/M/D
 */

function DMA_To_AMD($dt){

	list ( $d, $m, $a ) = explode("/",$dt);
	return sprintf("%0.4d/%0.2d/%0.2d",$a,$m,$d);
}


/**
 * Purpose: Converte a data de formato M/D/A para D/M/A
 */

function MDA_To_DMA($dt){

	list ( $d, $m, $a ) = explode("-",$dt);
	return sprintf("%0.2d/%0.2d/%0.2d",$m,$d,$a);
}


/**
 * Purpose: Obter ID de uma seq�encia
 */

function GetIdentity($seq,$SaguAssert=true,$msg=""){

	$conn = new Connection;
	$conn->Open();
	$sql = "select nextval('$seq')";
	$query = @$conn->CreateQuery($sql);

	$success = false;

	if ( @$query->MoveNext() )
	{
		$id = $query->GetValue(1);

		$success = true;
	}

	$err = $conn->GetError();
	$query->Close();
	SaguAssert(!$SaguAssert || $success,$msg ? $msg : "Nao foi possivel obter um c�digo de '$seq'<br><br>$err!");

	return $id;
}


/**
 * userid : allowed | denied : url1,url2,
 */

function CheckAccess($user,$path){

	global $LoginACL;

	$file = @fopen($LoginACL,"r");

	if ( $file )
	{
		$ok = false;

		$done = false;

		while ( $ln = fgets($file,4096) )
		{
			// ignore comment or empty lines
			if ( ereg("^ *#|^ *$",$ln) )
			continue;

			// userid: url,url,...
			list ( $uid, $action, $url_list ) = explode(":",$ln);

			$uid      = trim($uid);
			$action   = strtoupper(trim($action));
			$url_list = trim($url_list);

			if ( $uid == $user || $uid == "*" )
			{
				$a = explode(",",$url_list);

				for ( $i=0; $i < count($a); $i++ )
				{
					$ok = false;

					$s = trim($a[$i]);

					if ( $action == "ALLOW" )
					{
						$ok = $path == "*" || ereg("^$s",$path);

						if ( $ok )
						{
							$done = true;
							break;
						}
					}

					else if ( $action == "DENY" )
					{
						$ok = $path != "*" && ! ereg("^$s",$path);

						if ( ! $ok )
						{
							$done = true;
							break;
						}
					}

					else
					ASSERT(1,"ERROR: Invalid ACCESS CONTROL option!");
				}
			}

			if ( $done )
			break;
		}

		fclose($file);

		if ( ! $ok )
		{
			LogSQL("*** ACL ACCESS DENIED (uid=$user,path=$path) ***");

			SaguAssert($ok,"ACCESS DENIED");
		}
	}
}


/**
 * converte real para inteiro
 */

function real_to_int($valor){

	$valor_string = "$valor";
	$valor_novo = "";
	$n = 0;
	while (($n<strlen($valor_string)) && ($valor_string[$n] != "."))
	{
		$valor_novo = "$valor_novo$valor_string[$n]";
		$n ++;
	}
	return($valor_novo);
}


function GetEmpresa($id,$SaguAssert){

	$sql = "select id,razao_social from configuracao_empresa where id=$id";

	$conn = new Connection;

	$conn->Open();

	$query = $conn->CreateQuery($sql);

	if ( $query->MoveNext() )
	$obj = $query->GetValue(2);

	$query->Close();

	$conn->Close();

	if ( $SaguAssert )
	SaguAssert(!empty($obj),"Empresa [$id] nao definido!");

	return $obj;
}

function GetCampus($id,$SaguAssert){

    $sql = "select id,nome_campus from campus where id=$id";

    $conn = new Connection;

    $conn->Open();

    $query = $conn->CreateQuery($sql);

    if ( $query->MoveNext() )
    $obj = $query->GetValue(2);

    $query->Close();

    $conn->Close();

    if ( $SaguAssert )
    SaguAssert(!empty($obj),"Campus [$id] nao definido!");

    return $obj;
}

?>
