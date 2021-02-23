<?php
namespace Tropaframework\Shipment\Model;

/**
 * @author: Vitor Deco
 */
abstract class ModelAbstract
{
	public function clear()
	{
		//todas as variáveis declaradas
		$vars = array_keys(get_class_vars(get_class($this)));
	
		//loop para montar o array e limpar
		foreach( $vars as $var ) $this->$var = null;
	}
	
	public function populate($array)
	{
		foreach( $array as $key=>$value )
		{
			$method_name = "set" . ucfirst($key);
			if( method_exists($this, $method_name) )
			{
				$this->$method_name($value);
			}
		}
	
		return $this;
	}
	
	public function toArray()
	{
		//todas as variáveis declaradas
		$vars = array_keys(get_class_vars(get_class($this)));
		
		//loop para montar o array e retornar
		$return = array();
		foreach( $vars as $var ) $return[$var] = $this->$var;
		return $return;
	}
	
	public function toObject()
	{
	   return (object)$this->toArray();
	}
}