<?php
namespace Application\Classes;

/**
 * @NAICHE | Deco
 * Class with all messages
 */
class MailMessage extends \Tropaframework\Email\Mail
{
	private $url = null;
	
	private $email_admin = array('vitordeco@gmail.com');
	
	public function __construct()
	{
		$this->url = 'http://'.$_SERVER["HTTP_HOST"];
	}
	
	public function contatoSucesso($to, $replace)
	{
	    $message = '
			<p>Novo contato enviado pelo site em ' . date('d/m/Y H:i:s') . ', segue os dados que foram preenchidos:</p>
			<p><b>Nome:</b> {nome}</p>
			<p><b>Telefone:</b> {telefone}</p>
			<p><b>Email:</b> {email}</p>
			<p><b>Assunto:</b>{assunto}</p>
			<p><b>Mensagem:</b>{mensagem}</p>
		';
	    
	    $this->setSubject = 'Novo contato - GOOVER';
	    $this->addTo = $to;
	    return $this->sendReplace($message, $replace);
	}	

    private function sendReplace($message, $replace)
    {
    	//array replace add items
    	$replace['url'] = $this->url;
    	
    	//array search
    	$search = array();
    	foreach( array_keys($replace) as $value ) $search[] = '{' . $value . '}';
    	
    	//replace
    	$message = str_replace($search, $replace, $message);
    	
    	//trim
    	$message = trim($message);
    	
    	return $this->send($message);
    }
    
    protected function createHTML($msg)
    {
    	$html = '
    	<table border="0" cellspacing="0" style="font-family:\'Arial\'; font-size:14px; color:#777; margin:auto;">
    		<tr>
    			<td>
    				<p style="text-align:center; margin:10px 0; color:#000;">
    					E-mail enviado por <a href="http://'.$_SERVER["HTTP_HOST"].'" style="color:#000; text-decoration:none;">Jurídico Já</a>.
    				</p>
    			</td>
    		</tr>
    
    		<tr>
    			<td style="width:600px; background-color:#fff; padding:20px; text-align:center;">
    				<a href="http://'.$_SERVER["HTTP_HOST"].'">
    					<img src="http://'.$_SERVER["HTTP_HOST"].'/assets/application/img/layout/logotipo.png" />
    				</a>
    			</td>
    		</tr>
    
    		<tr>
    			<td style="width:600px; background-color:#fff; color:#000; padding:30px; font-size:14px; border-bottom:15px solid #ff7500;">
    				' . $msg . '
    			</td>
    		</tr>
    	</table>
    	';
    
    	return $html;
    }
}