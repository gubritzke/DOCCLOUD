<?php
namespace Model\ModelTableGateway;
use Zend\Db\TableGateway\TableGateway;

/**
 * @NAICHE - Vitor Deco
 */
class ModelTableGateway
{
	/**
	 * tablename list
	 * @var stdclass
	 */
	protected $tb = null;
	
	/**
	 * sql build
	 * @var \Zend\Db\Sql\Sql
	 */
	protected $sql = null;
	
    /**
     * Table Gateway
     * @var \Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway = null;

    /**
     * Database adapter
     * @var \Zend\Db\Zend\Db\Adapter\AdapterInterface
     */
    protected $adapter = null;

    /**
     * Table Name
     * @var string
     */
    protected $tableName = null;

    /**
     * Primary key
     * @var int
     */
    protected $primary_key = null;

    /**
     * Fields list
     * @var array
     */
    protected $fields = array();
    
    /**
     * Fields required
     * @var array
     */
    protected $required = array();
    
    /**
     * Fields errors in array
     * @var array
     */
    protected $error_result = array();
    
    /**
     * define os valores das variaveis globais da class
     * @param	string $tableName
     * @param	string $adapter
     * @return	void
     */
    public function __construct($tableName, $adapter) 
    {
        $this->tableName = $tableName;
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($tableName, $adapter);
        $this->sql = new \Zend\Db\Sql\Sql($this->adapter);
    }
    
    /**
     * retorna o array de erros
     * @return array
     */
    public function getErrorResults()
    {
    	return $this->error_result;
    }
    
    /**
     * retorna todos os campos obrigatórios
     * @return array
     */
    public function getRequired()
    {
    	return $this->required;
    }
    
	/**
	 * retorna a coluna que foi definida como chave primária
	 * @return number
	 */
    public function getPrimaryKey()
    {
    	return $this->primary_key;
    }
    
    /**
     * retorna o table gateway
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function getTableGateway()
    {
    	return $this->tableGateway;
    }
    
    /**
     * verifica se há erros
     * @return boolean
     */
    public function isValidFields()
    {
    	return (count($this->error_result)) ? false : true;
    }
    
    /**
     * filtra todos os dados, retornando apenas os que foram definidos
     * @param array $fields
     * @param array $data
     * @return array
     */
    public function filter($fields, $data)
    {
    	$filter = array();
    	foreach( $data as $k => $v ) 
    	{
    		if( in_array($k, $fields) )
    		{
    			$filter[$k] = $v;
    		}
    	}
    
    	return $filter;
    }
    
    /**
     * valida campos, se todos os campos estiverem preenchidos
     * @param array $required
     * @param array $data
     * @return this
     */
    public function validate($required, $data)
    {
    	$this->error_result = array();
    	foreach( $required as $field ) 
    	{
    		if( !array_key_exists($field, $data) )
    		{
    			$this->error_result[$field] = 'Campo obrigatório';
    		}
    	}
    	return $this;
    }
    
    /**
     * retorna o ResultSet selecionando tudo no banco
     * @return ResultSet
     */
    public function fetchAll()
    {
    	return $this->tableGateway->select();
    }
    
    /**
     * executa uma query e retorna o resultado em um array
     * @param string $query
     * @return array
     */
    public function query($query)
    {
    	return $this->adapter->query($query, $this->adapter::QUERY_MODE_EXECUTE)->toArray();
    }
    
    /**
     * insere ou atualiza no banco de dados
     * @param array $set
     * @param string|int $id
     * @return int
     */
    public function save($set, $id=null)
    {
    	if( !empty($id) )
    	{
    		$set['modificado'] = date('Y-m-d H:i:s');
    		$set = $this->filter($this->fields, $set);
    		
    		$this->tableGateway->update($set, $this->primary_key . " = '" . $id . "'");
    		return $id;
    		
    	} else {
    		$set['criado'] = date('Y-m-d H:i:s');
    		$set = $this->filter($this->fields, $set);
    		
    		$this->tableGateway->insert($set);
    		return $this->tableGateway->lastInsertValue;
    	}
    }
    
    /**
     * atualiza no banco de dados
     * @param array $set
     * @param string $where
     */
    public function update($set, $where)
    {
    	$set['modificado'] = date('Y-m-d H:i:s');
    	$set = $this->filter($this->fields, $set);
    
    	return $this->tableGateway->update($set, $where);
    }
    
    /**
     * deleta no banco de dados
     * @param string|int $where
     * @return int
     */
    public function delete($where)
    {
    	if( is_numeric($where) )
    	{
    		return $this->tableGateway->delete($this->primary_key . " = '" . $where . "'");
    	
    	} else {
    		return $this->tableGateway->delete($where);
    	}
    }
    
    /**
     * paginação
     * @param string $qry
     * @param number $page_current
     * @param number $records_per_page
     * @return array
     */
    public function pagination($qry, $page_current = 1, $records_per_page = 1)
    {
    	//executa a query
    	$sql = new \Zend\Db\Sql\Sql($this->adapter);
    	$records = $this->adapter->query($sql->getSqlStringForSqlObject($qry), $this->adapter::QUERY_MODE_EXECUTE)->count();
    	//echo json_encode($records); exit;
    	
    	//define offset
    	$page_current = ($page_current>ceil($records/$records_per_page) ? ceil($records/$records_per_page) : $page_current);
    	$page_current = ($page_current<=0) ? 1 : $page_current;
    	$offset = ($page_current-1) * $records_per_page;
    	
    	//define quantos estão sendo exibidos
    	$displaying = ($page_current*$records_per_page);
    	$displaying = ($displaying > $records) ? $records : $displaying;
    	
    	//set pagination vars
    	$pagination = array();
    	$pagination['offset'] = $offset;
    	$pagination['records'] = $records;
    	$pagination['displaying'] = $displaying;
    	$pagination['current'] = $page_current;
    	$pagination['first'] = 1;
    	$pagination['last'] = ceil($records/$records_per_page);
    	$pagination['prev'] = $page_current > 1 ? $page_current - 1 : 1;
    	$pagination['next'] = $page_current < $pagination['last'] ? $page_current + 1 : $pagination['last'];
    	
    	return $pagination;
    }
    
    public function current()
    {
        $this->current = true;
        return $this;
    }
    
    public function execute($qry, $filter)
    {
    	if ($filter['where']){
    		$qry->where($filter['where']);
    	}
    	
    	if ($filter['having']){
    		$qry->having($filter['having']);
    	}
    	
    	if ($filter['order']){
    		$qry->order(new \Zend\Db\Sql\Expression($filter['order']));
    	}
    	
    	if ($filter['page']){
    		$pagination = $this->pagination($qry, $filter['page'], $filter['limit']);
    		$qry->offset($pagination['offset']);
    	}
    	
    	if ($filter['limit']){
    		$qry->limit($filter['limit']);
    	}
    	
    	$result = $this->sql->getSqlStringForSqlObject($qry);
    	$result = $this->adapter->query($result, $this->adapter::QUERY_MODE_EXECUTE)->toArray();
    	
    	if( isset($pagination) ){
    		return array('pagination' => $pagination, 'result' => $result);
    	} else {
    		return $result;
    	}
    }
}