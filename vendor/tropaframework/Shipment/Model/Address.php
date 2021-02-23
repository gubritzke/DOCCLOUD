<?php
namespace Tropaframework\Shipment\Model;

/**
 * @author: Vitor Deco
 */
class Address extends ModelAbstract
{
    protected $cep;
    protected $logradouro;
    protected $numero;
    protected $complemento;
    protected $bairro;
    protected $cidade;
    protected $estado;
    protected $pais;
    protected $referencia;
	
    public function getCep()
    {
    	return $this->cep;
    }
    
    public function setCep($value)
    {
    	$value = str_replace('-', '', $value);
    	$this->cep = $value;
    }
    
    public function getLogradouro()
    {
        return $this->logradouro;
    }

    public function setLogradouro($value)
    {
        $this->logradouro = $value;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero($value)
    {
        $this->numero = $value;
    }
    
	public function getComplemento()
    {
    	return $this->complemento;
    }
    
    public function setComplemento($value)
    {
    	$this->complemento = $value;
    }
    
    public function getBairro()
    {
    	return $this->bairro;
    }
    
    public function setBairro($value)
    {
    	$this->bairro = $value;
    }
    
    public function getCidade()
    {
    	return $this->cidade;
    }
    
    public function setCidade($value)
    {
    	$this->cidade = $value;
    }
    
    public function getEstado()
    {
    	return $this->estado;
    }
    
    public function setEstado($value)
    {
    	$this->estado = $value;
    }

    public function getPais()
    {
    	return $this->pais;
    }
    
    public function setPais($value)
    {
    	$this->pais = $value;
    }
    
    public function getReferencia()
    {
    	return $this->referencia;
    }
    
    public function setReferencia($value)
    {
    	$this->referencia = $value;
    }
}