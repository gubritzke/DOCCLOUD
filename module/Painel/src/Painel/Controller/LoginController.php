<?php
namespace Painel\Controller;
use Application\Classes\GlobalController;
use Zend\View\Model\ViewModel;

class LoginController extends GlobalController
{
    public function indexAction()
    {
    	//call actions
    	$method = $this->params()->fromPost('method');
    	if( method_exists($this, $method) ) $this->$method();
    	
    	//view
    	$this->setTitle('Login');
    	return new ViewModel($this->view);
    }
   	
    public function logoutAction()
    {
    	//destroy session
    	$session = new \Zend\Session\Container('Auth');
    	$session->getManager()->destroy();
    	
    	//redirecionamento
    	return $this->redirect()->toUrl('/painel/login');
    }
	
	protected function logarAdminAction()
    {
    	//login
    	$login = $this->params()->fromPost('lg');
    	
    	//password
    	$password = $this->params()->fromPost('pw');
    	
    	//redirecionamento
    	$redirect = !empty($this->params()->fromPost('r')) ? $this->params()->fromPost('r') : '/painel';
    	
    	try {
    		//verifica se os dados são válidos
    		$AuthAdapter = new \Zend\Authentication\Adapter\DbTable(
    			$this->adapter, 
    			$this->tb->login_painel, 
    			'login', 
    			'senha', 
    			'md5(?)'
    		);
    		$AuthAdapter->setIdentity($login);
    		$AuthAdapter->setCredential($password);
    		
    		//retorna erro caso inválido
    		if( !$AuthAdapter->authenticate()->isValid() ) 
    		{
    			throw new \Exception('Dados inválidos.');
    		}
    		
    		//dados retornados do login
    		$resultAuth = $AuthAdapter->getResultRowObject();

    		//echo '<pre>'; print_r($resultAuth); exit;

    		//set session
    		$session = new \Zend\Session\Container('Auth');
    		$session->offsetSet('me', $resultAuth);
    		
    		//echo '<pre>'; print_r($resultAuth); exit;
    		
    	} catch(\Exception $e){
    		$this->flashMessenger()->addErrorMessage($e->getMessage());
    	}
    	
    	//redirecionamento
    	return $this->redirect()->toUrl($redirect);
    }
}