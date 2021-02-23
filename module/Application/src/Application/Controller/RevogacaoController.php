<?php
namespace Application\Controller;
use Application\Classes\GlobalController;
use Zend\View\Model\ViewModel;

class RevogacaoController extends GlobalController
{

    public function indexAction()
    {   
        $this->head->setTitle('REVOGAÇÃO');
        return new ViewModel($this->view);
    }

}