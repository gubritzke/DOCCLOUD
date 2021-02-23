<?php
namespace Application\Controller;
use Application\Classes\GlobalController;
use Zend\View\Model\ViewModel;


class NoticiasController extends GlobalController
{

    public function indexAction()
    {   
        $this->head->setTitle('NOTÍCIAS');
        return new ViewModel($this->view);
    }

    public function detalheAction()
    {
        $this->head->setTitle('NOTÍCIAS INTERNA');
        return new ViewModel($this->view);
    }

}