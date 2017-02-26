<?php

	/**
	*@file credits.php
	*@package php-curl-rest
	*@brief credits and copyrights specification
	*@author see below and read the following statement:
	* After being fed up by looking at the docs to use curl in php, this work is an attempt to 
	*   uniformize curl calls in php and combine all in one point with plugins and commands autoloaded.
	*  curl is a great tool but sometimes tricky to make it work, specially with ftps..Michael C Brant
	*   http_rest inspirated me to do the same with ftp. This is a least one week work please feel free
	*  to contribute and submit patches!
	*  For now I made FTP(S) and HTTP plugin, the plugins command are not limited to standard one
	*   simply add a file command under the directory matching the command name...and voila.
	*and is under copyrights of the author mentioned under RELEASE_AUTHOR
	*@version  V2.0
	*@note
	*	Start of project : 31.01.2017 
	**/

	//==============8< CREDITS STARTS HERE===========8<=====================
	define("RELEASE_PROJECT","php-curl-rest");
	define("RELEASE_VERSION","2.0");
	define("RELEASE_DATE","26.02.2017");
	define('RELEASE_COPYRIGHT','2017');
	define("RELEASE_AUTHOR","Olivier Lutzwiller, Michael C Brant");
	define("RELEASE_LANGUAGE", "en"); 
	//==============8< CREDITS ENDS HERE=============8<=====================


	/**
	*@name  Cache_Disallow
	*@brief Prevent the browser from caching the result.
	*@access public
	*@note this function ensures all the page will be completely processed
	**/
	function Cache_Disallow(){
	//----------------------------------
		
		// Date in the past
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT') ;
		// always modified
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT') ;
		// HTTP/1.1
		header('Cache-Control: no-store, no-cache, must-revalidate') ;
		header('Cache-Control: post-check=0, pre-check=0', false) ;
		// HTTP/1.0
		header('Pragma: no-cache') ;

		// Set the response format.
		header( 'Content-Type:text/html; charset=utf-8' ) ;

		clearstatcache();
	}

	/**
	*@name  Credits_start
	*@brief Generates the credits with the head of the page 
	*@access public
	*@internal In XHTML or in HTML with DTD, iframe 100% height fails..
	*@note this function loads the engine for reporting  results in live 
	**/
	 function Credits_start() {
	 //------------------------------------
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
		echo '<html  lang="'.RELEASE_LANGUAGE.'">';
		echo '<head><title>'.RELEASE_PROJECT.' '.RELEASE_VERSION.' (&copy;'.RELEASE_AUTHOR.')</title>';
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
		echo '<meta name="author" content="'.RELEASE_AUTHOR.'"/>';
		echo '<meta name="copyright" content="&copy; copyright '.RELEASE_COPYRIGHT.',
 '.RELEASE_AUTHOR.'"/>';
		echo '<meta name="robots" content="noindex, nofollow" />';
		echo '</head>';
		echo "\n<body>";
	}

	function get_credits() {
	//-----------------------------
		return 'Welcome on '.RELEASE_PROJECT.' v'.RELEASE_VERSION." ( ".RELEASE_DATE. ' by '.RELEASE_AUTHOR.' )';
	}

	/**
	*@name  Credits_stop
	*@brief Generates the footer 
	*@access public
	*@note this closes the XHTML page
	**/
	  function Credits_stop() {
	 //--------------------------------------
		echo "</body></html>";
	 }


?>
