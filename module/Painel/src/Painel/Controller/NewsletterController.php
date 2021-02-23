<?php
namespace Painel\Controller;
use Application\Classes\GlobalController;
use Model\EventosPalestrantes;
use Model\Inscricoes;
use Model\Newsletter;
use Model\Palestrantes;
use Zend\View\Model\ViewModel;
use Model\ModelContatos;
use Model\ModelServicos;
use Model\ModelPrecosServicosComarcas;
use Model\CategoriasEventos;
use Model\MmCategoriasEventos;
use Model\Eventos;
use Model\LocaisEventos;


class NewsletterController extends GlobalController
{
	public function indexAction()
    {
        //echo '<pre>'; print_r('lalala'); exit;

        $this->view['get'] = $this->params()->fromQuery();
        
		$db = new Newsletter($this->tb, $this->adapter);
		$this->view['result'] = $db->getFilter($this->view['get']['de'],$this->view['get']['ate']);

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
    


    public function excelAction(){

        $db = new Newsletter($this->tb, $this->adapter);
        $result =  $db->get();


        $csv = '';

        $csv .= 'email;';
        $csv .= 'criado;';

        $csv .= PHP_EOL;

        foreach ($result as $row) {
            $csv .= $row['email'] . ';';
            $csv .= $row['criado'] . ';';

            $csv .= PHP_EOL;
        }

        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename=newsletter.csv');
        header('Content-Encoding: UTF-8');
        header('Pragma: no-cache');
        echo $csv; exit;
    }

    
    public function deleteAction(){
        $id = $this->params('id');
        
        $post = array();
        $post['status'] = 'excluido';
        
        $db = new Newsletter($this->tb, $this->adapter);
        $retorno = $db->delete('id_newsletter = '.$id);
        
        $this->flashMessenger()->addSuccessMessage('Excluido com sucesso');
        return $this->redirect()->toUrl('/'.$this->layout()->routes['module'].'/'.$this->layout()->routes['controller']);
        
    }

}