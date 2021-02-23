<?php
namespace Model;

use Model\ModelTableGateway\ModelTableGateway;
use Zend\Db\Adapter\Adapter;

/**
 * @author Deco
 */
class Newsletter extends ModelTableGateway
{
	protected $tb = null;

	protected $primary_key = 'id_newsletter';
	
	public $fields = array('id_newsletter','email','modificado','criado');
		
	public function __construct($tb, $adapter)
	{
		$this->tb = $tb;
		parent::__construct($this->tb->newsletter, $adapter);
	}
	
	public function get()
	{
		$qry = $this->sql->select();
		$qry->from(['newsletter' => $this->tableName]);

		$qry->group('id_newsletter');
		
		$result = $this->sql->getSqlStringForSqlObject($qry);
		$result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
		return  $result->toArray();
	}

    public function getFilter($de, $ate)
    {
        $qry = $this->sql->select();
        $qry->from(['newsletter' => $this->tableName]);

        if(!empty($de)){
            $de = $this->consertaData($de);
            $qry->where('newsletter.criado >= "'.$de.'"');
        }

        if(!empty($ate)){
            $ate = $this->consertaData($ate);
            $qry->where('newsletter.criado <= "'.$ate.'"');
        }

        $qry->group('id_newsletter');

        $result = $this->sql->getSqlStringForSqlObject($qry);
        $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
        return  $result->toArray();
    }
	
	
	
	/**
	 * salva
	 */
	public function set($post,$id=null){
		$result = array(
				'error' => 0,
				'data' => $post,
				'msg' => null,
		);
		
		if($id != null){
			$result['msg'] = 'Contato Editado com sucesso';
		}else{
			$result['msg'] = 'Contato Criado com sucesso';
		}
			
		try{
		    
			$result['data'][$this->getPrimaryKey()] = $this->save($post, $id);
			
		} catch(\Exception $e){
			$result['error'] = 1;
			$result['msg'] = $e->getMessage();
			$result['error_results'] = $this->getErrorResults();
		}
		//view
		return $result;
	}
	
	public function getById($id)
	{
	    $qry = $this->sql->select();
	    $qry->from(['newsletter' => $this->tableName]);
	    $qry->where('newsletter.id_newsletter = '.$id);
	    $qry->group('id_newsletter');
	    $result = $this->sql->getSqlStringForSqlObject($qry);
	    $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
	    return  $result->current();
	}

    protected function consertaData($data){
        $data = explode('/', $data);
        $calendar = $data[2].'-'.$data[1].'-'.$data[0];

        return date('Y-m-d',strtotime(str_replace("/", "-", $calendar))).' 23:59:59';
    }

}