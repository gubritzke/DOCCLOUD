<?php
namespace Application\Controller;
use Application\Classes\GlobalController;
use Zend\View\Model\ViewModel;


class FaleConoscoController extends GlobalController
{

    public function indexAction()
    {   
        $this->head->setTitle('FALE CONOSCO');
        return new ViewModel($this->view);
    }

}