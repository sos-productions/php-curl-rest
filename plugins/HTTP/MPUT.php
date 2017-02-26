<?php

    /**
      *
      *   Defines the command to perform HTTP M(ultiple)PUT on passed resource reference, data can be in form allowed by curl_setopt CURLOPT_POSTFIELDS
      * 
        @file MPUT.php
      * @package php_curl_rest/plugins/HTTP
      * @version 1.0.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.0 - 26.02.2017 - First version  
      **/
      
      class HTTP_MPUT
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
	    @name HTTP_MPUT
	    @param HTTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function HTTP_MPUT(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="MPUT";
		  $this->version="1.0.0";
		  $this->date="26.02.2017";
		  $this->authors="Michael C Brant,Olivier Lutzwiller";  
	    }
	 
	 
	 
	  /**
	  * Method to perform multiple PUT actions using curl_multi_exec. The max_handles parameter is optional and can be used to change the default maximum number of allowable multi_exec handles.
	  * 
	  * @param array $actions
	  * @param array $data
	  * @param integer $max_handles
	  * @param boolean $success
	  * @return rest_client
	  * @throws Exception
	  */
	  public function multi_put($actions = NULL, $data = NULL, $max_handles = NULL,&$success) {
	  
	      $plugin=&$this->plugin;
	  
	      if (!is_array($actions)) {
		  throw new Exception('A non-array value was passed for actions parameter - ' . __METHOD__ . ' Line ' . __LINE__);
	      }
	      if (!is_array($data)) {
		  throw new Exception('A non-array value was passed for data parameter - ' . __METHOD__ . ' Line ' . __LINE__);
	      }
	      if (!is_null($max_handles)) {
		  $plugin->set_max_multi_exec_handles($max_handles);
	      }
	      
	      $handles_needed = count($actions);
	      $data_count = count($data);

	      // verify that the number of handles requested does not exceed the max number of handles
	      if ($handles_needed > $plugin->_get_max_multi_exec_handles()) {
		  throw new Exception('The number of handles requested exceeds maximum allowed number of handles - ' . __METHOD__ . ' Line ' . __LINE__); 
	      }
	      
	      // verify that the number of data elements matches the number of action elements
	      if ($handles_needed !== $data_count) {
		  throw new Exception('The number of actions requested does not match the number of data elements provided - ' . __METHOD__ . ' Line ' . __LINE__); 
	      } 
	      
	      // set up curl handles
	      $plugin->_curl_multi_setup($handles_needed);
	      $plugin->_set_multi_request_urls($actions);
	      $plugin->_set_multi_request_data($data);
	      foreach($plugin->_get_curl_multi_handle_array() as $curl) {
		  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT'); // explicitly set the method to PUT 
	      }
	      
	      $success=false;
	      $plugin->_curl_multi_exec();
	      $success=true;
	      
	      return $plugin;

	  }
    

          /**
	    @brief Execute the MPUT Command
	    @name HTTP_MPUT_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string resource
	    @param $extra array data
	    @param $conf integer max handles
	    @return array
	    **/
	    function HTTP_MPUT_run(&$success,$data,$extra,$conf) {
	   //----------------------------------------------
		   $actions = $data; 
		   $data = $extra; 
		   $max_handles=$conf;
		   $this->multi_put($actions, $data, $max_handles, $success) ;
		   $items=$this->plugin->get_multi_response_bodies();    
		   return $items;
	    }
      
      
      }


?>