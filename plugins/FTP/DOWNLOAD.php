<?php

      /**
      *
      *   Defines the command to perform FTP DOWNLOAD 
      * 
        @file DOWNLOAD.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 22.02.2017 - First version  
      **/
      
      class FTP_DOWNLOAD 
      {
      
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $command;
	    
	    /** @var FTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
      
          /**
	    @name FTP_DOWNLOAD
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_DOWNLOAD(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->command="DOWNLOAD";
		  $this->version="1.0.0";
	    }
      
	    function FTP_DOWNLOAD_run(&$success,$data,$extra=NULL) {
	   //------------------------------------------------------	

		    $serverFilename=$data;
		    $items=$this->plugin->downloadFileContent($serverFilename,$success);
				    
		   return $items;
	    }
      
      
      }


?>