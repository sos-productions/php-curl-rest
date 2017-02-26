<?php

      /**
      *
      *   Defines the command to perform FTP GET 
      * 
        @file GET.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.0 - 23.02.2017 - downloadFile from ../FTP.php is deported here
      *		1.0.0 - 22.02.2017 - First version    
      **/
      
      class FTP_GET
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
	    @name FTP_GET
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_GET(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="GET";
		  $this->version="1.2.0";
		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
      
      
          /**
	    * download file from the server 
	    *
	    * @brief file with the matching name should exist in the current directory of the server
	    * @name downloadFile
	    * @access public
	    * @since 1.0
	    * @version 1.0
	    * @param $filename string - the server file name 
	    * @param $success bool - true if success, false if failure, set automaticaly
	    * @return array
	    */
	    public function downloadFile($serverFilename,$localFile,&$success) 
	    {
		  $plugin=&$this->plugin;
		  $options=&$plugin->options;
		  $ch=&$plugin->_get_curl_handle();
		
		  $items=$plugin->ftp_curl_download($ch,$options,$serverFilename,$localFile,$success);
		    
		  if($success) 
		  {
		       $plugin->logMessage("FILE SUCCESSFULLY DOWNLOADED FROM $serverFilename TO LOCAL $localFile");
		  }
		  return $items;
	    }
			
      
	    /**
	    @brief Execute the GET Command
	    @name FTP_GET_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string Server Filename
	    @param $extra string local Filename
	    **/
	    function FTP_GET_run(&$success,$data,$extra) {
	   //----------------------------------------------
		  $serverFilename=$data;
		  $localFilename=$extra;
		  $items=$this->downloadFile($serverFilename,$localFilename,$success);
				    
		   return $items;
	    }
      
      
      }


?>