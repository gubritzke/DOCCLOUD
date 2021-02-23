<?php
namespace Tropaframework\Shipment\Model;

/**
 * @author: Vitor Deco
 */
class Item extends ModelAbstract
{
	protected $id;
	protected $descricao;
	protected $ean;
	protected $preco;
	protected $quantidade;
	protected $peso;
	protected $comissao;
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setId($value)
	{
		$this->id = $value;
	}

	public function getDescricao()
	{
		return $this->descricao;
	}
	
	public function setDescricao($value)
	{
		$this->descricao = $value;
	}

	public function getEan()
	{
		return $this->ean;
	}
	
	public function setEan($value)
	{
		$this->ean = $value;
	}

	public function getPreco()
	{
		return $this->preco;
	}
	
	public function setPreco($value)
	{
		$this->preco = $value;
	}

	public function getQuantidade()
	{
		return $this->quantidade;
	}
	
	public function setQuantidade($value)
	{
		$this->quantidade = $value;
	}
	
	public function getPeso()
	{
		return $this->peso;
	}
	
	public function setPeso($value)
	{
		$this->peso = $value;
	}

	public function getComissao()
	{
		return $this->comissao;
	}
	
	public function setComissao($value)
	{
		$this->comissao = $value;
	}
}