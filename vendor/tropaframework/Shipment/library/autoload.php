<?php
spl_autoload_register( function($class){

	$class = str_replace('\\', '/', $class);
	$class = __DIR__ . '/'. $class . '.php';
// 	echo $class;
	
    if( file_exists($class) )
    {
    	require_once($class);
    	return true;
    }
	
    return false;
    
});