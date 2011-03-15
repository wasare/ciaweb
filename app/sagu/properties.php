<?php
  // Properties.php implements a mecanism to store session related data
  // in a file based on client IP addresses. The properties are maintained
  // only for registered IP addresses.
  //
  // The file requires common.php

  class Properties
  {
    var $fname;
    var $props;

    // ----------------------------------------------------------------
    // After creation of a Properties object with new, this function must
    // be called in order to initialize the instance. Basically the function 
    // defines the filename based on the remote IP address, which is translated
    // from dot notation aaa.bbb.ccc.ddd into the form aaa_bbb_ccc_ddd
    // By default the files are prefixed by the string "SAGU.properties." followed
    // by the translated IP. The default directory is /tmp.
    //
    // After the filename has been determined, the function tries to load the
    // the properties.
    // ----------------------------------------------------------------
    function Create()
    { global $REMOTE_ADDR;

      $host = ereg_replace("\.","_",$REMOTE_ADDR);

      $this->fname = "/tmp/SAGU.properties.$host";

      @$this->Load();  // use @ to ignore warning in case of file does not exist

      $this->Set("host",$host);  // initialize the array
    }

    // ----------------------------------------------------------------
    // Tries to load the properties specified by $fname
    // Each line of the properties file has to be of the format:
    //
    //   key=url_encoded_value
    //
    // Empty lines are ignored.
    // ----------------------------------------------------------------
    function Load()
    {
      $file = @fopen($this->fname,"r");

      if ( $file )
      {
        while ( $line = fgets($file,1024) )
        {
          list ( $key, $value ) = explode("=",trim($line));

          if ( !$key )
            continue;

          $this->props[$key] = $value;
        }

        fclose($file);
      }
    }

    // ----------------------------------------------------------------
    // Tries to load the properties specified by $fname
    // Each line of the properties file has to be of the format:
    //
    //   key=url_encoded_value
    //
    // Empty lines are ignored.
    // ----------------------------------------------------------------
    function Save()
    {
      $file = @fopen($this->fname,"w");

      if ( $file )
      {
        while ( list( $key, $val ) = each( $this->props ) )
          fputs($file,"$key=". urlencode($val) . "\n");
      }
    }

    // ----------------------------------------------------------------
    // Delete the properties file (should be called at logout)
    // ----------------------------------------------------------------
    function Cleanup()
    {
      @unlink($this->fname);
    }

    // ----------------------------------------------------------------
    // Get a property value with a user defined default value
    // ----------------------------------------------------------------
    function Get($name,$default='')
    {
      $value = $this->props[$name];

      if ( defined($value) )
        $value = $default;

      return $value;
    }

    // ----------------------------------------------------------------
    // Sets a property value
    // ----------------------------------------------------------------
    function Set($name,$value)
    {
      $this->props[$name] = $value;
    }

    // ----------------------------------------------------------------
    // Prints the content of the properties
    // ----------------------------------------------------------------
    function DumpContent()
    {
      while ( list( $key, $val ) = each( $this->props ) )
        echo("$key = $val<br>\n");
    }
  }

  $properties = new Properties;

  $properties->Create();
?>
