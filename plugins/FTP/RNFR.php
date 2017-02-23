<?php

      /**
      *
      *   Defines the command to perform FTP RNFR 
      * 
        @file RNFR.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 22.02.2017 - First version  
      **/
      

      class FTP_RNFR 
      {
      
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $command;
	    
	    /** @var FTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
      
          /**
	    @name FTP_RNFR
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_RNFR(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->command="RNFR";
		  $this->version="1.0.0";
	    }
      
	    function FTP_RNFR_run(&$success,$data,$extra=NULL) {
	   //------------------------------------------------------	
		   $remoteDir=$data;
		   $command=$this->command;
		   
		   $inFile=$data;
		   $items=$this->plugin->renameFrom($inFile,$success);
		   
		   return $items;
	    }
      
      
      }


?>