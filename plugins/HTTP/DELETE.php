<?php

    /**
      *
      *   Defines the command to perform HTTP DELETE on passed resource reference
      * 
        @file DELETE.php
      * @package php_curl_rest/plugins/HTTP
      * @version 1.1.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.1.0 - 26.02.2017 -  delete from ../HTTP.php is deported here
      *		1.0.0 - 22.02.2017 - First version  
      **/
      

      class HTTP_DELETE
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
	    @name HTTP_DELETE
	    @param HTTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function HTTP_DELETE(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="DELETE";
		  $this->version="1.1.0";
		  $this->date="26.02.2017";
		  $this->authors="Michael C Brant,Olivier Lutzwiller";  
	    }
	    
	    /**
	    * Method to execute DELETE on server
	    * 
	    * @param string $action
	      @param $success bool will be set to true if command succeed, else false
	    * @return rest_client
	    * @throws Exception
	    */
	    public function delete($action = NULL,&$success) {
	    
		$plugin=&$this->plugin;
		if (!is_string($action)) {
		    throw new Exception('Nothing passed for data parameter - ' . __METHOD__ . ' Line ' . __LINE__);
		}
		$plugin->_curl_setup();
		$plugin->_set_request_url($action);
		
		$ch=&$plugin->_get_curl_handle();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); // explicitly set the method to DELETE
		
		$success=false;
		$plugin->_curl_exec();
		$success=true;
		
		return $plugin;
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
		   $this->delete($resource,$success);
		   $items=$this->plugin->get_response_body();
    
		   return $items;
	    }
      
      
      }


?>