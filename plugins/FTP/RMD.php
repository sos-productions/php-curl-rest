<?php

      /**
      *
      *   Defines the command to perform FTP RMD 
      * 
        @file RMD.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 22.02.2017 - First version  
      **/
      
      class FTP_RMD 
      {
      
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $command;
	    
	    /** @var FTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
      
          /**
	    @name FTP_RMD
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_RMD(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->command="RMD";
		  $this->version="1.0.0";
	    }
      
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
		    $items=$this->plugin->removeDirectory($remoteDir,$max_depth,$success);
		    
		   return $items;
	    }
      
      
      }


?>