<?php
namespace Application\Controller;
use Application\Classes\GlobalController;
use Zend\View\Model\ViewModel;
class EquipamentosHomologadosController extends GlobalController
{

    public function indexAction()
    {   
        $this->head->setTitle('EQUIPAMENTOS HOMOLOGADOS');
        return new ViewModel($this->view);
    }

}