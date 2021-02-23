<?php
namespace Application\Classes;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

/**
 * @NAICHE - Deco
 * Esta classe possui funcoes que encurtam as funcoes padroes do ZF2
 */
class GlobalController extends AbstractActionController
{
	/**
	 * set vars to view in controller
	 * @var array
	 */
	protected $view = array();
	
	/**
	 * @var
	 */
	protected $head = null;
	
	/**
	 * Tables
	 *
	 * @var object
	 */
	protected $tb = null;
	
	/**
	 * Database adapter
	 *
	 * @var \Zend\Db\Zend\Db\Adapter\AdapterInterface
	 */
	protected $adapter = null;
	
	/**
	 * Estende o metodo para definir os parametros globais da class
	 *
	 * @return void
	 */
	
	protected function attachDefaultListeners()
	{
		parent::attachDefaultListeners();
	
		$this->tb = $this->getServiceLocator()->get('tb');
		$this->adapter = $this->getServiceLocator()->get('db');
	}
	
	public function modelFactory($model)
	{
	    $model = "\\Model\\" . $model;
	    return new $model($this->adapter);
	}
	
	/**
	 * @see \Zend\Mvc\Controller\AbstractActionController::onDispatch()
	 */
	public function onDispatch(MvcEvent $e)
	{
	    
	    
	    //controlar as permissões de acesso
	    $response = $this->checkPermissions();
	    if( $response !== true ) return $response;
	    
	    //forçar o HTTPS nas páginas selecionadas
	    $response = $this->forceHTTPS();
	    if( $response !== true ) return $response;

		//define o cabeçalho do HTML
		$version = "0001";
		$this->head = new \Tropaframework\Head\Head($this->getServiceLocator(), 'Application', $version);
        $this->head->setJs('https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js', true);

        //libs
        $this->head->setCss('font/flaticon.css');
        $this->head->setCss('hover/hover-min.css');
        $this->head->setCss('animate/animate.css');
        $this->head->setCss('https://use.fontawesome.com/releases/v5.0.6/css/all.css', true);
        $this->head->setCss('https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css',true);
        $this->head->setCss('https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' );
        $this->head->setJs('view/' . $this->layout()->routes['controller'] . '.js');
        //$this->head->setJs('https://code.jquery.com/jquery-3.3.1.js', true);
        $this->head->setJs('init.js');
        $this->head->setJs('effects.js');
        $this->head->setJs('https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js', true);
        $this->head->addCarousel();
        $this->head->setTitle('DOC CLOUD - ');

        $this->head->addMask();
        $this->head->addValidation();

		//suahouse
// 		$this->head->setJs("https://i9abc.housecrm.com.br/track/origem.js", true);
// 		$this->head->setJs("suahouse.js");
		

		
		$this->CEOGoole();
		
		$data = [];
		$data['fundacao'] = 0;
		$data['estrutura'] = 0;
		$data['alvenaria'] = 0;
		$data['revestimento_interno'] = 0;
		$data['revestimento_externo'] = 0;
		$data['conclusao_obra'] = 0;
// 		echo json_encode($data); exit;
		
		//call parent method
		return parent::onDispatch($e);
	}
	
	private function CEOGoole()
	{
	    
	    if ( $_GET['utm_medium'] ) {
	        $_SESSION['utm_medium'] = $_GET['utm_medium'];
	    }
	    
	    if ( $_GET['campaign'] ) {
	        $_SESSION['campaign'] = $_GET['campaign'];
	    }
	    
	    if ( $_GET['utm_source'] ) {
	        $_SESSION['utm_source'] = $_GET['utm_source'];
	    }
	    
	    if ( $_GET['gclid'] ) {
	        $_SESSION['gclid'] = $_GET['gclid'];
	    }
	    
	}
	
	/**
	 * set title in controller
	 * @param string $str
	 */
	public function setTitle($str)
	{
		$this->getServiceLocator()->get('ViewHelperManager')->get('headTitle')->set($str);
	}
	
	/**
	 * set js in controller
	 * @param string $str
	 */
	public function setJS($str, $pos=100)
	{
		$js = $this->getServiceLocator()->get('ViewHelperManager')->get('headScript');
		$js->offsetSetFile($pos, $str, 'text/javascript');
	}
	
	/**
	 * set css in controller
	 * @param string $str
	 */
	public function setCSS($str)
	{
		$css = $this->getServiceLocator()->get('ViewHelperManager')->get('headLink');
		$css->appendStylesheet($str, '');
	}
	
	/**
	 * forçar protocolo HTTPS nas páginas específicas
	 */
	private function forceHTTPS()
	{
	    //define as rotas
	    $routes = $this->layout()->routes;
	
	    //define o ambiente
	    $environment = $this->layout()->config_host['env'];
	
	    //define a url atual
	    $url = $_SERVER['REQUEST_URI'];
	
	    //define o protocolo atual
	    $protocolCurrent = ($_SERVER['HTTPS'] == true) ? 'https://' : 'http://';
	
	    //protocolo do site
	    $cond1 = ($routes['module'] == 'bag365');
	    $cond2 = in_array($routes['controller'], ['login','cadastro','checkout','conta']);
// 		$protocolChange = ( $cond1 && $cond2 ) ? 'https://' : 'http://';
	    $protocolChange = ( $cond1 ) ? 'https://' : 'http://';
	
	    //redirecionamento
	    $cond1 = ($environment != 'local');
	    $cond2 = ($protocolCurrent != $protocolChange);
	    if( $cond1 && $cond2 )
	    {
	        $redirect = $protocolChange . $_SERVER['HTTP_HOST'] . $url;
	        return $this->redirect()->toUrl($redirect);
	    }
	
	    return true;
	}
	
	/**
	 * faz o controle de acesso nas áreas restritas
	 */
	private function checkPermissions()
	{
	    //define as rotas
	    $routes = $this->layout()->routes;
	
	    //define o usuário logado
	    $me = $this->layout()->me;
	    
	    //define a url atual
	    $url = $_SERVER['REQUEST_URI'];
	
	    try {
	        	
	        //acesso restrito ao painel
	        if( ($routes['module'] == 'painel') && empty($me->login) )
	        {
	            //restrito para todos os controllers, exceto o login
	            if( $routes['controller'] != 'login' )
	            {
	                throw new \Exception(false);
	            }
	        }
	        	
	        return true;
	        	
	    } catch( \Exception $e ){
	        	
	        //mensagem de erro
	        if( $e->getMessage() != false )
	        {
	            $this->flashMessenger()->addErrorMessage($e->getMessage());
	        }
	        	
	        //redirecionamento
	        $redirect = ($routes['module'] == 'painel') ? '/painel' : null;;
	        $redirect .= '/login?r=' . $url;
	        
	        return $this->redirect()->toUrl($redirect);
	        	
	    }
	}
	
}