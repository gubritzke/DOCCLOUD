<?php
namespace Tropaframework\Shipment\Model;

use Tropaframework\Payment\ShipmentAbstract;

/**
 * @author: Vitor Deco
 */
class Package extends ModelAbstract
{
	protected $peso;
    protected $altura;
    protected $comprimento;
    protected $largura;

    /**
     * valor de todos os itens do pacote
     * @var number
     */
    protected $valor;
    
    /**
     * tipo do pacote pode ser Caixa ou Cilindro
     * @var string
     */
    protected $tipo;

    /**
     * código de rastreio
     * @var string
     */
    protected $rastreio;
    
    /**
     * ids dos itens que estão no pacote
     * @var array
     */
    protected $ids = array();
    
    /**
     * @var ShipmentMultiple
     */
    protected $shipments;
    
    public function getValor()
    {
    	return $this->valor;
    }
    
    public function setValor($value)
    {
    	$this->valor = $value;
    }
    
    public function getPeso()
    {
    	return $this->peso;
    }
    
    public function setPeso($value)
    {
    	//peso em gramas, faz o cálculo para transformar 150 em 0.150
    	$this->peso = $value / 1000;
    }
    
    public function getAltura()
    {
    	return $this->altura;
    }
    
    public function setAltura($value)
    {
    	$this->altura = $value;
    }
    
    public function getComprimento()
    {
    	return $this->comprimento;
    }
    
    public function setComprimento($value)
    {
    	$this->comprimento = $value;
    }
    
    public function getLargura()
    {
        return $this->largura;
    }
	
    public function setLargura($value)
    {
        $this->largura = $value;
    }
	
    public function getTipo()
    {
        return $this->tipo;
    }
	
    public function setTipo($value)
    {
        $this->tipo = $value;
    }
    
    public function getRastreio()
    {
    	return $this->rastreio;
    }
    
    public function setRastreio($value)
    {
    	$this->rastreio = $value;
    }
    
    public function getId()
    {
    	return $this->ids;
    }
    
    public function addId($value)
    {
    	$this->ids[] = (int)$value;
    }
	
    public function countIds()
    {
    	return count($this->ids);
    }
    
    /**
     * @return ShipmentMultiple
     */
    public function getShipments()
    {
    	return $this->shipments;
    }
    
    public function setShipments(ShipmentMultiple $shipmentMultiple)
    {
    	$this->shipments = $shipmentMultiple;
    }
}