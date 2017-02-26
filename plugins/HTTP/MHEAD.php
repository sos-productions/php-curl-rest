<?php

    /**
      *
      *   Defines the command to perform HTTP M(ultiple)HEAD on passed resource reference, data can be in form allowed by curl_setopt CURLOPT_HEADFIELDS
      * 
        @file MHEAD.php
      * @package php_curl_rest/plugins/HTTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 26.02.2017 - First version  
      **/
      
      class HTTP_MHEAD
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
	    @name HTTP_MHEAD
	    @param HTTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function HTTP_MHEAD(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="MHEAD";
		  $this->version="1.0.0";
		  $this->date="26.02.2017";
		  $this->authors="Michael C Brant,Olivier Lutzwiller";  
	    }
	      
	  /**
	  * Method to perform multiple HEAD actions using curl_multi_exec. The max_handles parameter is optional and can be used to change the default maximum number of allowable multi_exec handles.
	  * 
	  * @param array $actions
	  * @param integer $max_handles
	    @param $success bool will be set to true if command succeed, else false
	  * @return rest_client
	  * @throws Exception
	  */
	  public function multi_head($actions = NULL, $max_handles = NULL,&$success) {
	  
	      $plugin=&$this->plugin;
	      
	      if (!is_array($actions)) {
		  throw new Exception('A non-array value was passed for actions parameter - ' . __METHOD__ . ' Line ' . __LINE__);
	      }

	      if (!is_null($max_handles)) {
		  $plugin->set_max_multi_exec_handles($max_handles);
	      }
	      
	      $handles_needed = count($actions);

	      // verify that the number of handles requested does not exceed the max number of handles
	      if ($handles_needed > $plugin->_get_max_multi_exec_handles()) {
		  throw new Exception('The number of handles requested exceeds maximum allowed number of handles - ' . __METHOD__ . ' Line ' . __LINE__); 
	      }
	      
	      // set up curl handles
	      $plugin->_curl_multi_setup($handles_needed);
	      $plugin->_set_multi_request_urls($actions);
	      foreach($plugin->_get_curl_multi_handle_array() as $curl) {
		   curl_setopt($curl, CURLOPT_HTTPGET, true); // explicitly set the method to GET
		   curl_setopt($curl, CURLOPT_NOBODY, true); // set as HEAD request
	      }
	      $success=false;
	      $plugin->_curl_multi_exec();
	      $success=true;
	      
	      return $plugin;
	  }
      
          /**
	    @brief Execute the MHEAD Command
	    @name HTTP_MHEAD_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string resource
	    @param $extra array data
	    @param $conf integer max handles
	    @return array
	    **/
	    function HTTP_MHEAD_run(&$success,$data,$extra) {
	   //----------------------------------------------
		   $actions = $data; 
		   $max_handles=$éxtra;
		   $this->multi_head($actions, $max_handles, $success) ;
		   $items=$this->plugin->get_multi_response_bodies();    
		   return $items;
	    }
      
      
      }


?>