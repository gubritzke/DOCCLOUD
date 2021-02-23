<?php
namespace Tropaframework\Shipment;
use Tropaframework\Shipment\Model\Package;
use Tropaframework\Shipment\Model\Sender;
use Tropaframework\Shipment\Model\Recipient;
use Tropaframework\Shipment\Model\ShipmentMultiple;
use Tropaframework\Shipment\Model\Shipment;
use Tropaframework\Shipment\Validate\ValidateCalcularFrete;
use Tropaframework\Shipment\Model\Order;

require_once(dirname(__FILE__) . '/library/autoload.php');

/**
 * @NAICHE | Vitor Deco
 */
class ShipmentMandae extends ShipmentAbstract
{
	//método de envio
	const TYPE = "MANDAE";
	
	//token
	const TOKEN = "d1b36f02b3964e92ffbf572c310808fb";
	
	//token sandbox
	const TOKEN_SANDBOX = "1114ad3112f1dc123f81fc57fb826937";
	
	//class que faz o request na api
	protected $api;
	
	public function __construct()
	{
		//instancia da class que faz o request na api
		$url = "https://api.mandae.com.br/v2";
		$authorization = "Authorization: " . self::TOKEN;
		$this->api = new \Naicheframework\Api\Request($url, $authorization);
		
		//define o método de envio
		parent::__construct(self::TYPE);
	}
	
	public function setSandbox($bool)
	{
		if( $bool === true )
	    {
	    	$url = "https://sandbox.api.mandae.com.br/v2";
	    	$authorization = "Authorization: " . self::TOKEN_SANDBOX;
			$this->api = new \Naicheframework\Api\Request($url, $authorization);
	    }
	    
	    parent::setSandbox($bool);
	    return $this;
	}
	
	public function calcularFrete(Package $package, Sender $sender, Recipient $recipient)
	{
		try {
			
			//validar campos
			$validate = new ValidateCalcularFrete();
			if( !$validate::isNotEmpty($sender->getCep()) )
			{
				throw new \Exception($validate::ERROR_REQUIRED_REMETENTE_CEP);
			}
			
			if( !$validate::isNotEmpty($recipient->getCep()) )
			{
				throw new \Exception($validate::ERROR_REQUIRED_DESTINATARIO_CEP);
			}
			
			$cond1 = !$validate::isNotEmpty($package->getAltura());
			$cond2 = !$validate::isNotEmpty($package->getComprimento());
			$cond3 = !$validate::isNotEmpty($package->getLargura());
			$cond4 = !$validate::isNotEmpty($package->getPeso());
			$cond5 = !$validate::isNotEmpty($package->getValor());
			if( $cond1 || $cond2 || $cond3 || $cond4 || $cond5 )
			{
				throw new \Exception($validate::ERROR_REQUIRED);
			}
			
			//params para enviar
			$params = array();
			$params['declaredValue'] = $package->getValor(); //valor declarado
			$params['weight'] = ($package->getPeso() < 1) ? 1 : $package->getPeso(); //peso bruto da encomenda em kg
			$params['height'] = $package->getAltura(); //altura da encomenda
			$params['width'] = $package->getLargura(); //largura da encomenda
			$params['length'] = $package->getComprimento(); //comprimento da encomenda
			
			//faz a consulta na api
			$url = "postalcodes/" . $recipient->getCep() . "/rates";
			$response = $this->api->call($url, $params, 'POST', true)->result();
			
			//erros
			if( empty($response->shippingServices) )
			{
				throw new \Exception($validate::ERROR_CEP_AREA);
			}
			
			//resultado
			$result = new ShipmentMultiple();
			
			//resultado item
			foreach( $response->shippingServices as $row )
			{
				$servico = mb_strtolower($row->name);
				$servico = (strpos($servico, 'super rápido') !== false) ? self::ENTREGA_SUPERRAPIDO : ((strpos($servico, 'rápido') !== false) ? self::ENTREGA_RAPIDO : self::ENTREGA_ECONOMICO);
				
				$item = new Shipment();
				$item->setServico($servico);
				$item->setPrazo($row->days);
				$item->setValor($row->price);
				$result->addItem($item);
			}
			
			return $result;
			
		} catch( \Exception $e ){
			
			throw new \Exception($e->getMessage(), $e->getCode());
			
		}
	}
	
	public function solicitarEtiqueta(Order $order)
	{
		die("Integração em desenvolvimento!");
	}
	
	public function imprimirEtiqueta(Order $order)
	{
		die("Integração em desenvolvimento!");
	}
	
	public function rastrearPedido($codigo)
	{
		die("Integração em desenvolvimento!");
	}


}