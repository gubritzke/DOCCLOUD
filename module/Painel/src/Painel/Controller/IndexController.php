<?php
namespace Painel\Controller;
use Application\Classes\GlobalController;
use Zend\View\Model\ViewModel;

class IndexController extends GlobalController
{
    public function indexAction()
    {
    	//Feito para resolver problema de rota
    	//if( strpos($this->getRequest()->getUri(), 'index') === false ) $this->redirect()->toUrl('/painel/index');
    	
    	//view
    	return new ViewModel($this->view);
    }
}