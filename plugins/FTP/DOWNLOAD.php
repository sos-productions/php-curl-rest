<?php

      /**
      *
      *   Defines the command to perform FTP DOWNLOAD 
      * 
        @file DOWNLOAD.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.0 - 23.02.2017 - downloadFileContent from ../FTP.php is deported here
      *		1.0-0 - 22.02.2017 - First version    
      **/
      
      class FTP_DOWNLOAD 
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
	    @name FTP_DOWNLOAD
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_DOWNLOAD(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="DOWNLOAD";
		  $this->version="1.2.0";
		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
      
         /**
	    * Retrieve content of a remote file 
	    *
	    * @brief file with the matching name should exist in the current directory of the server
	    * @name downloadFileContent
	    * @access public
	    * @since 1.0
	    * @version 1.0
	    * @param $filename string - the server file name 
	    * @param $success bool - true if success, false if failure, set automaticaly
	    * @return string
	    */
	    public function downloadFileContent($serverFilename,&$success) 
	    {
		  $plugin=&$this->plugin;
		  $options=&$plugin->options;
		  $ch=&$plugin->_get_curl_handle();
		
		  $items=$plugin->ftp_curl_download_content($ch,$options,$serverFilename,$success);
		  
		  if($success) 
		  {
		      
		      $plugin->logMessage("CONTENT SUCCESSFULLY DOWNLOADED FROM $serverFilename");
		  }
		  return $items;
	    }
      
	  /**
	    @brief Execute the DOWNLOAD Command
	    @name FTP_DOWNLOAD_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string Server Filename
	    @param $extra not used
	    **/
	    function FTP_DOWNLOAD_run(&$success,$data,$extra=NULL) {
	   //------------------------------------------------------	

		    $serverFilename=$data;
		    $items=$this->downloadFileContent($serverFilename,$success);
				    
		   return $items;
	    }
      
      
      }


?>