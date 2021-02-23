<?php
namespace Tropaframework\Shipment\Model;

/**
 * modelo de agendamento para retirada de encomendas
 * @author: Vitor Deco
 */
class Schedule extends ModelAbstract
{
	protected $id;
	protected $cliente_id;
	protected $data;
	protected $veiculo;
	protected $endereco;
	protected $observacao;
	
    public function getId()
    {
    	return $this->id;
    }
    
    public function setId($value)
    {
    	$this->id = $value;
    }

    public function getClienteId()
    {
    	return $this->cliente_id;
    }
    
    public function setClienteId($value)
    {
    	$this->cliente_id = $value;
    }

    public function getData()
    {
    	return $this->data;
    }
    
    public function setData($value)
    {
    	$this->data = $value;
    }

    public function getVeiculo()
    {
    	return $this->veiculo;
    }
    
    public function setVeiculo($value)
    {
    	$this->veiculo = $value;
    }

    public function getEndereco()
    {
    	return $this->endereco;
    }
    
    public function setEndereco($value)
    {
    	$this->endereco = $value;
    }

    public function getObservacao()
    {
    	return $this->observacao;
    }
    
    public function setObservacao($value)
    {
    	$this->observacao = $value;
    }
}