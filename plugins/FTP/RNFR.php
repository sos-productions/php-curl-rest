<?php

      /**
      *
      *   Defines the command to perform FTP RNFR 
      * 
        @file RNFR.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.0 - 23.02.2017 - renameFrom  from ../FTP.php is deported here
      *		1.0.0 - 22.02.2017 - First version  
      **/
      

      class FTP_RNFR 
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
	    @name FTP_RNFR
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_RNFR(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="RNFR";
		  $this->version="1.2.0";
		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
      
      
        /**
	  * Rename from 
	  *
	  * @name renameFrom 
	  * @param $filename string - file's name
	  * @param $success boolean - the output flag, will be set to true if command succeed
	  * @return array
	  */
	  public function renameFrom($filename,&$success) 
	  {
	  
		$plugin=&$this->plugin;
		$options=&$plugin->options;
		$ch=&$plugin->_get_curl_handle();
		
		$plugin->logMessage("START RENAMING");
		
		$items=$plugin->ftp_curl_rename_from($ch,$options,$filename,$success);
		
		if($success) 
		{
		    $plugin->logMessage("FILE SUCCESSFULLY SELECTED FOR RENAME: $filename ");
		}
		
		return $items;
	  }
	  
	    /**
	    @brief Execute the RNFR Command
	    @name FTP_RNFR_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string remote file
	    @param $extra not used
	    @return string
	    **/
	    function FTP_RNFR_run(&$success,$data,$extra=NULL) {
	   //------------------------------------------------------	
		
		   $command=$this->command;
		   
		   $inFile=$data;
		   $items=$this->renameFrom($inFile,$success);
		   
		   return $items;
	    }
      
      
      }


?>