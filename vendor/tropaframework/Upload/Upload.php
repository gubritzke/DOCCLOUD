<?php
namespace Tropaframework\Upload;

/**
 * @NAICHE | Vitor Deco
 */
class Upload
{
	/**
	 * @var string
	 */
	protected $path_upload = null; 
	
	/**
	 * @var array
	 */
	protected $files = array();

	/**
	 * @var string
	 */
	protected $error = null;
	
	/**
	 * array com as extensões permitidas
	 * @var array
	 */
	protected $extensions = array();
	
	/**
	 * construtor define o caminho onde inserir o arquivo
	 * @param string $path
	 */
	public function __construct($path = null)
	{
		$path = is_null($path) ? '/assets/uploads/' : $path;
		$this->path_upload = $_SERVER['DOCUMENT_ROOT'] . $path;
	}
	
	/**
	 * upload unique file
	 * @param array $file
	 * @param string $target
	 */
	public function file($file, $target=null)
	{
		
		if( count($this->extensions) )
		{
			// Pega a extensão do arquivo
			$extFile = end(explode('.', strtolower($file['name'])));
			
			// Verifica se a extensão do arquivo é valida
			if( !in_array($extFile, $this->extensions))
			{
				$this->error = "Extensão não suportada.";
				return false;
			}
		}
		
		//verifica se existe algum erro
		switch( $file['error'] )
		{
			case UPLOAD_ERR_INI_SIZE:
				$this->error = 'O arquivo enviado excede o limite definido ('. ini_get('upload_max_filesize') .')';
				break;
			
			case UPLOAD_ERR_FORM_SIZE:
				$this->error = 'O arquivo enviado excede o limite definido para o formulário ('. ini_get('MAX_FILE_SIZE') .').';
				break;
			
			case UPLOAD_ERR_NO_FILE:
				$this->error = 'Nenhum arquivo foi enviado.';
				break;
			
			case UPLOAD_ERR_NO_TMP_DIR:
				$this->error = 'Erro ao carregar arquivo, pasta temporária inexistente.';
				break;
			
			case UPLOAD_ERR_CANT_WRITE:
				$this->error = 'Erro ao escrever o arquivo em disco.';
				break;
			
			default:
				if( $file['error'] > 0 )
				{
					$this->error = 'Ocorreu um erro ao realizar o upload.';
				}
				
				break;
		}
		
		if( !is_null($this->error) ) return false;
		
		//if not exists, make dir
		$this->createPath($target);
		
		//rename
		$filter = new \Zend\Filter\File\RenameUpload(array(
			"target" => $this->path_upload . $target,
			"use_upload_name" => true,
			"use_upload_extension" => true,
			"randomize" => true,
		));
		 
		//set file
		$this->files[] = $filter->filter($file);
		
		//return
		return $this;
	}
	
	/**
	 * define as extensões permitidas
	 * @param array $extensions
	 */
	public function setExtensions($extensions)
	{
		$this->extensions = $extensions;
		return $this;
	}
	
	/**
	 * get filenames
	 * @return array|false
	 */
	public function getFilename()
	{
		if( count($this->files) )
		{
			$return = array();
			
			foreach( $this->files as $file )
			{
				$filename = explode('/', $file['tmp_name']);
				$return[] = end($filename);
			}
			
			return $return;
		}
		
		return false;
	}
	
	/**
	 * get filename
	 * @return string|false
	 */
	public function getFilenameCurrent()
	{
		$filenames = $this->getFilename();
		
		if( count($filenames) )
		{
			return current($filenames);
		}
	
		return false;
	}
	
	public function getError()
	{
		return $this->error;
	}
	
	protected function createPath($path)
	{
		$pathname =  $this->path_upload . $path;
		if( !is_dir($pathname) )
		{
			return @mkdir($pathname, 0777, true);
		}
	}
}