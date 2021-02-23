<?php
namespace Application\Controller;
use Application\Classes\GlobalController;
use Zend\View\Model\ViewModel;


class QuemSomosController extends GlobalController
{

    public function indexAction()
    {   
        $this->head->setTitle('QUEM SOMOS');
        return new ViewModel($this->view);
    }

}