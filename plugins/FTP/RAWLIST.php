<?php

      /**
      *
      *   Defines the command to perform FTP RAWLIST 
      * 
        @file RAWLIST.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.1 - 23.02.2017 - items_output parameter added
      *		1.1.0 - 23.02.2017 - rawList  from ../FTP.php is deported here
      *		1.0.0 - 22.02.2017 - First version  
      **/
      
      class FTP_RAWLIST
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
	    @name FTP_RAWLIST
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_RAWLIST(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="RAWLIST";
		  $this->version="1.2.0";
		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
      
      	    /**
	    * returns the files items of a given directory its content
	    *
	    * @name rawList
	    * @access public
	    * @since 1.0
	    * @version 1.0
	    * @param $directory string - the directory pathname 
	    * @param $items:_output bool - entries will be parsed into items array
	    * @param $success bool - true if success, false if failure, set automaticaly
	    * @return array
	    */
	    public function rawList($directory,$items_output=false,&$success) 
	    {
		  $plugin=&$this->plugin;
		  $options=&$plugin->options;
		  $ch=&$plugin->_get_curl_handle();
		    
		  $ftplistonly=false;
		  $path=rtrim($plugin->ftp_curl_resolve_path($directory),"/")."/";
		  $entries=$plugin->ftp_curl_list($ch,$options,$path,$ftplistonly,$success);
		  
		  if($items_output) {
		        $items=array();
		        $base=$path;
			$plugin->ftp_curl_entries_to_items($base,$entries,$items);
			$entries=$items;  
		  }
		  return $entries;
	    }
      
          /**
	    @brief Execute the RAWLIST Command
	    @name FTP_RAWLIST_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string remote Directory
	    @param $extra boolean output format, false for entries (default),true for items. 
	    @return array
	    **/
	    function FTP_RAWLIST_run(&$success,$data,$extra=false) {
	   //------------------------------------------------------	
		    if(is_bool($data)) {
			  $remoteDir=".";
			  $items_output=$data;
		    }else {
			  $remoteDir=$data;
			  $items_output=($extra) ? $extra : false;
		    }
		   $items=$this->rawList($remoteDir,$items_output,$success);
		   return $items;
	    }
      
      
      }


?>