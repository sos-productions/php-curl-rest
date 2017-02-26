<?php

      /**
      *
      *   Defines the command to perform FTP UPLOAD 
      * 
        @file UPLOAD.php
      * @package php_curl_rest/plugins/FTP
      * @version 1.2.0
      * @author Olivier Lutzwiller 
      * @note 
      *		1.2.0 - 25.02.2017 - attribute command changed to name
      *		1.1.0 - 23.02.2017 - uploadFileContent  from ../FTP.php is deported here
      *		1.0.0 - 22.02.2017 - First version  
      **/
      
      class FTP_UPLOAD 
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
	    @name FTP_UPLOAD
	    @param FTP_curl_client $plugin instance of the plugin this command is issued
	    **/
	    function FTP_UPLOAD(&$plugin) {
	    //---------------------------
		  $this->plugin=&$plugin;
		  $this->name="UPLOAD";
		  $this->version="1.2.0";
		  $this->date="25.02.2017";
		  $this->authors="Olivier Lutzwiller";  
	    }
      
      
	  /**
	    * upload a content on the server whith a remote name in the current directory and returns its parent entries
	    *
	    * @brief creates a file on the server with the content provided
	    * @name uploadFileContent
	    * @access public
	    * @since 1.0
	    * @version 1.0
	    * @param $filename string - the server file name 
	    * @param $content  string - the content to be uploaded in the file
	    * @param $success bool - true if success, false if failure, set automaticaly
	    * @return array
	    */
	    public function uploadFileContent($filename,$content,&$success) 
	    {
		$plugin=&$this->plugin;
		$options=&$plugin->options;
		$ch=&$plugin->_get_curl_handle();
		
		$ftp_url=$plugin->ftp_curl_url();
		

		$items=$plugin->ftp_curl_upload($ch,$options,$filename,$content,$success);
		
		if($success) 
		{
		    $plugin->logMessage("CONTENT SUCCESSFULLY UPLOADED:$filename into $ftp_url ");
		}
		
		return $items;
	    }
      
        /**
	    @brief Execute the UPLOAD Command
	    @name FTP_UPLOAD_run
	    @param $success bool will be set to true if command succeed, else false
	    @param $data string  filename
	    @param $extra string content
	    @return string
	    **/	 
	    function FTP_UPLOAD_run(&$success,$data,$extra) {
	   //------------------------------------------------------	

		    $filename=$data;
		    $content=$extra;
		    $items=$this->uploadFileContent($filename,$content,$success);
				    
		   return $items;
	    }
      
      
      }


?>