<?php

//=========== DELETE CACHE ==================
  function delete_cache($dirName, $timeLimit, $verbose) {
 
	if (is_dir($dirName)) {
	  $files = scandir($dirName);
	}
	if ($timeLimit == 0) {  //if all files should be cleared
	 if ($verbose =="on") {
		echo "Deleting all files in $dirName...<br />";
		};
	}
	else {
		 if ($verbose =="on") {
			echo "Deleting files in $dirName older than ".($timeLimit/60)." minutes...<br />";
		}
	}
	foreach ($files as $file) {
	  if ($file == '.' || $file == '..') {
	    continue;
	  }

	  if ((fileatime($dirName . '/' . $file) < time() - $timeLimit) && is_file($dirName . '/' . $file)) {
	    if (unlink($dirName . '/' . $file)) {
	       if ($verbose =="on") {
		 	echo "$dirName/$file deleted successfully.";
		}
	    } else {
	     	if ($verbose =="on") {
	      		echo "$dirName/$file not deleted.";
	     	}
	    }
	     if ($verbose =="on") {
	    		echo '<br />';
		}
	  }
	}
	 if ($verbose =="on") {
     	echo '<br />';
     }
} //end delete function

//=========== DELETE CACHE ==================

?>
