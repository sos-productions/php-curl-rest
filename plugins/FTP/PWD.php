<?php

      /**
      *
      *   Defines the command to perform FTP PWD 
      * 
        @file PWD.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.0 - 23.02.2017 - printWorkingDir  from ../FTP.php is deported here
      *		1.0.0 - 22.02.2017 - First version    
      **/
      
      
      class FTP_PWD 
      {
      
	    /** @var string command Name of the command in Upercase should be the same after _ in classname */
	    public $name;
	    
	    /** @var FTP_curl_client plugin  instance of the plugin this command is issued */
	    protected $plugin;
	    
	    /** @var string version  the version of the command , should be compatible with version_compare() format*/
	    public $version;
 	    
  	    /** @var string date */
	    public $date;
	    
	    /** @var string authors */
	    public $authors;   
	        
          /**
	    @name FTP_PWD
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_PWD(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="PWD";
		  $this->version="1.2.0";
 		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
      
      
          /**
	    * returns current working directory 
	    *
	    * @name printWorkingDir
	    * @access public
	    * @since 1.1.0
	    * @version 1.1
	    * @param $success bool - true if success, false if failure, set automaticaly
	    * @return string
	    */
	    public function printWorkingDir(&$success) 
	    {
		$plugin=&$this->plugin;
		$options=&$plugin->options;
		$ch=&$plugin->_get_curl_handle();
		
		  return $plugin->ftp_curl_quote($ch,$options,"PWD",$success);	 
	    }
	    
	    /**
	    @brief Execute the PWD Command
	    @name FTP_PWD_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data not used
	    @param $extra not used
	    @return string
	    **/
	    function FTP_PWD_run(&$success,$data=NULL,$extra=NULL) {
	   //---------------------------------------------	
		    $items=$this->printWorkingDir($success); 
		   return $items;
	    }

      }


?>