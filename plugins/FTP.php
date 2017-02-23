<?php

      /**
      * MIT License
      * 
      * Copyright (c) 2017 Olivier Lutzwiller / all-informatic.com
      * 
      * Permission is hereby granted, free of charge, to any person obtaining a copy
      * of this software and associated documentation files (the "Software"), to deal
      * in the Software without restriction, including without limitation the rights
      * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
      * copies of the Software, and to permit persons to whom the Software is
      * furnished to do so, subject to the following conditions:
      * 
      * The above copyright notice and this permission notice shall be included in all
      * copies or substantial portions of the Software.
      * 
      * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
      * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
      * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
      * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
      * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
      * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
      * SOFTWARE.
      */
      
      /**
      *
      *   Simple wrapper for cURL functions to transfer an ASCII file over FTP with implicit SSL/TLS
      * 
        @file FTP.php
      * @package php_curl_rest/plugins
      * @version 1.1
      * @author Olivier Lutzwiller 
      * @note 
      *		0.1 - 11.02.2017 - First test version buggy 
      *		0.2 - 13.02.2017 - Working version without class-html
      *		0.5 - 14.02.2017 - Upload added works (requires reset)
      *		1.0 - 16.02.2017 - Class works with logging , comments etc...
      *		1.1 - 17.02.2017 - RNTO FIX added
      * @internal       
      *   I have tried to reproduce the Php equivalent to
      *
      *      curl (-v -u web) ftp://<$ftp_server>:<$ftp_port> --user "<$ftp_user_name>:<$ftp_user_pass>" -k --ftp-ssl -q "<quote>"
      *
      * @example
      *
      *	    
      *      $fc=new FTP_curl_client($ftp_user_name, $ftp_user_pass, $ftp_server, $ftp_port,$remoteDir);
      
	    $fc->command("MLSD");
	    $fc->command("MKD","tests");
	    echo($fc->command("PWD"));
	    $fc->command("CD","tests");
	    $fc->logMessage("=== DELE ==");
	    $fc->command("DELE","essai2.txt");
	    $fc->command("UPLOAD","essai.txt","Ceci est mon essai2");
	    echo($fc->command("DOWNLOAD","essai.txt"));
	    $fc->logMessage("=== RENAME ==");
	    $fc->command("RNFR","essai.txt");
	    $fc->command("RNTO","essai2.txt");
	    echo($fc->command("DOWNLOAD","essai2.txt"));
	    $fc->command("GET","essai2.txt");
	    $fc->logMessage("=== PUT ==");
	    $fc->command("PUT",$localFileName, $remoteFileName);
	    $fc->command("PUT",$localFileName, $remoteDir.$remoteFileName);
	    $fc->command("LS",".");
	    $fc->command("MKD","/tests/subtest/a/b/");
	    $fc->command("LS",".");
	    $fc->command("RMD","-R","/tests/");
	    //$fc->logMessage("=== FINISHED ==",true);
	    echo("<br>JOB DONE!");
      *	@info I wanted a lighweight version to avoid phpseclib that requires pear or flysystem/sftp ...
      **/

      
	//Private CURL FTP
	define("DEBUG_CURL_FTP",0);
	define("DEBUG_CURL_FTP_INSPECT_ITEM",1);
	define("DEBUG_CURL_FTP_INFO",1);
	define("CURL_FTP_MAXDEPTH",30);
	define("CURL_FTP_MAXLOGENTRIES",100);
    

	
	/**
	* FTP with SSL/TLS Class with curl
	*
	* @link 		
	* @category    Class
	* @author      Olivier Lutzwiller / all-informatic.com
	* @since       1.0
	*/
	class FTP_curl_client extends CommandsContainer {
	
		    // *** Class variables

		    /** @var current username to log in */
		    private $user;

		    /** @var current user password to log in */	
		    private $pass;
			    
		    /** @var string current remote path */	
		    private $path;
		    
		     /** @var string server port */
		    private $port;
		    
		     /** @var string server domain */
		    private $server;

		    /** @var resource cURL resource handle */
		    private $curl_handle;
		    
		    /** @var array current cURL options */
		    public $options;
		    
		    /** @var array cURL options available */
		    private $options_available;
		    
		    /** @var handle to debug file */
		     private $stderr;
		    
		    /** @var bool true if connected or logged in **/
		    public $connected;
		    
		    /** @var string last command */
		    public $command;
	
		    /** @var array commands */
		    public $commands =array();
		    
		     // *** Login facility
	
		    /** @var array message queue usefull to track errors */
		    private $messageArray = array();
		    
		   
		
		    /**
		    * Fill in the message in the log
		    *
		    * @note Set $error to true to debug
		    * @name logMessage
		    * @access public
		    * @param $message string - the message string to log
		    * @param $error boolean - if true, dumps the log and abort all (false per default)
		    * @param $reset boolean - clear the log if true (false per default)
		    **/
		    public function logMessage($message,$error=false,$reset=false) 
		    {
		    
			    if($error) 
			    {
				 $message="<hr><font color=\"red\"><u>FAILED WITH THE ERROR</u>:<br>$message<br>";
			    }
			    
			    //Limit logs to 100 entries
			    if($reset||(count($this->messageArray) > CURL_FTP_MAXLOGENTRIES)) $this->messageArray=array();
			    
			    $this->messageArray[] = $message;
			    
			    if($error)
				die(implode("<BR>",$this->getMessages())."</font>");
			    
		    }
		    
		    /**
		    * Fill the variable content in the log
		    *
		    * @note Set $error to true to debug
		    * @name logVar
		    * @access public
		    * @param $var mixed - the variable
		    * @param $error boolean - if true, dumps the log and abort all (false per default)
		    * @param $reset boolean - clear the log if true (false per default)
		    **/
		    public function logVar($var,$error=false) 
		    {
			    $message="<pre>".var_export($var,true)."</pre>";
			    $this->logMessage($message,$error);
		    }
		    
		   /**
		    * Return the log entries
		    *
		    * @note Set $error to true to debug
		    * @name getMessages
		    * @access public
		    * @return array
		    **/
		    public function getMessages()
		    {
			    return $this->messageArray;
		    }
	
		    
		    // === Curl FTP Commands primitives ====
		    
		     /**
		      * Get the list of entries or items of the directory whose url is provided
		      *
		      * @name ftp_curl_list
		      * @access protected
		      * @since 1.0
		      * @version 1.0
		      * @note  zero level depth
		      * @param $ch array - curl handler ressource
   		      * @param $options array - curl options cache 
   		      * @param $url string - the url of the directory
   		      * @param $ftplistonly boolean - true for entries, false for items
   		      * @param $success boolean - the output flag, will be set to true if listing succeed
		      * @return array
		      */
		      function ftp_curl_list($ch,$options,$url,$ftplistonly=true,&$success){
		      //-------------------------------------------------------------------
		      
		      
			  $ftp_curl_connected=$this->connected;
			  if(!$ftp_curl_connected) {
				$this->ftp_curl_exec($ch,$options,$ftp_curl_connected);
			  }
			  
			  $files=array();
			  $sucess=false;
			  
			  if(!$ftplistonly) {
			      $ftp_url=$this->ftp_curl_url("/");
			      $this->ftp_curl_set_url($ch,$options,$ftp_url);
			  }
			  
			  //!NOTE Normally, we should use MLST or LIST or DIR to get directory listing but some FTP server does not support those feature
			  $this->ftp_curl_quote($ch,$options,"CWD $url",$success,false); 
			   
			  if($success) {
			      if($ftplistonly) {
				  $this->ftp_curl_set_option($ch,"CURLOPT_FTPLISTONLY",1);
			      } else {
				  $this->ftp_curl_set_option($ch,"CURLOPT_FTPLISTONLY",0);
			      }
			      $this->ftp_curl_set_option($ch,"CURLOPT_RETURNTRANSFER",1);
			      $result = curl_exec($ch);
			      $this->ftp_curl_set_option($ch,"CURLOPT_FTPLISTONLY",0);
			      
			      
			      $files = explode("\n",trim($result));
			      if( count($files) == 0 ){
				  $files=array();
			      } 
			  }
			  
			  return $files;
		      }
		    
		   /**
		    * Changes the current directory return the entries 
		    *
		    * @name ftp_curl_cwd
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $dir string - directory's pathname
		    * @param $success boolean - the output flag, will be set to true if command succeed
		    */
		    function ftp_curl_cwd(&$ch,&$options,$dir,&$success) {
		    //-------------------------------------------
		    
			if(($dir[0]!="/") && ($dir[0] != ".")) $dir=$this->path.$dir; //relative path has to be converted into absolute path
			
			 $entries=$this->ftp_curl_quote($ch,$options,"CWD $dir",$success);
			  if($success) {
				$this->path=rtrim($dir,"/")."/";
			  }
			  return $entries;
		    }
	      
		  /**
		    * Deletes a file and returns the items in the same file's directory
		    *
		    * @name ftp_curl_dele
		    * @since 1.0
		    * @version 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $file string - file's pathname
		    * @param $success boolean - the output flag, will be set to true if command succeed
		    * @return array
		    */
		    function ftp_curl_dele(&$ch,&$options,$file,&$success) {
		    //---------------------------------------------
			  $entries=array();
			  $base="";
			  if( $this->ftp_curl_file_exists($ch,$options,$file,$entries,$base)) {
			      $entries=$this->ftp_curl_quote($ch,$options,"DELE $file",$success);
			  } else {
			      $this->logMessage("FILE ALREADY DELETED :$file in $base");
			      $entries=$this->ftp_curl_list($ch,$options,$base,false,$success);
			      $success=true;
			  }
			  $items=array();
			  $this->ftp_curl_entries_to_items($base,$entries,$items);
			  return $items;
		    }
	      
	          /**
		    * removes a given directory and returns parents directory items
		    *
		    * @note if $may_depth value is positive, it will delete all its subdirs and files else it will fail if the directory is not empty.
		    * @name ftp_curl_rmd
		    * @since 1.0
		    * @version 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $dir string - pathname to the directory to be deleted
		    * @param $max_depth integer - the recursity depth
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return array
		    */
		    function ftp_curl_rmd(&$ch,&$options,$dir,$max_depth,&$success) {
		    //------------------------------------------------------------------
			  $entries=array();
			  $base="";
			  if( $this->ftp_curl_dir_exists($ch,$options,$dir,$entries,$base)) {
			      
				$base=$dir;
				$successlist=false;
				
				//Get the entries to delete 
			 	$entries=$this->ftp_curl_callback_on_dir($ch,$options,$dir,"inspect_items",$max_depth,$successlist);
			 	$entries=array_keys($entries);
			 	
			 	//don't forget the root entry
			 	array_unshift($entries,$dir);
			 	 $this->logVar($entries);
			 	 
			 	//process it 
			 	$subentries=array();
			 	$e=count($entries)-1;
			 	
			 	if(($max_depth==0)&&($e >0)) {
				      $this->logMessage("DELETE DIR PROCESS FAILED FINISHED, DIR IS NOT EMPTY, $e ITEM(S) FOUND! CONSIDER RMD -R TO FORCE DELETION",true);
			 	} else {
				      $this->logMessage("DIR EXISTS..DELETE ALL INSIDE TO DELETE IT");
				      while($e>=0) {
					  $entry=$entries[$e];
					  $this->logMessage("=== START PROCESS ENTRY($e) '$entry' FOR DELETION");
					
					  $subsuccess=false;
					  if(substr($entry, -1) == "/") { //Directory
					      $this->logMessage("DELETE SUBDIR '$entry'");
					      $subentries=$this->ftp_curl_quote($ch,$options,"RMD $entry",$subsuccess);
					  } else { //File
					      $this->logMessage("DELETE SUBFILE '$entry'");
					      $subentries=$this->ftp_curl_quote($ch,$options,"DELE $entry",$subsuccess);
					  }
					  $this->logMessage("=== END PROCESS ENTRY($e) '$entry' FOR DELETION");
					  $success=$subsuccess;
					  $e=$e-1;
				      } 
				       $this->logMessage("DELETE DIR PROCESS FINISHED");
				}
				$entries=$subentries;
			      
			  } else {
			      $this->logMessage("DIR ALREADY REMOVED :$dir in $base");
			      $entries=$this->ftp_curl_list($ch,$options,$base,false,$success);
			      $success=true; //Already removed
			  }
			  $items=array();
			  $this->ftp_curl_entries_to_items($base,$entries,$items);
			  return $items;
		    }
	      
		    /**
		    * creates a new directory and returns its parent entries
		    *
		    * @name ftp_curl_mkd
		    * @note will consider a success if directory already exists else an error if in depth directories when $max_depth is set to 0
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $dir string - the remote directory path 
		    * @param $max_depth  int - max level depth
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return array
		    */
		    function ftp_curl_mkd(&$ch,&$options,$dir,$max_depth,&$success) {
		    //-----------------------------------------------------------------
			  $entries=array();
			  $items=array();
			  $base="/";
			  
			  if(!$this->ftp_curl_dir_exists($ch,$options,$dir,$entries,$base)) {
			  
			      $this->logMessage("DIR DOES NOT EXIST..CREATE IT");
			       
			      //Build the list of subdirectories
			      $this->logMessage("=== BUILD LIST OF SUBDIR(S) TO CREATE");
			      $dirs=array();
			      $s=$max_depth+1;
			      while($dir != "//") {
				    array_unshift($dirs,$dir);
				    $dir=dirname(rtrim($dir,"/"))."/";
			      }
			      $this->logVar($dirs);
			       
			      $dMax=count($dirs);
			      for($d=0;$d<$dMax;$d++) {
				    $subdir=$dirs[$d];
				    $subsuccess=false;
				    $this->logMessage("CREATE SUBDIR $subdir");
				   
				    if(!$this->ftp_curl_dir_exists($ch,$options,$subdir,$entries,$base)) {
					  $entries=$this->ftp_curl_quote($ch,$options,"MKD $subdir",$subsuccess);
				    }else {
					$subsuccess=true;
				    }
				    $success=$subsuccess;
			      }
			  }else {
			      $this->logMessage("DIR ALREADY CREATED :$dir in $base");
			      $entries=$this->ftp_curl_list($ch,$options,$base,false,$success);	
			      $success=true; //Already created
			  }
			  
			
			  $this->ftp_curl_entries_to_items($base,$entries,$items);
			  //$this->logVar($items);
			  return $items;
		    }
		    
		    /**
		    * put a local file whose name is provided on the server whith a remote name in the current directory and returns its parent entries
		    *
		    * @name ftp_curl_put
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $localFileName string - the local file name 
		    * @param $serverFilename  string - the remote file name
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return array
		    */
		     function ftp_curl_put(&$ch,$options,$localFileName,$serverFilename,&$success) {
		    //----------------------------------------------
		    
			$this->ftp_curl_reset($ch,$options,$serverFilename);
		      
			$options["CURLOPT_UPLOAD"]=1;
			$success=false;
			
			 // set the local file to be sent to the server
			 
			 if(file_exists($localFileName)) {
			      $fp = fopen($localFileName, 'r');
			      $options["CURLOPT_INFILE"]=$fp;
			      $options["CURLOPT_INFILESIZE"]=filesize($localFileName);
			      
			      // Trigger the put
			      $entries=$this->ftp_curl_exec($ch,$options,$success);
			      
			      // Cleanup
			      $options["CURLOPT_UPLOAD"]=0;
			      unset($options["CURLOPT_INFILE"]);
			      unset($options["CURLOPT_INFILESIZE"]);
			      fclose($fp);
			}else {
			      $this->logMessage("PUT FAILED, LOCAL FILE $localFileName NOT FOUND OR CANNOT BE READ");
			}
			
			$items=array();
			
			//Read the directory back to check
			if($success) {
			    
			    $indir="";
			    $list=array();
			    $this->ftp_curl_file_exists($ch,$options,$serverFilename,$list,$indir,false);
			    $this->logMessage("PUT $serverFilename in $indir SUCCEED");
			    $entries=$list;
			    $this->ftp_curl_entries_to_items($indir,$entries,$items);
			}
			
			return  $items;
		    }
		    
		    /**
		    * upload a content on the server whith a remote name in the current directory and returns its parent entries
		    *
		    * @brief creates a file on the server with the content provided
		    * @name ftp_curl_upload
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $filename string - the server file name 
		    * @param $content  string - the content to be uploaded in the file
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return array
		    */
		    function ftp_curl_upload(&$ch,&$options, $filename, $content ,&$success) {
		    //--------------------------------------------------------------------------
	
		    	   // $this->ftp_curl_set_option($ch,"CURLOPT_INFILE",null); is not possible for the next upload, we have to reset all
			  $this->ftp_curl_reset($ch,$options,$filename);
	  
			    $options["CURLOPT_UPLOAD"]=1;
		

			    // open memory stream for writing
			    $stream = fopen( 'php://temp', 'w+' );
			    // check for valid stream handle
			    if ( ! $stream )
				    throw new Exception( 'Could not open php://temp for writing.' );
				    
			    // write file into the temporary stream
			    fwrite( $stream, $content );
			    // rewind the stream pointer
			    rewind( $stream );
			    
			    // set the file to be uploaded
			    $options["CURLOPT_INFILE"]=$stream;
			    			    
			    //trigger the upload
			    $success=false;
			    $entries=$this->ftp_curl_exec($ch,$options,$success);
			
			
			    // cleanup
			     $options["CURLOPT_UPLOAD"]=0;
			     unset($options["CURLOPT_INFILE"]);
			     //$this->ftp_curl_reset($ch,$options);
			     // close the stream handle
			    fclose( $stream );
			    //fclose($this->stderr);
			    
			    
			    $items=array();
			    
			    //Read the directory back to check
			    if($success) {
				
				$indir="";
				$list=array();
				$this->ftp_curl_file_exists($ch,$options,$filename,$list,$indir,false);
				$this->logMessage("UPLOAD $filename in $indir SUCCEED");
				$entries=$list;
				$this->ftp_curl_entries_to_items($indir,$entries,$items);
			    }
			    
			    return  $items;
			
		    }
	      
		    /**
		    * download file from the server and returns its string content
		    *
		    * @brief file with the matching name should exist in the current directory of the server
		    * @name ftp_curl_download_content
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $filename string - the server file name 
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return string
		    */
		    public function ftp_curl_download_content(&$ch,&$options,$file_name,&$success){
		    //---------------------------------------------------------------------
		    
			$path=$this->ftp_curl_resolve_path($file_name);
			$ftp_url=$this->ftp_curl_url($path);
			
			$this->ftp_curl_set_url($ch,$options,$ftp_url);

			$options["CURLOPT_RETURNTRANSFER"]=1;

			 //trigger the upload
			$success=false;
			$result=$this->ftp_curl_exec($ch,$options,$success);
			    
			if( strlen($result) ){
			    return $result;
			} else {
			    return '';
			}

		    }
		    
		    /**
		    * download file from the server 
		    *
		    * @brief file with the matching name should exist in the current directory of the server
		    * @name ftp_curl_download_content
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $serverFilename  string - the remote file name
		    * @param $localFileName string - the local file name 
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return string
		    */
		    public function ftp_curl_download(&$ch,&$options,$serverFile,$localFile,&$success){
		    //----------------------------------------------------------------------------------------
		    
			$base_dir=dirname(__FILE__)."/../";
			$localFilePath=$base_dir."/".$localFile;
			$base=dirname($localFilePath);
			
			if(is_writeable($base)) {
			
			      $file = fopen($localFilePath, 'w');
			      
			      $path=$this->ftp_curl_resolve_path($serverFile);
			      $ftp_url=$this->ftp_curl_url($path);
			      
			      $this->ftp_curl_set_url($ch,$options,$ftp_url);

			      $options["CURLOPT_FILE"]=$file;
			      $options["CURLOPT_RETURNTRANSFER"]=1;
			       
			      //trigger the download
			      $success=false;	      
			      $result=$this->ftp_curl_exec($ch,$options,$success);
				  
			      fclose($file);
			      
			      unset($options["CURLOPT_FILE"]);
			      
			} else {
			      $this->logMessage("CANNOT WRITE FILE '".basename($localFile)."' : DIRECTORY '".$base."' IS NOT WRITEABLE",true);
			      $success=false;	   
			}

			return $success;
		    }
		      
		      
	      
	          /**
		    * Rename from
		    *
		    * @note a file with the matching name should exist in the current directory of the server
		    * @name ftp_curl_rename_from
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $filename string - the server file name 
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return string
		    */
		    public function ftp_curl_rename_from(&$ch,&$options,$filename,&$success){
		    //---------------------------------------------------------------------
		    
		
			$entries=array();
			$base="";
			
			 
			if( $this->ftp_curl_file_exists($ch,$options,$filename,$entries,$base)) {
			  
			    $entries=$this->ftp_curl_quote($ch,$options,"RNFR $filename",$success);
			    
			    //Entries is the content of the file, cancel it else ftp_curl_entries_to_items will crash
			    $entries=array();
			    
			} else {
			    $this->logMessage("FILE MISSING TO RENAME FROM : $filename in $base",true);
			   // $entries=$this->ftp_curl_list($ch,$options,$base,false);
			   
			}
			$items=array();
			$this->ftp_curl_entries_to_items($base,$entries,$items);
			
			return $items;

		    }
	      
		     /**
		    * Rename to
		    *
		    * @name ftp_curl_rename_to
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $filename string - the server file name 
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return string
		    */
		    public function ftp_curl_rename_to(&$ch,&$options,$filename,&$success){
		    //---------------------------------------------------------------------
		    
			$entries=array();
			$base="";
			
			if(!$this->ftp_curl_file_exists($ch,$options,$filename,$entries,$base)) {
			    $entries=$this->ftp_curl_quote($ch,$options,"RNTO $filename", $success);
			} else {
			    $this->logMessage("FILE EXISTING WITH THE WISHED RENAME NAME $filename in $base",true);
			    //$entries=$this->ftp_curl_list($ch,$options,$base,false);
			    //$success=true;
			    
			}
			
			$items=array();
			$this->ftp_curl_entries_to_items($base,$entries,$items);
			
			return $items;

		    }
		    
		    /**
		    * Retrieves a file by its name
		    *
		    * @name ftp_curl_retrieve
		    * @access public
		    * @note DOES NOT WORK !
		    * @since 1.0
		    * @version 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $filename string - the server file name 
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return string
		    */
		    public function ftp_curl_retrieve(&$ch,&$options,$filename,&$success){
		    //---------------------------------------------------------------------
		    
			$entries=array();
			$base="";
			if($this->ftp_curl_file_exists($ch,$options,$filename,$entries,$base)) {
			    $entries=$this->ftp_curl_quote($ch,$options,"RETR $filename",$success);
			} else {
			    $this->logMessage("GET FAILED, WISHED FILE $filename  DOES NOT EXISTS IN $base");
			    $entries=$this->ftp_curl_list($ch,$options,$base,false,$success);
			    $success=true;
			}
			$items=array();
			$this->ftp_curl_entries_to_items($base,$entries,$items);
			
			return $items;

		    }
	      
		    // ==== Curl PHP Core functions===
		    
		    // *** core commands *** 
	      
		    /**
		    * Log in on the ftp server and return true if success
		    *
		    * @name ftp_curl_ftp_login
		    * @access protected
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache , containing connection settings
		    * @return boolean
		    */    
		    function ftp_curl_ftp_login(&$ch,&$options) {
		    //---------------------------------------	
			 
			  $success=false;
			  $this->ftp_curl_exec($ch,$options,$success);
			  $server=$this->server;
			  $ftpUser=$this->user;
			  if(!$success) {
			      $this->logMessage('FTP connection has failed!');    
			      $this->logMessage('Attempted to connect to ' . $server . ' for user ' . $ftpUser,true);
			  }else {
			      $this->logMessage('Connected to ' . $server . ', for user ' . $ftpUser);
			  }
			  $this->connected=$success;
			  return $success;
		    }
		    
		  /**
		    * Executes a raw ftp command whose value is the provided quote
		    *
		    * @name ftp_curl_quote
		    * @access protected
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $quote string - a raw ftp command
		    * @param $success boolean - the output flag, will be set to true if command succeed
		    * @param $haltonerror boolean - true to stop when command failed
		    */
		    function ftp_curl_quote(&$ch,&$options,$quote,&$success,$haltonerror=true) {
		    //-----------------------------------------------------------------------
		    
		    
			  $ftp_curl_connected=&$this->connected;
			  
			  if(!$ftp_curl_connected) {
				
				$halt=($haltonerror)? "HALT ON ERROR $quote" :"DO NOT HALT ON ERROR $quote";
				$this->logMessage($halt);
				$ftp_curl_connected=$this->ftp_curl_ftp_login($ch,$options);
			  }
			  
			 
			
			  $entries=array();
			  
			  if($ftp_curl_connected) { 
			  
			      $halt=($haltonerror)? "HALT ON ERRORS FOR $quote" :"IGNORE ERRORS OF $quote";
			      $this->logMessage($halt);
			        
			      if(preg_match("/^RNTO/",$quote)) {
				  $this->logMessage("START $quote"); 
			      }
			        
			      $this->ftp_curl_set_option($ch,"CURLOPT_QUOTE",array($quote));
			      
			      $data=curl_exec($ch);
			      
			      //!NOTE According to syslog, RNTO is BUGGY, it returns 550 error code, because
			      //it does [retr] [infile] instead of [retr] [outfile] ans rnto twice!
			      if(preg_match("/^RNTO (.+)/",$quote,$matches)) {
			      
				  //RNTO quits even, this is ugly so we have to reconnect nicefully
				  $this->ftp_curl_reset($ch,$options);
				  $ftp_curl_connected=$this->ftp_curl_ftp_login($ch,$options);
			      
				   //to check behind the existence of outfile by ourselves to check if renaming succeed!
				  $outfilename=$matches[1];
				  $base="";
				  $success=$this->ftp_curl_file_exists($ch,$options,$outfilename,$entries,$base);
				  $list=$this->ftp_curl_list($ch,$options,$base,false,$success);	
				  $entries=$list; //real conversion items to entries will be made after
				  $this->logVar($list);
				  $result=($success) ? " SUCCEEDED" : "FAILED";
				  $this->logMessage("RNTO FIX CHECK FOR ".$outfilename." IN $base ".$result);
				  
			      } else if($quote == "PWD") {
				  
				  //Data does not contains current Working directory but its entries instead
				  //so we have to extract from info array
				  $info = curl_getinfo($ch);
				  $url=$info["url"];
				  $entries=preg_replace("#^(ftp|ftps)://([^\/]+)#","",$url) ;
				  $success=($entries == $this->path);
				  
			      }else {
				  $error_no = curl_errno($ch);
				  $success=($error_no == 0);
					
				  if($success) {
				      $entries=explode("\n", trim($data));
				  } else {
				      $error_no = curl_errno($ch);
				      $error_msg = curl_error($ch);
				      $this->logMessage("FTP QUOTE ERROR: $quote <BR/>error_no: ".$error_no . "<br/>msg: " . str_replace("QUOT","",$error_msg),$haltonerror);
				      $success=null;
				  }
			      }
			  } else {
			      $this->logMessage("ftp_curl_quote Error: Can not connect with options");
			      $this->logVar($options,true);
			  }
			  return $entries;
		    }
	      
	             /**
		    * Executes the options and return result
		    *
		    * @name ftp_curl_exec
		    * @access protected
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $success boolean - the output flag, will be set to true if command succeed
		    * @return mixed
		    */    
		     function ftp_curl_exec(&$ch,$options,&$success) {
		    //----------------------------------------------
			$this->ftp_curl_set_options($ch,$options);
			$data=curl_exec($ch);
			$error_no = curl_errno($ch);
			$success=($error_no == 0);
			return $data;
		    }
		    
		    /**
		    * Reset curl and restore the options
		    *
		    * @name ftp_curl_reset
		    * @access protected
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $path the default path ( '/' if not provided)
		    */ 
		    function ftp_curl_reset(&$ch,&$options,$path="/") {
		    //----------------------------------------------------
			    $ftp_port=$this->port;
			    $ftp_server=$this->ftp_curl_url("/");
			    $ftp_user_name=$this->user;
			    $ftp_user_pass=$this->pass;
			
			
			    //Reset curl!
			    curl_reset($ch);
			    
			    //I assume reset disconnect client
			    $this->connected=false;
			    
			    if($path!="/") $path=$this->ftp_curl_resolve_path($path);
			    $ftp_url=rtrim($ftp_server,"/").$path;
			    
			    //$this->stderr=&fopen("curl.txt", "w"); 
			    
			    $this->ftp_curl_set_url($ch,$options,$ftp_url);
			    $this->ftp_curl_setup($ch,$options,$ftp_port,$ftp_user_name,$ftp_user_pass);
		    
		    }
		    
		    // *** entries & items and recusivity support ***
	      
		    /**
		    * Inspect items
		    
		    * @note this callback function should not be called directly, use ftp_curl_callback_on_(entries|dir) instead
		    * @name inspect_items
		    * @access private
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $base string - pathname to a base directory
		    * @param $items array - the output array holding the items
		    * @param $indepth boolean - true for indepth recursivity
		    */
		    function inspect_items(&$ch,&$options,$item,$base,&$items,$indepth) {
		    //---------------------------------------------------------
		    
		      $itemname=$item['name'];
			if(($item['type'] == "directory")) {
			    $dirname=$itemname;
			    
			    if($dirname[0] != ".") { //Skip . and ..
			    
				  if(intval($item["number"]) == 2) { //Empty directory has only two entries : "." and ".."
				      if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->logMessage("<dd>EMPTY DIR found $dirname</dd>");
				      //var_dump($item);
				      $items[$base.$dirname."/"]=$item;
				  } else {
					if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->logMessage("<dd>DIR found $dirname</dd>");
					  $items[$base.$dirname."/"]=$item;
					if(($indepth-1) >0) { //If recursivity depth limit no reached...
						if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->logMessage("<dd><dl><dt>&gt;&gt;ENTERING</dt>");
						$success=false;
						//Let's go deeper one level depth...
						$entries=$this->ftp_curl_cwd($ch,$options,$dirname,$success);
						if($success) {
						      $this->ftp_curl_callback_on_entries($ch,$options,$base.$dirname."/",$entries,"inspect_items",$items,$indepth);
						} else {
						      if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->logMessage("<font color=\"red\">WARNING:CANNOT LIST CONTENT, insufficent rights!</a>",true);
						}
						if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->logMessage("<dt>&lt;&lt;QUITTING</dt></dl></dd>");
						$ret=false;
						//cdup...
						$this->ftp_curl_cwd($ch,$options,"..", $ret);
					}
				  }
			    }
			    
			}else { //File
			    $filename=$itemname;
			   
			    if(DEBUG_CURL_FTP_INSPECT_ITEM)  $this->logMessage("<dd>FILE found $filename</dd>");
			    $items[$base.$filename]=$item; 
			 
			}
		    }
		
	      

		    /**
		    * Apply a callback on each entry (file or directory) and generate items array
		    *
		    * @name ftp_curl_callback_on_entries
		    * @access protected
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $base string - pathname to a base directory
		    * @param $entries array - a array holding entries, generated afer a listing of directory
		    * @param $callback string - name of the method to use a callback
		    * @param $items array - the output array holding the items
		    * @param $indepth boolean - true for indepth recursivity
		    */
		    function ftp_curl_callback_on_entries(&$ch,&$options,$base,$entries,$callback,&$items,&$indepth) {
		    //------------------------------------------------------------------------------------------------
			//echo "<br>ftp_curl_callback_on_entries";
			//var_dump($entries);
			foreach ($entries as $entry) { 
			
				  if($entry != "") { //If not Empty directory
				  
					$chunks = preg_split("/\s+/", $entry);
					
					if(count($chunks) ==9) {
					    list($item['rights'], $item['number'], $item['user'], $item['group'], $item['size'], $item['month'], $item['day'], $item['time'],$item['name']) = $chunks;
					    
					    $item['type'] = $chunks[0]{0} === 'd' ? 'directory' : 'file';
					    array_splice($chunks, 0, 8);
					    if($callback) {
						$this->{$callback}($ch,$options,$item,$base,$items,$indepth);
					    }else {
						$name=$base.$item['name'];
						if($item['type'] == 'directory') $name=$name."/";
						$items[$name]=$item;
					    }
					} else {
					    $this->logMessage("<br>ERROR: At least one entry is corrupted for $base, dumping the entry");
					    $this->logVar($entry);
					    $this->logMessage("<br>Here is the list of other entries:");
					    $this->logVar($entries);
					    $this->logMessage("MAYDAY!!!",true);
					}
				  }
				  
			  }
			//  echo "<br>ftp_curl_callback_on_entries gives items";
			//var_dump($items);
		    }
	     
	      	    
	      	    /**
		    * Applies a callback on directory entries and returns items array
		    *
		    * @name ftp_curl_callback_on_dir
		    * @access protected
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $dir string - pathname to a base directory
		    * @param $callback string - name of the method to use a callback, default used is inspect_items
		    * @param $may_depth integer - the recursity depth, 0 per default
		    * @param $indepth boolean - true for indepth recursivity
		    * @return array
		    */
		    function ftp_curl_callback_on_dir(&$ch,&$options,$dir,$callback="inspect_items",$max_depth=0,&$success) {
		    //-------------------------------------------------------------------------------------------
			$items = array();
			$entries=$this->ftp_curl_cwd($ch,$options,$dir,$success);
			//var_dump($entries);
			if($success) {	  
			      $dir=rtrim($dir,"/")."/";
			      $this->ftp_curl_callback_on_entries($ch,$options, $dir,$entries,$callback,$items,$max_depth);
			}
			return $items;
		    }
	      
		    
		  /**
		    * Converts entries to items
		    *
		    * @name ftp_curl_entries_to_items
		    * @note entries is a pathname list of file or directory, directory ends with "/"
		    * @access protected
		    * @since 1.0
		    * @param $base string - base directory pathname 
		    * @param $entries array - a array holding entries, generated afer a listing of directory
		    * @param $items array - the output array holding the items
		    */
		    function ftp_curl_entries_to_items($base="",$entries,&$items) {
		    //----------------------------------------------------------
			    $indepth=0;
			    $callback=null;
			    $ch=null;
			    $options=null;
			    $this->ftp_curl_callback_on_entries($ch,$options,$base,$entries,$callback,$items,$indepth);
		    }
		    
		     // --- Item existence primitives 
		    
		    
		    /**
		    * Check if file of the provided pathname exists on the server 
		    *
		    * @name ftp_curl_file_exists
		    * @access protected
		    * @since 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $file string - file pathname 
		    * @param $indir array - output paramter, will be set
		    * @param $ftplistonly  boolean - true for entries (default), false for items
		    * @return boolean
		    */
		      function ftp_curl_file_exists($ch,$options,$file,&$list,&$indir,$ftplistonly=true) {
		      //---------------------------------------------------------------------------------
			    $file=$this->ftp_curl_resolve_path($file);
			    $file_name=basename($file);
			    $indir=rtrim(dirname($file),"/")."/";
			    $success=false;
			    $list=$this->ftp_curl_list($ch,$options,$indir,$ftplistonly,$success);
			    return (in_array($file_name,$list));
		      }
		      
		    /**
		      * Check if directory of the provided pathname exists on the server 
		      *
		      * @name ftp_curl_file_exists
		      * @access protected
		      * @since 1.0
		      * @param $ch array - curl handler ressource
		      * @param $options array - curl options cache 
		      * @param $dir string - directory pathname 
		      * @param $indir array - output paramter, will be set
		      * @param $ftplistonly  boolean - true for entries (default), false for items
		      * @return boolean
		      */
		      function ftp_curl_dir_exists($ch,$options,$dir,&$list,&$indir) {
		      //--------------------------------------------------------------
			    
			    $updir="/".trim($dir,"/");
			    $dir_name=basename($updir);
			    $indir=rtrim(dirname($updir),"/")."/";
			    $success=false;
			    $list=$this->ftp_curl_list($ch,$options,$indir,true,$success);
			    $this->logMessage("DIR EXISTS $dir_name in $indir?");
			    return (in_array($dir_name,$list));
		      }
		     
		     // **** Options related ***
		     
		     
		    /**
		    * Allows to save connections parameters in curl options cache excepted the server url
		    *
		    * @name ftp_curl_setup
		    * @access protected
		    * @since 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $ftp_port int - the port value
		    * @param $ftp_user_name string - the user name
		    * @param $ftp_user_pass string - the user password in clear
		    * @param $timeout int - timeour value in seconds (10 per default),
		    * @param $passive_mode  boolean - true for entries (default), false for items
		    */
		    function ftp_curl_setup(&$ch,&$options,$ftp_port,$ftp_user_name,$ftp_user_pass,$timeout=10,$passive_mode=true) {
		    //----------------------------------------------------------------------------------------
		    
			 // $stderr=&$this->stderr;
		    
			  //global settings	
			  $options["CURLOPT_PORT"]=$ftp_port;
			  $options["CURLOPT_USERPWD"]="$ftp_user_name:$ftp_user_pass";
			  $options["CURLOPT_TIMEOUT"]=$timeout;
			  
			  //debug stuff
			  if(DEBUG_CURL_FTP) {
			      $options["CURLOPT_VERBOSE"]=true;
			     // $options["CURLOPT_STDERR"]=$stderr;
			      $options["CURLOPT_RETURNTRANSFER"]=false; 
			  } else {
			      $options["CURLOPT_VERBOSE"]=false;
			      //$options["CURLOPT_STDERR"]=$stderr;
			      //return the data instead of outputting it.
			      $options["CURLOPT_RETURNTRANSFER"]=true; 
			  }
			  
			  //SSL stuff
			  $options["CURLOPT_SSL_VERIFYPEER"]=0;  //use for development only; unsecure 
			  $options["CURLOPT_SSL_VERIFYHOST"]=0;  //use for development only; unsecure
			  $options["CURLOPT_FTP_SSL"]=CURLOPT_FTPSSLAUTH;
			  $options["CURLOPT_FTPSSLAUTH"]=CURLFTPAUTH_TLS;
			  
			  //$options[CURLOPT_SSLVERSION]=3;
			  //end SSL
			  
			  // cURL FTP enables passive mode by default, so disable it by enabling the PORT command and allowing cURL to select the IP address for the data connection
			    if ( ! $passive_mode )
				    $options["CURLOPT_FTPPORT"] = '-';
			    
			  //ftp_curl_flush_options($ch,$options);
		    }
		    
		    /**
		    * Allows to save in curl options cache the server url
		    *
		    * @name ftp_curl_set_url
		    * @access protected
		    * @since 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $url string - url
		    */
		     function ftp_curl_set_url(&$ch,&$options,$url) {
		    //---------------------------------------------
			if(!$url)  $this->logMessage("ftp_curl_set_url ERROR: URL CAN NOT BE EMPTY");
			$options["CURLOPT_URL"]=$url;
		    }
		    
		    /**
		    * Allows to save cache the server url and flush connections settings and into curl options cache
		    *
		    * @name ftp_curl_set_connection_setting
		    * @access protected
		    * @since 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - curl options cache 
		    * @param $ftp_url string - url
		    */
		    function ftp_curl_set_connection_settings($ftp_url) {
		    //-------------------------------------------------
			$ch=&$this->curl_handle;
			$options=&$this->options;
			$ftp_port=$this->port;
			$ftp_user_name=$this->user;
			$ftp_user_pass=$this->pass;
			$this->ftp_curl_set_url($ch,$options,$ftp_url);
			$this->ftp_curl_setup($ch,$options,$ftp_port,$ftp_user_name,$ftp_user_pass);
		    }
	      
		    /**
		    * Set curl option by name and value 
		    *
		    * @note coherency check is made, if an option with this name does not exist in the curl extension, it will trigger an error
		    * @name ftp_curl_set_option
		    * @access protected
		    * @since 1.0
		    * @param $ch array - curl handler ressource
		    * @param $option_name string - option's name
		    * @param $option_value string - the value to set for this option
		    */
	            function ftp_curl_set_option(&$ch,$option_name, $option_value) {
		    //-------------------------------------------------------------------------------
			$options_available=$this->options_available;
		    
			if ((!array_key_exists($option_name,$options_available))||(!curl_setopt( $ch,$options_available[$option_name], $option_value ))) 
					    $this->logMessage( sprintf( 'Can not set cURL option: %s [%s] - %s', $option_name,  curl_errno( $ch ), curl_error( $ch )  ),true);
		    }
		    
		    /**
		    * Set all options from the options cache
		    *
		    * @note coherency check is made, instead of a generic "cannot set options" error with curl_setopt_array(), we will know which option trigger an error
		    * @name ftp_curl_set_options
		    * @access protected
		    * @since 1.0
		    * @param $ch array - curl handler ressource
		    * @param $options array - options cache
		    */
		    function ftp_curl_set_options(&$ch,$options) {
		    //------------------------------------------------
			    foreach ( $options as $option_name => $option_value ) {
				    $this->ftp_curl_set_option($ch,$option_name, $option_value);
			    }
		    }
		    
		       
		   /** 
		    * Returns curl options constants 
		    *
		    * @name ftp_curl_options_avalaible
		    * @access protected
		    * @link http://php.net/manual/fr/function.get-defined-constants.php
		    * @return string on error 
		    * @return array
		    */
		    function ftp_curl_options_avalaible() {
		    //----------------------------------
			$contants=get_defined_constants(true);
			$curl_constants=$contants["curl"];
			$prefix="CURLOPT_";
			$dump=array();
			foreach ($curl_constants as $key=>$value)
			    if (substr($key,0,strlen($prefix))==$prefix)  $curl_options[$key] = $value;
			
			if(empty($curl_options)) { 
			    return "Error: No Constants found with prefix '".$prefix."'"; 
			}else { 
			      return $curl_options; 
			}
		    }
		    
		    // *** path & url builders ***

		  
		   /**
		    * resolve any path to absolute one
		    *
		    * @name ftp_curl_resolve_path
		    * @access protected
		    * @since 1.0
		    * @param $path string - a relative path
		    * @return string
		    */
		    function ftp_curl_resolve_path($path) {
		    //-----------------------------------
			  if($path == ".")  {
				$path=$this->path;
			  }else if(($path[0]!="/") && ($path[0] != ".")) $path=$this->path.$path;
			  return $path;
		    }
	      
		    /**
		    * Get url from an optional path 
		    *
		    * @name ftp_curl_url
		    * @access protected
		    * @since 1.0
		    * @param $path string - an absolute path
		    * @return string
		    */
		    function ftp_curl_url($path="") {
		    //---------------------------------
			if(!$path) $path=$this->path;
			return "ftp://{$this->server}{$path}";
		    }
		 
		  

		    
		   /**
		  * Setup parameters to FTP server
		  *
		  * @constructor
		  * @access public
		  * @since 1.0
		  * @param string $username
		  * @param string $password
		  * @param string $server
		  * @param int $port
		  * @param string $initial_path
		  * @param bool $passive_mode
		  * @return \FTP_curl_client
		  */
		  public function __construct( $username, $password, $server, $port = 21, $initial_path = '/', $passive_mode = false ) {
		  //=====================================================================================================================
	
		      $this->user = $username;
		      $this->pass = $password;
		      $this->server = $server;
		      $this->path = $initial_path;

		      // check for blank username
		      if ( ! $username )
			      $this->logMessage( 'FTP Username is blank.',true );
		      // don't check for blank password (highly-questionable use case, but still)
		      // check for blank server
		      if ( ! $server )
			      $this->logMessage( 'FTP Server is blank.',true);
		      // check for blank port
		      if ( ! $port )
			      $this->logMessage( 'FTP Port is blank.',true);
			      
		      // set host/initial path
		      $this->url = $this->ftp_curl_url();
		      
		      // setup connection
		      $this->curl_handle = curl_init();
		      
		      // check for successful connection
		      if ( ! $this->curl_handle )
			      $this->logMessage( 'Could not initialize cURL.' );
			      
		      // A cache of options      
		      $options=array();
		      $this->options=$options;
		      $this->options_available=$this->ftp_curl_options_avalaible();
		      
		      
		      // cURL FTP enables passive mode by default, so disable it by enabling the PORT command and allowing cURL to select the IP address for the data connection
		      if ( ! $passive_mode )
			$options["CURLOPT_FTPPORT"] = '-';
			
		      // for error msg logging
		     // $this->stderr= fopen("curl.txt", "w"); 
		      
		      // Will be true when loggin is successful
		      $this->connected=false;
		      
		      //Registers once commands and their class
		      if(!$this->commands) {
			  $files=include_all_from_dir(dirname(__FILE__)."/FTP");
			
			  foreach($files as $file) {
			      $command=str_replace(".php","",basename($file));
			      $class_command="FTP_$command";
			      $this->commands[]=$command;
			      parent::addCommand($command,new $class_command($this));
			  }
		      }
		  }
		    
		   //-----------------------------
		   
		    /**
		    * Change the current directory and returns list of files
		    *
		    * @name changeDir
		    * @access public
		    * @since 1.0
		    * @param string $directory - remote directory path to create
		    * @param bool $success - true if success, false if failure, set automaticaly
		    * @return array
		    */
		   public function changeDir($directory,&$success)
		  {
			
			$ftp_url=$this->ftp_curl_url();
			$this->ftp_curl_set_connection_settings($ftp_url);
			$ch=&$this->curl_handle;
			$options=&$this->options;
			$max_depth=0; //No recusivity
			
			$remoteDir=$directory;
			$remoteDir=$this->ftp_curl_resolve_path($remoteDir);
			$items=$this->ftp_curl_callback_on_dir($ch,$options,$remoteDir,"inspect_items",$max_depth,$success);
			if($success) {
			      $this->logMessage("Current directory is now: $remoteDir ");
			 }
			return $items;
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
			
			$ftp_url=$this->ftp_curl_url();
			$this->ftp_curl_set_connection_settings($ftp_url);
			$ch=&$this->curl_handle;
			$options=&$this->options;
			
			$remoteDir=$directory;
			$remoteDir=$this->ftp_curl_resolve_path($remoteDir);
			$items=$this->ftp_curl_callback_on_dir($ch,$options,$remoteDir,"inspect_items",$max_depth,$success);
			
			return $items;	
		    }
		    
		    /**
		    * removes a given directory and returns its parent entries
		    *
		    * @name removeDirectory
		    * @access public
		    * @since 1.0
		    * @param string $directory - the remote directory path 
		    * @param bool $success - true if success, false if failure, set automaticaly
		    * @return array
		    */
		     public function removeDirectory($directory,$max_depth=0,&$success) 
		     {
	
			  $ftp_url=$this->ftp_curl_url();
			  $this->ftp_curl_set_connection_settings($ftp_url);
			  $ch=&$this->curl_handle;
			  $options=&$this->options;
			  
			  $remoteDir=$directory;
			  $items=$this->ftp_curl_rmd($ch,$options,$remoteDir,$max_depth,$success);
			  
			  if($success) {
			      $this->logMessage("DIR SUCCESSFULLY DELETED: $remoteDir ");
			  }
			  return $items;	
		    }
		    
		    /**
		    * create a new directory and returns its parent entries
		    *
		    * @name createDirectory
		    * @note will consider a success if directory already exists else an error if in depth directories when $max_depth is set to 0
		    * @access public
		    * @since 1.0
		    * @param string $directory - the remote directory path 
		    * @param int $max_depth  - max level depth
		    * @param bool $success - true if success, false if failure, set automaticaly
		    * @return array
		    */
		     public function createDirectory($directory,$max_depth=0,&$success) 
		     {
			  $ftp_url=$this->ftp_curl_url();
			  $this->ftp_curl_set_connection_settings($ftp_url);
			  $ch=&$this->curl_handle;
			  $options=&$this->options;
			  
			  $remoteDir=rtrim($this->ftp_curl_resolve_path($directory),"/")."/";
			
			  
			  $items=$this->ftp_curl_mkd($ch,$options,$remoteDir,$max_depth,$success);
			 
			  
			 
			  if($success) 
			  {
				$this->logMessage("DIR SUCCESSFULLY CREATED: $remoteDir ");
			  }
			 
			  return $items;	
		    }
		    
		    /**
		    * Deletes a file 
		    *
		    * @name deleteFile 
		    * @param $file string - file's pathname
		    * @param $success boolean - the output flag, will be set to true if command succeed
		    * @return array
		    */
		    public function deleteFile($file,&$success) 
		    {
		    
			  $ch=&$this->curl_handle;
			  $options=&$this->options;
			  
			  
			  $ftp_url=$this->ftp_curl_url();
			  $this->ftp_curl_set_connection_settings($ftp_url);
			
			  $filepath=$this->ftp_curl_resolve_path($file);
			  $items=$this->ftp_curl_dele($ch,$options,$filepath,$success);
			  
			  if($success) 
			  {
			      $this->logMessage("FILE SUCCESSFULLY DELETED: $filepath ");
			  }
			  
			  return $items;
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
		    
			  $ch=&$this->curl_handle;
			  $options=&$this->options;
			  
			  
			/*  $ftp_url=$this->ftp_curl_url();
			  $this->ftp_curl_set_connection_settings($ftp_url);
			
			  $filepath=$this->ftp_curl_resolve_path($file);*/
			  
			  
			  $this->logMessage("START");
			  
			  $items=$this->ftp_curl_rename_from($ch,$options,$filename,$success);
			  
			  if($success) 
			  {
			      $this->logMessage("FILE SUCCESSFULLY SELECTED FOR RENAME: $filename ");
			  }
			  
			  return $items;
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
		    
			  $ch=&$this->curl_handle;
			  $options=&$this->options;
			  
			  
			/*  $ftp_url=$this->ftp_curl_url();
			  $this->ftp_curl_set_connection_settings($ftp_url);
			
			  $filepath=$this->ftp_curl_resolve_path($file);*/
			  
			  $items=$this->ftp_curl_rename_to($ch,$options,$filename,$success);
			  
			  if($success) 
			  {
			      $this->logMessage("FILE SUCCESSFULLY RENAMED INTO: $filename ");
			  }
			  
			  return $items;
		    }
		    
		   /**
		    * Download a file locally
		    *
		    * @name getFile
		    * @param $filename string - file's name
		    * @param $success boolean - the output flag, will be set to true if command succeed
		    * @return array
		    */
		    public function getFile($filename,&$success) 
		    {
		    
			  $ch=&$this->curl_handle;
			  $options=&$this->options;
			  
			  
			/*  $ftp_url=$this->ftp_curl_url();
			  $this->ftp_curl_set_connection_settings($ftp_url);
			
			  $filepath=$this->ftp_curl_resolve_path($file);*/
			  
			  $items=$this->ftp_curl_get($ch,$options,$filename,$success);
			  
			  if($success) 
			  {
			      $this->logMessage("FILE SUCCESSFULLY RETRIEVED: $filename ");
			  }
			  
			  return $items;
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
			$ch=&$this->curl_handle;
			$options=&$this->options;
			$ftp_url=$this->ftp_curl_url();
			

			$items=$this->ftp_curl_upload($ch,$options,$filename,$content,$success);
			
			if($success) 
			{
			    $this->logMessage("CONTENT SUCCESSFULLY UPLOADED:$filename into $ftp_url ");
			}
			
			return $items;
		    }
		    
		    /**
		    * upload a local file on the server whith a remote name in the current directory and returns its parent entries
		    *
		    * @name uploadFile
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $localFileName string - the local file name 
		    * @param $serverFilename  string - the remote file name
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return array
		    */
		    public function uploadFile($localFileName,$serverFilename,&$success) 
		    {
			  $ch=&$this->curl_handle;
			  $options=&$this->options;
			  $ftp_url=$this->ftp_curl_url();
			
			  $items=$this->ftp_curl_put($ch,$options,$localFileName,$serverFilename,$success);
			  
			  if($success) 
			  {
				$this->logMessage("FILE SUCCESSFULLY PUT: $serverFilename into $ftp_url ");
			  }
			  
			  return $items;
		    }
		    
		    
		    /**
		    * Retrieve content of a remote file 
		    *
		    * @brief file with the matching name should exist in the current directory of the server
		    * @name downloadFileContent
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $filename string - the server file name 
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return array
		    */
		    public function downloadFileContent($serverFilename,&$success) 
		    {
			  $ch=&$this->curl_handle;
			  $options=&$this->options;
			
			  $items=$this->ftp_curl_download_content($ch,$options,$serverFilename,$success);
			  
			  if($success) 
			  {
			      
			      $this->logMessage("CONTENT SUCCESSFULLY DOWNLOADED FROM $serverFilename");
			  }
			  return $items;
		    }
			
		    /**
		    * download file from the server 
		    *
		    * @brief file with the matching name should exist in the current directory of the server
		    * @name downloadFile
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $filename string - the server file name 
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return array
		    */
		    public function downloadFile($serverFilename,$localFile,&$success) 
		    {
			  $ch=&$this->curl_handle;
			  $options=&$this->options;
			
			  $items=$this->ftp_curl_download($ch,$options,$serverFilename,$localFile,$success);
			    
			  if($success) 
			  {
			      $this->logMessage("FILE SUCCESSFULLY DOWNLOADED FROM $serverFilename TO LOCAL $localFile");
			  }
			  return $items;
		    }
			
			
		    /**
		    * returns current working directory 
		    *
		    * @name printWorkingDir
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return string
		    */
		    public function printWorkingDir(&$success) 
		    {
			  $ch=&$this->curl_handle;
			  $options=&$this->options;
			  return $this->ftp_curl_quote($ch,$options,"PWD",$success);	 
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
			  $ch=&$this->curl_handle;
			  $options=&$this->options;
			  $ftplistonly=true;
			  $path=rtrim($this->ftp_curl_resolve_path($directory),"/")."/";
			  $list=$this->ftp_curl_list($ch,$options,$path,$ftplistonly,$success);
			  
			  return $list;
			 
		    }
		    
		    /**
		    * returns the files items of a given directory its content
		    *
		    * @name rawList
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $directory string - the directory pathname 
		    * @param $success bool - true if success, false if failure, set automaticaly
		    * @return array
		    */
		    public function rawList($directory,&$success) 
		    {
			  $ch=&$this->curl_handle;
			  $options=&$this->options;
			  $ftplistonly=false;
			  $path=rtrim($this->ftp_curl_resolve_path($directory),"/")."/";
			  $entries=$this->ftp_curl_list($ch,$options,$path,$ftplistonly,$success);
			  
			  return $entries;
		    }
		    	

		    /**
		    * run a given command with variable parameters
		    *
		    * @note This is the main function, an helper to run commands
		    * @name command
		    * @access public
		    * @since 1.0
		    * @version 1.0
		    * @param $command string - the command name
		    * @param $data string - the first parameter
		    * @param $extra string - the second parameter
		    * @return array 
		    **/
		    public function command($command,$data=".",$extra="") {
		    //------------------------------------------------------
		    
			$ch=&$this->curl_handle;
			$options=&$this->options;
			$this->command="$command $data $extra";
			$this->logMessage("<font color=\"blue\">PROCESSING COMMAND: <b>{$this->command}</b></font></br>",false,10);
			 
			$success =false;
			
			$commands=$this->commands;
			
			if(in_array($command,$commands)) {
			
			       /* $remoteDir=$data;
				if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->logMessage("<dl><dt><u><b>$command $remoteDir</b> gives</u></dt>");
				$items=$this->changeDir($remoteDir,$success);
				if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->logMessage("</dl>");  
				*/
				$args=func_get_args();
				$args[0]=&$success;
				$items=call_user_func_array(array($this, "FTP_{$command}_run"),$args);
			  
			
			}else {
			
			   die("<BR> COMMAND '$command' NOT EXTRACTED");
			    
			    switch($command) {
				/*case "CWD":
				case "CD":
				case "MLSD"://MLSD can not be called directly, we use CWD instead who provide list of files
				case "LS":
				    $remoteDir=$data;
				    if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->logMessage("<dl><dt><u><b>$command $remoteDir</b> gives</u></dt>");
				    $items=$this->changeDir($remoteDir,$success);
				    if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->logMessage("</dl>");
				    break;
				case "LSR": //Recursive listing
				    $remoteDir=$data;
				    $max_depth=($extra) ? $extra:CURL_FTP_MAXDEPTH;
				    if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->logMessage("<dl><dt><u><b>$command $remoteDir</b> gives</u></dt>");
				    $items=$this->listallItemsRecursively($directory,$max_depth,$success);
				    if(DEBUG_CURL_FTP_INSPECT_ITEM) $this->logMessage("</dl>");
				    break;*/
			      case "RNFR":
				    $inFile=$data;
				    $items=$this->renameFrom($inFile,$success);
				    break;
			      case "RNTO":
				    $outFile=$data;
				    $items=$this->renameTo($outFile,$success);
				    break;
				case "RMD":
				    $remoteDir=$data;
				    if(strtoupper($data) == "-R") {
					$remoteDir=$extra; 
					$max_depth=CURL_FTP_MAXDEPTH;
				    }else {
					$remoteDir=$data;
					$max_depth=0;
				    }
				    $items=$this->removeDirectory($remoteDir,$max_depth,$success);
				    break;	
				case "MKD":
				    if(strtoupper($data) == "-P") { //Recursive
					$remoteDir=$extra; 
					$max_depth=CURL_FTP_MAXDEPTH;
				    }else {
					$remoteDir=$data;
					$max_depth=0;
				    }
				    $items=$this->createDirectory($remoteDir,$max_depth,$success);
				    break;
				case "DELE":
				    $remoteFile=$data;
				    $items=$this->deleteFile($remoteFile,$success);
				    break;
				case "UPLOAD": //Upload content to current path
				    $filename=$data;
				    $content=$extra;
				    $items=$this->uploadFileContent($filename,$content,$success);
				    break;
				case "PUT":
				    $localFileName=$data;
				    $serverFilename=$extra;
				    $items=$this->uploadFile($localFileName,$serverFilename,$success);
				    break;
				case "DOWNLOAD":
				    $serverFilename=$data;
				    $items=$this->downloadFileContent($serverFilename,$success);
				    break;
				case "RETR":    
				case "GET":
				    $serverFilename=$data;
				    $localFilename=$extra;
				    $items=$this->downloadFile($serverFilename,$localFilename,$success);
				    break;
				case "NLIST":
				    $remoteDir=$data; 
				    $items=$this->namedList($remoteDir,$success);
				    break;
				case "PWD":
				    $items=$this->printWorkingDir($success);
				    break;
				case "RAWLIST":
				    $remoteDir=$data;
				    $items=$this->rawList($remoteDir,$success);
				    break;
				default:
				    $this->logMessage("UNSUPPORTED COMMAND '$command'",true);
			    }

			}
		      
			if(!$success&&is_bool($success)) {  //success is null when error has already been processed
				$error_no = curl_errno($ch);
				$error_msg = curl_error($ch);
				$this->logMessage("FTP ERROR: $command $data <BR/>error_no: ".$error_no . "<br/>msg: " . $error_msg,true);
			}  
			
			if($success) {
			    $this->logVar($items);
			}
			
			return $items;
		    
		    }
	
	    
		/**
		* Attempt to close cURL handle
		* Note - errors suppressed here as they are not useful
		* 
		* @access public
		*/
		public function __destruct() {
			@curl_close( $this->curl_handle );
			//if($this->stderr) fclose($this->stderr);
		}
        
      }
?>