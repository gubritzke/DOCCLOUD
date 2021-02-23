<?php
namespace Tropaframework\Shipment\Model;

/**
 * @author: Vitor Deco
 */
class ShipmentMultiple extends ModelAbstract
{
	/**
	 * @var Shipment
	 */
	protected $itens = array();
	
	public function toArray()
	{
		$result = array();
		foreach( $this->itens as $item ) $result[$item->getServico()] = $item->toArray();
		return $result;
	}
	
	public function toObject()
	{
	    $result = array();
	    foreach( $this->itens as $item ) $result[$item->getServico()] = $item->toObject();
	    return $result;
	}
	
    public function addItem(Shipment $shipment)
    {
        $this->itens[] = $shipment;
    }
    
    public function delItem($index)
    {
    	unset($this->itens[$index]);
    }
	
    /**
     * @return Shipment
     */
    public function getItens()
    {
        return $this->itens;
    }
	
    /**
     * @return number
     */
    public function itemLenght()
    {
        return count($this->itens);
    }
    
    /**
     * @param string $value
     * @return Shipment
     */
    public function getItemByService($value)
    {
    	foreach( $this->itens as $item )
    	{
    		if( $item->getServico() == $value )
    		{
    			return $item;
    		}
    	}
    	
    	$item = new Shipment();
    	return $item;
    }
}