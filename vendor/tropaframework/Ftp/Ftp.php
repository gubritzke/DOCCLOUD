<?php
namespace Tropaframework\Ftp;

class Ftp
{
	/**
	 * instancia do FTP 
	 * @var FTP stream $ftp_stream
	 */
	private $ftp_stream;
	
	/**
	 * iniciar a conexão no ftp
	 * @param string $host
	 */
	public function __construct($host)
	{
		$this->ftp_stream = ftp_connect($host);
	}
	
	/**
	 * faz login no FTP
	 * @return bool
	 */
	public function login($username, $password)
	{
		$response = ftp_login($this->ftp_stream, $username, $password);
		return ($response == false) ? false : true;
	}
	
	/**
	 * finalizar conexão com FTP
	 */
	public function logout()
	{
		return ftp_close($this->ftp_stream);
	}
	
	/**
	 * inserir arquivo
	 * @param string $remote_file
	 * @param string $local_file
	 */
	public function put($remote_file, $local_file)
	{
		return ftp_put($this->ftp_stream, $remote_file, $local_file, FTP_BINARY);
	}
	
	/**
	 * criar diretório
	 * @param string $directory
	 */
	public function mkdir($directory)
	{
		return ftp_mkdir($this->ftp_stream, $directory);
	}
	
	/**
	 * listar arquivos de um diretório 
	 * @param string $path
	 * @return array|false
	 */
	public function nlist($path)
	{
		return ftp_nlist($this->ftp_stream, $path);
	}
	
	/**
	 * verificar se o arquivo existe no diretório
	 * @param string $path
	 * @param string $find
	 * @return bool
	 */
	public function file_exist($path, $filename)
	{
		 $array = ftp_nlist($this->ftp_stream, $path);
		 return in_array($filename, $array);
	}
	
	/**
	 * retornar o ftp_stream
	 */
	public function getStream()
	{
		return $this->ftp_stream;
	}
}