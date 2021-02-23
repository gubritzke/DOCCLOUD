<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Painel for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Painel;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;

class Module implements AutoloaderProviderInterface
{
	public function onBootstrap(MvcEvent $e)
	{
		$eventManager        = $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);
	
		// Register a render event
		$app = $e->getParam('application');
		$app->getEventManager()->attach('render', array($this, 'setLayoutTitle'));
		
		// call event
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
	function beforeDispatch(MvcEvent $e)
	{
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
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    		// if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    /*
     * @NAICHE - Leandro
     */
    public function init(ModuleManager $manager)
    {
    	$events = $manager->getEventManager();
    	$sharedEvents = $events->getSharedManager();
    	 
    	$sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e){
    		$controller = $e->getTarget();
    		if (get_class($controller) == 'Painel\Controller\LoginController') {
    			$controller->layout('painel/login');
    		} else {
    			$controller->layout('painel/layout');
    		}
    	}, 100);
    }
    
}
