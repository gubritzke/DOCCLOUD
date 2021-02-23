<?php
namespace Tropaframework\Helper;

/**
 * @NAICHE | Vitor Deco
 */
class Validate
{
	public $result = array();
	
	public function setError($key, $value)
	{
		$this->result[$key] = $value;
		return $this;
	}
	
	public function isValid()
	{
		return !count($this->result);
	}
	
	public function result()
	{
		return $this->result;
	}
	
	public function current()
	{
		return current($this->result);
	}

	public function json()
	{
		echo json_encode($this->result); exit;
	}
	
	public function isAjax()
	{
		if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') )
		{
			return true;
		}
		
		return false;
	}
	
	public static function counter($array, $length)
	{
		$array = array_filter($array);
		return (count($array) == $length);
	}
	
	public static function isNotEmpty($field) 
	{
		return !empty($field);
	}
	
	public static function isArrayNotEmpty($fields, $required=array())
	{
		foreach( $required as $key )
		{
			if( empty($fields[$key]) ) return false;
		}
		
		return true;
	}
	
	public static function isDate($date, $required=false)
	{
		if( !$required && ($date == null) ) return true;
		
		$date = explode("/", $date);
		$day = $date[0];
		$month = $date[1];
		$year = $date[2];
		
		return @checkdate($month, $day, $year);
	}
	
	public static function isEmail($email, $required=false)
	{
		if( !$required && ($email == null) ) return true;
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	public static function isURL($url, $required=false)
	{
		if( !$required && ($url == null) ) return true;
		return filter_var($url, FILTER_VALIDATE_URL);
	}
	
	public static function isBool($bool, $required=false)
	{
		if( !$required && ($bool == null) ) return true;
		return filter_var($bool, FILTER_VALIDATE_BOOLEAN);
	}
	
	public static function isInteger($integer, $required=false)
	{
		if( !$required && ($integer == null) ) return true;
		return filter_var($integer, FILTER_VALIDATE_INT);
	}
	
	public static function isEan($barcode, $required=false)
	{
		//exception if ean is empty and not required
		if( !$required && ($barcode == null) ) return true;
		
		//check to see if barcode is 13 digits long
		if (!preg_match("/^[0-9]{13}$/", $barcode)) {
			return false;
		}
		$digits = $barcode;
		
		//1. Add the values of the digits in the
		//even-numbered positions: 2, 4, 6, etc.
		$even_sum = $digits[1] + $digits[3] + $digits[5] +
		$digits[7] + $digits[9] + $digits[11];
		
		//2. Multiply this result by 3.
		$even_sum_three = $even_sum * 3;
		
		//3. Add the values of the digits in the
		//odd-numbered positions: 1, 3, 5, etc.
		$odd_sum = $digits[0] + $digits[2] + $digits[4] +
		$digits[6] + $digits[8] + $digits[10];
		
		//4. Sum the results of steps 2 and 3.
		$total_sum = $even_sum_three + $odd_sum;
		
		//5. The check character is the smallest number which,
		//when added to the result in step 4, produces a multiple of 10.
		$next_ten = (ceil($total_sum / 10)) * 10;
		$check_digit = $next_ten - $total_sum;
		
		//if the check digit and the last digit of the
		//barcode are OK return true;
		if ($check_digit == $digits[12]) {
			return true;
		}
		
		return false;
	}

	public static function isCPF($cpf, $required=false)
	{
		if( !$required && ($cpf == null) ) return true;
		
		//Etapa 1: Cria um array com apenas os digitos numericos, isso permite receber o cpf em diferentes formatos como "000.000.000-00", "00000000000", "000 000 000 00" etc...
		$j=0;
		for($i=0; $i<(strlen($cpf)); $i++)
		{
			if(is_numeric($cpf[$i]))
			{
				$num[$j]=$cpf[$i];
				$j++;
			}
		}
		
		//Etapa 2: Conta os digitos, um cpf valido possui 11 digitos numericos.
		if(count($num)!=11)
		{
			$isCpfValid=false;
		}
		
		//Etapa 3: Combinacoes como 00000000000 e 22222222222 embora nao sejam cpfs reais resultariam em cpfs validos apos o calculo dos digitos verificares e por isso precisam ser filtradas nesta parte.
		else
		{
			for($i=0; $i<10; $i++)
			{
				if ($num[0]==$i && $num[1]==$i && $num[2]==$i && $num[3]==$i && $num[4]==$i && $num[5]==$i && $num[6]==$i && $num[7]==$i && $num[8]==$i)
				{
					$isCpfValid=false;
					break;
				}
			}
		}
		
		//Etapa 4: Calcula e compara o primeiro digito verificador.
		if(!isset($isCpfValid))
		{
			$j=10;
			for($i=0; $i<9; $i++)
			{
				$multiplica[$i]=$num[$i]*$j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$resto = $soma%11;
				
			if($resto<2)
			{
				$dg=0;
			}
			else
			{
				$dg=11-$resto;
			}
			if($dg!=$num[9])
			{
				$isCpfValid=false;
			}
		}
		
		//Etapa 5: Calcula e compara o segundo digito verificador.
		if(!isset($isCpfValid))
		{
			$j=11;
			for($i=0; $i<10; $i++)
			{
				$multiplica[$i]=$num[$i]*$j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$resto = $soma%11;
			if($resto<2)
			{
				$dg=0;
			}
			else
			{
				$dg=11-$resto;
			}
		
			if($dg!=$num[10])
			{
				$isCpfValid=false;
			}
			else
			{
				$isCpfValid=true;
			}
		}
		
		//Etapa 6: Retorna o Resultado em um valor booleano.
		return $isCpfValid ? true : false;
	}

	public static function isCNPJ($cnpj, $required=false)
	{
		if( !$required && ($cnpj == null) ) return true;
		
		//Etapa 1: Cria um array com apenas os digitos numéricos, isso permite receber o cnpj em diferentes formatos como "00.000.000/0000-00", "00000000000000", "00 000 000 0000 00" etc...
		$j=0;
		for($i=0; $i<(strlen($cnpj)); $i++)
		{
			if(is_numeric($cnpj[$i]))
			{
				$num[$j]=$cnpj[$i];
				$j++;
			}
		}
			
		//Etapa 2: Conta os dígitos, um Cnpj válido possui 14 dígitos numéricos.
		if(count($num)!=14)
		{
			$isCnpjValid=false;
		}
			
		//Etapa 3: O número 00000000000 embora não seja um cnpj real resultaria um cnpj válido após o calculo dos dígitos verificares e por isso precisa ser filtradas nesta etapa.
		if ($num[0]==0 && $num[1]==0 && $num[2]==0 && $num[3]==0 && $num[4]==0 && $num[5]==0 && $num[6]==0 && $num[7]==0 && $num[8]==0 && $num[9]==0 && $num[10]==0 && $num[11]==0)
		{
			$isCnpjValid=false;
		}
			
		//Etapa 4: Calcula e compara o primeiro dígito verificador.
		else
		{
			$j=5;
			for($i=0; $i<4; $i++)
			{
				$multiplica[$i]=$num[$i]*$j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$j=9;
			for($i=4; $i<12; $i++)
			{
				$multiplica[$i]=$num[$i]*$j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$resto = $soma%11;
			if($resto<2)
			{
				$dg=0;
			}
			else
			{
				$dg=11-$resto;
			}
			if($dg!=$num[12])
			{
				$isCnpjValid=false;
			}
		}
			
		//Etapa 5: Calcula e compara o segundo dígito verificador.
		if(!isset($isCnpjValid))
		{
			$j=6;
			for($i=0; $i<5; $i++)
			{
				$multiplica[$i]=$num[$i]*$j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$j=9;
			for($i=5; $i<13; $i++)
			{
				$multiplica[$i]=$num[$i]*$j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$resto = $soma%11;
			if($resto<2)
			{
				$dg=0;
			}
			else
			{
				$dg=11-$resto;
			}
			if($dg!=$num[13])
			{
				$isCnpjValid=false;
			}
			else
			{
				$isCnpjValid=true;
			}
		}
		
		//Etapa 6: Retorna o Resultado em um valor booleano.
		return $isCnpjValid ? true : false;
	}
}