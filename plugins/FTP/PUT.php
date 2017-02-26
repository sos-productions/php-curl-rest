<?php

      /**
      *
      *   Defines the command to perform FTP PUT 
      * 
        @file PUT.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.0 - 23.02.2017 - uploadFile from ../FTP.php is deported here
      *		1.0-0 - 22.02.2017 - First version     
      **/
      
      class FTP_PUT
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
	    @name FTP_PUT
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_PUT(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="PUT";
		  $this->version="1.2.0";
		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
	    
	      /**
	    * upload a local file on the server whith a remote name in the current directory and returns its parent entries
	    *
	    * @name uploadFile
	    * @access public
	    * @since 1.0
	    * @version 1.0
	    * @param $localFileName string - the local file name 
	    * @param $serverFilename  string - the remote file name
	    * @param $success bool - true if success, false if failure, set automaticaly
	    * @return array
	    */
	    public function uploadFile($localFileName,$serverFilename,&$success) 
	    {
		  $plugin=&$this->plugin;
		  $options=&$plugin->options;
		  $ch=&$plugin->_get_curl_handle();
		  
		  
		  $ftp_url=$plugin->ftp_curl_url();
		
		  $items=$plugin->ftp_curl_put($ch,$options,$localFileName,$serverFilename,$success);
		  
		  if($success) 
		  {
			$plugin->logMessage("FILE SUCCESSFULLY PUT: $serverFilename into $ftp_url ");
		  }
		  
		  return $items;
	    }
      
        /**
	    @brief Execute the PUT Command
	    @name FTP_PUT_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string local Filename
	    @param $extra string Server Filename
	    **/
	    function FTP_PUT_run(&$success,$data,$extra) {
	   //----------------------------------------------
		   $localFileName=$data;
		   $serverFilename=$extra;
		   $items=$this->uploadFile($localFileName,$serverFilename,$success);
				    
		   return $items;
	    }
      
      
      }


?>