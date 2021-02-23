<?php
namespace Application\Controller;
use Application\Classes\GlobalController;
use Zend\View\Model\ViewModel;


class RepositorioController extends GlobalController
{

    public function indexAction()
    {   
        $this->head->setTitle('REPOSITÓRIO');
        return new ViewModel($this->view);
    }

}