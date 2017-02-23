<?php

    /**
      *
      *   Defines the command to perform HTTP HEAD 
      * 
        @file HEAD.php
      * @package php_curl_rest/plugins/HTTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 22.02.2017 - First version  
      **/
      
      class HTTP_HEAD
      {
      
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $command;
	    
	    /** @var HTTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
      
          /**
	    @name HTTP_HEAD
	    @param HTTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function HTTP_HEAD(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->command="HEAD";
	    }
      
            /**
	    @brief Execute the HEAD Command
	    @name HTTP_HEAD_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string $resource
	    @param $extra string not used
	    @return array
	    **/
	    function HTTP_HEAD_run(&$success,$data,$extra=NULL) {
	   //----------------------------------------------
		   $resource=$data;
		   $items=$this->plugin->headResponse($resource, $success);
				    
		   return $items;
	    }
      
      
      }


?>