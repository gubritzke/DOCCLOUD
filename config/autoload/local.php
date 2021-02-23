<?php
return array(
		'db' => array(
        	    'dsn'		=> 'mysql:dbname=doccloud;host=localhost',
        	    'username'	=> 'root',
        	    'password'	=> '',
//         	    'dsn'		=> 'mysql:dbname=tropadigital_360_i9;host=103.195.101.57',
//     		    'username'	=> 'tropadigital_i9',
//     		    'password'	=> 'tropamaneira',
		),

		'view_manager' => array(
				'display_not_found_reason' => true,
				'display_exceptions'       => true,
		),

		'phpSettings' => array(
				'display_startup_errors'        => true,
				'display_errors'                => true,
				'error_reporting'               => E_ALL & ~E_NOTICE,
				'max_execution_time'            => 30,
		),

		//Config Host's Api
		'config_host' => array(
				'env' => 'local',
		),
);