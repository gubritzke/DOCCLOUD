<?php
namespace Tropaframework\Image;

/**
 * @NAICHE | Vitor Deco
 */
class Image
{
	public static function init($url)
	{
		$url = self::urlProtocol($url);
		$url = '/img/' . self::encrypt($url) . '.jpg';
		return $url;
	}
	
	public static function urlProtocol($url)
	{
		//define o protocolo
		if( ($_SERVER['HTTPS'] == true) )
		{
			$url = str_replace('http://', 'https://', $url);
		}
	
		return $url;
	}
	
	public static function encrypt($string)
	{
		return base64_encode($string);
	}
	
	public static function decrypt($string)
	{
		return base64_decode($string);
	}
	
}