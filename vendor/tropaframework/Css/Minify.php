<?php
namespace Tropaframework\Css;

class Minify
{
	protected $result = array();
	
	public function __construct()
	{
	}
	
	public function generate($css_array, $css_replace=false)
	{
		//loop in css
		foreach( $css_array as $href )
		{
			//apenas CSS com diretorio minify
			if( strpos($href, 'minify')!==false )
			{
				//filename minify - local
				$filename_min = str_replace('./', $_SERVER['DOCUMENT_ROOT'] . '/', $href);
				
				//filename minify path
				$filename_min_path = substr($filename_min, 0, strrpos($filename_min, '/'));
					
				//filename full
				$filename_full = str_replace('/minify', '', $filename_min);
				
				//o arquivo full precisa existir
				//cria o arquivo minify caso nao exista ou caso $css_replace=true
				if( file_exists($filename_full) && ($css_replace || !file_exists($filename_min)) )
				{
					//conteudo do css
					$content = @file_get_contents($filename_full);
					
					//cria o diretorio minify
					if( !is_dir($filename_min_path) ) 
					{
						mkdir($filename_min_path, 0777);
						@chmod($filename_min_path, 0777);
					}
					
					//salva o minify
					@file_put_contents($filename_min, $this->createMinify($content));
					
					//set result
					$this->result[] = $href;
				}
			}
		}
		
		return $this;
	}
	
	public function getResult()
	{
		return $this->result;
	}
	
	public static function checkExists($filename)
	{
		//filename minify - local
		$filename_min = $_SERVER['DOCUMENT_ROOT'] . '/' . $filename;
		
		//filename minify path
		$filename_min_path = substr($filename_min, 0, strrpos($filename_min, '/'));
			
		//filename full
		$filename_full = str_replace('/minify', '', $filename_min);
		
		return file_exists($filename_full);
	}
	
	private function createMinify($buffer, $recursive = true)
	{
		//remove comments
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		
		//remove whitespace
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
		$buffer = str_replace(array('  ', '   ', '    '), ' ', $buffer);
		
		$buffer = str_replace(array('; ', ' ;'), ';', $buffer);
		$buffer = str_replace(array('> ', ' >'), '>', $buffer);
		$buffer = str_replace(array('+ ', ' +'), ' + ', $buffer);
		
		//remove space near commas
		$buffer = str_replace(', ', ',', $buffer);
		$buffer = str_replace(' ,', ',', $buffer);
		
		//remove last dot with comma
		$buffer = str_replace(';}', '}', $buffer);
		
		//remove space after colons
		$buffer = str_replace(': ', ':', $buffer);
		
		//remove space before and after {}
		$buffer = str_replace(' {', '{', $buffer);
		$buffer = str_replace(' }', '}', $buffer);
		$buffer = str_replace('{ ', '{', $buffer);
		$buffer = str_replace('} ', '}', $buffer);
		
		if( $recursive )
		{
			$buffer = $this->createMinify($buffer, false);
		}
		
		return $buffer;
	}
}