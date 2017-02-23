<?php

      /**
      *
      *   Defines the command to perform FTP MKD 
      * 
        @file DOWNLOAD.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 22.02.2017 - First version  
      **/
      
      class FTP_MKD 
      {
      
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $command;
	    
	    /** @var FTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
      
          /**
	    @name FTP_MKD
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_MKD(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->command="MKD";
		  $this->version="1.0.0";
	    }
      
	    function FTP_MKD_run(&$success,$data,$extra) {
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
		    $items=$this->plugin->createDirectory($remoteDir,$max_depth,$success);
				    
		   return $items;
	    }
      
      
      }


?>