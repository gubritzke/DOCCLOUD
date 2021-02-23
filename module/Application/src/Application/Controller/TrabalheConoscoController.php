<?php
namespace Application\Controller;
use Application\Classes\GlobalController;
use Zend\View\Model\ViewModel;

class TrabalheConoscoController extends GlobalController
{

    public function indexAction()
    {   
        $this->head->setTitle('TRABALHE CONOSCO');
        return new ViewModel($this->view);
    }

}