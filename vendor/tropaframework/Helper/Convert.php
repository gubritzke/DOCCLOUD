<?php
namespace Tropaframework\Helper;

/**
 * @NAICHE | Vitor Deco
 */
class Convert
{
	/**
	 * convert number to real
	 * @return float
	 */
	public static function toReal($number) 
	{
		return number_format($number, 2, ',', '.');
	}
	
	/**
	 * limit text
	 * @param string $str
	 * @param integer $limit
	 * @param bool $strip_tags
	 */
	public static function limitText($str, $limit, $strip_tags = false)
	{
		if ($strip_tags == true)
			$str = strip_tags($str);
		
		$string = strlen($str) > $limit ? substr($str, 0, $limit) . '...' : $str;
		
		return html_entity_decode($string);
	}
	
	/**
	 * format text with paragraphs
	 * @param string $subject
	 * @return string
	 */
	public static function formatText($subject)
	{
		return str_replace(PHP_EOL, '<br/>', $subject);
	}
	
	/**
	 * break text in words
	 * @param string $str
	 */
	public static function breakText($str, $start=1, $end=1)
	{
		$start--;
		$end--;
		$str = explode(' ', $str);
		
		$return = array();
		for( $i=$start; $i<=$end; $i++ )
		{
			$return[] = $str[$i];
		}
		
		
		$return = implode(' ', $return);
		$return = trim($return);
		return $return;
	}
	
	/**
	 * convert link in iframe
	 * @return string|false
	 */
	public static function youtubeLink($url, $autoplay = false) 
	{
	    if( strpos($url, 'youtube.com')===false ) return false;
	    
        return preg_replace(
            "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
            "<iframe style=\"border:0;\" width=\"100%\" height=\"340px\" src=\"//www.youtube.com/embed/$2\" allowfullscreen></iframe>",
            $url
        );
    }
    
    public static function getLinkEmbed($url)
    {
    	 
    	$youtube = explode('?v=', $url);
    	return $youtube[1];
    	 
    }
    
    public static function getIdYoutube($url)
    {
    	
    	$youtube = \Naicheframework\Helper\Convert::youtubeLink($url);
    	$explode = explode('embed/', $youtube);
    	$explode = explode('"', $explode[1]);
    	return $explode[0];
    	
    }
    
    public static function getThumbYoutube($url)
    {
    	
    	$youtube = \Naicheframework\Helper\Convert::youtubeLink($url);
    	$explode = explode('embed/', $youtube);
    	$explode = explode('"', $explode[1]);
    	return 'https://i.ytimg.com/vi/'.$explode[0].'/hqdefault.jpg';
    	
    }
    
    /**
	 * @param string $date
	 * @return string
     */
    public static function date($date)
    {
    	//hoje
    	if( date('Ymd', strtotime($date)) == date('Ymd') )
    	{
    		$return = "Hoje às " . date('H:i', strtotime($date));
    	
    	//ontem
    	} elseif( date('Ymd', strtotime($date)) == date('Ymd', strtotime('-1 day')) )
    	{
    		$return = "Ontem às " . date('H:i', strtotime($date));
    		
    	//default
    	} else 
    	{
    		$return = date('d/m/Y', strtotime($date)) . " às " . date('H:i', strtotime($date));
    	}
    	
    	return $return;
    }
    
    /**
     * converte a data para dia da semana
     * @param string $date
     * @return string
     */
    public static function date_to_weekday($date)
    {
    	$weeklist = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];
    	$weeknumber = date('w', strtotime($date));
    	return $weeklist[$weeknumber];
    }
    
    /**
     * gera códigos para padronizar 
     * @param string $input
     * @return string
     */
    public static function code($input, $length=5, $insert_before=null, $insert_after=null)
    {
    	return $insert_before . str_pad($input, $length, 0, STR_PAD_LEFT) . $insert_after;
    }
    
    /**
     * verifica se uma imagem existe, caso não existir, gera uma imagem contendo as iniciais do nome
     * @param string $image
     * @param string $name
     * @return string
     */
    public static function imageLink($image, $name)
    {
    	$image_path = str_replace('./', $_SERVER['DOCUMENT_ROOT'] . '/', $image);
    	if( @is_array(getimagesize($image_path)) )
    	{
    		return $image;
    	
    	} else {
    		$words = explode(' ', $name);
    		$letters = !empty($words[0][0]) ? $words[0][0] : null;
    		$letters .= !empty($words[1][0]) ? $words[1][0] : null;
    		return 'http://placehold.it/300/f2f2f2/777?text=' . mb_strtoupper($letters);
    	}
    }
    
    /**
     * Remove a acentuação de uma string
     * @param string $var
     * @param bool $whitespace
     * @return string
     */
    public static function removeEspecialChars($var, $whitespace=false)
    {
    	$var = trim($var);
    
    	$var = str_replace(array("/{&aacute;}/","/{&Aacute;}/","/{&agrave;}/","/{&Agrave;}/","/{&atilde;}/","/{&Atilde;}/"),"a",$var);
    	$var = str_replace(array("/{&eacute;}/","/{&Eacute;}/","/{&egrave;}/","/{&Egrave;}/"),"e",$var);
    	$var = str_replace(array("/{&oacute;}/","/{&Oacute;}/","/{&ograve;}/","/{&Ograve;}/","/{&otilde;}/","/{&Otilde;}/"),"o",$var);
    	$var = str_replace(array("/{&uacute;}/","/{&Uacute;}/","/{&ugrave;}/","/{&Ugrave;}/"),"u",$var);
    	$var = str_replace(array("/{&iacute;}/","/{&Iacute;}/","/{&igrave;}/","/{&Igrave;}/"),"i",$var);
    	$var = str_replace("/{&ccedil;}/","c}/",$var);
    
    	$var = preg_replace("(á|Á|à|À|ã|Ã)","a",$var);
    	$var = preg_replace("(é|É|è|È|ê|Ê)","e",$var);
    	$var = preg_replace("(ó|Ó|ò|Ò|ô|Ô|õ|Õ)","o",$var);
    	$var = preg_replace("(ú|Ú|ù|Ù)","u",$var);
    	$var = preg_replace("(í|Í|ì|Ì)","i",$var);
    
    	$var = str_replace("ç","c",$var);
    	$var = str_replace("Ç","C",$var);
    	$var = preg_replace('/[^a-zA-Z0-9\s_-]/','',$var);
    
    	if ( !$whitespace ) $var = str_replace(array(' ','__'),'-',$var);
    
    	return $var;
    }
    
    /**
     * retorna o HTML com o número de estrelas
     * @param int $number
     */
    public static function rating($number)
    {
    	$html = null;
    	$html .= ($number >= 0.5) ? ($number >= 1 ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-half-o"></i>') : '<i class="fa fa-star-o"></i>';
    	$html .= ($number >= 1.5) ? ($number >= 2 ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-half-o"></i>') : '<i class="fa fa-star-o"></i>';
    	$html .= ($number >= 2.5) ? ($number >= 3 ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-half-o"></i>') : '<i class="fa fa-star-o"></i>';
    	$html .= ($number >= 3.5) ? ($number >= 4 ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-half-o"></i>') : '<i class="fa fa-star-o"></i>';
    	$html .= ($number >= 4.5) ? ($number >= 5 ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-half-o"></i>') : '<i class="fa fa-star-o"></i>';
    	return $html;
    }
    
    /**
     * retorna o HTML com a barra de porcentagem
     * @param int $number
     * @param int $total
     */
    public static function percentBar($number, $total)
    {
    	$pencent = ($total <= 0) ? 0 : ($number*100/$total);
    	$html = '<div class="percent-bar"><div style="width:' . $pencent . '%;"></div></div>';
    	return $html;
    }
    
    /**
     * faz o download de uma image usando a URL
     * @param string $image_url
     * @param string $image_file
     */
    public static function urlToFile($image_url, $image_file)
    {
    	$fp = fopen($image_file, 'w+');
    	$ch = curl_init($image_url);
    	//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //enable SSL
    	curl_setopt($ch, CURLOPT_FILE, $fp);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    	curl_setopt($ch, CURLOPT_VERBOSE, true);
    	curl_exec($ch);
    	curl_close($ch);
    	fclose($fp);
    }
    
    /**
     * converter CSV para array
     * @param file $filename
     * @param bool $removeFirst
     */
    public static function csvToArray($filename, $removeFirst=true)
    {
    	$csvData = file_get_contents($filename);
    	$lines = explode(PHP_EOL, $csvData);
    	
    	$array = array();
    	foreach( $lines as $line )
    	{
    		$array[] = str_getcsv($line, ';');
    	}
    	
    	if( $removeFirst === true )
    	{
    		unset($array[0]);
    	}
    	
    	return $array;
    }
}