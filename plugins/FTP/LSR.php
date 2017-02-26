<?php

      /**
      *
      *   Defines the command to perform FTP LSR (recursive listing)
      * 
        @file LSR.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.0 - 23.02.2017 - listallItemsRecursively from ../FTP.php is deported here
      *		1.0.0 - 22.02.2017 - First version  
      **/
      
      class FTP_LSR
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
	    @name FTP_LSR
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_LSR(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="LSR";
		  $this->version="1.2.0";
 		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
	    
	    
           /**
	    * List recursively all items of a given directory
	    *
	    * @name listallItemsRecursively
	    * @access public
	    * @since 1.0
	    * @param string $directory - the remote directory path to list
	    * @param int $max_depth  - max level depth
	    * @param bool $success - true if success, false if failure, set automaticaly
	    * @return array
	    */
	    public function listallItemsRecursively($directory,$max_depth=CURL_FTP_MAXDEPTH,&$success) 
	    {
		$plugin=&$this->plugin;
		$options=&$plugin->options;
		$ch=&$plugin->_get_curl_handle();
		
		$ftp_url=$plugin->ftp_curl_url();
		$plugin->ftp_curl_set_connection_settings($ftp_url);
		
		$remoteDir=$directory;
		$remoteDir=$plugin->ftp_curl_resolve_path($remoteDir);
		$items=$plugin->ftp_curl_callback_on_dir($ch,$options,$remoteDir,"inspect_items",$max_depth,$success);
		
		return $items;	
	    }
	    
	   /**
	    @brief Execute the LSR Command
	    @name FTP_LSR_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string remote Directrory
	    @param $extra integar optional depth parameter
	    @return array
	    **/
	    function FTP_LSR_run(&$success,$data,$extra) {
	   //------------------------------------------------------	
	
		   $command=$this->command;
		   
		    $remoteDir=$data;
		    $max_depth=($extra) ? $extra:CURL_FTP_MAXDEPTH;
		    if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->plugin->logMessage("<dl><dt><u><b>$command $remoteDir</b> gives</u></dt>");
		    $items=$this->listallItemsRecursively($directory,$max_depth,$success);
		    if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->plugin->logMessage("</dl>");
		   
		   return $items;
	    }
      
      
      }


?>