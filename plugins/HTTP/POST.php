<?php

    /**
      *
      *   Defines the command to perform HTTP POST on passed resource reference, data can be in form allowed by curl_setopt CURLOPT_POSTFIELDS
      * 
        @file POST.php
      * @package php_curl_rest/plugins/HTTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 22.02.2017 - First version  
      **/
      
      class HTTP_POST
      {
      
	  /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $command;
	    
	    /** @var HTTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
      
          /**
	    @name HTTP_POST
	    @param HTTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function HTTP_POST(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->command="POST";
	    }
      
          /**
	    @brief Execute the POST Command
	    @name HTTP_POST_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string resource
	    @param $extra array data
	    @return array
	    **/
	    function HTTP_POST_run(&$success,$data,$extra) {
	   //----------------------------------------------
		   $resource=$data;
		   $items=$this->plugin->postResponse($resource, $extra, $success) ;
				    
		   return $items;
	    }
      
      
      }


?>