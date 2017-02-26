<?php

      /**
      *
      *   Defines the command to perform FTP LS 
      * 
        @file LS.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.0 - 23.02.2017 - changeDir from ../FTP.php is deported here
      *		1.0.0 - 22.02.2017 - First version  
      **/
      
      class FTP_LS 
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
	    @name FTP_LS
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_LS(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="LS";
		  $this->version="1.2.0";
		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
	    
	     
	   /**
	  * Change the current directory and returns list of files
	  *
	  * @name changeDir
	  * @access public
	  * @since 1.1.0
	  * @param string $directory - remote directory path to create
	  * @param bool $success - true if success, false if failure, set automaticaly
	  * @return array
	  */
	  public function changeDir($directory,&$success)
	    {
		  
		  $plugin=&$this->plugin;
		  $options=&$plugin->options;
		  $ch=&$plugin->_get_curl_handle();
		  
		  $ftp_url=$plugin->ftp_curl_url();
		  
		  $plugin->ftp_curl_set_connection_settings($ftp_url);
		  
		  $max_depth=0; //No recusivity
		  
		  $remoteDir=$directory;
		  $remoteDir=$plugin->ftp_curl_resolve_path($remoteDir);
		  
		  
		  $items=$plugin->ftp_curl_callback_on_dir($ch,$options,$remoteDir,"inspect_items",$max_depth,$success);
		  
		  
		  
		  if($success) {
			$plugin->logMessage("Current directory is now: $remoteDir ");
		    }
		  return $items;
	    }
      
	  /**
	    @brief Execute the LS Command
	    @name FTP_LS_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string remote Directrory
	    @param $extra not used
	    @return string
	    **/
	    function FTP_LS_run(&$success,$data,$extra=NULL) {
	   //------------------------------------------------------	
		   $remoteDir=$data;
		   $command=$this->command;
		   
		   if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->plugin->logMessage("<dl><dt><u><b>$command $remoteDir</b> gives</u></dt>");
		    //MLSD can not be called directly, we use CWD instead who provide list of files
		   $items=$this->changeDir($remoteDir,$success);
		   if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->plugin->logMessage("</dl>"); 
		   return $items;
	    }
      
      
      }


?>