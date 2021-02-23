<?php
namespace Application\Controller;
use Application\Classes\GlobalController;
use Zend\View\Model\ViewModel;


class IndexController extends GlobalController
{

    public function indexAction()
    {   
        $this->head->setTitle('HOME');
        return new ViewModel($this->view);
    }

}