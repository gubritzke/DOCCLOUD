<?php
namespace Tropaframework\Shipment\Model;

/**
 * @author: Vitor Deco
 */
class OrderMultiple extends ModelAbstract
{
	/**
	 * @var Order
	 */
	protected $order = array();
	
	/**
	 * criar um novo grupo
	 * @param Order $order
	 */
	public function addOrder(Order $order)
	{
		$this->order[] = $order;
	}
	
	/**
	 * @return Order
	 */
	public function getOrder()
	{
		return $this->order;
	}
	
	/**
	 * @return int
	 */
	public function countOrder()
	{
		$count = 0;
		
		foreach( $this->order as $order )
		{
			$count += $order->countPackage();
		}
		
		return $count;
	}
	
	/**
	 * alias
	 * @return int
	 */
	public function countPackage()
	{
		return $this->countOrder();
	}
	
	/**
	 * @param string $value
	 * @return Order
	 */
	public function getOrderByGroup($value)
	{
		foreach( $this->order as $order )
		{
			if( $order->getGroup() == $value )
			{
				return $order;
			}
		}
		
		$order = new Order();
		return $order;
	}
	
}