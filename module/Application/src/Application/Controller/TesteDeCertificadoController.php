<?php
namespace Application\Controller;
use Application\Classes\GlobalController;
use Zend\View\Model\ViewModel;


class TesteDeCertificadoController extends GlobalController
{

    public function indexAction()
    {   
        $this->head->setTitle('TESTE DE CERTIFICADO');
        return new ViewModel($this->view);
    }

}