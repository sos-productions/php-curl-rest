<?php


         /**
	*@brief php-curl-rest, example script to do some ftp and http without curl headaches... 
	*
	*@file index.php
	*@package php_curl_rest 
	*@note
	*	V2.0 20170225 - Protocol and doc fix, password and username now given after
	*	V1.0 20170222 - first Version 
	*@author: Olivier LUTZWILLER / all-informatic.com
	*@internal For FTPS facility
	*@compatibility php4/5  
	*@version 3.0 
	*@License LGPL/MIT
	*@Date 22.02.2017
	***/
	
	
      
      // Include the curl_rest_client core 
      include('bootstrap.php');
      
       //Private CURL FTP
      define("DEBUG_CURL_FTP",0);
      define("DEBUG_CURL_FTP_INSPECT_ITEM",1);
      define("DEBUG_CURL_FTP_INFO",1);
      define("CURL_FTP_MAXDEPTH",30);
      define("CURL_FTP_MAXLOGENTRIES",100);
      
      // *** Define your host, username, and password
      
      $ftp_server="ftpes://myserver.com";
      $ftp_port=21;
      $ftp_user_name="my_user";
      $ftp_user_pass="my_pass";
      $ftp_passive=false;
      
      
      die("Please setup your FTP connections params in this file");

      //directory relative to this script
      $localDir="download/";
      $localFileName = "test.txt";
      $remoteDir="/";
      $remoteFileName = "test.txt";

      
      include("credits.php");
      
      Cache_Disallow();
      Credits_start();
      echo "<h1>".get_credits()."</h1><hr>";
    
      $cr=new curl_rest("FTP");
      $cr->client($ftp_server, $ftp_port,$remoteDir,$ftp_passive);
      $cr->set_username($ftp_user_name);
      $cr->set_password($ftp_user_pass);
      
      $cr->command("MLSD"); //Or 
      $cr->command("MKD","tests");
      echo("Current dir:".$cr->command("PWD")."<br>");
      $cr->command("CD","tests");
      $cr->command("DELE","essai2.txt");
      $cr->command("UPLOAD","essai.txt","Ceci est mon essai2");
      echo($cr->command("DOWNLOAD","essai.txt"));
      $cr->command("RNFR","essai.txt");
      $cr->command("RNTO","essai2.txt");
      echo($cr->command("DOWNLOAD","essai2.txt"));
      $cr->command("GET","essai2.txt",$localDir."essai3.txt");
      $cr->command("PUT", $localDir.$localFileName, $remoteFileName);
      $cr->command("PUT", $localDir.$localFileName, $remoteDir.$remoteFileName);
      echo("<br>Current named list (NLIST):");
      var_dump($cr->command("NLIST"));
      echo("<br>Current raw list (RAWLIST):");
      var_dump($cr->command("RAWLIST"));
      echo("<br>Current raw list (RAWLIST - itemized):");
      var_dump($cr->command("RAWLIST",true));
      $cr->command("LS",".");
      $cr->command("MKD","/tests/subtest/a/b/");
      $cr->command("LS",".");
      $cr->command("RMD","-R","/tests/");
      //$fc->logMessage("=== FINISHED ==",true);
      
      
      $cr=new curl_rest("HTTP");
      $cr->client('https://ixquick.com');
      $cr->set_user_agent('Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)');
       //$cr->set_basic_auth_credentials('username', 'password');
      /*echo($cr->command("GET","/"));
      var_dump($cr->command("MGET",array("http://www.shenyun.com","http://www.falundafa.org","http://www.epochtimes.com"),5));
      */
      echo("<br>JOB DONE!");
      Credits_stop();

?>