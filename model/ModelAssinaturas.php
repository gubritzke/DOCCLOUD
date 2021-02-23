<?php
namespace Model;
use Model\ModelTableGateway\ModelTableGateway;
use Zend\Db\Adapter\Adapter;

class ModelAssinaturas extends ModelTableGateway
{
	protected $primary_key = '';

	
	public function __construct($tb, $adapter)
	{
        $this->tb = $tb;

        $metadata = new \Zend\Db\Metadata\Metadata($adapter);
        $this->fields = $metadata->getColumnNames($this->tb->assinaturas);
        $this->primary_key = $this->fields[0];

        parent::__construct($this->tb->assinaturas, $adapter);
	}

    
	public function get($status = array())
	{
	    $qry = $this->sql->select();
        $qry->from(['assinaturas'=>$this->tableName]);

        if(!empty($status)){
            //ajusta o array para o IN
            for($i=0; $i < sizeof($status); $i++){
                $status[$i]= '"'.$status[$i].'"';
            }
            $str_status = implode(',', $status);
            $qry->where('assinaturas.status IN ('.$str_status.')');
        }


        $result = $this->sql->getSqlStringForSqlObject($qry);
        $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
        return  $result->toArray();
    }

    public function getAssinaturaByUsers(){
        $qry = $this->sql->select();
        $qry->from(['assinaturas'=>$this->tableName]);

        $qry->join(['planos' => $this->tb->planos],
            'planos.id_plano = assinaturas.id_plano',
            ['plano', 'descricao'], $qry::JOIN_LEFT);

        $qry->join(['usuarios' => $this->tb->usuarios],
            'usuarios.id_usuario = assinaturas.id_usuario',
            ['nome','sobrenome','email','nickname','perfil'], $qry::JOIN_LEFT);

        $qry->join(['login' => $this->tb->login],
            'usuarios.id_usuario = login.id_usuario',
            ['id_login','bloqueado'], $qry::JOIN_LEFT);



        $qry->where('(assinaturas.status = "ativo" OR  assinaturas.status = "inativo")');
        $qry->where('assinaturas.id_plano = 2');

        $result = $this->sql->getSqlStringForSqlObject($qry);
        $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
        return  $result->toArray();
    }

    public function getAssinaturaCancelada($id_usuario){
        $qry = $this->sql->select();
        $qry->from(['assinaturas'=>$this->tableName]);

        $qry->join(['planos' => $this->tb->planos],
            'planos.id_plano = assinaturas.id_plano',
            ['plano', 'descricao'], $qry::JOIN_LEFT);

        $qry->where('assinaturas.status = "inativo"');
        $qry->where('assinaturas.id_usuario = '.$id_usuario);
        $qry->where('assinaturas.id_plano = 2');

        $result = $this->sql->getSqlStringForSqlObject($qry);
        $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
        return  $result->current();
    }

    public function getTrialByUsuario($id_usuario){
        $qry = $this->sql->select();
        $qry->from(['assinaturas'=>$this->tableName]);

        $qry->where('assinaturas.status = "ativo"');
        $qry->where('assinaturas.id_usuario = '.$id_usuario);
        $qry->where('assinaturas.id_plano = 1');

        $result = $this->sql->getSqlStringForSqlObject($qry);
        $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
        return  $result->current();
    }

    public function getCodigoAssinaturaByUser($id_usuario){
        $qry = $this->sql->select();
        $qry->from(['assinaturas'=>$this->tableName]);

        $qry->where('assinaturas.status = "ativo"');
        $qry->where('assinaturas.id_usuario = '.$id_usuario);

        $qry->join(['transacoes' => $this->tb->transacoes],
            'transacoes.id_assinatura =  assinaturas.id_assinatura',
            ['codigo'], $qry::JOIN_LEFT);

        $qry->order('transacoes.criado DESC');
        $result = $this->sql->getSqlStringForSqlObject($qry);
        $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
        return  $result->current();
    }

    public function getByIdUsuario($id_usuario){
        $qry = $this->sql->select();
        $qry->from(['assinaturas'=>$this->tableName]);

        $qry->where('assinaturas.status = "ativo"');
        $qry->where('assinaturas.id_usuario = '.$id_usuario);
        $qry->where('assinaturas.id_plano != 1');

        $qry->join(['planos' => $this->tb->planos],
            'planos.id_plano = assinaturas.id_plano',
            ['plano', 'descricao'], $qry::JOIN_LEFT);

        $result = $this->sql->getSqlStringForSqlObject($qry);
        $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
        return  $result->current();
    }

    public function getAssinaturasExistentesByUsuario($id_usuario, $status = array()){
        $qry = $this->sql->select();
        $qry->from(['assinaturas'=>$this->tableName]);

        $qry->where('assinaturas.id_usuario = '.$id_usuario);
        $qry->where('assinaturas.id_plano != 1');

        if(!empty($status)){
            //ajusta o array para o IN
            for($i=0; $i < sizeof($status); $i++){
                $status[$i]= '"'.$status[$i].'"';
            }
            $str_status = implode(',', $status);
            $qry->where('assinaturas.status IN ('.$str_status.')');
        }

        $result = $this->sql->getSqlStringForSqlObject($qry);
        $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
        return  $result->current();
    }

    public function getByIdUsuarioMensal($id_usuario){
        $qry = $this->sql->select();
        $qry->from(['assinaturas'=>$this->tableName]);



        $qry->join(['planos' => $this->tb->planos],
            'planos.id_plano = assinaturas.id_plano',
            ['plano', 'descricao'], $qry::JOIN_LEFT);

        $qry->where('planos.id_plano = 2');
        $qry->where('assinaturas.status = "ativo"');
        $qry->where('assinaturas.id_usuario = '.$id_usuario);

        $result = $this->sql->getSqlStringForSqlObject($qry);
        $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
        return  $result->current();
    }
	
	public function set($post,$id=null){
	    $result = array(
	        'error' => 0,
	        'data' => $post,
	        'msg' => null,
	    );
	
	    if($id != null){
	        $result['msg'] = 'Assinatura Editada com sucesso';
	    }else{
	        $result['msg'] = 'Assinatura Criada com sucesso';
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



    public function getById($id){
        $qry = $this->sql->select();
        $qry->from(['assinaturas'=>$this->tableName]);
        $qry->where('assinaturas.id_assinatura = '.$id);
        $result = $this->sql->getSqlStringForSqlObject($qry);
        $result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE);
        return  $result->current();

    }

    public function setTrial($id_usuario){
	    $post = array();
	    $post['id_usuario'] = $id_usuario;
	    $post['id_plano'] = 1;

        $dt = date("Y-m-d");
	    $post['validade'] = date( "Y-m-d", strtotime( "$dt +7 days" ) );
	    $post['status'] = "ativo";


	    return $this->set($post, null);
    }



}