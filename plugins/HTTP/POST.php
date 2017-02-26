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
      *		1.0.0 - 26.02.2017 - First version  
      **/
      
      class HTTP_POST
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
	    @name HTTP_POST
	    @param HTTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function HTTP_POST(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="POST";
		  $this->version="1.0.0";
		  $this->date="26.02.2017";
		  $this->authors="Michael C Brant,Olivier Lutzwiller";  
	    }
	    
	     /**
	    * Method to exexute POST on server
	    * 
	    * @param mixed $action
	    * @param mixed $data
	    * @param boolean $success
	    * @return rest_client
	    * @throws Exception
	    */
	    public function post($action = NULL, $data = NULL,&$success) {
	    
	    
		$plugin=&$this->plugin;
		
		if (!is_string($action)) {
		    throw new Exception('A non-string value was passed for action parameter - ' . __METHOD__ . ' Line ' . __LINE__);
		}
		if (is_null($data)) {
		    throw new Exception('Nothing passed for data parameter - ' . __METHOD__ . ' Line ' . __LINE__);
		}
		
		$plugin->_curl_setup();
		$plugin->_set_request_url($action);
		$plugin->_set_request_data($data);
		
		
		$ch=&$plugin->_get_curl_handle();
		curl_setopt($ch, CURLOPT_POST, true); // explicitly set the method to POST
		
		$success=false;
		$plugin->_curl_exec();
		$success=true;
		
		return $plugin;
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
		   $this->post($resource, $extra, $success) ;
		   $items=$this->plugin->get_response_body();    
		   return $items;
	    }
      
      
      }


?>