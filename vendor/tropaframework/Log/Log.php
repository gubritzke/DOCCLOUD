<?php
namespace Tropaframework\Log;

class Log
{
	private static $user = false;
	private static $error = null;
	
	public static function error($message, $var=array())
	{
		$var['type'] = 'ERROR';
		$var['message'] = $message;
		self::register($var);
	}
	
	public static function debug($message, $var=array())
	{
		$var['type'] = 'DEBUG';
		$var['message'] = $message;
		self::register($var);
		self::checkError();
	}
	
	public static function setUser($user)
	{
		self::$user = $user;
	}
	
	public static function checkError()
	{
		$error = error_get_last();
		if( serialize(self::$error) != serialize($error) )
		{
			self::$error = $error;
			self::error('retorno da função error_get_last()', $error);
		}
	}
	
	public static function register($var=array(), $dir=null)
	{
		//estrutura padrão de data
		if( empty($dir) ) $dir = date("Y/m/d");
		
		//cria o caminho de onde irá salvar o log
		$path = $_SERVER['DOCUMENT_ROOT'] . "/_logs/" . $dir . "/";
		$path = str_replace(['public/', 'public_html/'], '', $path);
		if( !is_dir($path) ) @mkdir($path, 0777, true);
		
		//cria o arquivo ou utiliza um existente
		$filename = date('Y-m-d') . ".txt";
		$file = fopen($path . $filename, "a+");
		
		//real ip
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];
		$ip = filter_var($client, FILTER_VALIDATE_IP) ? $client : (filter_var($forward, FILTER_VALIDATE_IP) ? $forward : $remote);
		
		//add informações
		$var['page'] = strtok($_SERVER["REQUEST_URI"], '?');
		$var['date'] = date('Y-m-d H:i:s');
		$var['ip'] = $ip;
		if( !empty($_GET) ) $var['get'] = $_GET;
		if( !empty($_POST) ) $var['post'] = $_POST;
		if( !empty(self::$user) ) $var['user'] = self::$user;
		$string = json_encode($var) . PHP_EOL;
		
		//escreve e fecha o arquivo
		fwrite($file, $string);
		fclose($file);
	}
}