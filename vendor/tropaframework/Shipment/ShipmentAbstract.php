<?php
namespace Tropaframework\Shipment;
use Tropaframework\Shipment\Model\Package;
use Tropaframework\Shipment\Model\Sender;
use Tropaframework\Shipment\Model\Recipient;
use Tropaframework\Shipment\Model\Order;
use Tropaframework\Shipment\Model\ShipmentMultiple;

/**
 * @NAICHE | Vitor Deco
 */
abstract class ShipmentAbstract
{
	const ENTREGA_SUPERRAPIDO = "Super Rápido";
	const ENTREGA_RAPIDO = "Rápido";
	const ENTREGA_ECONOMICO = "Econômico";
	
	/**
	 * tipo de envio utilizado
	 * @var string
	 */
	private $shipment_type;
	
	/**
	 * define o ambiente
	 * @var bool
	 */
	protected $is_sandbox = false;
	
	public function __construct($shipment_type)
	{
		$this->shipment_type = $shipment_type;
	}
	
	public function getShipmentType()
	{
		return $this->shipment_type;
	}
	
	public function setSandbox($bool)
	{
		$this->is_sandbox = (bool)$bool;
		return $this;
	}
	
	/**
	 * @param Package $package
	 * @param Sender $sender
	 * @param Recipient $recipient
	 * @return ShipmentMultiple
	 */
	public abstract function calcularFrete(Package $package, Sender $sender, Recipient $recipient);
	
	/**
	 * @param Order $order
	 * @return Order
	 */
	public abstract function solicitarEtiqueta(Order $order);
	
	/**
	 * @param Order $order
	 * @return Order
	 */
	public abstract function imprimirEtiqueta(Order $order);

	/**
	 * @param string $codigo
	 */
	public abstract function rastrearPedido($codigo);
}