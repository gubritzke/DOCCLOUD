<?php
/**
 * Zend Framework (http://framework.zend.com/)
*
* @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
* @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
*/

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
	public function onBootstrap(MvcEvent $e)
	{
		$eventManager        = $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);

		/*
		 * @NAICHE | Leandro
		 * PEGA AS CONFIGURACOES DO CONFIG E SETA NO INI_SET
		 */
		$config = $e->getApplication()->getServiceManager()->get('config');
		$phpSettings = $config['phpSettings'];
		if( $phpSettings ) {
			foreach($phpSettings as $key => $value)
				ini_set($key, $value);
		}

		// Register a render event
		$app = $e->getParam('application');
		$app->getEventManager()->attach('render', array($this, 'setLayoutTitle'));

		/*
		 * @NAICHE - Leandro
		 * VARIAVEIS PARA VIEW
		 */
		$viewModel = $e->getViewModel();
		$viewModel->setVariable('config_host', $config['config_host']);
		

		/*
		 * @NAICHE - Leandro
		 */
		$eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
				$this,
				'beforeDispatch'
		), 100);
		$eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
				$this,
				'afterDispatch'
		), -100);
	}

	/**
	 * called before any controller action called.
	 * @NAICHE - Leandro
	 */
	public function beforeDispatch(MvcEvent $e)
	{
		//set vars to view
		$viewModel = $e->getViewModel();
		 
		/*
		 * @NAICHE - Deco
		 * registra as rotas em uma vari�vel global
		 */
		$matches 	= $e->getRouteMatch();
		$module		= $matches->getParam('__NAMESPACE__');
		$controller	= $matches->getParam('__CONTROLLER__');
		$action		= $matches->getParam('action');
		 
		$routes = array();
		$routes['module']		= strtolower(substr($module, 0, strpos($module, '\\')));
		$routes['controller']	= strtolower($controller);
		$routes['action']		= strtolower($action);
		$viewModel->setVariable('routes', $routes);

		
		
		

		//echo '<pre>'; print_r($lg);exit;
		 
		/*
		 * @NAICHE - Deco
		 * verifica se a sess�o do login est� ativac
		 */
		$session = new \Zend\Session\Container('Auth');
		if( $session->offsetExists('me') )
		{
			$viewModel->setVariable('me', $session->offsetGet('me'));
		}
		
		if( $session->offsetExists('meCliente') )
		{
		    $viewModel->setVariable('meCliente', $session->offsetGet('meCliente'));
		}
		 
		/*
		 * @NAICHE - Deco
		 * inicializa os logs
		 */
		$this->logsInit($viewModel);
	}

	/**
	 * called after any controller action called. Do any operation.
	 * @NAICHE - Leandro
	 */
	public function afterDispatch(MvcEvent $e)
	{
	}

	/**
	 * @param  \Zend\Mvc\MvcEvent $e The MvcEvent instance
	 * @return void
	 */
	public function setLayoutTitle($e)
	{
		//get service manager
		$sm = $e->getApplication()->getServiceManager();

		//get view model
		$viewModel = $e->getViewModel();

		//get view helper manager from the application service manager
		$viewHelperManager = $sm->get('viewHelperManager');

		//to view
		$viewModel->setVariable('headTitle', $viewHelperManager->get('headTitle'));
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
					'Tropaframework' => __DIR__ . '/../../vendor/tropaframework',
				    'Model' => __DIR__ . '/../../model',
				),
			),
		);
	}

	public function getViewHelperConfig()
	{
		return array(
				'factories' => array(
							'message' => function($sm) {
								return new View\Helper\Message($sm->getServiceLocator()->get('ControllerPluginManager')->get('flashmessenger'));
							},
							
						)
				);
	}

	public function getServiceConfig()
	{
		return array(
				'factories' => array(

						'tb' => function ($sm) {
						$tabelas = $sm->get('config');
						return (object) $tabelas['tb'];
						},

						)
				);
	}

	/**
	 * log registrado sempre que uma p�gina � carregada
	 */
	private function logsInit($viewModel)
	{
		//define as rotas
		$routes = $viewModel->getVariable('routes');

		//define o usu�rio
		$me = $viewModel->getVariable('me');

		//define o login
		$login = (!empty($me->login) ? $me->login : false);

		//caso existir, registra o usu�rio logado
		\Tropaframework\Log\Log::setUser($login);

		//registra o log
		\Tropaframework\Log\Log::debug('acesso', array(
				'module' => $routes['module'],
				'controller' => $routes['controller'],
				'action' => $routes['action'],
				'get' => $_GET,
				'post' => $_POST,
		));
	}
}