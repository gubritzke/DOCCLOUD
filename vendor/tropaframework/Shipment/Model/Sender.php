<?php
namespace Tropaframework\Shipment\Model;

/**
 * modelo do remetente
 * @author: Vitor Deco
 */
class Sender extends Address
{
    protected $nome;

    public function getNome()
    {
    	return $this->nome;
    }
    
    public function setNome($value)
    {
    	$this->nome = $value;
    }
}