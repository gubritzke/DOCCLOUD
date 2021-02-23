<?php
namespace Tropaframework\Shipment\Service;
use Tropaframework\Shipment\Model\OrderMultiple;
use Tropaframework\Shipment\ShipmentAbstract;
use Tropaframework\Shipment\Model\ShipmentMultiple;
use Tropaframework\Shipment\Model\Shipment;

/**
 * @author: Vitor Deco
 */
class ServicePackage
{
	/**
	 * calcula o frete de todos os pacotes de todos os grupos
	 * @param OrderMultiple $orderMultiple
	 * @param ShipmentAbstract $shipment
	 * @return OrderMultiple
	 */
	public function calcularFretes(OrderMultiple $orderMultiple, ShipmentAbstract $shipment)
	{
		//loop em todos os grupos de pacotes
		foreach( $orderMultiple->getOrder() as $order )
		{
			$sender = $order->getRemetente();
			$recipient = $order->getDestinatario();
			
			//loop em todos os pacotes de um grupo
			foreach( $order->getPackage() as $package )
			{
				$shipments = $shipment->calcularFrete($package, $sender, $recipient);
				$package->setShipments($shipments);
			}
		}
		
		return $orderMultiple;
	}
	
	/**
	 * agrupa os fretes de todos os pacotes de todos os grupo
	 * @param OrderMultiple $orderMultiple
	 * @return ShipmentMultiple
	 */
	public function agruparFretes(OrderMultiple $orderMultiple)
	{
		//resultado
		$shipmentMultiple = new ShipmentMultiple();
		
		//loop em todas as entregas calculadas
		foreach( $orderMultiple->getOrder() as $order )
		{
			foreach( $order->getPackage() as $package )
			{
				foreach( $package->getShipments()->getItens() as $row )
				{
					//verificar se o serviço já existe
					$item = $shipmentMultiple->getItemByService($row->getServico());
					
					if( empty($item->getServico()) )
					{
						//adicionar item
						$item = new Shipment();
						$item->setServico($row->getServico());
						$item->setPrazo($row->getPrazo());
						$item->setValor($row->getValor());
						$item->setObservacao($row->getObservacao());
						$item->setQuantidade(1);
						$shipmentMultiple->addItem($item);
						
					} else {
						
						//definir novo prazo
						$prazo = ($item->getPrazo() >= $row->getPrazo()) ? $item->getPrazo() : $row->getPrazo();
						
						//definir novo valor
						$valor = ($item->getValor() + $row->getValor());
						
						//definir a quantidade de pacotes
						$quantidade = ($item->getQuantidade() + 1);
						
						//atualizar item
						$item->setPrazo($prazo);
						$item->setValor($valor);
						$item->setQuantidade($quantidade);
					}
				}
			}
		}
		
		//remover os serviços que não foram retornados para todos os pacotes
		$countPackage = $orderMultiple->countPackage();
		foreach( $shipmentMultiple->getItens() as $index => $shipment )
		{
			if( $countPackage != $shipment->getQuantidade() )
			{
				$shipmentMultiple->delItem($index);
			}
		}
		
		//echo'<pre>'; print_r($shipmentMultiple); exit;
		return $shipmentMultiple;
	}
	
}