<?php
namespace Tropaframework\Payment;
require_once(dirname(__FILE__) . '/library/Requests/Requests.php');
require_once(dirname(__FILE__) . '/library/autoload.php');

use Moip\Moip;
use Moip\Auth\BasicAuth;

/**
 * @NAICHE | Vitor Deco
 */
class PaymentMoip extends PaymentAbstract
{
	//chave pública
	public static $public_key	= '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiIXoSRYoWNgGzkjzaGH8
Z1GSaLjC7hj3cx9Sc9iJ/qSbf4TW7fGLx5QY2b9lJUaSIw4ceOPkBWaixWfFhgGD
KXwxbZW3jgAclIdyTamX5Z4vXo6TJ74Ov4/A4E4LhytdAdAAMQYNed17f+SeMTzt
tXKciWWfjulvuZFrxOF3/4GhmMCTWfZ3YzcPGMhrUmgXt/VkRbgUIVqbqDNavmuE
7Q2vBYo4T6uz5cyerSVH9Un4+ZYwRPoawvUaTP4THBRkf8gUpA+bnFDGMf+Tclk8
+UwaAMRx3ZIfVHWD07LxQ6rF6Hnu02413HRBZQh48UM256bGvnk7+8M1iikJ6pXS
0wIDAQAB
-----END PUBLIC KEY-----';
	
	//chave JS para assinaturas
	protected $key_js	= 'DI9LTOQW4Q4XTCVTAK01OB7OIGEUH9LY';
	
	//token da conta
	protected $token	= 'EIS8L5TZAUIWNZQBZYULTVV0AETXWGCY';
	
	//key da conta
	protected $key		= '1YWTTNDMRL7DD83AH3URNACQBKMPZT9WMBJ43AVR';
	
	//moip ID
	protected $moip_id	= 'MPA-C3617A3740AA';
	
	//instancia da class
	private $payment;
	
	public function __construct()
	{
		//autoloader
		\Requests::register_autoloader();
		
		//define o tipo de pagamento
		parent::__construct("moip");
	}
	
	/**
	 * retorna o status do pagamento
	 * @param integer $status
	 */
	public static function getStatus($status)
	{
		switch( $status )
		{
			case self::STATUS_PENDING:
				return "Aguardando pagamento"; break;
	
			case self::STATUS_PAID:
				return "Pagamento realizado"; break;
					
			case self::STATUS_CANCELED:
				return "Cancelado"; break;
		}
	}
	
	/**
	 * retorna o link do boleto
	 * @param string $code
	 * @param bool $is_sandbox
	 */
	public static function getBoletoLink($code, $is_sandbox=false)
	{
		$env = ($is_sandbox) ? "-sandbox" : null;
		return "https://checkout" . $env . ".moip.com.br/boleto/" . $code . "/print";
	}
	
	public function getTransaction($code)
	{
		try {
			//define qual ambiente foi escolhido
			$sandbox = ($this->is_sandbox) ? Moip::ENDPOINT_SANDBOX : Moip::ENDPOINT_PRODUCTION;
			$this->payment = new Moip(new BasicAuth($this->token, $this->key), $sandbox);
			//echo'<pre>'; print_r($this->payment); exit;
			
			//selecionar os dados do pagamento
			$payment = $this->payment->orders()->payments()->get($code);
			
			//define o status
			$status = $payment->getStatus();
			$status = ($status == "CANCELLED") ? self::STATUS_CANCELED : (($status == "AUTHORIZED") ? self::STATUS_PAID : self::STATUS_PENDING);
			
			$result = (object)array(
				'reference' => $payment->getId(),
				'code' => $payment->getId(),
				'date' => $payment->getUpdatedAt(),
				'status' => $status,
				'transaction' => $payment->getFundingInstrument(),
			);
			
			return $result;

		} catch( Exception $e ){
			
			if( $e->getMessage() != "" )
			{
				throw new \Exception($e->getMessage());
			
			} else {
				$error = $e->getErrors();
				$description = current($error)->getDescription();
				throw new \Exception($description);
			}
		}
	}
	
	public function checkTransactions(array $filters)
	{
	}
	
	public function render()
	{
		try {
			//define qual ambiente foi escolhido
			$sandbox = ($this->is_sandbox) ? Moip::ENDPOINT_SANDBOX : Moip::ENDPOINT_PRODUCTION;
			$this->payment = new Moip(new BasicAuth($this->token, $this->key), $sandbox);
			//echo'<pre>'; print_r($this->payment); exit;
			
			//criar o comprador
			$customer = $this->payment->customers()->setOwnId(uniqid())
			->setFullname($this->sender_data['name'])
			->setEmail($this->sender_data['email'])
			->setTaxDocument($this->sender_data['document'])
			->setPhone($this->sender_data['phone_ddd'], $this->sender_data['phone_number'])
			->setBirthDate($this->sender_data['birthdate'])
			->addAddress(
				'SHIPPING',
				$this->shipping_address['street'], 
				$this->shipping_address['number'],
				$this->shipping_address['district'],
				$this->shipping_address['city'],
				$this->shipping_address['state'],
				$this->shipping_address['postalcode'],
				$this->shipping_address['complement']
			)
			->create();
			//echo'<pre>'; print_r($customer); exit;
			
			//define a identificação única do pedido
			$order = $this->payment->orders()->setOwnId($this->getPaymentId());
			
            //loop nos pedidos agrupados por loja
			foreach( $this->order_items as $receiver => $items )
			{
                //adicionar os itens ao pedido
                foreach( $items as $item )
                {
                    $product = $item['description'];
                    $quantity = (int)$item['quantity'];
                    $detail = $item['id'];
                    $price = (int)($item['amount'] * 100);
                    $order->addItem($product, $quantity, $detail, $price);
                }
            }
            
			//dados do custo de entrega
			if( !empty($this->shipping_cost) )
			{
				$value = (int)($this->shipping_cost * 100);
				$order->setShippingAmount($value);
			}
			
			//adicionar um desconto
			if( !empty($this->payment_discount) )
			{
				$value = (int)($this->payment_discount * 100);
				$order->setDiscount($value);
			}
			
			//vincular comprador com pedido
			$order->setCustomer($customer)->create();
			
			//processar o pagamento cartão de crédito
			if( !empty($this->payment_info['hash']) )
			{
				//dados do dono do cartão
				$customerCreditCard = $this->payment->customers()->setOwnId(uniqid())
				->setFullname($this->payment_info['name'])
				->setEmail($this->sender_data['email'])
				->setTaxDocument($this->sender_data['document'])
				->setPhone($this->sender_data['phone_ddd'], $this->sender_data['phone_number'])
				->setBirthDate($this->sender_data['birthdate'])
				->create();
				
				//dados do cartão
				$hash = $this->payment_info['hash'];
				$installmentCount = (int)$this->payment_info['parcel'];
				$statementDescriptor = $this->payment_info['name'];
				
				//processar pagamento
				$payment = $order->payments()
				->setCreditCardHash($hash, $customerCreditCard)
				->setInstallmentCount($installmentCount)
				->setStatementDescriptor($statementDescriptor)
				->execute();
			
			//processar o pagamento boleto
			} else {
				
				//dados do boleto
				$logo_uri = 'http://' . $_SERVER['REQUEST_URI'] . '/assets/application/img/moip/logo.png';
				$expiration_date = new \DateTime("+2 days");
				$instruction_lines = [];
				
				//processar pagamento
				$payment = $order->payments()
				->setBoleto($expiration_date, $logo_uri, $instruction_lines)
				->execute();
			}
			
			//define o status
			$status = $payment->getStatus();
			$status = ($status == "CANCELLED") ? self::STATUS_CANCELED : (($status == "AUTHORIZED") ? self::STATUS_PAID : self::STATUS_PENDING);
			
			//retorno do pagamento
			return (object)array(
				'id' => $payment->getId(),
				'status' => $status,
				'detail' => $payment->getFundingInstrument(),
			);
			
		} catch( \Exception $e ){
			
			if( $e->getMessage() != "" )
			{
				throw new \Exception($e->getMessage());
			
			} else {
				$error = $e->getErrors();
				if( !empty($error) )
				{
					$description = current($error)->getDescription();
				} else {
					$description = 'Sistema de pagamento indisponível no momento!';
				}
				
				throw new \Exception($description);
			}
		}
	}

	//Pagamento Boleto
	private function paymentBoleto($order, $customer)
	{
		$logo_uri = 'https://cdn.moip.com.br/wp-content/uploads/2016/05/02163352/logo-moip.png';
		$expiration_date = new \DateTime();
		$instruction_lines = ['INSTRUÇÃO 1', 'INSTRUÇÃO 2', 'INSTRUÇÃO 3'];
		try {
			$payment = $order->payments()
			->setBoleto($expiration_date, $logo_uri, $instruction_lines)
			->execute();
			
			return $payment;
			
		} catch (Exception $e) {
			printf($e->__toString());
		}
	}
	
	//Pagamento Crédito
	private function paymentCredit($order, $customer)
	{
		try {
			$payment = $order->payments()
			->setCreditCardHash($this->payment_hash, $customer)
			->setInstallmentCount(3)
			->setStatementDescriptor('teste de pag')
			->setDelayCapture(false)
			->execute();
			
// 			$payment = $order->payments()->setCreditCard(12, 21, '4073020000000002', '123', $customer)
// 			->execute();
			
			return $payment;
			
		} catch (Exception $e) {
			 printf($e->__toString());
			 exit;
		}
	}
	
	//Consultando um pedido
	private function orderGet($order_id)
	{
		try {
			$order = $this->payment->orders()->get($order_id);
			return $order;
			
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	
	//Criando um pedido
	private function orderCreate($customer)
	{
		try {
			$order = $this->payment->orders()->setOwnId(uniqid())
			->addItem("bicicleta 1",1, "sku1", 10000)
			->addItem("bicicleta 2",1, "sku2", 11000)
			->setShippingAmount(3000)->setAddition(1000)->setDiscount(5000)
			->setCustomer($customer)
			->create();
		
			return $order;
			
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	
	//Consultando os dados de um comprador
	private function customerGet()
	{
		try {
			
			$moip_id = "CUS-Y62LPWILTHZ0";
			$customer = $this->payment->customers()->get($moip_id);
			return $customer;
		
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	
	//Criando um comprador
	private function customerCreate($customer_id)
	{
		//Criando um comprador
// 		$customer = $this->customerCreate();
// 		echo'<pre>'; print_r($customer); exit;
		
		//Consultando os dados de um comprador
// 		$customer = $this->customerGet();
// 		echo'<pre>'; print_r($customer); exit;
		
		//Criando um pedido
// 		$order = $this->orderCreate($customer);
// 		echo'<pre>'; print_r($order); exit;
		
		//Consultando um pedido
// 		$order = $this->orderGet('ORD-P1RIQ0U2Q35V');
// 		$order = $this->orderGet('ORD-BIFHKZNSIM19');
// 		echo'<pre>'; print_r($order); exit;
		
		//Criando um pagamento
// 		$payment = $this->paymentCredit($order, $customer);
// 		echo'<pre>'; print_r($payment); exit;
		
		try {
			$customer = $this->payment->customers()->setOwnId($customer_id)
			->setFullname('Teste Teste')
			->setEmail('fulano@email.com')
			->setBirthDate('1988-12-30')
			->setTaxDocument('22222222222')
			->setPhone(11, 66778899)
			->addAddress('BILLING',
				'Rua de teste', 123,
				'Bairro', 'Sao Paulo', 'SP',
				'01234567', 8)
			->addAddress('SHIPPING',
				'Rua de teste do SHIPPING', 123,
				'Bairro do SHIPPING', 'Sao Paulo', 'SP',
				'01234567', 8)
			->create();
			
			return $customer;
				
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}