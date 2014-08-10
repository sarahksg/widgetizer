<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <title>Script to Clear the Cache</title>
  </head>
  <body>
<?php

include('library/widget_functions.php');
 $limit_minutes = (isset($_GET['age'])) ? $_GET['age'] : 60; //the age, in minutes, to leave files in cache
  $verbose = (isset($_GET['verbose'])) ? $_GET['verbose'] : 'on'; //whether to output status and information

 if ($verbose =="on") {
	echo "Use ?age=<number of minutes> if you would like to keep files longer than the default of 60 minutes, or clear ones newer than that.  Use verbose=off/on to show these help and status messages.<br /><br />";
 }
//you can set age on the query string to delete entries except for the ones in the last x minutes. 
//You also can set it to 0 to delete everything.
$limit_minutes = (isset($_GET['age'])) ? $_GET['age'] : 60; //cache files to be kept within this time
$limit_seconds=($limit_minutes * 60); //convert to minutes
//in   echo "the limit seconds is $limit_seconds" ;


 //run function on appropriate paths
 delete_cache('library/cache', $limit_seconds, $verbose);
 delete_cache('library/cache/raintpl',$limit_seconds, $verbose);
if ($verbose =="on") {
 	echo "Done.";
 }

?>
   </body>
</html>