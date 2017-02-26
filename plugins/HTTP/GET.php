<?php

    /**
      *
      *   Defines the command to perform HTTP GET on URL [hostname].[uri_base].[resource parameter passed to method]]
      * 
        @file GET.php
      * @package php_curl_rest/plugins/HTTP
      * @version 1.1.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.1.0 - 25.02.2017 - get from ../HTTP.php is deported here
      *		1.0.0 - 22.02.2017 - First version  
      **/
      
      class HTTP_GET
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
	    @name HTTP_GET
	    @param HTTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function HTTP_GET(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="GET";
		  $this->version="1.1.0";
		  $this->date="25.02.2017";
		  $this->authors="Michael C Brant,Olivier Lutzwiller";  
	    }
      
	  /**
	    * Method to execute GET on server
	    * 
	    * @since 1.1.0
	    * @param string $action
	    * @param boolean $success
	    * @return rest_client
	    * @throws Exception
	    */
	    public function get($action = NULL,&$success) {
	    
		$plugin=&$this->plugin;
		
		if (!is_string($action)) {
		    throw new Exception('A non-string value was passed for action parameter - ' . __METHOD__ . ' Line ' . __LINE__);
		}
		$plugin->_curl_setup();
		$plugin->_set_request_url($action);
		
		$ch=&$plugin->_get_curl_handle();
		
		//Some extras that do a big diff to avoid to get a blank page specially when grabing homepage, we don't know the real file index.(htm|html|asp|php..) 
		curl_setopt($ch , CURLOPT_FOLLOWLOCATION, 1);
		      
		curl_setopt($ch, CURLOPT_HTTPGET, true); // explicitly set the method to GET
		
		$success=false;
		$plugin->_curl_exec();
		$success=true;
		
		return $plugin;
	    }
      
      
          /**
	    @brief Execute the GET Command
	    @name HTTP_GET_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string Server Filename
	    @param $extra not used
	    **/
	    function HTTP_GET_run(&$success,$data,$extra=NULL) {
	   //-----------------------------------------------
		  $resource=$data;
		  $this->get($resource,$success);
		  $items=$this->plugin->get_response_body();
		  return $items;
	    }
      
      
      }


?>