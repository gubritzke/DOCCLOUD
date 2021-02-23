<?php
namespace Tropaframework\Helper;


/**
 * @NAICHE | Vitor Deco
 */
class Constants
{
    public static function getStates()
    {
    	return array("AC"=>"Acre", "AL"=>"Alagoas", "AM"=>"Amazonas", "AP"=>"Amapá","BA"=>"Bahia","CE"=>"Ceará","DF"=>"Distrito Federal","ES"=>"Espírito Santo","GO"=>"Goiás","MA"=>"Maranhão","MT"=>"Mato Grosso","MS"=>"Mato Grosso do Sul","MG"=>"Minas Gerais","PA"=>"Pará","PB"=>"Paraíba","PR"=>"Paraná","PE"=>"Pernambuco","PI"=>"Piauí","RJ"=>"Rio de Janeiro","RN"=>"Rio Grande do Norte","RO"=>"Rondônia","RS"=>"Rio Grande do Sul","RR"=>"Roraima","SC"=>"Santa Catarina","SE"=>"Sergipe","SP"=>"São Paulo","TO"=>"Tocantins");
    }
    
    public static function getDays()
    {
    	$return = array();
    	
    	for( $i=1; $i<=31; $i++ )
    	{
    		$day = str_pad($i, 2, "0", STR_PAD_LEFT);
    		$return[$day] = $day;
    	}
    	
    	return $return;
    }
    
    public static function getMonths()
    {
    	$return = array();
    	$return['01'] = 'Janeiro';
    	$return['02'] = 'Fevereiro';
    	$return['03'] = 'Março';
    	$return['04'] = 'Abril';
    	$return['05'] = 'Maio';
    	$return['06'] = 'Junho';
    	$return['07'] = 'Julho';
    	$return['08'] = 'Agosto';
    	$return['09'] = 'Setembro';
    	$return['10'] = 'Outubro';
    	$return['11'] = 'Novembro';
    	$return['12'] = 'Dezembro';
    	return $return;
    }
    
    public static function getYears($start='now', $end='1930')
    {
    	//define o ano atual
    	if( $start == 'now' ) $start = date('Y');
    	if( $end == 'now' ) $end = date('Y');
    	
    	//array com os anos
    	$return = array();
    	
    	//loop em todos os anos
    	for( $i=$start; $i>=$end; $i-- )
    	{
    		$return[$i] = $i;
    	}
    	
    	return $return;
    }
    
    public static function getWeight()
    {
    	return array(
    		"250" => "Até 250g",
    		"500" => "de 250g até 500g",
    		"750" => "de 500g até 750g",
    		"1000" => "de 750g até 1kg",
    		"2000" => "de 1kg até 2kg",
    		"3000" => "de 2kg até 3kg",
    		"5000" => "de 3kg até 5kg",
    	);
    }
    
    public static function getDDDs()
    {
    	return array(
	    	'11',
	    	'12',
	    	'13',
	    	'14',
	    	'15',
	    	'16',
	    	'17',
	    	'18',
	    	'19',
	    	'21',
	    	'22',
	    	'24',
	    	'27',
	    	'28',
	    	'31',
	    	'32',
	    	'33',
	    	'34',
	    	'35',
	    	'37',
	    	'38',
	    	'41',
	    	'42',
	    	'43',
	    	'44',
	    	'45',
	    	'46',
	    	'47',
	    	'48',
	    	'49',
	    	'51',
	    	'53',
	    	'54',
	    	'55',
	    	'61',
	    	'62',
	    	'63',
	    	'64',
	    	'65',
	    	'66',
	    	'67',
	    	'68',
	    	'69',
	    	'71',
	    	'73',
	    	'74',
	    	'75',
	    	'77',
	    	'79',
	    	'81',
	    	'82',
	    	'83',
	    	'84',
	    	'85',
	    	'86',
	    	'87',
	    	'88',
	    	'89',
	    	'91',
	    	'92',
	    	'93',
	    	'94',
	    	'95',
	    	'96',
	    	'97',
	    	'98',
	    	'99',
    	);
    }
}