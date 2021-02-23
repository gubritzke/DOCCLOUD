<?php
namespace Tropaframework\Payment;
require_once(dirname(__FILE__) . '/library/autoload.php');

/**
 * @NAICHE | Vitor Deco
 */
class PaymentPagSeguro extends PaymentAbstract
{	
	//email da conta
	protected $email = 'vitor@tech2you.com.br';
	
	//token da conta
	protected $token = '32FB8EECC5F54757AA2852D8804095C9';
	
	//token da conta para testes
	protected $sandbox = 'ED51987C12C34F3EB85E6ABC577DC662';
	
	//instancia da class do PagSeguro
	private $payment;
	
	public function __construct()
	{
		//inicializa a API PagSeguro
		\PagSeguro\Library::initialize();
		$this->payment = new \PagSeguro\Domains\Requests\Payment();
		
		//define o tipo de pagamento
		parent::__construct("pagseguro");
	}
	
	public function checkTransactions(array $filters)
	{
		//define qual ambiente foi escolhido
		if( $this->is_sandbox === true )
		{
			\PagSeguro\Configuration\Configure::setEnvironment('sandbox');
			$this->token = $this->sandbox;
		}
		
		//define algumas opções para a consulta
		$options = array();
		
		if( !empty($filters['initial_date']) )
		{
			$options['initial_date'] = date("Y-m-d", strtotime($filters['initial_date'])) . 'T00:00';
		}
		
		if( !empty($filters['final_date']) )
		{
			$options['final_date'] = date("Y-m-d", strtotime($filters['final_date'])) . 'T00:00';
		}
		
// 		$options['page'] = 1;
// 		$options['max_per_page'] = 20;
		
		try {
			//define as credenciais de acesso ao PagSeguro
			\PagSeguro\Configuration\Configure::setAccountCredentials($this->email, $this->token);
			
			//recupera as credenciais de acesso ao PagSeguro
			$credentials = \PagSeguro\Configuration\Configure::getAccountCredentials();
			
			//busca por código de referência
			if( !empty($filters['reference']) )
			{
				//request
				$response = \PagSeguro\Services\Transactions\Search\Reference::search(
					$credentials, $filters['reference'], $options
				);
				//echo'<pre>'; print_r($response); exit;
				
				//resultado da consulta
				$transactions = $response->getTransactions();
			}
			if( empty($transactions) ) return false;
			
			//caso tenha um resultado monta um array padrão de retorno
			$result = array();
			foreach( $transactions as $transaction )
			{
				/*
				1 - Aguardando pagamento
				2 - Em análise
				3 - Paga
				4 - Disponível
				5 - Em disputa
				6 - Devolvida
				7 - Cancelada
				*/
				$status = $transaction->getStatus() == 3 ? parent::STATUS_PAID : ($transaction->getStatus() == 7 ? parent::STATUS_CANCELED : parent::STATUS_PENDING);
				
				$result[] = (object)array(
					'reference' => $transaction->getReference(),
					'code' => $transaction->getCode(),
					'date' => $transaction->getDate(),
					'status' => $status,
					'transaction' => $transaction,
				);
			}
			//echo'<pre>'; print_r($result); exit;
			
			return $result;
			
		} catch( Exception $e ){
			die($e->getMessage());
		}
	}
	
	public function render()
	{
		//define qual ambiente foi escolhido
		if( $this->is_sandbox === true )
		{
			\PagSeguro\Configuration\Configure::setEnvironment('sandbox');
			$this->token = $this->sandbox;
		}
		
		//define o tipo de moeda
		$this->payment->setCurrency("BRL");
		
		//define a identificação única do pedido
		$this->payment->setReference($this->getPaymentId());
		
		//define a URL de retorno
		$this->payment->setRedirectUrl($this->getRedirectUrl());
		
// 		//define a URL para notificação
// 		$this->payment->setNotificationUrl($_SERVER['HTTP_HOST']);

		//dados do comprador
		if( !empty($this->sender_data) )
		{
			//converter charset
			$this->sender_data['name'] = utf8_decode($this->sender_data['name']);
			$this->payment->setSender()->setName($this->sender_data['name']);
			$this->payment->setSender()->setEmail($this->sender_data['email']);
			$this->payment->setSender()->setPhone()->withParameters($this->sender_data['phone_ddd'], $this->sender_data['phone_number']);
			$this->payment->setSender()->setDocument()->withParameters('CPF', $this->sender_data['document']);
		}
		
		//dados do endereço de entrega
		if( !empty($this->shipping_address) )
		{
			//converter charset
			$this->shipping_address['street'] = utf8_decode($this->shipping_address['street']);
			$this->shipping_address['district'] = utf8_decode($this->shipping_address['district']);
			$this->shipping_address['city'] = utf8_decode($this->shipping_address['city']);
			$this->shipping_address['state'] = utf8_decode($this->shipping_address['state']);
			$this->shipping_address['country'] = utf8_decode($this->shipping_address['country']);
			$this->shipping_address['complement'] = utf8_decode($this->shipping_address['complement']);
			
			$this->payment->setShipping()->setAddress()->withParameters(
				$this->shipping_address['street'],
				$this->shipping_address['number'],
				$this->shipping_address['district'],
				$this->shipping_address['postalcode'],
				$this->shipping_address['city'],
				$this->shipping_address['state'],
				$this->shipping_address['country'],
				$this->shipping_address['complement']
			);
		}
		
		//adicionar os itens ao pedido
		foreach( $this->order_items as $item )
		{
			$id = $item['id'];
			$description = utf8_decode($item['description']);
			$quantity = $item['quantity'];
			$amount = number_format($item['amount'], 2);
			$weight = $item['weight'];
			$shippingCost = $item['$shippingCost'];
			$this->payment->addItems()->withParameters($id, $description, $quantity, $amount, $weight, $shippingCost);
		}
		
		//dados do custo de entrega
		if( !empty($this->shipping_cost) )
		{
			$value = number_format($this->shipping_cost, 2);
			$this->payment->setShipping()->setCost()->withParameters($value);
		}
		
		//dados do tipo de frete
		if( $this->shipping_type == 'Sedex' )
		{
			$shipping_type = \PagSeguro\Enum\Shipping\Type::SEDEX;
		
		} elseif ( $this->shipping_type == 'Pac' )
		{
			$shipping_type = \PagSeguro\Enum\Shipping\Type::PAC;
		
		} else {
			$shipping_type = \PagSeguro\Enum\Shipping\Type::NOT_SPECIFIED;
		}
		$this->payment->setShipping()->setType()->withParameters($shipping_type);
		
 		//adicionar um desconto
 		if( !empty($this->payment_discount) )
 		{
 			$value = number_format(($this->payment_discount * -1), 2);
 			$this->payment->setExtraAmount($value);
 		}
		
		try {
			
			\PagSeguro\Configuration\Configure::setAccountCredentials($this->email, $this->token);
			return $this->payment->register(\PagSeguro\Configuration\Configure::getAccountCredentials());
		
		} catch (Exception $e) {
			die($e->getMessage());
			
		}
	}
}