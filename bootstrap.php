<?php

      /**
      *
      *   Bootstrapping File for Universal rest client
      * 
      *	@file bootstrap.php
      * @package php_curl_rest
      * @version 1.0
      * @author Olivier Lutzwiller / all-informatic.com
      * @note .
      *		1.0 - 17.02.2017 - First version
      **/
 
      if (!extension_loaded('curl')) {
	      die("This package depends on curl, please install it before");
      }
      
      
      //Recursives functions, faster and eats less memory!

	function list_files($dir,&$files,$recurse=false) {
	//-----------------------------------
	  // $files=getFilesFromDir($dir);
	    if ($handle = opendir($dir)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if($recurse&&is_dir($dir.'/'.$file)) {
					$dir2 = $dir.'/'.$file;
					//$files[] = 
					list_files($dir2,$files,$recurse);
				}
				else {
				  $files[] = $dir.'/'.$file;
				}
			}
		}
		closedir($handle);
	  }
	  return count($files);
	}

	
	function include_all_from_dir($dir) {
	//----------------------------------
	    $files=array();
	    if(list_files($dir,$files,false)) {
		foreach($files as $file) {
		    include($file);
		}
	    }
	    return $files;
	}
      
      
	  /** 
	  * Plugins Container
	  *
	  * @class CommandsContainer
	  * @usage
	  
	  
		    class Main extends CommandsContainer  {

			    public function __construct()
			      {
					    parent::addCommand("child",new Main_child());
					    ....
					    parent::addCommand("childN",new Main_childN());
			      }
		     }		
	  
	  ***/
	  abstract class CommandsContainer
	  {
	    
	    
	      // array containing all the extended classes
		private $_exts = array();
		public $_this;
    
		function __construct(){$this->_this = $this;}
    
		public function addCommand($from="",&$object)
		{
		    $this->_exts[]=array("$from" => &$object);
		    
		}

		public function __get($varname)
		{
		    foreach($this->_exts as &$ext)
		    {
			foreach($ext as $from => &$subext) {
			    if(property_exists($subext,$varname))
					    return $subext->$varname;
			}
		    }
		}

		public function __call($method,$args)
		{
		    foreach($this->_exts as &$ext)
		    {
			foreach($ext as $from => &$subext) {
			    if(method_exists($subext,$method)) {
				    //http://stackoverflow.com/questions/782846/recommended-replacement-for-deprecated-call-user-method
					    return call_user_func_array(array(&$subext, $method),$args);//return call_user_method_array($method,$subext,$args); is deprecated
				    }
			}
		    }
		    throw new Exception("This Method {$method} doesn't exists"."\n");
		}
		
		function &getCommand($name) 
		{
			    foreach($this->_exts as &$ext)
		    {
			foreach($ext as $from => &$subext) {
				    if($from == $name) 
					    return $subext;
			    }
		    }
		}

	  }

	
	      
      class curl_rest {	
		
	    public $type;
	    private $client;
	    
	    /**
	    *
	    *  Get the rest client instance 
	    *
	    *@note this is the main interface
	    *@constructor
	    *@param string type - a plugin type, ie for now FTP or HTTP 
	    **/
	    function __construct($type){
	    //==========================
		$this->type=$type;
	    }
	    
	    /**
	    *
	    *  Get the rest client instance 
	    *
	    *@name client
	    *@access public
	    *@note this is the main interface
	    *@name curl_rest_client
	    *@param string type - a plugin type, ie for now FTP or HTTP 
	    **/
	    function client() {
	    //------------------
		    $type=strtoupper($this->type);
		    $plugin_dir=dirname(__FILE__)."/plugins";
		    if(file_exists("{$plugin_dir}/{$type}.php")) {
			
			$class="{$type}_curl_client";
			if(!class_exists($class))
			    include("{$plugin_dir}/{$type}.php");
			//new $class($params[0],....,$params[n]);
			$params = func_get_args();
			
			$class = new ReflectionClass($class);
			$instance = $class->newInstanceArgs($params);
			$this->client=&$instance;
			return $instance;
			
		    }else {
			die("Unsupported REST client type $type");
		    }
	    }
	    
	    /**
	    *  Proxy function to call methods of the client
	    *
	    *@name _call
	    *@access public
	    *@param $method string - a method of the client of the class "{$type}_curl_client";
	    *@param $args array 
	    **/
	    public function __call($method,$args)
	    {
	    
		try {
		    if($this->client) {
			  $client=&$this->client;
			  if(method_exists($client,$method)) {
				  //http://stackoverflow.com/questions/782846/recommended-replacement-for-deprecated-call-user-method
				  return call_user_func_array(array(&$client, $method),$args);//return call_user_method_array($method,$subext,$args); is deprecated
			  } else
				throw new Exception("This Method {$method} doesn't exists"."\n");
		    } else {
			throw new Exception("curl_rest({$this->type}):Please call method client() first");
		    }
		} catch(Exception $e) {
		    echo("<br>$method ERROR:".$e->getMessage()."<hr>");
		    echo "<pre>";
		    var_dump($e);
		    echo "</pre>";
		}
	    }
	   
	}   
      
?>
