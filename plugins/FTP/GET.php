<?php

      /**
      *
      *   Defines the command to perform FTP GET 
      * 
        @file GET.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 22.02.2017 - First version  
      **/
      
      class FTP_GET
      {
      
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $command;
	    
	    /** @var FTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
      
          /**
	    @name FTP_GET
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_GET(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->command="GET";
		  $this->version="1.0.0";
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
		  $items=$this->plugin->downloadFile($serverFilename,$localFilename,$success);
				    
		   return $items;
	    }
      
      
      }


?>