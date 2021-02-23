<?php
namespace Tropaframework\Shipment\Model;

/**
 * modelo do destinatário
 * @author: Vitor Deco
 */
class Recipient extends Address
{
    protected $nome;
    protected $documento;
    protected $telefone;
    protected $email;
	
    public function getNome()
    {
    	return $this->nome;
    }
    
    public function setNome($value)
    {
    	$this->nome = $value;
    }

    public function getDocumento()
    {
    	return $this->documento;
    }
    
    public function setDocumento($value)
    {
    	$this->documento = $value;
    }

    public function getTelefone()
    {
    	return $this->telefone;
    }
    
    public function setTelefone($value)
    {
    	$this->telefone = $value;
    }

    public function getEmail()
    {
    	return $this->email;
    }
    
    public function setEmail($value)
    {
    	$this->email = $value;
    }
}