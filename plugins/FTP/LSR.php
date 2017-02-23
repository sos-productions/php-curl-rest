<?php

      /**
      *
      *   Defines the command to perform FTP LSR (recursive listing)
      * 
        @file LSR.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 22.02.2017 - First version  
      **/
      
      class FTP_LSR
      {
      
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $command;
	    
	    /** @var FTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
      
          /**
	    @name FTP_LSR
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_LSR(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->command="LSR";
		  $this->version="1.0.0";
	    }
      
	    function FTP_LSR_run(&$success,$data,$extra) {
	   //------------------------------------------------------	
		   $remoteDir=$data;
		   $command=$this->command;
		   
		    $remoteDir=$data;
		    $max_depth=($extra) ? $extra:CURL_FTP_MAXDEPTH;
		    if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->plugin->logMessage("<dl><dt><u><b>$command $remoteDir</b> gives</u></dt>");
		    $items=$this->plugin->listallItemsRecursively($directory,$max_depth,$success);
		    if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->plugin->logMessage("</dl>");
		   
		   return $items;
	    }
      
      
      }


?>