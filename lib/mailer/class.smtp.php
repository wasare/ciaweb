<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
São Paulo - Brasil
*************************************************************************/



class phpmailer{

	var $Priority			= 3;
	var $CharSet			= "UTF-8";
	var $ContentType		= "text/plain";
	var $Encoding			= "8bit";
	var $From				= "root@localhost";
	var $FromName			= "root";
	var $Subject			= "";
	var $Body				= "";
	var $WordWrap			= true;
	var $MailerDebug		= false;
	var $UseMSMailHeaders	= true;
	var $IPAddress			= "unknown";
	var $timezone			= "+0300";

	// SMTP

	var $Host        = "localhost";
	var $Port        = 25;
	var $Helo        = "";
	var $Timeout     = 10; // Socket timeout in sec.

	/////////////////////////////////////////////////
	// PRIVATE VARIABLES
	/////////////////////////////////////////////////
	var $version        = "";
	var $to             = array();
	var $cc             = array();
	var $bcc            = array();
	var $ReplyTo        = array();
	var $attachment     = array();
	var $CustomHeader   = array();
	var $boundary       = false;
	var $ErrorAlerts    = Array();
	var $blUseAuthLogin = false;
	var $AuthUser       = "";
	var $AuthPass       = "";

	function UseAuthLogin($user,$pass) {
		$this->blUseAuthLogin = true;
		$this->AuthUser = $user;
		$this->AuthPass = $pass;
	}
	
	/////////////////////////////////////////////////
	// VARIABLE METHODS
	/////////////////////////////////////////////////
	function IsHTML($bool) {
		if($bool == true)
			$this->ContentType = "text/html";
		else
			$this->ContentType = "text/plain";
	}

	/////////////////////////////////////////////////
	// LOAD VARIABLES
	/////////////////////////////////////////////////

	function Start() {
		global $appname,$appversion;
		$this->Version = $appname." ".$appversion;
		$this->Helo = ereg_replace("[^A-Za-z0-9]","",$appname);
	}


	/////////////////////////////////////////////////
	// RECIPIENT METHODS
	/////////////////////////////////////////////////	

	
	function AddAddress($address, $name = "") {
		$cur = count($this->to);
		$this->to[$cur][0] = trim($address);
		$this->to[$cur][1] = $name;
	}

	function AddCC($address, $name = "") {
		$cur = count($this->cc);
		$this->cc[$cur][0] = trim($address);
		$this->cc[$cur][1] = $name;
	}

	function AddBCC($address, $name = "") {
		$cur = count($this->bcc);
		$this->bcc[$cur][0] = trim($address);
		$this->bcc[$cur][1] = $name;
	}

	function AddReplyTo($address, $name = "") {
		$cur = count($this->ReplyTo);
		$this->ReplyTo[$cur][0] = trim($address);
		$this->ReplyTo[$cur][1] = $name;
	}


	/////////////////////////////////////////////////
	// MAIL SENDING METHODS
	/////////////////////////////////////////////////

	/**
	 * Send method creates message and assigns Mailer.  Returns bool.
	 * @public
	 * @returns bool
	 */
	function Send() {
		global $use_sendmail;
		if(count($this->to)+count($this->cc)+count($this->bcc) == 0) {
			$this->error_handler("You must provide at least one recipient email address");
			return false;
		}
		$header = $this->create_header();
		if(($body = $this->create_body()) === false)
		   return false;
		if($use_sendmail) {
			if($this->sendmail_send($header, $body) === false)
			   return false;
		} else {
			if($this->smtp_send($header, $body) === false)
			   return false;
		}
		return sprintf("%s%s", $header, $body);
	}

	function sendmail_send($header, $body) {
		global $path_to_sendmail;
		if(strtoupper(substr(PHP_OS,0,3)) == "WIN") {
			$this->error_handler("Sendmail is not supported under Win32 systems");
			return false;
		}
		$sendmail = sprintf("%s -t", $path_to_sendmail);
		if(!@$mail = popen($sendmail, "w")) {
			$this->error_handler(sprintf("Could not execute %s", $path_to_sendmail));
			return false;
		}
		fputs($mail, $header);
		fputs($mail, $body);
		pclose($mail);
		return true;
	}

	function smtp_send($header, $body) {
		global $enable_debug;
		$smtp = new SMTP;
		$smtp->do_debug = $enable_debug;
		$hosts = explode(";", $this->Host);
		$index = 0;
		$connection = false;
		
		while($index < count($hosts) && $connection == false) {
			if($smtp->Connect($hosts[$index], $this->Port, $this->Timeout))
				$connection = true;
			$index++;
		}

		if(!$connection) {
			$this->error_handler("SMTP Error: could not connect to SMTP host server(s)");
			return false;
		}

		if($this->blUseAuthLogin) {
			if(!$smtp->AuthHello($this->Helo,$this->AuthUser,$this->AuthPass)) {
			   $this->error_handler("SMTP Error: Invalid username/password");
			   return false;
			}
		} else
			$smtp->Hello($this->Helo);

		$smtp->MailFrom(sprintf("<%s>", $this->From));
		for($i = 0; $i < count($this->to); $i++)
			if(!$smtp->Recipient(sprintf("<%s>", $this->to[$i][0]))) {
				$this->error_handler("SMTP Error: Recipient not accepted. Verify your relay rules");
				return false;
			}
		for($i = 0; $i < count($this->cc); $i++)
			if(!$smtp->Recipient(sprintf("<%s>", $this->cc[$i][0]))) {
				$this->error_handler("SMTP Error: Recipient not accepted. Verify your relay rules");
				return false;
			}

		for($i = 0; $i < count($this->bcc); $i++)
			if(!$smtp->Recipient(sprintf("<%s>", $this->bcc[$i][0]))) {
				$this->error_handler("SMTP Error: Recipient not accepted. Verify your relay rules");
				return false;
			}

		if(!$smtp->Data(sprintf("%s%s", $header, $body))) {
		   $this->error_handler("SMTP Error: Data not accepted");
		   return false;
		}

		$smtp->Quit();
	}

	/////////////////////////////////////////////////
	// MESSAGE CREATION METHODS
	/////////////////////////////////////////////////

	function addr_append($type, $addr) {
		$addr_str = "";
		if(trim($addr[0][1]) != "")
			$addr_str .= sprintf("%s: \"%s\" <%s>",$type, $addr[0][1], $addr[0][0]);
		else
			$addr_str .= sprintf("%s: %s",$type,$addr[0][0]);

		if(count($addr) > 1) {
			for($i = 1; $i < count($addr); $i++) {
				if(trim($addr[$i][1]) != "")
					$addr_str .= sprintf(", \r\n\t\"%s\" <%s>", $addr[$i][1], $addr[$i][0]);
				else
					$addr_str .= sprintf(", \r\n\t\"%s\"", $addr[$i][0]);
			}
			$addr_str .= "\r\n";
		} else
			$addr_str .= "\r\n";

		return($addr_str);
	}

	function wordwrap($message, $length) {
		$line = explode("\r\n", $message);
		$message = "";
		for ($i=0 ;$i < count($line); $i++) 
		{
			$line_part = explode(" ", trim($line[$i]));
			$buf = "";
			for ($e = 0; $e<count($line_part); $e++) 
			{
				$buf_o = $buf;
				if ($e == 0)
					$buf .= $line_part[$e];
				else 
					$buf .= " " . $line_part[$e];
				if (strlen($buf) > $length and $buf_o != "")
				{
					$message .= $buf_o . "\r\n";
					$buf = $line_part[$e];
				}
			}
			$message .= $buf . "\r\n";
		}
		
		return ($message);
	}

	function create_header() {
		global $use_sendmail;
		$this->Start();
		$header = array();
		$header[] = sprintf("Received: from client %s for UebiMiau2.7 (webmail client); %s %s\r\n", $this->IPAddress, date("D, j M Y G:i:s"), $this->timezone);
		$header[] = sprintf("Date: %s %s\r\n", date("D, j M Y G:i:s"), $this->timezone);
		$header[] = sprintf("From: \"%s\" <%s>\r\n", $this->FromName, trim($this->From));
		if(count($this->to) > 0)
			$header[] = $this->addr_append("To", $this->to);
		if(count($this->cc) > 0)
			$header[] = $this->addr_append("Cc", $this->cc);
		if(count($this->bcc) > 0 && $use_sendmail)
			$header[] = $this->addr_append("Bcc", $this->bcc);

		if(count($this->ReplyTo) > 0)
			$header[] = $this->addr_append("Reply-to", $this->ReplyTo);
		$header[] = sprintf("Subject: %s\r\n", trim($this->Subject));
		$header[] = sprintf("X-Priority: %d\r\n", $this->Priority);
		$header[] = sprintf("X-Mailer: %s\r\n", $this->Version);
		$header[] = sprintf("X-Original-IP: %s\r\n", $this->IPAddress);
		$header[] = sprintf("Content-Transfer-Encoding: %s\r\n", $this->Encoding);
		$header[] = sprintf("Return-Path: %s\r\n", trim($this->From));
		
		// Add custom headers
		for($index = 0; $index < count($this->CustomHeader); $index++)
		   $header[] = sprintf("%s\r\n", $this->CustomHeader[$index]);

		if($this->UseMSMailHeaders)
		   $header[] = $this->UseMSMailHeaders();

		// Add all attachments
		if(count($this->attachment) > 0)
		{
			$header[] = sprintf("Content-Type: multipart/mixed; charset=\"%s\";\r\n", $this->CharSet);
			$header[] = sprintf("\tboundary=\"--=%s\"\r\n", $this->boundary);
		}
		else
			$header[] = sprintf("Content-Type: %s; charset=\"%s\";\r\n", $this->ContentType, $this->CharSet);
		
		$header[] = "MIME-Version: 1.0\r\n";
		
		return(join("", $header)."\r\n");
	}

	function create_body() {
		if($this->WordWrap)
			$this->Body = $this->wordwrap($this->Body, $this->WordWrap);
		if(count($this->attachment) > 0) {
			if(!$body = $this->attach_all())
			   return false;
		}
		else
			$body = $this->Body;
		
		return(trim($body));		
	}
	
	
	/////////////////////////////////////////////////
	// ATTACHMENT METHODS
	/////////////////////////////////////////////////

	function AddAttachment($path, $name = "", $type= "application/octet-stream") {
		if(!@is_file($path)) {
			$this->error_handler(sprintf("Could not find %s file on filesystem", $path));
			return false;
		}
		$filename = basename($path);
		if($name == "")
		   $name = $filename;
		
		$this->boundary = "_b" . md5(uniqid(time()));

		$cur = count($this->attachment);
		$this->attachment[$cur][0] = $path;
		$this->attachment[$cur][1] = $filename;
		$this->attachment[$cur][2] = $name;
		$this->attachment[$cur][3] = $type;

		return true;
	}


	function attach_all() {

		$mime = sprintf("----=%s\r\n", $this->boundary);
		$mime .= sprintf("Content-Type: %s\r\n", $this->ContentType);
		$mime .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
		$mime .= sprintf("%s\r\n", $this->Body);
		
		for($i = 0; $i < count($this->attachment); $i++) {
			$path = $this->attachment[$i][0];
			$filename = $this->attachment[$i][1];
			$name = $this->attachment[$i][2];
			$type = $this->attachment[$i][3];
			$mime .= sprintf("----=%s\r\n", $this->boundary);
			$mime .= sprintf("Content-Type: %s; name=\"%s\"\r\n",$type,$name);
			$mime .= "Content-Transfer-Encoding: base64\r\n";
			$mime .= sprintf("Content-Disposition: attachment; filename=\"%s\"\r\n\r\n", $name);
			if(!$mime .= sprintf("%s\r\n\r\n", $this->encode_file($path)))
			   return false;
		}
		$mime .= sprintf("\r\n----=%s--\r\n", $this->boundary);
		
		return $mime;
	}

	function encode_file ($path) {
		if(!@$fd = fopen($path, "rb"))
		{
			$this->error_handler("File Error: Could not open file $path");
			return false;
		}
		$file = fread($fd, filesize($path));
		
		// chunk_split is found in PHP >= 3.0.6
		$encoded = chunk_split(base64_encode($file));
		fclose($fd);
		
		return($encoded);
	}
	
	/////////////////////////////////////////////////
	// MESSAGE RESET METHODS
	/////////////////////////////////////////////////

	function ClearAddresses() {
	   $this->to = array();
	}

	function ClearCCs() {
	   $this->cc = array();
	}

	function ClearBCCs() {
	   $this->bcc = array();
	}

	function ClearReplyTos() {
	   $this->ReplyTo = array();
	}

	function ClearAllRecipients() {
	   $this->to = array();
	   $this->cc = array();
	   $this->bcc = array();
	}

	function ClearAttachments() {
	   $this->attachment = array();
	}

	function ClearCustomHeaders() {
	   $this->CustomHeader = array();
	}

	/////////////////////////////////////////////////
	// MISCELLANEOUS METHODS
	/////////////////////////////////////////////////

	function error_handler($msg) {
		$this->ErrorAlerts[] = $msg;
		if($this->MailerDebug == true) {
			print("<h3>Mailer Error</h3>");
			print("Description:<br>");
			printf("<font color=\"FF0000\">%s</font>", $msg);
		}
	}

	function AddCustomHeader($custom_header) {
	   $this->CustomHeader[] = $custom_header;
	}

	function UseMSMailHeaders() {
	   $MSHeader = "";
	   if($this->Priority == 1)
	      $MSPriority = "High";
	   elseif($this->Priority == 5)
	      $MSPriority = "Low";
	   elseif($this->Priority == 3)
	      $MSPriority = "Normal";
	   else
	      $MSPriority = "Medium";
	      
	   $MSHeader .= sprintf("X-MSMail-Priority: %s\r\n", $MSPriority);
	   $MSHeader .= sprintf("Importance: %s\r\n", $MSPriority);
	   
	   return($MSHeader);
	}


}
// End of class

class SMTP {
    var $SMTP_PORT = 25; # the default SMTP PORT
    var $CRLF = "\r\n";  # CRLF pair

    var $smtp_conn;      # the socket to the server
    var $error;          # error if any on the last call
    var $helo_rply;      # the reply the server sent to us for HELO

    var $do_debug;       # the level of debug to perform

    /*
     * SMTP()
     *
     * Initialize the class so that the data is in a known state.
     */
    function SMTP() {
        $this->smtp_conn = 0;
        $this->error = null;
        $this->helo_rply = null;
        $this->do_debug = 0;
    }

    /************************************************************
     *                    CONNECTION FUNCTIONS                  *
     ***********************************************************/


    /*
     * Connected()
     *
     * Returns true if connected to a server otherwise false
     */
    function Connected() {
        if(!empty($this->smtp_conn)) {
            $sock_status = socket_get_status($this->smtp_conn);
            if($sock_status["eof"]) {
                # hmm this is an odd situation... the socket is
                # valid but we aren't connected anymore
                $this->Close();
                return false;
            }
            return true; # everything looks good
        } 
        return false;
    }


    /*
     * Connect($host, $port=0, $tval=30)
     *
     * Connect to the server specified on the port specified.
     * If the port is not specified use the default SMTP_PORT.
     * If tval is specified then a connection will try and be
     * established with the server for that number of seconds.
     * If tval is not specified the default is 30 seconds to
     * try on the connection.
     *
     * SMTP CODE SUCCESS: 220
     * SMTP CODE FAILURE: 421
     */
    function Connect($host,$port=0,$tval=30) {
        # set the error val to null so there is no confusion
        $this->error = null;

        # make sure we are __not__ connected
        if($this->connected()) {
            # ok we are connected! what should we do?
            # for now we will just give an error saying we
            # are already connected
            $this->error =
                array("error" => "Already connected to a server");
            return false;
        }

        if(empty($port)) {
            $port = $this->SMTP_PORT;
        }

        #connect to the smtp server
        $this->smtp_conn = fsockopen($host,    # the host of the server
                                     $port,    # the port to use
                                     $errno,   # error number if any
                                     $errstr,  # error message if any
                                     $tval);   # give up after ? secs
        # verify we connected properly
        if(empty($this->smtp_conn)) {
            $this->error = array("error" => "Failed to connect to server",
                                 "errno" => $errno,
                                 "errstr" => $errstr);
            return false;
        }

        # sometimes the SMTP server takes a little longer to respond
        # so we will give it a longer timeout for the first read
        //if(function_exists("socket_set_timeout"))
        //   socket_set_timeout($this->smtp_conn, 1, 0);

        # get any announcement stuff
        $announce = $this->get_lines();

        # set the timeout  of any socket functions at 1/10 of a second
        //if(function_exists("socket_set_timeout"))
        //   socket_set_timeout($this->smtp_conn, 0, 100000);

        return true;
    }


    /*
     * Close()
     *
     * Closes the socket and cleans up the state of the class.
     * It is not considered good to use this function without
     * first trying to use QUIT.
     */
    function Close() {
        $this->error = null; # so there is no confusion
        $this->helo_rply = null;
        if(!empty($this->smtp_conn)) { 
            # close the connection and cleanup
            fclose($this->smtp_conn);
            $this->smtp_conn = 0;
        }
    }


    /**************************************************************
     *                        SMTP COMMANDS                       *
     *************************************************************/

    /*
     * Data($msg_data)
     *
     * Issues a data command and sends the msg_data to the server
     * finializing the mail transaction. $msg_data is the message
     * that is to be send with the headers. Each header needs to be
     * on a single line followed by a <CRLF> with the message headers
     * and the message body being seperated by and additional <CRLF>.
     *
     * Implements rfc 821: DATA <CRLF>
     *
     * SMTP CODE INTERMEDIATE: 354
     *     [data]
     *     <CRLF>.<CRLF>
     *     SMTP CODE SUCCESS: 250
     *     SMTP CODE FAILURE: 552,554,451,452
     * SMTP CODE FAILURE: 451,554
     * SMTP CODE ERROR  : 500,501,503,421
     */
    function Data($msg_data) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Data() without being connected");
            return false;
        }
		$this->send_line("DATA");

        //fputs($this->smtp_conn,"DATA" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);


        if($code != 354) {
            $this->error =
                array("error" => "DATA command not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            return false;
        }

        # the server is ready to accept data!
        # according to rfc 821 we should not send more than 1000
        # including the CRLF
        # characters on a single line so we will break the data up
        # into lines by \r and/or \n then if needed we will break
        # each of those into smaller lines to fit within the limit.
        # in addition we will be looking for lines that start with
        # a period '.' and append and additional period '.' to that
        # line. NOTE: this does not count towards are limit.

        # normalize the line breaks so we know the explode works
        $msg_data = str_replace("\r\n","\n",$msg_data);
        $msg_data = str_replace("\r","\n",$msg_data);
        $lines = explode("\n",$msg_data);

        # we need to find a good way to determine is headers are
        # in the msg_data or if it is a straight msg body
        # currently I'm assuming rfc 822 definitions of msg headers
        # and if the first field of the first line (':' sperated)
        # does not contain a space then it _should_ be a header
        # and we can process all lines before a blank "" line as
        # headers.
        $field = substr($lines[0],0,strpos($lines[0],":"));
        $in_headers = false;
        if(!empty($field) && !strstr($field," ")) {
            $in_headers = true;
        }

        $max_line_length = 998; # used below; set here for ease in change

        while(list(,$line) = @each($lines)) {
            $lines_out = null;
            if($line == "" && $in_headers) {
                $in_headers = false;
            }
            # ok we need to break this line up into several
            # smaller lines
            while(strlen($line) > $max_line_length) {
                $pos = strrpos(substr($line,0,$max_line_length)," ");
                $lines_out[] = substr($line,0,$pos);
                $line = substr($line,$pos + 1);
                # if we are processing headers we need to
                # add a LWSP-char to the front of the new line
                # rfc 822 on long msg headers
                if($in_headers) {
                    $line = "\t" . $line;
                }
            }
            $lines_out[] = $line;

            # now send the lines to the server
            while(list(,$line_out) = @each($lines_out)) {
                if($line_out[0] == ".") {
                    $line_out = "." . $line_out;
                }
                $tmpdata .= $line_out.$this->CRLF;
            }
        }
        # ok all the message data has been sent so lets get this
        # over with aleady
		$this->send_line($tmpdata.$this->CRLF.".");
        //fputs($this->smtp_conn, $this->CRLF . "." . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($code != 250) {
            $this->error =
                array("error" => "DATA not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            return false;
        }
        return true;
    }


    /*
     * Hello($host="")
     *
     * Sends the HELO command to the smtp server.
     * This makes sure that we and the server are in
     * the same known state.
     *
     * Implements from rfc 821: HELO <SP> <domain> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE ERROR  : 500, 501, 504, 421
     */
    function Hello($host="") {
        $this->error = null; # so no confusion is caused
        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Hello() without being connected");
            return false;
        }
        # if a hostname for the HELO wasn't specified determine
        # a suitable one to send
        if(empty($host)) {
            # we need to determine some sort of appopiate default
            # to send to the server
            $host = "localhost";
        }

		$this->send_line("HELO " . $host);

        //fputs($this->smtp_conn,"HELO " . $host . $this->CRLF);
		
        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($code != 250) {
            $this->error =
                array("error" => "HELO not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            return false;
        }
        $this->helo_rply = $rply;

        return true;
    }

    function AuthHello($host="",$user="",$pass="") {

        $this->error = null; # so no confusion is caused
        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Hello() without being connected");
            return false;
        }
        # if a hostname for the HELO wasn't specified determine
        # a suitable one to send
        if(empty($host)) {
            # we need to determine some sort of appopiate default
            # to send to the server
            $host = "localhost";
        }

        $this->send_line("EHLO ".$host);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);
        if($code != 250) {
            $this->error =
                array("error" => "EHLO not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            return false;
        }
        $this->helo_rply = $rply;
        $this->send_line("AUTH LOGIN");
        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($code != 334) {
            $this->error =
                array("error" => "AUTH LOGIN not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            return false;
        }

        $this->send_line(base64_encode($user));
        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($code != 334) {
            $this->error =
                array("error" => "USER not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            return false;
        }

        $this->send_line(base64_encode($pass));
        $rply = $this->get_lines();
        $code = substr($rply,0,3);
        if($code != 235) {
            $this->error =
                array("error" => "PASSWORD not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            return false;
        }
        return true;
    }

    /*
     * MailFrom($from)
     *
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command.
     *
     * Implements rfc 821: MAIL <SP> FROM:<reverse-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,421
     */
    function MailFrom($from) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Mail() without being connected");
            return false;
        }

        $this->send_line("MAIL FROM:" . $from);
		
        //fputs($this->smtp_conn,"MAIL FROM:" . $from . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($code != 250) {
            $this->error =
                array("error" => "MAIL not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            return false;
        }
        return true;
    }



    /*
     * Quit($close_on_error=true)
     *
     * Sends the quit command to the server and then closes the socket
     * if there is no error or the $close_on_error argument is true.
     *
     * Implements from rfc 821: QUIT <CRLF>
     *
     * SMTP CODE SUCCESS: 221
     * SMTP CODE ERROR  : 500
     */
    function Quit($close_on_error=true) {
        $this->error = null; # so there is no confusion

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Quit() without being connected");
            return false;
        }

        # send the quit command to the server
        $this->send_line("QUIT");
        //fputs($this->smtp_conn,"quit" . $this->CRLF);

        # get any good-bye messages
        $byemsg = $this->get_lines();

        $rval = true;
        $e = null;

        $code = substr($byemsg,0,3);
        if($code != 221) {
            # use e as a tmp var cause Close will overwrite $this->error
            $e = array("error" => "SMTP server rejected quit command",
                       "smtp_code" => $code,
                       "smtp_rply" => substr($byemsg,4));
            $rval = false;
        }

        if(empty($e) || $close_on_error) {
            $this->Close();
        }

        return $rval;
    }

    /*
     * Recipient($to)
     *
     * Sends the command RCPT to the SMTP server with the TO: argument of $to.
     * Returns true if the recipient was accepted false if it was rejected.
     *
     * Implements from rfc 821: RCPT <SP> TO:<forward-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250,251
     * SMTP CODE FAILURE: 550,551,552,553,450,451,452
     * SMTP CODE ERROR  : 500,501,503,421
     */
    function Recipient($to) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Recipient() without being connected");
            return false;
        }

        $this->send_line("RCPT TO:" . $to);
        //fputs($this->smtp_conn,"RCPT TO:" . $to . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($code != 250 && $code != 251) {
            $this->error =
                array("error" => "RCPT not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            return false;
        }
        return true;
    }

    function get_lines() {
        $data = "";
        while($str = fgets($this->smtp_conn,515)) {
            $data .= $str;
            # if the 4th character is a space then we are done reading
            # so just break the loop
            if(substr($str,3,1) == " ") { break; }
       }
         if($this->do_debug) {
			$tmp = ereg_replace("(\r|\n)","",$data);
			echo("<font style=\"font-size:12px; font-family: Courier New; background-color: white; color: black;\"><- <b>".htmlspecialchars($tmp)."</b></font><br>\r\n");flush();
         }
        return $data;
    }

    function send_line($data) {
		fputs($this->smtp_conn,$data.$this->CRLF);
		if($this->do_debug) {
			$data = htmlspecialchars($data);
			echo("<font style=\"font-size:12px; font-family: Courier New; background-color: white; color: black;\">-> ".nl2br($data)."</font><br>\r\n");flush();
		}
    }

}

?>
