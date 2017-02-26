<?php

      /**
      *
      *   Defines the command to perform FTP NLIST
      * 
        @file NLIST.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      * 	1.1.0 - 23.02.2017 - namedList  from ../FTP.php is deported here
      *		1.0.0 - 22.02.2017 - First version   
      **/
      
      
      class FTP_NLIST
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
	    @name FTP_NLIST
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_NLIST(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="NLIST";
		  $this->version="1.2.0";
		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
      
      
	    /**
	      * returns the list of files of a given directory its content
	      *
	      * @name namedList
	      * @access public
	      * @since 1.0
	      * @version 1.0
	      * @param $directory string - the directory pathname 
	      * @param $success bool - true if success, false if failure, set automaticaly
	      * @return array
	      */
	      public function namedList($directory,&$success) 
	      {
		    $plugin=&$this->plugin;
		    $options=&$plugin->options;
		    $ch=&$plugin->_get_curl_handle();
		  
		    $ftplistonly=true;
		    $path=rtrim($plugin->ftp_curl_resolve_path($directory),"/")."/";
		    $list=$plugin->ftp_curl_list($ch,$options,$path,$ftplistonly,$success);
		    
		    return $list;
			
	      }
	      
	    /**
	    @brief Execute the NLIST Command
	    @name FTP_NLIST_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string remote Directory
	    @param $extra not used
	    @return string
	    **/
	    function FTP_NLIST_run(&$success,$data,$extra) {
	   //----------------------------------------------
		$remoteDir=$data; 
		$items=$this->namedList($remoteDir,$success);
				    
		   return $items;
	    }
      
      
      }


?>