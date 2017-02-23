<?php

    /**
      *
      *   Defines the command to perform HTTP GET on URL [hostname].[uri_base].[resource parameter passed to method]]
      * 
        @file GET.php
      * @package php_curl_rest/plugins/HTTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 22.02.2017 - First version  
      **/
      
      class HTTP_GET
      {
      
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $command;
	    
	    /** @var HTTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
      
          /**
	    @name HTTP_GET
	    @param HTTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function HTTP_GET(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->command="GET";
		  $this->version="1.0.0";
	    }
      
          /**
	    @brief Execute the GET Command
	    @name HTTP_GET_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string Server Filename
	    @param $extra not used
	    **/
	    function HTTP_GET_run(&$success,$data,$extra) {
	   //----------------------------------------------
		  $resource=$data;;
		  $items=$this->plugin->getResponse($resource,$success);
				    
		   return $items;
	    }
      
      
      }


?>