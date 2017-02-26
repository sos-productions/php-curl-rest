<?php

    /**
      *
      *   Defines the command to perform FTP DELE 
      * 
        @file DELE.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.0 - 23.02.2017 - deleteFile from ../FTP.php is deported here
      *		1.0.0 - 22.02.2017 - First version  
      **/
      
      class FTP_DELE 
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
	    @name FTP_DELE
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_DELE(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="DELE";
		  $this->version="1.2.0";
		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
      
      
	    /**
	    * Deletes a file 
	    *
	    * @name deleteFile 
	    * @since 1.1.0
	    * @param $file string - file's pathname
	    * @param $success boolean - the output flag, will be set to true if command succeed
	    * @return array
	    */
	    public function deleteFile($file,&$success) 
	    {
	    
		  $plugin=&$this->plugin;
		  $options=&$plugin->options;
		  $ch=&$plugin->_get_curl_handle();
		  
		  
		  $ftp_url=$plugin->ftp_curl_url();
		  $plugin->ftp_curl_set_connection_settings($ftp_url);
		
		  $filepath=$plugin->ftp_curl_resolve_path($file);
		  $items=$plugin->ftp_curl_dele($ch,$options,$filepath,$success);
		  
		  if($success) 
		  {
		      $plugin->logMessage("FILE SUCCESSFULLY DELETED: $filepath ");
		  }
		  
		  return $items;
	    }
	    
	     /**
	    @brief Execute the DELE Command
	    @name FTP_DELE_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string remote file
	    @param $extra not used
	    @return string
	    **/
	    function FTP_DELE_run(&$success,$data,$extra=NULL) {
	   //-----------------------------------------------
		   
		   $remoteFile=$data;
		   $items=$this->deleteFile($remoteFile,$success);
				    
		   return $items;
	    }
      
      
      }


?>