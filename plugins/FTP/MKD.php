<?php

      /**
      *
      *   Defines the command to perform FTP MKD 
      * 
        @file MKD.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.0 - 23.02.2017 - createDirectory from ../FTP.php is deported here
      *		1.0-0 - 22.02.2017 - First version  
      **/
      
      class FTP_MKD 
      {
      
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $name;
	    
	    /** @var FTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;

  	    /** @var string date */
	    public $date;
	    
	    /** @var string authors */
	    public $authors;   
	    
          /**
	    @name FTP_MKD
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_MKD(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="MKD";
		  $this->version="1.2.0";
		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
	    
	    /**
	    * create a new directory and returns its parent entries
	    *
	    * @name createDirectory
	    * @note will consider a success if directory already exists else an error if in depth directories when $max_depth is set to 0
	    * @access public
	    * @since 1.0
	    * @param string $directory - the remote directory path 
	    * @param int $max_depth  - max level depth
	    * @param bool $success - true if success, false if failure, set automaticaly
	    * @return array
	    */
	      public function createDirectory($directory,$max_depth=0,&$success) 
	      {
	      	  $plugin=&$this->plugin;
		  $options=&$plugin->options;
		  $ch=&$plugin->_get_curl_handle();
	      
		  $ftp_url=$plugin->ftp_curl_url();
		  $plugin->ftp_curl_set_connection_settings($ftp_url);
		  
		  $remoteDir=rtrim($plugin->ftp_curl_resolve_path($directory),"/")."/";
		
		  
		  $items=$plugin->ftp_curl_mkd($ch,$options,$remoteDir,$max_depth,$success);
		  
		  
		  
		  if($success) 
		  {
			$plugin->logMessage("DIR SUCCESSFULLY CREATED: $remoteDir ");
		  }
		  
		  return $items;	
	    }
		    
	    /**
	    @brief Execute the MKD Command
	    @name FTP_MKD_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string remote Directory
	    @param $extra not used
	    @return string
	    **/
	    function FTP_MKD_run(&$success,$data,$extra=NULL) {
	   //------------------------------------------------------	
		   $remoteDir=$data;
		   $command=$this->command;
		   
		    if(strtoupper($data) == "-P") { //Recursive
			$remoteDir=$extra; 
			$max_depth=CURL_FTP_MAXDEPTH;
		    }else {
			$remoteDir=$data;
			$max_depth=0;
		    }
		    $items=$this->createDirectory($remoteDir,$max_depth,$success);
				    
		   return $items;
	    }
      
      
      }


?>