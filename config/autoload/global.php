<?php
return array(
		'db' => array(
				'driver'	=> 'Pdo',
				'dsn'		=> 'mysql:dbname=tropadigital_i9;host=vps.tropa.digital',
				'username'	=> 'tropadigital_i9',
				'password'	=> '@mudar123',
				'driver_options' => array(
						PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
				),
		),

		'service_manager' => array(
				'factories' => array(
						'db' => 'Zend\Db\Adapter\AdapterServiceFactory',
				),
		),
		
		'tb' => array(
            'login_painel'=>'login_painel',
            'contatos'=>'contatos',
            'newsletter'=>'newsletter',
            'trabalhe_conosco'=>'trabalhe_conosco',
            'depoimentos'=>'depoimentos',
            'equipamentos'=>'equipamentos'
		),

		'view_manager' => array(
				'base_path' 				=> '/',
				'display_not_found_reason'	=> false,
				'display_exceptions'		=> false,
				'doctype'                  	=> 'HTML5',
				'not_found_template'       	=> 'error/404',
				'exception_template'       	=> 'error/index',
		),

		'phpSettings'   => array(
				'display_startup_errors'        => false,
				'display_errors'                => false,
				'error_reporting'               => 0,
				'max_execution_time'            => 60,
				'date.timezone'                 => 'America/Sao_Paulo',
				'default_charset'               => 'UTF-8',
		),

		//Config Host's Api
		'config_host' => array(
				'env' => 'production',
		),

);