<?php
namespace Painel\Controller;
use Application\Classes\GlobalController;
use Model\EventosPalestrantes;
use Model\Inscricoes;
use Model\Depoimentos;
use Model\Palestrantes;
use Zend\View\Model\ViewModel;
use Model\ModelDepoimentos;
use Model\ModelServicos;
use Model\ModelPrecosServicosComarcas;
use Model\CategoriasEventos;
use Model\MmCategoriasEventos;
use Model\Eventos;
use Model\LocaisEventos;


class DepoimentosController extends GlobalController
{
	public function indexAction()
    {
        //echo '<pre>'; print_r('lalala'); exit;

        $this->view['get'] = $this->params()->fromQuery();
        
		$db = new Depoimentos($this->tb, $this->adapter);
		$this->view['result'] = $db->getFilter($this->view['get']['de'],$this->view['get']['ate'], array('ativo','inativo'));

		//echo '<pre>'; print_r($this->view['result']); exit;
		
 		$page = $this->params()->fromQuery('page', 0);
 		$paginator = new \Zend\Paginator\Paginator( new \Zend\Paginator\Adapter\ArrayAdapter( $this->view['result'] ) );
 		$paginator->setItemCountPerPage( 10 );
 		$paginator->setCurrentPageNumber( $page );
 		$this->view['result'] = $paginator;
		//view
		$view = new ViewModel($this->view);
		return $view;
    }

    public function edtAction(){
	    if(!empty($this->params('id'))){

            $this->view['id'] = $id = $this->params('id');

            $db = new Depoimentos($this->tb, $this->adapter);
            $this->view['post'] = $db->getById($id);


            return new ViewModel($this->view);
        }else{
            return new ViewModel();
        }
    }

    public function saveAction(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            $post = $this->params()->fromPost();
            $files = $this->params()->fromFiles();

            $db = new Depoimentos($this->tb, $this->adapter);
            $retorno = $db->set($post,$files, $post['id_depoimento']);


            $this->flashMessenger()->addSuccessMessage($retorno['msg']);
            return $this->redirect()->toUrl('/painel/'.$this->layout()->routes['controller']);

        }else{
            return $this->redirect()->toUrl('/');
        }
    }


    public function deleteAction(){
        $id = $this->params('id');
        
        $post = array();
        $post['status'] = 'excluido';
        
        $db = new Depoimentos($this->tb, $this->adapter);
        $retorno = $db->save($post,$id);
        
        $this->flashMessenger()->addSuccessMessage('Excluido com sucesso');
        return $this->redirect()->toUrl('/'.$this->layout()->routes['module'].'/'.$this->layout()->routes['controller']);
        
    }

}