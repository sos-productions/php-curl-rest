<?php

    /**
      *
      *   Defines the command to perform HTTP HEAD 
      * 
        @file HEAD.php
      * @package php_curl_rest/plugins/HTTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 26.02.2017 - head function from ../HTTP.php is deported here
      *		1.1.0 - 25.02.2017 - attribute command changed to name
      *		1.0.0 - 22.02.2017 - First version  
      **/
      
      class HTTP_HEAD
      {
      
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $name;
	    
	    /** @var HTTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
	    
  	    /** @var string date */
	    public $date;
	    
	    /** @var string authors */
	    public $authors;   
	    
          /**
	    @name HTTP_HEAD
	    @param HTTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function HTTP_HEAD(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="HEAD"; 
		  $this->version="1.2.0";
		  $this->date="26.02.2017";
		  $this->authors="Michael C Brant,Olivier Lutzwiller";  
	    }
      
      
          /**
	  * Method to execute HEAD on server
	  * 
	  * @param string $action
	  * @return rest_client
	  * @throws Exception
	  */
	  public function head($action = NULL,&$success) {
	  
	      $plugin=&$this->plugin;
	  
	      if (!is_string($action)) {
		  throw new Exception('A non-string value was passed for action parameter - ' . __METHOD__ . ' Line ' . __LINE__);
	      }
	      $plugin->_curl_setup();
	      $plugin->_set_request_url($action);
	      
	      
	      $ch=&$plugin->_get_curl_handle();
	      curl_setopt($ch, CURLOPT_HTTPGET, true); // explicitly set the method to GET
	      curl_setopt($ch, CURLOPT_NOBODY, true); // set as HEAD request
	      
	      $success=false;
	      $plugin->_curl_exec();
	      $success=true;
		
	      return $plugin;
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
		   $this->head($resource, $success);
		   $items=$this->plugin->get_response_body();		    
		   return $items;
	    }
      
      
      }


?>