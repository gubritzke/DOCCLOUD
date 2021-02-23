<?php
namespace Tropaframework\Payment;
require_once(dirname(__FILE__) . '/library/Requests/Requests.php');
require_once(dirname(__FILE__) . '/library/autoload.php');

use Moip\Moip;
use Moip\Auth\BasicAuth;
use Moip\Auth\OAuth;

/**
 * @NAICHE | Vitor Deco
 */
class PaymentMoipSplit extends PaymentAbstract
{
	//chave pública
	protected $public_key	= '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjvUGCQF7chP2LdeX6PMq
hfKHTqYVkl5aBqVHtysZJAq2hg0d5V0Eae659F+/BOb6BQvzfI/bvyhN522cZYmw
tNwi08qRYHBsY8+a2Js7ql5dOvObBLEo/d5VESwUnqukJGCei/B67xo8o64e348/
AGmd8EFRDa2S9D0a+ODAXmtsteFewKctGzuU8rJZEGRWI16HpMEBsaFT5CtL81/L
RKXcGp2S7BaTbzG2klJV5dzzJblfYV+ZiZHr1f7olozyrqBoGc7XukyysjaaWcpK
YlY87ar+UyDOt3J6+SeWI/uqtksVTZBlA74ojtSwhqg7VmKA/vrQgoaL+Fhzm/A+
XwIDAQAB
-----END PUBLIC KEY-----';
	
	//chave JS para assinaturas
	protected $key_js	= 'KNDRPHJFFMPNRN0BKJZ39GH1GNA6CWGH';
	
	//token da conta
	protected $token	= '6PEWLACNVXWRVIIKPBXMDMOSYVMADFJU';
	
	//key da conta
	protected $key		= 'MAZVI73PW60SYYCSLR53TEWPNHWPZV229ZIXRJU8';
	
	//moip ID
	protected $moip_id	= 'MPA-2473045E51C7';
	
	//instancia da class
	private $payment;

	public function __construct()
	{
	    //autoloader
	    \Requests::register_autoloader();
	
	    //define o tipo de pagamento
	    parent::__construct("moip-split");
	}
	
	public function setSandbox($bool=true)
	{
	    if( $bool === true )
	    {
	        $this->public_key = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiIXoSRYoWNgGzkjzaGH8
Z1GSaLjC7hj3cx9Sc9iJ/qSbf4TW7fGLx5QY2b9lJUaSIw4ceOPkBWaixWfFhgGD
KXwxbZW3jgAclIdyTamX5Z4vXo6TJ74Ov4/A4E4LhytdAdAAMQYNed17f+SeMTzt
tXKciWWfjulvuZFrxOF3/4GhmMCTWfZ3YzcPGMhrUmgXt/VkRbgUIVqbqDNavmuE
7Q2vBYo4T6uz5cyerSVH9Un4+ZYwRPoawvUaTP4THBRkf8gUpA+bnFDGMf+Tclk8
+UwaAMRx3ZIfVHWD07LxQ6rF6Hnu02413HRBZQh48UM256bGvnk7+8M1iikJ6pXS
0wIDAQAB
-----END PUBLIC KEY-----';
	
	        $this->key_js	= 'DI9LTOQW4Q4XTCVTAK01OB7OIGEUH9LY';
	
	        //token da conta
	        $this->token	= 'EIS8L5TZAUIWNZQBZYULTVV0AETXWGCY';
	
	        //key da conta
	        $this->key		= '1YWTTNDMRL7DD83AH3URNACQBKMPZT9WMBJ43AVR';
	
	        //moip ID
	        $this->moip_id	= 'MPA-C3617A3740AA';
	    }
	     
	    parent::setSandbox($bool);
	    return $this;
	}
	
	public function getPublicKey()
	{
	    return $this->public_key;
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
			$payment = $this->payment->payments()->get($code);
			//echo'<pre>'; print_r($payment); exit;
            
			//define o status
			$status = $payment->getStatus();
			$status = ($status == "CANCELLED") ? self::STATUS_CANCELED : (($status == "AUTHORIZED") ? self::STATUS_PAID : self::STATUS_PENDING);
			
			//informações sobre a transação
			$transaction = current($payment->getPayments())->fundingInstrument;
			
			//array para retornar
			$result = (object)array(
				'reference' => $payment->getId(),
				'code' => $payment->getId(),
				'date' => $payment->getUpdatedAt(),
				'status' => $status,
				'transaction' => $transaction,
			);
			
			return $result;

        } catch (\Moip\Exceptions\UnautorizedException $e) {
            //StatusCode 401
            throw new \Exception($e->getMessage());
            
        } catch (\Moip\Exceptions\ValidationException $e) {
            //StatusCode entre 400 e 499 (exceto 401)
            printf($e->__toString());
            
        } catch (\Moip\Exceptions\UnexpectedException $e) {
            //StatusCode >= 500
           throw new \Exception($e->getMessage());
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
			
			//limpar os caracteres
			$this->sender_data['name'] = \Naicheframework\Helper\Convert::removeEspecialChars($this->sender_data['name'], true);
			$this->shipping_address['street'] = \Naicheframework\Helper\Convert::removeEspecialChars($this->shipping_address['street'], true);
			$this->shipping_address['district'] = \Naicheframework\Helper\Convert::removeEspecialChars($this->shipping_address['district'], true);
			
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
			
			//criar multipedidos
			$multiorder = $this->payment->multiorders()->setOwnId($this->getPaymentId());
            
            //quantidade de pedidos
            $orderCount = count($this->order_items);
            
			//loop nos pedidos agrupados por loja
			//echo'<pre>'; print_r($this->order_items); exit;
			foreach( $this->order_items as $receiver => $items )
			{
				//definir as comissões em porcentagem
				$percentual_primary = (int)$items['store']['percentual'];
				$percentual_secondary = (int)(100 - $percentual_primary);
				unset($items['store']);
				
				//calcular subtotal dos pedidos desse grupo
				$subtotal = 0;
				
				//define a identificação única do pedido
				$order = $this->payment->orders()->setOwnId($this->getPaymentId());
				
                //definir o frete do pedido
                $shippingCost = 0;
                
				//adicionar os itens ao pedido
				foreach( $items as $item )
				{
                    $shippingCost += $item['shippingCost'];
                    
					$product = \Naicheframework\Helper\Convert::removeEspecialChars($item['description'], true);
					$quantity = (int)$item['quantity'];
					$detail = $item['id'];
					$price = (int)($item['amount'] * 100);
					$subtotal += ($item['amount'] * $item['quantity']);
					$order->addItem($product, $quantity, $detail, $price);
				}
				
				//dados do custo de entrega
				if( !empty($shippingCost) )
				{
					$value = (int)($shippingCost * 100);
					$order->setShippingAmount($value);
				}
				
				//adicionar um desconto
				if( !empty($this->payment_discount) )
				{
					$value = (int)($this->payment_discount * 100 / $orderCount);
					$order->setDiscount($value);
					
					//calcular o subtotal com desconto
					$subtotal = ($subtotal - $this->payment_discount);
				}
                
				//vincular comprador
				$order->setCustomer($customer);
                
				//definir as comissões com valores fixos
				$fixed_primary = round(($subtotal * $percentual_primary / 100), 2);
				$fixed_secondary = round(($subtotal - $fixed_primary), 2);
				$fixed_primary = $fixed_primary * 100;
				$fixed_secondary = $fixed_secondary * 100;
				//echo $fixed_primary; exit;
				
                //vincular vendedores
				$order->addReceiver($this->moip_id, 'PRIMARY');
				$order->addReceiver($receiver, 'SECONDARY', $fixed_secondary);
                
                //add pedido
				$multiorder->addOrder($order);
			}
			$multiorder->create();
			//echo'<pre>'; print_r($multiorder); exit;
			
			//processar o pagamento CARTÃO DE CRÉDITO
			/* 
			 * Cartão fake para testes
			 * 4012001037141112
			 * 123
			 * 12/18
			 */
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
				$statementDescriptor = "bag365.com.br"; //nome na fatura
				
				//processar pagamento
				$payment = $multiorder->multipayments()
				->setCreditCardHash($hash, $customerCreditCard)
				->setInstallmentCount($installmentCount)
				->setStatementDescriptor($statementDescriptor)
				->execute();
			
			//processar o pagamento BOLETO
			} else {
				
			    //define a url atual
			   	$url = $_SERVER['HTTP_HOST'];
			    
			    //define o protocolo atual
			    $protocol = ($_SERVER['HTTPS'] == true) ? 'https://' : 'http://';
			    
				//dados do boleto
				$logo_uri = $protocol . $url . '/assets/application/img/moip/logo.png';
				$expiration_date = new \DateTime("+2 days");
				$instruction_lines = [];
				
				//processar pagamento
				$payment = $multiorder->multipayments()
				->setBoleto($expiration_date, $logo_uri, $instruction_lines)
				->execute();
			}
			//echo'<pre>'; print_r($payment); exit;
			
			//define o status
			$status = $payment->getStatus();
			$status = ($status == "CANCELLED") ? self::STATUS_CANCELED : (($status == "AUTHORIZED") ? self::STATUS_PAID : self::STATUS_PENDING);
			
			//informações sobre a transação
			$transaction = current($payment->getPayments())->fundingInstrument;
			
			//retorno do pagamento
			return (object)array(
				'id' => $payment->getId(),
				'status' => $status,
				'detail' => $transaction,
			);
			
        } catch (\Moip\Exceptions\UnautorizedException $e) {
            //StatusCode 401
            throw new \Exception($e->getMessage());
            
        } catch (\Moip\Exceptions\ValidationException $e) {
            //StatusCode entre 400 e 499 (exceto 401)
            printf($e->__toString());
            
        } catch (\Moip\Exceptions\UnexpectedException $e) {
            //StatusCode >= 500
           throw new \Exception($e->getMessage());
        }    
	
	}
	
}