<?php
namespace Tropaframework\Session;
use Zend\Session\Container;

/**
 * @NAICHE | Vitor Deco
 */
class Session
{
	/**
	 * set session
	 * @param string $key
	 * @param string $value
	 */
	public static function set($key, $value)
	{
		$session = new Container('base');
		$session->offsetSet($key, $value);
	}
	
	/**
	 * get session
	 * @param string $key
	 * @param boolean $unset
	 */
	public static function get($key, $unset=false)
	{
		$session = new Container('base');
		$result = $session->offsetGet($key);
		if( $unset ) $session->offsetUnset($key);
		return !empty($result) ? $result : false;
	}
	
	/**
	 * unset session
	 * @param string $key
	 */
	public static function unset($key)
	{
		$session = new Container('base');
		return $session->offsetUnset($key);
	}
	
	/**
	 * has session
	 * @param string $key
	 */
	public static function has($key)
	{
		$session = new Container('base');
		return $session->offsetExists($key);
	}
}