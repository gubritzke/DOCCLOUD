<?php
namespace Tropaframework\Api;

class Request
{
	protected $url = null;
	
	protected $urlExtra = null;
	
	protected $authorization = null;
	
	protected $debug = array();
	
	protected $result = array();
	
	public function __construct($api, $authorization=null)
	{
		$this->setUrl($api);
		$this->setAuthorization($authorization);
	}
	
	public function setUrl($url)
	{
		$this->url = $url;
	}
	
	public function setUrlExtra($str)
	{
		$this->urlExtra = $str;
	}
	
	public function setAuthorization($auth)
	{
		$this->authorization = $auth;
	}

	public function getUrl()
	{
		return $this->url;
	}
	
	public function getUrlExtra()
	{
		return $this->urlExtra;
	}
	
	public function getAuthorization()
	{
		return $this->authorization;
	}
	
	/**
	 * faz as requisições à API
	 * @param string $url
	 * @param array $params
	 * @param string $type
	 * @param bool $debug
	 */
	public function callDeprecated($url, $params=[], $type='POST')
	{
		//define a url
		if( !empty($this->urlExtra) )
		{
			$url = $this->url . '/' . $this->urlExtra . '/' . $url;
		} else {
			$url = $this->url . '/' . $url;
		}
		
		//define os parâmetros
		$params = http_build_query($params);
		if( $type == 'GET' ) $url .= '?' . $params;
		
		//define os headers
		$header = array("Accept: application/json", $this->authorization);
		
		//função curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		
		//executa o curl
		$response = curl_exec($ch);
		$response = json_decode($response);
		
		//informações da requisição
		$this->debug = curl_getinfo($ch);
		
		//salva o resultado
		$this->result = $response;
		
		//retorna o this
		return $this;
	}
	
	public function call($url, $params=[], $type='POST', $isJsonData=false)
	{
		//define a url
		if( !empty($this->urlExtra) )
		{
			$url = $this->url . '/' . $this->urlExtra . '/' . $url;
		} else {
			$url = $this->url . '/' . $url;
		}
		
		//define os headers
		$header = array();
		$header[] = "Accept: application/json";
		$header[] = $this->authorization;
		
		//define os parâmetros
		$data = http_build_query($params);
		
		//função curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
		
		//GET
		if( $type == 'GET' )
		{
			$url .= '?' . $data;
			curl_setopt($ch, CURLOPT_URL, $url);

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			
		//POST
		} else {
			
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			
			if( $isJsonData === true )
			{
				$header[] = "Content-type: application/json";
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
					
			} else {
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}
			
		}
		
		//executa o curl
		$response = curl_exec($ch);
		$response = json_decode($response);
		
		//informações da requisição
		$this->debug = curl_getinfo($ch);
		
		//salva o resultado
		$this->result = $response;
		
		//retorna o this
		return $this;
	}
	
	/**
	 * exibe o debug
	 */
	public function debug()
	{
		echo'<pre>';
		print_r($this->debug);
		print_r($this->result);
		exit;
	}
	
	/**
	 * retorna o resultado
	 */
	public function result()
	{
		return $this->result;
	}
	
	/**
	 * retorna o primeiro elemento do resultado
	 */
	public function current()
	{
		$this->result = current($this->result);
		return $this->result;
	}
	
	/**
	 * retorna a quantidade de registros
	 */
	public function count()
	{
		return count($this->result);
	}
}