<?php
namespace Model;

use Model\ModelTableGateway\ModelTableGateway;
use Zend\Db\Adapter\Adapter;

/**
 * @author Deco
 */
class Contatos extends ModelTableGateway 
{
	protected $primary_key = '';

	public function __construct($tb, $adapter)
	{
		$this->tb = $tb;

        $metadata = new \Zend\Db\Metadata\Metadata($adapter);
        $this->fields = $metadata->getColumnNames($this->tb->contatos);
        $this->primary_key = $this->fields[0];

		parent::__construct($this->tb->contatos, $adapter);
	}
	
	public function get()
	{
		$qry = $this->sql->select();
		$qry->from(['contatos' => $this->tableName]);

		$qry->group('id_contato');
		
		$result = $this->sql->getSqlStringForSqlObject($qry);
		$result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
		return  $result->toArray();
	}

    public function getFilter($de, $ate)
    {
        $qry = $this->sql->select();
        $qry->from(['contatos' => $this->tableName]);

        if(!empty($de)){
            $de = $this->consertaData($de);
            $qry->where('contatos.criado >= "'.$de.'"');
        }

        if(!empty($ate)){
            $ate = $this->consertaData($ate);
            $qry->where('contatos.criado <= "'.$ate.'"');
        }

        $qry->group('id_contato');

        $result = $this->sql->getSqlStringForSqlObject($qry);
        $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);

        //echo '<pre>'; print_r($result); exit;
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
	    $qry->from(['contatos' => $this->tableName]);
	    $qry->where('contatos.id_contato = '.$id);
	    $qry->group('id_contato');
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