<?php
return array(
		'controllers' => array(
			'invokables' => array(
				'Application\Controller\Index'                  => 'Application\Controller\IndexController',
                'Application\Controller\EquipamentosHomologados'=> 'Application\Controller\EquipamentosHomologadosController',
                'Application\Controller\FaleConosco'            => 'Application\Controller\FaleConoscoController',
                'Application\Controller\Noticias'               => 'Application\Controller\NoticiasController',
                'Application\Controller\QuemSomos'              => 'Application\Controller\QuemSomosController',
                'Application\Controller\Repositorio'            => 'Application\Controller\RepositorioController',
                'Application\Controller\Revogacao'              => 'Application\Controller\RevogacaoController',
                'Application\Controller\Solucoes'               => 'Application\Controller\SolucoesController',
                'Application\Controller\TesteDeCertificado'     => 'Application\Controller\TesteDeCertificadoController',
                'Application\Controller\TrabalheConosco'        => 'Application\Controller\TrabalheConoscoController',
			),
		),
        'router' => array(
				'routes' => array(
					'home' => array(
						'type' => 'Zend\Mvc\Router\Http\Literal',
						'options' => array(
							'route'    => '/',
							'defaults' => array(
								'controller' => 'Application\Controller\Index',
								'action'     => 'index',
							),
			       		),
					),
					 
					'application' => array(
						'type'    => 'Segment',
						'options' => array(
							'route'    => '[]',
							'defaults' => array(
	  							'__NAMESPACE__' => 'Application\Controller',
								'controller'    => 'Index',
								'action'        => 'index',
							),
						),
						'may_terminate' => true,
						'child_routes' => array(
							'default' => array(
								'type'    => 'Segment',
								'options' => array(
									'route'    => '/[:controller[/:action][/:id][/]]',
									'constraints' => array(
										'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
										'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
										'id'         => '[0-9]+',
									),
   								    'defaults' => array(
	  							    ),
							    ),
                            ),
                        ),
					),

				    //imóvel
				    'imovel' => array(
				        'type' => 'Segment',
				        'options' => array(
				            'route'    => '/imovel[/:slug][/]',
				            'constraints' => array(
				                'slug' => '[a-zA-Z0-9_-]*',
				            ),
				            'defaults' => array(
				                '__NAMESPACE__' => 'Application\Controller',
				                'module' 		=> 'Application',
				                'controller' 	=> 'Imoveis',
				                'action'     	=> 'detalhe',
				            ),
				        ),
				    ),
				    
				    
				    //newsletter
				    'newsletter' => array(
				        'type' => 'Segment',
				        'options' => array(
				            'route'    => '/newsletter[/:slug][/]',
				            'constraints' => array(
				                'slug' => '[a-zA-Z0-9_-]*',
				            ),
				            'defaults' => array(
				                '__NAMESPACE__' => 'Application\Controller',
				                'module' 		=> 'Application',
				                'controller' 	=> 'Newsletter',
				                'action'     	=> 'detalhe',
				            ),
				        ),
				    ),
				    
				    //newsletter
				    'newsletter_2' => array(
				        'type' => 'Segment',
				        'options' => array(
				            'route'    => '/newsletter',
				            'constraints' => array(
				                'slug' => '[a-zA-Z0-9_-]*',
				            ),
				            'defaults' => array(
				                '__NAMESPACE__' => 'Application\Controller',
				                'module' 		=> 'Application',
				                'controller' 	=> 'Newsletter',
				                'action'     	=> 'index',
				            ),
				        ),
				    ),
				    
				),
		),
		'service_manager' => array(
				'abstract_factories' => array(
						'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
						'Zend\Log\LoggerAbstractServiceFactory',
				),
				'aliases' => array(
						'translator' => 'MvcTranslator',
				),
		),
		'translator' => array(
	        'locale' => 'pt_BR',
	        'translation_file_patterns' => array(
	            array(
	                'type'     => 'gettext',
	                'base_dir' => __DIR__ . '/../language',
	                'pattern'  => '%s.mo',
	            ),
	        ),
	    ),
		'view_manager' => array(
				'not_found_template'       => '/../view/error/404.phtml',
				'exception_template'       => '/../view/error/index.phtml',
				'template_map' => array(
						'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
						'error/404'               => __DIR__ . '/../view/error/404.phtml',
						'error/index'             => __DIR__ . '/../view/error/index.phtml',
						'error/404/debug'         => __DIR__ . '/../view/error/404_debug.phtml',
						'error/index/debug'       => __DIR__ . '/../view/error/index_debug.phtml',
				),
				'template_path_stack' => array(
						__DIR__ . '/../view',
				),
		),

		// Placeholder for console routes
		'console' => array(
				'router' => array(
						'routes' => array(
						),
				),
		),
		'view_helpers' => array(
				'invokables'=> array(
						'example' => 'Application\View\Helper\Example',
						'pagination' => 'Application\View\Helper\Pagination',
						
				)
		),
);
