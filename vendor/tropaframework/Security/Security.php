<?php
namespace Tropaframework\Security;

/**
 * @NAICHE | Vitor Deco
 */
class Security
{
	/**
	 * chave para criptografar/descriptografar
	 * @var string
	 */
	const ENCRYPTION_KEY = "N@!CH3";
	
	/**
	 * este metodo faz a criptografia de uma string 
	 * @param string $pure_string
	 * @param string $encryption_key
	 * @return string returns an encrypted & utf8-encoded
	 */
	public static function encrypt($pure_string, $encryption_key = self::ENCRYPTION_KEY)
	{
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		
		$encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
		$encrypted_string = base64_encode($encrypted_string);
		return $encrypted_string;
	}
	
	/**
	 * este metodo faz a descriptografia de uma string criptografada
	 * @param string $encrypted_string
	 * @param string $encryption_key
	 * @return string returns decrypted original string
	 */
	public static function decrypt($encrypted_string, $encryption_key = self::ENCRYPTION_KEY)
	{
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		
		$encrypted_string = base64_decode($encrypted_string);
		$decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv);
		return $decrypted_string;
	}
	
	/**
	 * limpa um array, evitando o sql injection 
	 * @param array $fields
	 * @return array
	 */
	public static function antiInjection($fields)
	{
		if( is_array($fields) ){
			$return = array_map("self::antiInjectionItem", $fields);
		} else {
			$return = self::antiInjectionItem($fields);
		}
		return $return;
	}
	
	/**
	 * limpa uma string, evitando o sql injection
	 * @param string $field
	 * @return string
	 */
	public static function antiInjectionItem($field)
	{
		if( !is_array($field) )
		{
			$field = preg_replace("/(from|alter table|select|insert|delete|update|were|drop table|show tables|#|--|'|\\\\)/i", "", $field);
			$field = trim($field);
			$field = strip_tags($field);
			if( !get_magic_quotes_gpc() ) $field = addslashes($field);
			return $field;
			
		} else {
			return self::antiInjection($field);
		}
	}
}