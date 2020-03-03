<?php 

add_action( 'init', 'clear_large_autoptimize_cache' );

function clear_large_autoptimize_cache () {
	# Automatically clear autoptimizeCache if it goes beyond 256MB
	if (class_exists('autoptimizeCache')) {
		 $myMaxSize = 256000; # You may change this value to lower like 100000 for 100MB if you have limited server space
		 $statArr=autoptimizeCache::stats(); 
		 $cacheSize=round($statArr[1]/1024);
		 if ($cacheSize>$myMaxSize){
			 autoptimizeCache::clearall();
			 header("Refresh:0"); # Refresh the page so that autoptimize can create new cache files
		 }
	}
}