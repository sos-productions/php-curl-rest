<?php

      /**
      *
      *   Defines the command to perform FTP RMD 
      * 
        @file RMD.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.0 - 23 02 2017 - removeDirectory deported from FTP.php
      *		1.0.0 - 22.02.2017 - First version  
      **/
      
      class FTP_RMD 
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
	    @name FTP_RMD
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_RMD(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="RMD";
		  $this->version="1.2.0";
		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
	    
	    /**
	    * removes a given directory and returns its parent entries
	    *
	    * @name removeDirectory
	    * @access public
	    * @since 1.1
	    * @param string $directory - the remote directory path 
	    * @param bool $success - true if success, false if failure, set automaticaly
	    * @return array
	    */
	      public function removeDirectory($directory,$max_depth=0,&$success) 
	      {

		  $plugin=&$this->plugin;
		  $options=&$plugin->options;
		  $ch=&$plugin->_get_curl_handle();
	      
		  $ftp_url=$plugin->ftp_curl_url();
		  $plugin->ftp_curl_set_connection_settings($ftp_url);
		 	  
		  $remoteDir=$directory;
		  $items=$plugin->ftp_curl_rmd($ch,$options,$remoteDir,$max_depth,$success);
		  
		  if($success) {
		      $plugin->logMessage("DIR SUCCESSFULLY DELETED: $remoteDir ");
		  }
		  return $items;	
	    }
	    
      	  /**
	    @brief Execute the RMD Command
	    @name FTP_RMD_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string remote Directrory or -R to indicate will all subdirectories or even if directory is not empty
	    @param $extra string remote Directory and its branches to be removed
	    @return string
	    **/
	    function FTP_RMD_run(&$success,$data,$extra) {
	   //------------------------------------------------------	
		    $remoteDir=$data;
		    if(strtoupper($data) == "-R") {
			$remoteDir=$extra; 
			$max_depth=CURL_FTP_MAXDEPTH;
		    }else {
			$remoteDir=$data;
			$max_depth=0;
		    }
		    $items=$this->removeDirectory($remoteDir,$max_depth,$success);
		    
		   return $items;
	    }
      
      
      }


?>