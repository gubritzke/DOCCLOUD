<?php
namespace Model;

use Model\ModelTableGateway\ModelTableGateway;
use Zend\Db\Adapter\Adapter;

/**
 * @author Deco
 */
class Equipamentos extends ModelTableGateway
{
	protected $primary_key = '';

	public function __construct($tb, $adapter)
	{
		$this->tb = $tb;

        $metadata = new \Zend\Db\Metadata\Metadata($adapter);
        $this->fields = $metadata->getColumnNames($this->tb->equipamentos);
        $this->primary_key = $this->fields[0];

		parent::__construct($this->tb->equipamentos, $adapter);
	}
	
	public function get()
	{
		$qry = $this->sql->select();
		$qry->from(['equipamentos' => $this->tableName]);

		$qry->group('id_equipamento');
		
		$result = $this->sql->getSqlStringForSqlObject($qry);
		$result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
		return  $result->toArray();
	}

    public function getFilter($de, $ate, $status = array())
    {
        $qry = $this->sql->select();
        $qry->from(['equipamentos' => $this->tableName]);


        if(!empty($status)){
            //ajusta o array para o IN
            for($i=0; $i < sizeof($status); $i++){
                $status[$i]= '"'.$status[$i].'"';
            }
            $str_status = implode(',', $status);
            $qry->where('equipamentos.status IN ('.$str_status.')');
        }

        if(!empty($de)){
            $de = $this->consertaData($de);
            $qry->where('equipamentos.criado >= "'.$de.'"');
        }

        if(!empty($ate)){
            $ate = $this->consertaData($ate);
            $qry->where('equipamentos.criado <= "'.$ate.'"');
        }

        $qry->group('id_equipamento');
        $qry->order('equipamentos.ordem ASC');

        $result = $this->sql->getSqlStringForSqlObject($qry);
        $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);

        //echo '<pre>'; print_r($result); exit;
        return  $result->toArray();
    }
	
	
	
	/**
	 * salva
	 */
	public function set($post,$file = null,$id=null ){
		$result = array(
				'error' => 0,
				'data' => $post,
				'msg' => null,
		);
		
		if($id != null){
			$result['msg'] = 'Equipamento Editado com sucesso';
		}else{
			$result['msg'] = 'Equipamento Criado com sucesso';
		}
			
		try{

            //  Usar em caso de upload de imagem ou arquivo
            if(!empty($file['imagem'])){
                if(!empty($file['imagem']['name'])){
                    $upload = new \Tropaframework\Upload\Upload();
                    $uploadResult = $upload->setExtensions(['jpg','png'])->file($file['imagem'], "equipamentos");
                    if( $uploadResult === false ) throw new \Exception($upload->getError());
                    $post['imagem'] = str_replace("equipamentos\\","",$upload->getFilenameCurrent() );
                }
            }
		    
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
	    $qry->from(['equipamentos' => $this->tableName]);
	    $qry->where('equipamentos.id_equipamento = '.$id);
	    $qry->group('id_equipamento');
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