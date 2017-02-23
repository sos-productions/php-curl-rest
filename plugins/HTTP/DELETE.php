<?php

    /**
      *
      *   Defines the command to perform HTTP DELETE on passed resource reference
      * 
        @file DELETE.php
      * @package php_curl_rest/plugins/HTTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 22.02.2017 - First version  
      **/
      

      class HTTP_DELETE
      {
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $command;
	    
	    /** @var HTTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
      
      
	    /**
	    @name HTTP_DELETE
	    @param HTTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function HTTP_DELETE(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->command="DELETE";
		  $this->version="1.0.0";
	    }
      
	    /**
	    @brief Execute the DELETE Command
	    @name HTTP_DELETE_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string resource
	    @param $extra not used
	    @return string
	    **/
	    function HTTP_DELETE_run(&$success,$data,$extra=NULL) {
	   //-----------------------------------------------
		   
		   $resource=$data;
		   $items=$this->plugin->deleteResponse($resource, $success); 
				    
		   return $items;
	    }
      
      
      }


?>