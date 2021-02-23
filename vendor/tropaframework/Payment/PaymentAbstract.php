<?php
namespace Tropaframework\Payment;

/**
 * @NAICHE | Vitor Deco
 */
abstract class PaymentAbstract
{
	/**
	 * @var int
	 */
	const STATUS_PENDING = 0;
	const STATUS_PAID = 1;
	const STATUS_CANCELED = 2;
	
	/**
	 * identifica o meio de pagamento utilizado 
	 * @var string
	 */
	private $payment_type = null;
	
	/**
	 * informações de pagamento
	 * @var array
	 */
	public $payment_info = array();
	
	/**
	 * código que identifica a transação
	 * @var string
	 */
	protected $payment_id = null;
	
	/**
	 * define a url de retorno
	 * @var string
	 */
	protected $payment_redirect_url = null;
	
	/**
	 * define o valor do desconto
	 * @var float
	 */
	protected $payment_discount = null;
	
	/**
	 * define o endereço de entrega 
	 * @var array
	 */
	protected $shipping_address = array();

	/**
	 * define o valor da entrega
	 * @var float
	 */
	protected $shipping_cost = null;
	
	/**
	 * define o tipo da entrega
	 * @var string
	 */
	protected $shipping_type = null;
	
	/**
	 * define dados do comprador
	 * @var array
	 */
	protected $sender_data = array();
	
	/**
	 * define os itens do pedido
	 * @var array
	 */
	protected $order_items = array();
	
	/**
	 * identifica em qual ambiente está
	 * @var boolean
	 */
	protected $is_sandbox = false;
	
	/**
	 * construtor definindo qual o gateway de pagamento será usado
	 * @param string $payment_type
	 */
	public function __construct($payment_type)
	{
		$this->payment_type = $payment_type;
	}
	
	/**
	 * define o ambiente
	 * @param boolean $bool
	 * @return \Naicheframework\Payment\PaymentAbstract
	 */
	public function setSandbox($bool=true)
	{
		$this->is_sandbox = (bool)$bool;
		return $this;
	}
	
	/**
	 * define uma identificação para essa transação
	 * @param int|string $payment_id
	 */
	public function setPaymentId($payment_id)
	{
		$this->payment_id = $payment_id;
	}
	
	public function getPaymentId()
	{
		return $this->payment_id;
	}
	
	/**
	 * define um desconto
	 * @param float $payment_id
	 */
	public function setPaymentDiscount($value)
	{
		$this->payment_discount = $value;
	}
	
	public function getPaymentDiscount()
	{
		return $this->payment_discount;
	}
	
	/**
	 * define uma URL para retorno após concluir o processo
	 * @param string $payment_redirect_url
	 */
	public function setRedirectUrl($payment_redirect_url)
	{
		$this->payment_redirect_url = $payment_redirect_url;
	}
	
	public function getRedirectUrl()
	{
		return $this->payment_redirect_url;
	}
	
	/**
	 * define dados do comprador, adicionando em um array
	 * @param string $name
	 * @param string $email
	 * @param int $phone
	 * @param int $document
	 * @param string $birthdate
	 */
	public function setSenderData($name, $email, $phone, $document, $birthdate=null)
	{
		//evitar bugs no nome
		$name = preg_replace('/\d/', '', $name);
		$name = preg_replace('/[\n\t\r]/', ' ', $name);
		$name = preg_replace('/\s(?=\s)/', '', $name);
		$name = trim($name);
		
		//separar o ddd do número de telefone
		$phone = str_replace(['(',')','-',' '], '', $phone);
		$phone_ddd = substr($phone, 0, 2);
		$phone_number = substr($phone, 2);
		
		//remover caracteres do documento
		$document = str_replace(['.','-','/'], '', $document);
		
		$this->sender_data = array(
			'name' => utf8_decode($name), 
			'email' => $email, 
			'phone_ddd' => $phone_ddd,
			'phone_number' => $phone_number,
			'document' => $document,
			'birthdate' => $birthdate,
		);
	}
	
	public function getSenderData()
	{
		return $this->sender_data;
	}
	
	/**
	 * define o endereço de entrega
	 * @param string $street
	 * @param string $number
	 * @param string $district
	 * @param string $postalCode
	 * @param string $city
	 * @param string $state
	 * @param string $country
	 * @param string $complement
	 */
	public function setShippingAddress($street, $number, $district, $postalCode, $city, $state, $country, $complement = null)
	{
		$this->shipping_address = array(
			'street' => $street, 
			'number' => $number, 
			'district' => $district, 
			'postalcode' => $postalCode, 
			'city' => $city, 
			'state' => $state, 
			'country' => $country, 
			'complement' => $complement,
		);
	}
	
	public function getShippingAddress()
	{
		return $this->shipping_address;
	}
	
	/**
	 * define o custo da entrega
	 * @param float $shipping_cost
	 */
	public function setShippingCost($shipping_cost)
	{
		$this->shipping_cost = $shipping_cost;
	}
	
	public function getShippingCost()
	{
		return $this->shipping_cost;
	}
	
	/**
	 * define o tipo da entrega
	 * @param string $shipping_type
	 */
	public function setShippingType($shipping_type)
	{
		return $shipping_type;
	}
	
	public function getShippingType()
	{
		return $this->shipping_type;
	}
	
	public function getPaymentType()
	{
		return $this->payment_type;
	}
	
	/**
	 * adiciona os itens para enviar ao gateway
	 * @param int $id
	 * @param string $description
	 * @param int $quantity
	 * @param float $amount
	 * @param float $weight
	 * @param float $shippingCost
	 */
	public function addItem($id, $description, $quantity, $amount, $weight = null, $shippingCost = null)
	{
		$description = utf8_encode($description);
		$amount = number_format($amount, 2);
		
		$this->order_items[$id] = array(
			'id' => $id,
			'description' => $description,
			'quantity' => $quantity,
			'amount' => $amount,
			'weight' => $weight,
			'shippingCost' => $shippingCost,
		);
		
		return $this;
	}
	
	/**
	 * adiciona os itens para enviar ao gateway
	 * @param string $group
	 * @param array $store
	 * @param int $id
	 * @param string $description
	 * @param int $quantity
	 * @param float $amount
	 * @param float $weight
	 * @param float $shippingCost
	 */
	public function addItemGroup($group, $store, $id, $description, $quantity, $amount, $weight = null, $shippingCost = null)
	{
		$description = utf8_encode($description);
		$amount = number_format($amount, 2);
		
		$this->order_items[$group]['store'] = $store;
		
		$this->order_items[$group][$id] = array(
			'id' => $id,
			'description' => $description,
			'quantity' => $quantity,
			'amount' => $amount,
			'weight' => $weight,
			'shippingCost' => $shippingCost,
		);
	
		return $this;
	}
	
	public function getItems()
	{
		return $this->order_items;
	}
	
	/**
	 * recupera uma transação do gateway
	 * @param array $filters
	 */
	abstract public function checkTransactions(array $filters);
	
	/**
	 * faz a requisição ao gateway e retorna
	 */
	abstract public function render();
	
}