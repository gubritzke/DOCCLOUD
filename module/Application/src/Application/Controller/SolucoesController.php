<?php
namespace Application\Controller;
use Application\Classes\GlobalController;
use Zend\View\Model\ViewModel;


class SolucoesController extends GlobalController
{

    public function indexAction()
    {   
        $this->head->setTitle('SOLUÇÕES');
        return new ViewModel($this->view);
    }

}