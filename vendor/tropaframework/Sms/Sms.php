<?php
namespace Tropaframework\Sms;

class Sms
{
	protected $url = "http://backend.naichesms.com.br";
	protected $username = null;
	protected $password = null;
	
	public function __construct($username, $password, $url=null)
	{
		$this->username = $username;
		$this->password = $password;
		if( !empty($url) ) $this->url = $url;
	}
	
	public function send($cellphone, $message)
	{
		try {
			
			//celular - apenas numeros
			$cellphone = str_replace(["(",")","-"," "], "", $cellphone);
			
			//mensagem - limitar até 160 caracteres
			$message = substr($message, 0, 160);
			
			//validar celular - de 10 ou 11 caracteres
			if( !in_array(strlen($cellphone), [10,11]) )
			{
				throw new \Exception("Número de celular inválido!");
			}
			
			//enviar SMS
			$data = array(
				'user' => $this->username,
				'pass' => $this->password,
				'cel' => $cellphone,
				'msg' => $message,
			);
			$api = new \Naicheframework\Api\Request($this->url);
			$response = $api->call('naichesms/sms/envio', $data, "POST")->result();
			
			//return
			return $response->retorno;
			
		} catch( \Exception $e ){
			
			\Naicheframework\Log\Log::error("Erro no envio do SMS", $e->getMessage());
			return false;
			
		}
	}
}