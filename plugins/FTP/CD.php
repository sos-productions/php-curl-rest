<?php

      /**
      *
      *   Defines the command to perform FTP CD
      * 
        @file CD.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 22.02.2017 - First version  
      **/
      
      class FTP_CD 
      {
      
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $command;
	    
	    /** @var FTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
      
          /**
	    @name FTP_CD
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_CD(&$plugin) {
	    //-----------------
		  $this->plugin=&$plugin;
		  $this->command="CD";
		  $this->version="1.0.0";
	    }
      
	    function FTP_CD_run(&$success,$data,$extra=NULL) {
	   //------------------------------------------------------	
		   $remoteDir=$data;
		   $command=$this->command;
		   if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->plugin->logMessage("<dl><dt><u><b>$command $remoteDir</b> gives</u></dt>");
		   $items=$this->plugin->changeDir($remoteDir,$success);
		   if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->plugin->logMessage("</dl>");
		   return $items;
	    }
      
      
      }


?>