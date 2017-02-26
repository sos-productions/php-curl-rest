<?php

      /**
      *
      *   Defines the command to perform FTP RNTO 
      * 
        @file RNTO.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.0 - 23.02.2017 - renameTo  from ../FTP.php is deported here
      *		1.0.0 - 22.02.2017 - First version  
      **/
      class FTP_RNTO 
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
	    @name FTP_RNTO
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_RNTO(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="RNTO";
		  $this->version="1.2.0";
		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
      
	  /**
	    * Rename to
	    *
	    * @name renameTo
	    * @param $filename string - file's name
	    * @param $success boolean - the output flag, will be set to true if command succeed
	    * @return array
	    */
	    public function renameTo($filename,&$success) 
	    {
	    
		$plugin=&$this->plugin;
		$options=&$plugin->options;
		$ch=&$plugin->_get_curl_handle();
		  
		  
		  $items=$plugin->ftp_curl_rename_to($ch,$options,$filename,$success);
		  
		  if($success) 
		  {
		      $plugin->logMessage("FILE SUCCESSFULLY RENAMED INTO: $filename ");
		  }
		  
		  return $items;
	    }
		    
	  /**
	    @brief Execute the RNTO Command
	    @name FTP_RNTO_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string remote file
	    @param $extra not used
	    @return string
	    **/	    
	    function FTP_RNTO_run(&$success,$data,$extra=NULL) {
	   //------------------------------------------------------	

		   $command=$this->command;
		   
		   $inFile=$data;
		   $items=$this->renameTo($inFile,$success);
		   
		   return $items;
	    }
      
      
      }


?>